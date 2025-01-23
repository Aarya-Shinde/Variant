<?php
include '../db/dbconnect.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'exists';  // Email exists
    } else {
        echo 'not exists';  // Email does not exist
    }

    $stmt->close();
    $conn->close();
}
?>
