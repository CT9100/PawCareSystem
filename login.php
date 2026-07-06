<?php
session_start();
include("connection.php");

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Added role selection

    if (!empty($email) && !empty($password) && !empty($role)) {
        
        if ($role === 'customer') {
            // Query matching customer database schema
            $email = trim($_POST['username']);
            $password = trim($_POST['password']);

            $sql = "SELECT * FROM customer WHERE email='$email'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                if ($password === $row['password']) {
                    $_SESSION['customerID'] = $row['customerID'];
                    $_SESSION['role'] = 'customer';
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid Customer Email or Password!";
                }
            } else {
                $error = "Invalid Customer Email or Password!";
            }
        } else if ($role === 'staff') {
            // Query matching staff database schema (Assumed table 'staff' and column 'staffID')
            $email = trim($_POST['username']);
            $password = trim($_POST['password']);

            $sql = "SELECT * FROM staff WHERE email='$email'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                if ($password === $row['password']) {
                    $_SESSION['staffID'] = $row['staffID'];
                    $_SESSION['role'] = 'staff';
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid Staff Email or Password!";
                }
            } else {
                $error = "Invalid Staff Email or Password!";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>PawCare - Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            margin:0;
            font-family:'Poppins',sans-serif;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            overflow:hidden;
        }

        #bg-video{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            object-fit:cover;
            z-index:-2;
        }

        body::before{
            content:"";
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,.45);
            z-index:-1;
        }

        .login-container {
            display: flex;
            align-items: center;
            gap: 100px;
            max-width: 1000px;
            width: 100%;
            padding: 20px;
        }
        
       .left-panel{
            width:450px;
            text-align:center;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
        }

        .logo-text{
            font-size:52px;
            font-weight:700;
            color:#ffffff;
            margin-bottom:10px;
            text-shadow:2px 2px 10px rgba(0,0,0,0.5);
        }


        .logo-subtitle{
            font-size:22px;
            color:#ffffff;
            font-weight:500;
            margin-bottom:15px;
            text-shadow:2px 2px 8px rgba(0,0,0,0.5);
        }
       
        .right-panel{
            width:420px;
            background:rgba(255,255,255,.88);
            backdrop-filter:blur(20px);
            border-radius:20px;
            padding:45px;
            box-shadow:0 15px 35px rgba(0,0,0,.25);
            transition:.3s;
        }

        .right-panel:hover{
            transform:translateY(-4px);
            box-shadow:0 25px 50px rgba(0,0,0,.25);
        }

        .form-group{
            display:flex;
            flex-direction:column;
            margin-bottom:25px;
        }

        .form-group label{
            font-size:14px;
            font-weight:600;
            color:#555;
            margin-bottom:8px;
        }

        .welcome-text{
            margin-top:25px;
            font-size:18px;
            line-height:1.8;
            color:#ffffff;
            max-width:420px;
            margin-left:auto;
            margin-right:auto;
            text-shadow:2px 2px 8px rgba(0,0,0,0.5);
            font-weight:400;
        }

        .label-pill {
            background-color: #f3be6b; 
            color: #333;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 25px;
            width: 130px;
            text-align: center;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .input-field{
            width:100%;
            padding:14px;
            border:2px solid #ddd;
            border-radius:12px;
            font-size:15px;
            transition:.3s;
        }

        .input-field:focus{
            border-color:#5EC6D8;
            box-shadow:0 0 10px rgba(94,198,216,.3);
            outline:none;
        }
        
        select.input-field {
            cursor: pointer;
        }

        .action-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-login{
            width:100%;
            background:#5EC6D8;
            color:white;
            padding:14px;
            height:52px;
            border:none;
            border-radius:14px;
            font-size:18px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
        }

        .btn-login:hover{
            background:#42b7cb;
            transform:translateY(-2px);
        }

       .register-link{
            text-align:center;
            margin-top:20px;
        }

        .register-link a{
            color:#5EC6D8;
            font-weight:600;
            text-decoration:none;
        }

        .error-msg{
            background:#ffe8e8;
            color:#d32f2f;
            padding:12px;
            border-radius:10px;
            margin-bottom:20px;
            border-left:5px solid #d32f2f;
        }

        .brand{
            margin-bottom:0px;
        }

        .logo{
            width:250px;
            height:auto;
        }

        .password-container{
            position:relative;
        }

        .password-container .input-field{
            padding-right:50px;
        }

        .toggle-password{
            position:absolute;
            right:18px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            font-size:20px;
            user-select:none;
            color:#777;
            transition:.3s;
        }

        .toggle-password:hover{
            color:#5EC6D8;
        }
    </style>
</head>
<body>

    <video autoplay muted loop playsinline id="bg-video">
        <source src="videos/kitten.mp4" type="video/mp4">
    </video>

    <div class="login-container">
        
        <!-- Left graphic asset block matching layout -->
        <div class="left-panel">
            <div class="brand">
                <img src="images/paw.png" alt="PawCare Logo" class="logo">
                <h1 class="logo-text">PawCare</h1>
            </div>
            <p class="logo-subtitle">
                Pet Care Management System
            </p>
            <p class="welcome-text">
                Book professional pet grooming, veterinary check-ups,
                vaccinations and pet boarding services in one place.
            </p>
        </div>

        <!-- Right structural form handling entry parameters -->
        <div class="right-panel">
            <?php if(!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-header">
                <h2>Welcome Back 👋</h2>
                <p>
                    Log in to continue to your account.
                </p>
            </div>
            <form action="login.php" method="POST">
                <!-- New Role Selector Dropdown -->
                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-user"></i> Login As
                    </label>

                    <select name="role" class="input-field">
                        <option value="customer">Customer</option>
                        <option value="staff">Staff / Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-envelope"></i> Email Address
                    </label>

                    <input
                        type="email"
                        name="username"
                        class="input-field"
                        placeholder="Enter your email"
                        required>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-lock"></i> Password
                    </label>

                    <div class="password-container">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="input-field"
                            placeholder="Enter your password"
                            required>

                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="action-container">
                    <button type="submit" name="login" class="btn-login">Login</button>
                    
                    <div class="register-link">
                        Don't have an account?
                        <a href="register.php">Create one</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePassword(){
            const password = document.getElementById("password");
            const icon = document.querySelector(".toggle-password i");

            if(password.type === "password"){
                password.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }else{
                password.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
