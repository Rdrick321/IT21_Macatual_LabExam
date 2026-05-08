<?php

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
include("db.php");

$error = "";

/* prevent duplicate session issues */
if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
}

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)){
        $error = "All fields are required.";
    } else {

        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if($row = mysqli_fetch_assoc($result)){

            if(password_verify($password, $row['password'])){

                session_regenerate_id(true);

                $_SESSION['user'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['last_activity'] = time();

                if($row['role'] === 'superadmin'){
                    header("Location: superadmin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();

            } else {
                $error = "Invalid username or password";
            }

        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* GLOBAL */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
}

/* LOGIN CARD */
.container{
    width:380px;
    background:#ffffff;
    padding:35px;
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
    text-align:center;
    animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* TITLE */
h2{
    margin-bottom:8px;
    color:#0f172a;
    font-size:26px;
    font-weight:700;
}

.subtitle{
    font-size:13px;
    color:#64748b;
    margin-bottom:20px;
}

/* INPUT */
input{
    width:100%;
    padding:14px;
    margin:10px 0;
    border:1px solid #e2e8f0;
    border-radius:12px;
    outline:none;
    background:#f8fafc;
    transition:0.3s;
}

input:focus{
    border-color:#2563eb;
    background:#fff;
    box-shadow:0 0 0 4px rgba(37,99,235,0.15);
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:white;
    border:none;
    border-radius:12px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    margin-top:10px;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(37,99,235,0.3);
}

/* ERROR */
.error{
    background:#fee2e2;
    color:#b91c1c;
    padding:12px;
    border-radius:10px;
    margin-bottom:12px;
    font-size:14px;
}

/* FOOTER */
.footer{
    margin-top:15px;
    font-size:12px;
    color:#94a3b8;
}

</style>
</head>

<body>

<div class="container">

    <h2>Admin Login</h2>
    <div class="subtitle">Secure Role-Based Access System</div>

    <?php if(!empty($error)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>

    </form>

    <div class="footer">
        © <?= date("Y") ?> Student Management System
    </div>

</div>

</body>
</html>