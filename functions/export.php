<?php
require_once __DIR__ . "/db.php";
function exportToCsv($stmt, $filename = 'export.csv') {
    try {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fwrite($output, "\xEF\xBB\xBF");

        $first_row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($first_row) {
            fputcsv($output, array_keys($first_row));
            fputcsv($output, $first_row);
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } catch (PDOException $e) {
        die("Ошибка экспорта: " . $e->getMessage());
    }
}
?>