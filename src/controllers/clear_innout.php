<?php

session_start();
requireValidSession();

$user = $_SESSION['user'];

// Verifica se é admin
if (!$user->is_admin) {
    addErrorMsg('Acesso negado.');
    header('Location: day_records.php');
    exit;
}

// Verifica se foi passado um user_id e date específico, senão usa o do próprio admin hoje
$targetUserId = isset($_GET['user_id']) ? $_GET['user_id'] : $user->id;
$targetDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$records = WorkingHours::loadFromUserAndDate($targetUserId, $targetDate);

try {
    if ($records->id) {
        Database::executeSQL("DELETE FROM working_hours WHERE id = {$records->id}");
        addSuccessMsg('O registro de ponto foi limpo com sucesso!');
    } else {
        addErrorMsg('Não há registros de ponto para limpar neste dia.');
    }
} catch(Exception $e) {
    addErrorMsg('Erro ao limpar ponto.');
}

// Retorna para onde veio
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'day_records.php';
header("Location: {$referer}");