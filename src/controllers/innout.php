<?php

session_start();

requireValidSession();

$user = $_SESSION['user'];

$records = WorkingHours::loadFromUserAndDate($user->id, date('Y-m-d'));

try {
    $currentTime = strftime('%H:%M:%S', time());
    
    // IP Capture Local + Hostname
    $local_ip = $_SERVER['REMOTE_ADDR'];
    $hostname = gethostbyaddr($local_ip);
    
    $remote_ip = $local_ip;
    if ($hostname !== $local_ip && $hostname !== false) {
        $remote_ip = $local_ip . ' - ' . $hostname;
    }
    
    if (!$records->time1) {
        $records->last_ip = "Ent: " . $remote_ip;
    } else {
        $records->last_ip = $records->last_ip . " | Sai: " . $remote_ip;
    }

    $obs = isset($_POST['obs']) ? trim($_POST['obs']) : '';

    $records->innout($currentTime, $obs);
    
    addSuccessMsg('Ponto inserido com sucesso!');
} catch(AppException $e) {
    addErrorMsg($e->getMessage());
}

header('Location: day_records.php');