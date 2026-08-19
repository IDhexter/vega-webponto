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
    $registry = isset($registries[$date]) && $registries[$date]? $registries[$date]: null;
    
    $isWorkingPeriod = true;
    if (strtotime($date) < strtotime($selectedUser->start_date)) {
        $isWorkingPeriod = false;
    }
    if ($selectedUser->end_date && strtotime($date) > strtotime($selectedUser->end_date)) {
        $isWorkingPeriod = false;
    }

    // Apenas conta como dia de trabalho esperado se a pessoa realmente bateu o ponto.
    // Isso evita gerar -8h em dias que ela simplesmente não estava escalada (escalas mistas).
    if($isWorkingPeriod && $registry && $registry->time1) {
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