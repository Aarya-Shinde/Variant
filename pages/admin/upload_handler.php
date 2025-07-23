<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['epub_file']) && isset($_POST['novel_id'])) {
        $uploadDir = __DIR__ . '/text_extractor/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpFile = $_FILES['epub_file']['tmp_name'];
        $filename = basename($_FILES['epub_file']['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpFile, $targetPath)) {
            $novelId = escapeshellarg($_POST['novel_id']);
            $filePath = escapeshellarg($targetPath);
            $pythonScript = __DIR__ . '/text_extractor/extract_epub.py';

            // Run the Python script with arguments
            $cmd = "python \"$pythonScript\" $filePath $novelId";
            $output = shell_exec($cmd);

            echo "Success: " . nl2br($output);
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Missing file or novel ID.";
    }
}
?>
