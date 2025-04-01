<?php
session_start();
include '../../db/dbconnect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file']) && isset($_POST['novel_id'])) {
    $novel_id = intval($_POST['novel_id']);
    error_log("Novel ID received: " . $novel_id);

    // File storage path
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = basename($_FILES["file"]["name"]);
    $target_file = $upload_dir . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $filename); // Sanitize filename

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

        echo "Debug: Novel ID being passed: " . $novel_id;

        // Call Python script for extraction
        $python_script = "D:\\Xammp\\htdocs\\Variant\\text_extractor\\split_upload.py";
        $command = "python \"$python_script\" \"$target_file\" $novel_id";
        echo "Running command: $command";

        $output = shell_exec($command);
        error_log("Python script output: " . $output);

        header("Location: ../admin_dashboard.php?success=File uploaded and processed successfully.");
        exit();
    } else {
        header("Location: ../admin_dashboard.php?error=Error uploading file.");
        exit();
    }
} else {
    header("Location: ../admin_dashboard.php?error=Invalid request.");
    exit();
}
?>
