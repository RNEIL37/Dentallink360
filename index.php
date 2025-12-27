<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dentallink";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Prepare statement
$stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// Check user exists
if ($stmt->num_rows > 0) {

    $stmt->bind_result($id, $fullname, $db_password, $role);
    $stmt->fetch();

    // Compare plain text (change later to hashed)
    if ($password === $db_password) {

        // Save session
        $_SESSION['user_id'] = $id;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['role'] = $role;

        // Redirect by role
        if ($role === "admin") {
            $redirect = "admin/admin_profile_view.html";
        } elseif ($role === "staff") {
            $redirect = "staff/dashboard.php";
        } else {
            //userdashboard>>>
            $redirect = "staff/Dashboard.html";
        }

        echo "<script>
                alert('Login successful! Welcome, $fullname');
                window.location='$redirect';
              </script>";

    } else {
        echo "<script>
                alert('Incorrect password!');
                window.location='index.html';
              </script>";
    }

} else {
    echo "<script>
            window.location='index.html';
          </script>";
}

$stmt->close();
$conn->close();
?>
