<?php
session_start();
requireValidSession(true);

$yearAndMonth = (new DateTime())->format('Y-m');

$sql = "SELECT u.name, wh.work_date, wh.time1, wh.time2, wh.worked_time 
        FROM working_hours wh 
        JOIN users u ON u.id = wh.user_id 
        WHERE wh.work_date LIKE '{$yearAndMonth}-%'
        ORDER BY u.name, wh.work_date";

$result = Database::getResultFromQuery($sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_horas_' . $yearAndMonth . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Nome', 'Data', 'Entrada', 'Saida', 'Horas Trabalhadas'));

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $horas = getTimeStringFromSeconds($row['worked_time']);
        $data = (new DateTime($row['work_date']))->format('d/m/Y');
        fputcsv($output, array($row['name'], $data, $row['time1'], $row['time2'], $horas));
    }
}
fclose($output);
exit();