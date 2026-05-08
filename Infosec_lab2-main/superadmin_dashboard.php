<?php
session_start();
include("db.php");

/* =========================
   AUTH CHECK
========================= */
if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'superadmin'){
    header("Location: login.php");
    exit();
}

/* =========================
   SESSION TIMEOUT
========================= */
if(isset($_SESSION['last_activity'])){
    if(time() - $_SESSION['last_activity'] > 1800){
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
$_SESSION['last_activity'] = time();

/* =========================
   FLASH MESSAGES
========================= */
$error = $_SESSION['error'] ?? "";
$success = $_SESSION['success'] ?? "";
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Super Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins;}
body{background:#f1f5f9;}

/* ================= HEADER ================= */
.header{
    background:#fff;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
}

.btn{
    padding:10px 15px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
}

.primary{background:#2563eb;color:#fff;}
.danger{background:#ef4444;color:#fff;}
.info{background:#10b981;color:#fff;}

/* ================= TABLE ================= */
.container{
    max-width:1200px;
    margin:30px auto;
    padding:0 20px;
}

table{
    width:100%;
    background:#fff;
    border-collapse:collapse;
    border-radius:10px;
    overflow:hidden;
}

th{
    background:#2563eb;
    color:#fff;
    padding:14px;
}

td{
    padding:14px;
    border-bottom:1px solid #eee;
}

/* ================= ALERT ================= */
.alert{
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

.success{background:#dcfce7;color:#166534;}
.error{background:#fee2e2;color:#b91c1c;}

/* ================= MODAL ================= */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:999;
}

.modal.show{display:flex;}

.modal-box{
    background:#fff;
    width:420px;
    padding:25px;
    border-radius:15px;
    text-align:center;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:8px;
}

.actions{
    display:flex;
    gap:10px;
    margin-top:10px;
}

.actions button,
.actions a{
    flex:1;
    padding:10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    text-decoration:none;
    text-align:center;
}

.cancel{background:#e5e7eb;}
.confirm{background:#2563eb;color:#fff;}
.danger-btn{background:#ef4444;color:#fff;}

</style>
</head>

<body>

<!-- HEADER -->
<div class="header">

    <h3>Super Admin Dashboard</h3>

    <div>

        <!-- NEW BUTTON (ADMIN DASHBOARD ACCESS) -->
        <a href="dashboard.php" class="btn info">
            Admin Dashboard
        </a>

        <button class="btn primary" onclick="openCreate()">+ Create Admin</button>
        <button class="btn danger" onclick="openLogout()">Logout</button>
    </div>

</div>

<div class="container">

    <?php if($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <h3>Admin List</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
        </tr>

        <?php
        $result = mysqli_query($conn, "SELECT * FROM users WHERE role='admin'");
        while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['role'] ?></td>
        </tr>
        <?php } ?>
    </table>

</div>

<!-- CREATE MODAL -->
<div class="modal" id="createModal">
    <div class="modal-box">
        <h3>Create Admin</h3>
        <p>Enter admin details</p>

        <form onsubmit="return openConfirm(event)">
            <input type="text" id="username" placeholder="Username">
            <input type="password" id="password" placeholder="Password">

            <div class="actions">
                <button type="button" class="cancel" onclick="closeCreate()">Cancel</button>
                <button type="submit" class="confirm">Next</button>
            </div>
        </form>

    </div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal" id="confirmModal">
    <div class="modal-box">

        <h3>Confirm Creation</h3>
        <p>Are you sure?</p>

        <div class="actions">

            <button class="cancel" onclick="closeConfirm()">No</button>

            <form method="POST" action="create_admin.php">
                <input type="hidden" name="username" id="finalUsername">
                <input type="hidden" name="password" id="finalPassword">
                <button type="submit" class="confirm">Yes</button>
            </form>

        </div>

    </div>
</div>

<!-- ERROR MODAL -->
<div class="modal" id="errorModal">
    <div class="modal-box">
        <h3 style="color:#b91c1c;">Error</h3>
        <p id="errorText"></p>
        <button class="danger-btn" onclick="closeError()">Close</button>
    </div>
</div>

<!-- LOGOUT MODAL -->
<div class="modal" id="logoutModal">
    <div class="modal-box">
        <h3>Logout</h3>
        <p>Are you sure?</p>

        <div class="actions">
            <button class="cancel" onclick="closeLogout()">Cancel</button>
            <a href="logout.php" class="danger-btn">Logout</a>
        </div>

    </div>
</div>

<script>

function openCreate(){
    document.getElementById('createModal').classList.add('show');
}

function closeCreate(){
    document.getElementById('createModal').classList.remove('show');
}

function openConfirm(e){
    e.preventDefault();

    let u = document.getElementById('username').value;
    let p = document.getElementById('password').value;

    if(u === "" || p === ""){
        showError("All fields required");
        return false;
    }

    document.getElementById('finalUsername').value = u;
    document.getElementById('finalPassword').value = p;

    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirm(){
    document.getElementById('confirmModal').classList.remove('show');
}

function showError(msg){
    document.getElementById('errorText').innerText = msg;
    document.getElementById('errorModal').classList.add('show');
}

function closeError(){
    document.getElementById('errorModal').classList.remove('show');
}

function openLogout(){
    document.getElementById('logoutModal').classList.add('show');
}

function closeLogout(){
    document.getElementById('logoutModal').classList.remove('show');
}

window.onclick = function(e){
    if(e.target.classList.contains("modal")){
        e.target.classList.remove("show");
    }
};

</script>

</body>
</html>