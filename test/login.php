<?php 
include '../db/dbconnect.php'; // Ensure $conn is correctly initialized

session_start();

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT password_hash, is_admin FROM Users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_password_hash, $is_admin);
                $stmt->fetch();

                if (password_verify($password, $db_password_hash)) {
                    session_regenerate_id(true);
                    $_SESSION['email'] = $email;
                    $_SESSION['is_admin'] = $is_admin;

                    // Redirect based on role
                    if ($is_admin) {
                        header("Location: ../test/admin_dashboard.php");
                    } else {
                        header("Location: ../test/user_dashboard.php");
                    }
                    
                    exit();
                } else {
                    $message = "Incorrect password";
                    $toastClass = "bg-danger";
                }
            } else {
                $message = "Email not found";
                $toastClass = "bg-warning";
            }
            $stmt->close();
        } else {
            die("SQL prepare failed: " . $conn->error);
        }
    } else {
        $message = "Invalid email format";
        $toastClass = "bg-warning";
    }
}

$conn->close();
?>

