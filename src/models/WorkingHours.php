<?php

class WorkingHours extends Model {
    protected static $tableName = 'working_hours';
    
    protected static $columns = [
        'id',
        'user_id',
        'work_date',
        'time1',
        'time2',
        'time3',
        'time4',
        'last_ip',
        'worked_time',
        'obs_time1',
        'obs_time2'
    ];

    public static function loadFromUserAndDate($userId, $workDate) {
        $registry = self::getOne(['user_id' => $userId, 'work_date' => $workDate]);

        if (!$registry) {
            $registry = new WorkingHours([
                'user_id' => $userId,
                'work_date' => $workDate,
                'worked_time' => 0
            ]);
        }

        return $registry;
    }

    public function getNextTime() {
        if (!$this->time1) return 'time1';
        if (!$this->time2) return 'time2';
        return null;
    }

    public function getActiveClock() {
        $nextTime = $this->getNextTime();

        if ($nextTime === 'time1') {
            return null;
        } elseif ($nextTime === 'time2') {
            return 'workedInterval';
        } else {
            return null;
        }
    }

    public function innout($time, $obs = '') {
        $timeColumn = $this->getNextTime();

        if (!$timeColumn) {
            throw new AppException("Você já fez os 2 registros do dia!");
        }

        $this->$timeColumn = $time;
        
        if ($timeColumn === 'time1') {
            $this->obs_time1 = $obs;
        } elseif ($timeColumn === 'time2') {
            $this->obs_time2 = $obs;
        }
        
        $this->worked_time = getSecondsFromDateInterval($this->getWorkedInterval());
        
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    function getWorkedInterval() {
        [$t1, $t2] = $this->getTimes();

        $part1 = new DateInterval('PT0S');

        if ($t1) $part1 = $t1->diff(new DateTime());
        if ($t2) $part1 = $t1->diff($t2);

        return $part1;
    }

    function getLunchInterval() {
        return new DateInterval('PT0S');
    }

    function getExitTime() {
        [$t1, $t2] = $this->getTimes();

        $workday = DateInterval::createFromDateString('8 hours');

        if (!$t1) {
            return null;
        } elseif ($t2) {
            return $t2;
        } else {
            return $t1->add($workday);
        }
    }

    function getBalance() {
        if (isset($this->isWorkingPeriod) && !$this->isWorkingPeriod) {
            return '';
        }

        // Se não tiver batido ponto, não gera saldo negativo (escalas mistas)
        if (!$this->time1) return '';

        if ($this->worked_time == DAILY_TIME) return '-';

        $balance = $this->worked_time - DAILY_TIME;
        
        $balanceString = getTimeStringFromSeconds(abs($balance));
        
        $sign = $this->worked_time >= DAILY_TIME ? '+' : '-';
        
        return "{$sign}{$balanceString}";
    }

    public static function getAbsentUsers() {
        $today = new DateTime();

        $result = Database::getResultFromQuery("
            SELECT 
                name 
            FROM 
                users
            WHERE 
                end_date is NULL
            AND 
                id NOT IN (
                    SELECT 
                        user_id 
                    FROM 
                        working_hours
                    WHERE 
                        work_date = '{$today->format('Y-m-d')}'
                    AND 
                        time1 IS NOT NULL
                )
        ");

        $absentUsers = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($absentUsers, $row['name']);
            }
        }

        return $absentUsers;
    }

    public static function getWorkedTimeInMonth($yearAndMonth) {
        $startDate = (new DateTime("{$yearAndMonth}-1"))->format('Y-m-d');
        $endDate = getLastDayOfMonth($yearAndMonth)->format('Y-m-d');
        
        $result = static::getResultSetFromSelect([
            'raw' => "work_date BETWEEN '{$startDate}' AND '{$endDate}'"
        ], "sum(worked_time) as sum");
        
        return $result->fetch_assoc()['sum'];
    }

    public static function getMonthlyReport($userId, $date) {
        $registries = [];

        $startDate = getFirstDayOfMonth($date)->format('Y-m-d');
        $endDate = getLastDayOfMonth($date)->format('Y-m-d');

        $result = static::getResultSetFromSelect([
            'user_id' => $userId,
            'raw' => "work_date BETWEEN '{$startDate}' AND '{$endDate}'"
        ]);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $registries[$row['work_date']] = new WorkingHours($row);
            }
        }
        
        return $registries;
    }

    private function getTimes() {
        $times = [];

        $this->time1 ? array_push($times, getDateFromString($this->time1)) : array_push($times, null);
        $this->time2 ? array_push($times, getDateFromString($this->time2)) : array_push($times, null);

        return $times;
    }

    public static function getWorkingNow() {
        $today = date('Y-m-d');
        $sql = "SELECT u.name, wh.time1 
                FROM working_hours wh 
                JOIN users u ON u.id = wh.user_id 
                WHERE wh.work_date = '{$today}' 
                AND wh.time1 IS NOT NULL 
                AND wh.time2 IS NULL";
        
        $result = Database::getResultFromQuery($sql);
        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public static function getIncompletePunches() {
        $today = date('Y-m-d');
        $sql = "SELECT u.name, wh.work_date, wh.time1 
                FROM working_hours wh 
                JOIN users u ON u.id = wh.user_id 
                WHERE wh.work_date < '{$today}' 
                AND wh.time1 IS NOT NULL 
                AND wh.time2 IS NULL
                ORDER BY wh.work_date DESC";
        
        $result = Database::getResultFromQuery($sql);
        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public static function getHoursByEmployeeMonth($yearAndMonth) {
        $sql = "SELECT u.name, SUM(wh.worked_time) as total_seconds 
                FROM working_hours wh 
                JOIN users u ON u.id = wh.user_id 
                WHERE wh.work_date LIKE '{$yearAndMonth}-%' 
                GROUP BY u.id, u.name 
                ORDER BY total_seconds DESC";
        
        $result = Database::getResultFromQuery($sql);
        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

}

