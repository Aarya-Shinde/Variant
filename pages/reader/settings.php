<?php
session_start();
include '../../db/dbconnect.php';

$email = $_SESSION['email'];
$query = "SELECT * FROM Users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : null;

    // Check if the current password is correct
    if (!password_verify($currentPassword, $user['password_hash'])) {
        echo "<script>alert('Incorrect current password.');</script>";
    } else {
        $checkEmailQuery = "SELECT email FROM Users WHERE email = ? AND email != ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("ss", $newEmail, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            if ($newPassword) {
                $updateQuery = "UPDATE Users SET username = ?, email = ?, password_hash = ? WHERE email = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssss", $newUsername, $newEmail, $newPassword, $email);
            } else {
                $updateQuery = "UPDATE Users SET username = ?, email = ? WHERE email = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sss", $newUsername, $newEmail, $email);
            }

            if ($stmt->execute()) {
                $_SESSION['email'] = $newEmail;
                echo "<script>alert('Profile updated successfully!');</script>";
            }
        } else {
            echo "<script>alert('This email is already in use.');</script>";
        }
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4" style="font-family: 'Caveat', cursive; color: #4b3f3f;">Profile Settings</h2>
    <form method="POST" class="p-4 border rounded" style="background-color: #f9f5e3; border: 2px solid #dbc1ac; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
        <div class="mb-3">
            <label class="form-label" style="font-family: 'Merriweather', serif; color: #4b3f3f;">Username:</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" style="font-family: 'Merriweather', serif; color: #4b3f3f;">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" style="font-family: 'Merriweather', serif; color: #4b3f3f;">Current Password:</label>
            <input type="password" class="form-control" name="current_password" required>
        </div>
        <div class="mb-3">
            <label class="form-label" style="font-family: 'Merriweather', serif; color: #4b3f3f;">New Password (optional):</label>
            <input type="password" class="form-control" name="new_password">
        </div>
        <button type="submit" class="btn btn-primary" style="background-color: #4b3f3f; border-color: #4b3f3f;">Update Profile</button>
    </form>
</div>
