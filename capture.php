<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log the request
error_log("Received POST request: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $csv_file = $_POST['csv_file'] ?? 'emails.csv';

    error_log("Processed email: " . $email);
    error_log("CSV file: " . $csv_file);

    if ($email && $csv_file) {
        // Check if file exists, if not create it with headers
        if (!file_exists($csv_file)) {
            error_log("Creating new CSV file: " . $csv_file);
            $fp = fopen($csv_file, 'w');
            if ($fp === false) {
                error_log("Failed to create CSV file");
                echo json_encode(['success' => false, 'message' => 'Could not create file']);
                exit;
            }
            fputcsv($fp, ['Email', 'Timestamp'], ",", '"', "\\");
            fclose($fp);
        }

        // Append the new entry
        $fp = fopen($csv_file, 'a');
        if ($fp === false) {
            error_log("Failed to open CSV file for writing");
            echo json_encode(['success' => false, 'message' => 'Could not open file for writing']);
            exit;
        }

        $data = [$email, date('Y-m-d H:i:s')];
        fputcsv($fp, $data, ",", '"', "\\");
        fclose($fp);
        error_log("Successfully wrote to CSV file");
        echo json_encode(['success' => true]);
    } else {
        error_log("Invalid input - Email: " . ($email ? 'valid' : 'invalid') . ", CSV file: " . ($csv_file ? 'valid' : 'invalid'));
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 