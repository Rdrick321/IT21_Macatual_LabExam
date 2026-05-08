<?php
session_start();
include("db.php");

if($_SESSION['role'] !== 'superadmin'){
    $_SESSION['error'] = "Access denied";
    header("Location: superadmin_dashboard.php");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if(empty($username) || empty($password)){
    $_SESSION['error'] = "All fields required";
    header("Location: superadmin_dashboard.php");
    exit();
}

if(strlen($password) < 8){
    $_SESSION['error'] = "Password too short";
    header("Location: superadmin_dashboard.php");
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO users(username,password,role) VALUES(?,?,?)");
$role = "admin";

mysqli_stmt_bind_param($stmt, "sss", $username, $hashed, $role);

if(mysqli_stmt_execute($stmt)){
    $_SESSION['success'] = "Admin created successfully!";
} else {
    $_SESSION['error'] = "Failed to create admin";
}

header("Location: superadmin_dashboard.php");
exit();
?>