<?php
session_start();
include("db.php");

/* -------------------------
   SIMPLE AUTH CHECK
------------------------- */
if(!isset($_SESSION['user'])){
    die("Access denied.");
}

/* -------------------------
   SESSION TIMEOUT (30 mins)
------------------------- */
if(isset($_SESSION['last_activity'])){

    if(time() - $_SESSION['last_activity'] > 1800){

        session_destroy();
        header("Location: login.php");
        exit();

    }
}

$_SESSION['last_activity'] = time();

/* -------------------------
   CSRF TOKEN
------------------------- */
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

/* -------------------------
   ADD STUDENT
------------------------- */
if(isset($_POST['add'])){

    if(!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
        die("Invalid CSRF token.");
    }

    $student_id = trim($_POST['student_id']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $course_description = trim($_POST['course_description']);

    if(
        empty($student_id) ||
        empty($fullname) ||
        empty($email) ||
        empty($course) ||
        empty($course_description)
    ){
        $error = "All fields are required.";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format.";
    }
    else{

        $check = mysqli_prepare($conn,
            "SELECT id FROM students WHERE student_id = ?"
        );

        mysqli_stmt_bind_param($check, "s", $student_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if(mysqli_stmt_num_rows($check) > 0){
            $error = "Student ID already exists.";
        } else {

            $query = "INSERT INTO students
                     (student_id, fullname, email, course, course_description)
                     VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $student_id,
                $fullname,
                $email,
                $course,
                $course_description
            );

            if(mysqli_stmt_execute($stmt)){
                $success = "Student added successfully!";
            } else {
                $error = "Database insert failed.";
            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Student</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(rgba(15,23,42,0.78), rgba(15,23,42,0.78)),
    url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1400&auto=format&fit=crop') center/cover;
    padding:40px;
}

.container{
    width:1000px;
    display:flex;
    border-radius:24px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 20px 50px rgba(0,0,0,0.35);
}

.left-panel{
    width:45%;
    background:linear-gradient(135deg,#2563eb,#1e3a8a);
    color:white;
    padding:60px 45px;
}

.right-panel{
    width:55%;
    padding:55px 50px;
}

input{
    width:100%;
    padding:15px;
    border:1px solid #d1d5db;
    border-radius:12px;
    margin-bottom:10px;
    background:#f9fafb;
}

input:focus{
    border-color:#2563eb;
    outline:none;
}

.btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:#2563eb;
    color:white;
    font-weight:600;
    cursor:pointer;
}

/* BACK BUTTON */
.back-btn{
    display:block;
    text-align:center;
    margin-top:15px;
    padding:12px;
    background:#111827;
    color:white;
    text-decoration:none;
    border-radius:10px;
}

/* ALERT */
.error{background:#fee2e2;color:#b91c1c;padding:12px;border-radius:8px;margin-bottom:10px;}
.success{background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:10px;}

/* MODAL */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    display:none;
    justify-content:center;
    align-items:center;
}

.modal.show{display:flex;}

.modal-box{
    background:#fff;
    padding:25px;
    border-radius:15px;
    width:380px;
    text-align:center;
}

.actions{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.cancel-btn,.confirm-btn{
    flex:1;
    padding:10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.cancel-btn{background:#e5e7eb;}
.confirm-btn{background:#2563eb;color:white;}

/* 🔴 ERROR INPUT */
.input-error{
    border:2px solid #ef4444 !important;
    background:#fee2e2 !important;
}

</style>

</head>

<body>

<div class="container">

<div class="left-panel">
<h1>Student Management</h1>
<p>Manage records easily and securely.</p>
</div>

<div class="right-panel">

<h2>Add Student</h2>

<?php if($error): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<input type="text" name="student_id" placeholder="Student ID">
<input type="text" name="fullname" placeholder="Full Name">
<input type="email" name="email" placeholder="Email">
<input type="text" name="course" placeholder="Course">
<input type="text" name="course_description" placeholder="Course Description">

<button type="button" class="btn" id="openModal">Add Student</button>
<button type="submit" name="add" id="realSubmit" hidden></button>

</form>

<a href="dashboard.php" class="back-btn">← Back</a>

</div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal" id="confirmModal">
<div class="modal-box">
<h3>Confirm</h3>
<p>Are you sure you want to add this student?</p>

<div class="actions">
<button class="cancel-btn" id="closeModal">No</button>
<button class="confirm-btn" id="confirmSubmit">Yes</button>
</div>

</div>
</div>

<!-- ERROR MODAL -->
<div class="modal" id="errorModal">
<div class="modal-box">
<h3 style="color:red;">Error</h3>
<p>Please fill all fields.</p>
<button class="confirm-btn" id="closeError">OK</button>
</div>
</div>

<script>

const modal = document.getElementById("confirmModal");
const errorModal = document.getElementById("errorModal");

const inputs = document.querySelectorAll("input[name='student_id'], input[name='fullname'], input[name='email'], input[name='course'], input[name='course_description']");

document.getElementById("openModal").onclick = ()=>{
    modal.classList.add("show");
};

document.getElementById("closeModal").onclick = ()=>{
    modal.classList.remove("show");
};

document.getElementById("closeError").onclick = ()=>{
    errorModal.classList.remove("show");
};

document.getElementById("confirmSubmit").onclick = ()=>{

    let hasError = false;

    inputs.forEach(i=>{
        if(i.value.trim() === ""){
            i.classList.add("input-error");
            hasError = true;
        }else{
            i.classList.remove("input-error");
        }
    });

    if(hasError){
        modal.classList.remove("show");
        errorModal.classList.add("show");
        return;
    }

    document.querySelector("button[name='add']").click();
};

window.onclick = function(e){
    if(e.target.classList.contains("modal")){
        e.target.classList.remove("show");
    }
};

</script>

</body>
</html>