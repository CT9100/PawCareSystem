<?php
session_start();
include("connection.php"); // Include verbatim connection script

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Added role selection

    if (!empty($email) && !empty($password) && !empty($role)) {
        
        if ($role === 'customer') {
            // Query matching customer database schema
            $sql = "SELECT * FROM customer WHERE email='$email' AND password='$password'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['customerID'] = $row['customerID']; 
                $_SESSION['role'] = 'customer';
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid Customer Email or Password!";
            }
        } else if ($role === 'staff') {
            // Query matching staff database schema (Assumed table 'staff' and column 'staffID')
            $sql = "SELECT * FROM staff WHERE email='$email' AND password='$password'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['staffID'] = $row['staffID']; 
                $_SESSION['role'] = 'staff';
                
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid Staff Email or Password!";
            }
        }
        
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #8cd3e6; /* Light blue background matching image_46570e.png */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            display: flex;
            align-items: center;
            gap: 50px;
            max-width: 900px;
            width: 100%;
            padding: 20px;
        }
        /* Left Section (Image & Logo Stack) */
        .left-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 50%;
        }
        .showcase-img {
            width: 100%;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .logo-box {
            background: white;
            padding: 10px 25px;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: -40px; /* Overlaps slightly like image_46570e.png */
            width: 180px;
        }
        .logo-text {
            font-weight: bold;
            font-size: 22px;
            color: #2b5c8f;
        }
        /* Right Section (Form Card) */
        .right-panel {
            width: 50%;
            background-color: #e1e1e1; /* Off-white container card */
            border: 5px solid white;
            border-radius: 12px;
            padding: 40px 30px;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            width: 100%;
        }
        .label-pill {
            background-color: #f3be6b; /* Muted orange/yellow pills */
            color: #333;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 25px;
            width: 130px;
            text-align: center;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .input-field {
            flex-grow: 1;
            background: transparent;
            border: none;
            border-bottom: 2px dashed #333; /* Dashed input lines from layout */
            margin-left: 20px;
            padding: 5px 10px;
            font-size: 16px;
            color: #333;
            outline: none;
        }
        /* Styled select element to match theme */
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
        .btn-login {
            background-color: #f3be6b;
            color: #333;
            font-weight: bold;
            border: none;
            padding: 12px 50px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.1s;
        }
        .btn-login:active {
            transform: scale(0.98);
        }
        .btn-register {
            background-color: #f3be6b;
            color: #333;
            font-weight: 500;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            line-height: 1.4;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-register strong {
            display: block;
            font-weight: bold;
        }
        .error-msg {
            color: #d9534f;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    
    <!-- Left graphic asset block matching layout -->
    <div class="left-panel">
        <img src="IniKucing.png" alt="Grooming Cat" class="showcase-img">
        <div class="logo-box">
            <span class="logo-text">🐾 PawCare</span>
        </div>
    </div>

    <!-- Right structural form handling entry parameters -->
    <div class="right-panel">
        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <!-- New Role Selector Dropdown -->
            <div class="form-group">
                <div class="label-pill">I am a...</div>
                <select name="role" class="input-field" required>
                    <option value="customer" selected>Customer</option>
                    <option value="staff">Staff / Admin</option>
                </select>
            </div>

            <div class="form-group">
                <div class="label-pill">Username</div>
                <input type="text" name="username" class="input-field" required placeholder="Enter Email...">
            </div>

            <div class="form-group">
                <div class="label-pill">Password</div>
                <input type="password" name="password" class="input-field" required>
            </div>

            <div class="action-container">
                <button type="submit" name="login" class="btn-login">Login</button>
                
                <a href="register.php" class="btn-register">
                    Don't have account?
                    <strong>Register now</strong>
                </a>
            </div>
        </form>
    </div>

</div>

</body>
</html>