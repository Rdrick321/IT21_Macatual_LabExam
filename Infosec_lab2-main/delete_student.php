<?php
session_start();
include("db.php");

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','superadmin'])){
    die("Access denied");
}

$id = $_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: dashboard.php");
exit();
?>