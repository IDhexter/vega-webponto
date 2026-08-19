<?php

session_start();

requireValidSession();

$currentDate = new DateTime();

$user = $_SESSION['user'];
$selectedUserId = $user->id;
$users = null;

if ($user->is_admin) {
    $users = User::get();
    $selectedUserId = isset($_POST['user']) && $_POST['user'] ? $_POST['user'] : $user->id;
    
    // PROCESS PUNCH EDITS
    if (isset($_POST['punches']) && is_array($_POST['punches'])) {
        foreach ($_POST['punches'] as $date => $times) {
            $t1 = isset($times['time1']) && trim($times['time1']) ? trim($times['time1']) : null;
            $t2 = isset($times['time2']) && trim($times['time2']) ? trim($times['time2']) : null;
            
            if ($t1 && strlen($t1) == 5) $t1 .= ':00';
            if ($t2 && strlen($t2) == 5) $t2 .= ':00';
            
            $workedTime = 0;
            if ($t1 && $t2) {
                $workedTime = strtotime($t2) - strtotime($t1);
                if ($workedTime < 0) $workedTime = 0;
            }
            
            $existing = WorkingHours::getOne(['user_id' => $selectedUserId, 'work_date' => $date]);
            
            if ($existing) {
                // Ignore if it hasn't changed (optimization)
                if ($existing->time1 != $t1 || $existing->time2 != $t2) {
                    $existing->time1 = $t1;
                    $existing->time2 = $t2;
                    $existing->worked_time = $workedTime;
                    $existing->update();
                }
            } else if ($t1 || $t2) {
                $newPunch = new WorkingHours([
                    'user_id' => $selectedUserId,
                    'work_date' => $date,
                    'time1' => $t1,
                    'time2' => $t2,
                    'worked_time' => $workedTime
                ]);
                $newPunch->insert();
            }
        }
        addSuccessMsg('Horários atualizados com sucesso!');
    }
}

$selectedPeriod = isset($_POST['period']) && $_POST['period'] ? $_POST['period'] : $currentDate->format('Y-m');
$periods = [];
for ($yearDiff = 0; $yearDiff <= 2; $yearDiff++) {
    $year = date('Y') - $yearDiff;
    for($month = 12; $month >= 1; $month--) {
        $date = new DateTime("{$year}-{$month}-1");
        $periods[$date->format('Y-m')] = strftime('%B de %Y', $date->getTimestamp());
    }
}

$registries = WorkingHours::getMonthlyReport($selectedUserId, $selectedPeriod);
$selectedUser = User::getOne(['id' => $selectedUserId]);

$report = [];
$workDay = 0;
$sumOfWorkedTime = 0;
$lastDay = getLastDayOfMonth(new DateTime("{$selectedPeriod}-1"))->format('d');

for($day = 1; $day <= $lastDay; $day++) {
    $date = $selectedPeriod . '-' . sprintf('%02d', $day);
    $registry = isset($registries[$date]) && $registries[$date]? $registries[$date] : null;

    $isWorkingPeriod = true;
    if (strtotime($date) < strtotime($selectedUser->start_date)) {
        $isWorkingPeriod = false;
    }
    if ($selectedUser->end_date && strtotime($date) > strtotime($selectedUser->end_date)) {
        $isWorkingPeriod = false;
    }

    if ($isWorkingPeriod && $registry && $registry->time1) {
        $workDay++;
    }

    if($registry) {
        $sumOfWorkedTime += $registry->worked_time;
        $registry->isWorkingPeriod = $isWorkingPeriod;
        array_push($report, $registry);
    } else {
        $newRegistry = new WorkingHours([
            'work_date' => $date,
            'worked_time' => 0
        ]);
        $newRegistry->isWorkingPeriod = $isWorkingPeriod;
        array_push($report, $newRegistry);
    }
}

$expectedTime = $workDay * DAILY_TIME;
$balance = getTimeStringFromSeconds(abs($sumOfWorkedTime - $expectedTime));
$sign = ($sumOfWorkedTime >= $expectedTime) ? '+' : '-';

loadTemplateView('monthly_report', [
    'report' => $report,
    'sumOfWorkedTime' => getTimeStringFromSeconds($sumOfWorkedTime),
    'balance' => "{$sign}{$balance}",
    'selectedPeriod' => $selectedPeriod,
    'periods' => $periods,
    'selectedUserId' => $selectedUserId,
    'users' => $users,
]);