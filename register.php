<?php
session_start();
include("connection.php"); // Include verbatim connection script[cite: 2]

$message = "";
$messageClass = "";

if (isset($_POST['register'])) {
    // Collect and sanitize user input data according to database attributes[cite: 2]
    $name = mysqli_real_escape_string($conn, $_POST['username']); // Name maps to Username field
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $messageClass = "error-msg";
    } else {
        // Check if username/name or email already exists in customer database[cite: 2]
        $check_sql = "SELECT * FROM customer WHERE name='$name' OR email='$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "Username or Email already registered!";
            $messageClass = "error-msg";
        } else {
            
            // --- CUSTOM AUTOMATIC ID GENERATION FOR VARCHAR ---
            // 1. Fetch the largest current numerical value casted from your customerID column
            $id_query = "SELECT MAX(CAST(customerID AS UNSIGNED)) AS max_id FROM customer";
            $id_result = mysqli_query($conn, $id_query);
            $id_row = mysqli_fetch_assoc($id_result);
            
            // 2. If table is empty, start at 1. Otherwise, increment the highest ID by 1.
            $next_id_number = ($id_row['max_id'] == null) ? 1 : intval($id_row['max_id']) + 1;
            
            // 3. Convert it back to a string format to fit your VARCHAR requirement
            $new_customerID = strval($next_id_number); 
            // --------------------------------------------------

            // Insert new customer record along with our manually generated VARCHAR ID
            $sql = "INSERT INTO customer (customerID, name, email, phone, password, address) 
                    VALUES ('$new_customerID', '$name', '$email', '$phone', '$password', '$address')";
            
            if (mysqli_query($conn, $sql)) {
                $message = "Account created successfully! Your ID is $new_customerID. Redirecting to login...";
                $messageClass = "success-msg";
                header("refresh:2; url=login.php");
            } else {
                $message = "Registration failed: " . mysqli_error($conn);
                $messageClass = "error-msg";
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
    <title>PawCare - Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            margin:0;
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
            display:flex;
            justify-content:center;
            align-items:center;
            width:100%;
        }

        .right-panel{
            width:750px;
            max-width:90%;
            background:rgba(255,255,255,.88);
            backdrop-filter:blur(20px);
            border-radius:20px;
            padding:45px;
            box-shadow:0 15px 35px rgba(0,0,0,.25);
            transition:.3s;
        }

        .right-panel:hover{
            transform:translateY(-4px);
        }

        .form-header{
            text-align:center;
            margin-bottom:25px;
        }

        .form-header h2{
            margin:0;
        }

        .form-group{
            display:flex;
            flex-direction:column;
            margin-bottom:18px;
        }

        .form-group label{
            font-size:14px;
            font-weight:600;
            color:#555;
            margin-bottom:8px;
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

        .btn-login{
            width:100%;
            background:#5EC6D8;
            color:white;
            padding:14px;
            border:none;
            border-radius:14px;
            font-size:18px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
            margin-top:10px;
        }

        .btn-login:hover{
            background:#42b7cb;
            transform:translateY(-2px);
        }

        .error-msg{
            background:#ffe8e8;
            color:#d32f2f;
            padding:12px;
            border-radius:10px;
            margin-bottom:20px;
            border-left:5px solid #d32f2f;
            text-align:center;
        }

        .success-msg{
            background:#e8ffe8;
            color:#2e7d32;
            padding:12px;
            border-radius:10px;
            margin-bottom:20px;
            border-left:5px solid #2e7d32;
            text-align:center;
        }

        .login-link{
            text-align:center;
            margin-top:15px;
        }

        .login-link a{
            color:#5EC6D8;
            font-weight:600;
            text-decoration:none;
        }

        .password-container{
            position:relative;
        }

        .toggle-password{
            position:absolute;
            right:18px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#777;
        }

        .register-grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:15px 20px;
        }

        .full-width{
            grid-column: span 2;
        }

        .form-group{
            margin-bottom:0;
        }

        textarea.input-field{
            height:90px;
            resize:none;
        }
    </style>
</head>

<body>

<video autoplay muted loop playsinline id="bg-video">
    <source src="videos/kitten.mp4" type="video/mp4">
</video>

<div class="login-container">

    <div class="right-panel">

        <div class="form-header">
            <h2>Create Account 🐾</h2>
            <p>Register to start using PawCare</p>
        </div>

        <?php if(!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="register-grid">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="input-field" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="input-field" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" class="input-field" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="input-field" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="input-field" required>
            </div>

            <div class="form-group full-width">
                <label>Address</label>
                <textarea name="address" class="input-field" required></textarea>
            </div>

            <button type="submit" name="register" class="btn-login full-width">
                Create Account
            </button>
        </form>
    </div>

</div>

</body>
</html>