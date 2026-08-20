<?php

session_start();
requireValidSession();

$user = $_SESSION['user'];

$targetUserId = isset($_GET['user_id']) ? $_GET['user_id'] : $user->id;
$targetDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Só admin pode limpar ponto de outras pessoas ou de dias anteriores
if (($targetUserId != $user->id || $targetDate != date('Y-m-d')) && !$user->is_admin) {
    addErrorMsg('Acesso negado.');
    header('Location: day_records.php');
    exit;
}

$records = WorkingHours::loadFromUserAndDate($targetUserId, $targetDate);

try {
    if ($records->id) {
        if ($records->time2) {
            // Se tem saída, limpa apenas a saída
            $records->time2 = null;
            $records->obs_time2 = null;
            $records->worked_time = 0;
            
            // Remove o log "Sai:" do IP
            if (strpos($records->last_ip, ' | Sai:') !== false) {
                $parts = explode(' | Sai:', $records->last_ip);
                $records->last_ip = $parts[0];
            }
            
            $records->update();
            addSuccessMsg('Sua Saída foi desfeita. Você pode bater o ponto de saída novamente.');
        } elseif ($records->time1) {
            // Se só tem entrada, limpa a entrada (deleta o registro)
            Database::executeSQL("DELETE FROM working_hours WHERE id = {$records->id}");
            addSuccessMsg('Sua Entrada foi desfeita. O dia está zerado.');
        }
    } else {
        addErrorMsg('Não há registros de ponto para desfazer.');
    }
} catch(Exception $e) {
    addErrorMsg('Erro ao desfazer ponto.');
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'day_records.php';
header("Location: {$referer}");