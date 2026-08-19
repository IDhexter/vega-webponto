<?php

session_start();

requireValidSession(true);

$activeUsersCount = User::getActiveUsersCount();

$yearAndMonth = (new DateTime())->format('Y-m');

$seconds = WorkingHours::getWorkedTimeInMonth($yearAndMonth);
$hoursInMonth = explode(':', getTimeStringFromSeconds($seconds))[0];

$workingNow = WorkingHours::getWorkingNow();
$incompletePunches = WorkingHours::getIncompletePunches();
$hoursByEmployee = WorkingHours::getHoursByEmployeeMonth($yearAndMonth);

loadTemplateView('manager_report', [
    'activeUsersCount' => $activeUsersCount,
    'hoursInMonth' => $hoursInMonth,
    'workingNow' => $workingNow,
    'incompletePunches' => $incompletePunches,
    'hoursByEmployee' => $hoursByEmployee
]);
