<?php
session_start();
include("db.php");

/* AUTH CHECK */
if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin'){
    die("Access denied");
}

/* SESSION TIMEOUT */
if(isset($_SESSION['last_activity'])){
    if(time() - $_SESSION['last_activity'] > 1800){
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
$_SESSION['last_activity'] = time();

/* CHECK REQUEST */
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: dashboard.php");
    exit();
}

/* GET DATA */
$id = intval($_POST['id']);
$student_id = trim($_POST['student_id']);
$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$course = trim($_POST['course']);

/* VALIDATION */
if(empty($id) || empty($student_id) || empty($fullname) || empty($email) || empty($course)){
    $_SESSION['error'] = "All fields are required.";
    header("Location: dashboard.php");
    exit();
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['error'] = "Invalid email format.";
    header("Location: dashboard.php");
    exit();
}

/* UPDATE QUERY */
$query = "UPDATE students 
          SET student_id = ?, fullname = ?, email = ?, course = ?
          WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

if($stmt){

    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $student_id,
        $fullname,
        $email,
        $course,
        $id
    );

    if(mysqli_stmt_execute($stmt)){
        $_SESSION['success'] = "Student updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update student.";
    }

} else {
    $_SESSION['error'] = "Database error.";
}

header("Location: dashboard.php");
exit();
?>