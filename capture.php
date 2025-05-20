<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $csv_file = filter_input(INPUT_POST, 'csv_file', FILTER_SANITIZE_STRING);

    if ($email && $csv_file) {
        // Check if file exists, if not create it with headers
        if (!file_exists($csv_file)) {
            $fp = fopen($csv_file, 'w');
            fputcsv($fp, ['Email', 'Timestamp']);
            fclose($fp);
        }

        // Append the new entry
        $fp = fopen($csv_file, 'a');
        if ($fp === false) {
            echo json_encode(['success' => false, 'message' => 'Could not open file for writing']);
            exit;
        }

        $data = [$email, date('Y-m-d H:i:s')];
        fputcsv($fp, $data);
        fclose($fp);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 