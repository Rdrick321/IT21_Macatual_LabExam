<?php
include("db.php");

$username = "superadmin";
$password = password_hash("super123", PASSWORD_DEFAULT);
$role = "superadmin";

$query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);
mysqli_stmt_execute($stmt);

echo "Super Admin created!";
// session timeout (30 min)
if(time() - $_SESSION['last_activity'] > 1800){
    session_destroy();
    header("Location: login.php");
    exit();
}

$_SESSION['last_activity'] = time();
?>