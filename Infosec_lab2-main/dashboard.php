<?php
session_start();
include("db.php");

/* AUTH CHECK */
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin'){
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

/* FETCH STUDENTS */
$result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* ================= BASE ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f1f5f9;
    min-height:100vh;
    padding:30px;
}

.container{
    max-width:1350px;
    margin:auto;
}

/* HEADER */
.header{
    background:#ffffff;
    border-radius:20px;
    padding:25px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    margin-bottom:30px;
}

.header-left h1{
    font-size:32px;
    color:#0f172a;
}

.header-left p{
    color:#64748b;
    font-size:15px;
}

.header-right{
    display:flex;
    gap:15px;
}

/* BUTTONS */
.btn{
    padding:13px 22px;
    border-radius:12px;
    text-decoration:none;
    color:white;
    font-size:14px;
    font-weight:600;
}

.add-btn{background:#2563eb;}
.logout-btn{background:#ef4444;}
.primary{background:#16a34a;}

/* TABLE */
.table-card{
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.card-header{
    padding:25px 30px;
    border-bottom:1px solid #e2e8f0;
    display:flex;
    justify-content:space-between;
}

.student-count{
    background:#eff6ff;
    color:#2563eb;
    padding:10px 16px;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#2563eb;
}

thead th{
    color:white;
    padding:18px;
    text-align:left;
}

tbody td{
    padding:18px;
    border-bottom:1px solid #e2e8f0;
}

/* BUTTONS */
.edit-btn{
    background:#3b82f6;
    color:white;
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
    margin-right:6px;
}

.delete-btn{
    background:#ef4444;
    color:white;
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

/* MODAL */
.modal{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.55);
    display:flex;
    justify-content:center;
    align-items:center;
    opacity:0;
    visibility:hidden;
    transition:0.3s;
    z-index:999;
}

.modal.show{
    opacity:1;
    visibility:visible;
}

.modal-box{
    width:380px;
    background:#fff;
    padding:30px;
    border-radius:18px;
    text-align:center;
}

/* EDIT MODAL */
.clean-edit{
    width:420px;
    padding:0;
    border-radius:14px;
    overflow:hidden;
}

.modal-title{
    padding:20px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.form{
    padding:20px;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.form input{
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

/* ACTIONS */
.actions{
    display:flex;
    gap:10px;
    padding:15px;
    border-top:1px solid #eee;
}

.btn-cancel{
    flex:1;
    background:#e5e7eb;
    border:none;
    padding:10px;
    border-radius:10px;
    cursor:pointer;
}

.btn-save{
    flex:1;
    background:#2563eb;
    color:white;
    border:none;
    padding:10px;
    border-radius:10px;
    cursor:pointer;
}

</style>

</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['user']) ?></h1>
        <p>Manage students efficiently</p>
    </div>

    <div class="header-right">
        <a href="add_student.php" class="btn add-btn">+ Add Student</a>

        <?php if($_SESSION['role'] === 'superadmin'){ ?>
            <a href="superadmin_dashboard.php" class="btn primary">Super Admin Dashboard</a>
        <?php } ?>

        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

<!-- TABLE -->
<div class="table-card">

<div class="card-header">
<h2>Student Records</h2>
<div class="student-count">Total Students: <?php echo mysqli_num_rows($result); ?></div>
</div>

<table>
<thead>
<tr>
<th>ID</th>
<th>Student ID</th>
<th>Name</th>
<th>Email</th>
<th>Course</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['student_id'] ?></td>
<td><?= $row['fullname'] ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['course'] ?></td>

<td>
<a href="#" class="edit-btn openEdit"
data-id="<?= $row['id'] ?>"
data-student="<?= $row['student_id'] ?>"
data-name="<?= $row['fullname'] ?>"
data-email="<?= $row['email'] ?>"
data-course="<?= $row['course'] ?>">
Edit
</a>

<!-- FIXED DELETE -->
<a href="#" class="delete-btn openDelete" data-id="<?= $row['id'] ?>">
Delete
</a>

</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editModal">
<div class="modal-box clean-edit">

<div class="modal-title">
<h3>Edit Student</h3>
</div>

<form method="POST" action="update_student.php" id="editForm">
<input type="hidden" name="id" id="edit_id">

<div class="form">
<input type="text" name="student_id" id="edit_student_id">
<input type="text" name="fullname" id="edit_fullname">
<input type="email" name="email" id="edit_email">
<input type="text" name="course" id="edit_course">
</div>

<div class="actions">
<button type="button" class="btn-cancel" id="closeEdit">Cancel</button>
<button type="submit" class="btn-save">Save</button>
</div>

</form>

</div>
</div>

<!-- DELETE MODAL -->
<div class="modal" id="deleteModal">

<div class="modal-box">

<h3>Confirm Delete</h3>
<p>Are you sure you want to delete this student?</p>

<div class="actions">

<button class="btn-cancel" id="cancelDelete">No</button>

<form method="GET" id="deleteForm">
    <input type="hidden" name="id" id="delete_id">
    <button type="submit" class="btn-save" style="background:#ef4444;">Yes Delete</button>
</form>

</div>

</div>
</div>

<script>

const editModal = document.getElementById("editModal");
const deleteModal = document.getElementById("deleteModal");
const form = document.getElementById("editForm");

/* EDIT */
document.querySelectorAll(".openEdit").forEach(btn=>{
btn.addEventListener("click", function(e){
e.preventDefault();

document.getElementById("edit_id").value = this.dataset.id;
document.getElementById("edit_student_id").value = this.dataset.student;
document.getElementById("edit_fullname").value = this.dataset.name;
document.getElementById("edit_email").value = this.dataset.email;
document.getElementById("edit_course").value = this.dataset.course;

editModal.classList.add("show");
});
});

/* CLOSE EDIT */
document.getElementById("closeEdit").onclick = ()=>{
editModal.classList.remove("show");
};

/* DELETE OPEN */
document.querySelectorAll(".openDelete").forEach(btn=>{
btn.addEventListener("click", function(e){
e.preventDefault();

document.getElementById("delete_id").value = this.dataset.id;

document.getElementById("deleteForm").action = "delete_student.php?id=" + this.dataset.id;

deleteModal.classList.add("show");
});
});

/* CANCEL DELETE */
document.getElementById("cancelDelete").onclick = ()=>{
deleteModal.classList.remove("show");
};

/* CLOSE BACKDROP */
window.onclick = function(e){
if(e.target.classList.contains("modal")){
e.target.classList.remove("show");
}
};

</script>

</body>
</html>