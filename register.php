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
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #8cd3e6; /* Light blue background matching image_46b0e5.png */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-container {
            display: flex;
            align-items: center;
            gap: 50px;
            max-width: 950px;
            width: 100%;
            padding: 20px;
        }
        /* Left Section (Image & Logo Stack) */
        .left-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 45%;
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
            margin-top: -40px; /* Slight offset overlap matching template layout */
            width: 180px;
        }
        .logo-text {
            font-weight: bold;
            font-size: 22px;
            color: #2b5c8f;
        }
        /* Right Section (Form Card) */
        .right-panel {
            width: 55%;
            background-color: #e1e1e1; /* Off-white container card */
            border: 5px solid white;
            border-radius: 12px;
            padding: 35px 30px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
        }
        .label-pill {
            background-color: #f3be6b; /* Muted orange/yellow pills */
            color: #333;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            width: 150px;
            text-align: center;
            font-size: 13px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex-shrink: 0;
        }
        .input-field {
            flex-grow: 1;
            background: transparent;
            border: none;
            border-bottom: 2px dashed #333; /* Dashed input lines from layout image_46b0e5.png */
            margin-left: 20px;
            padding: 5px 10px;
            font-size: 15px;
            color: #333;
            outline: none;
        }
        textarea.input-field {
            resize: none;
            height: 45px;
        }
        .action-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 25px;
        }
        .btn-submit {
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
        .btn-submit:active {
            transform: scale(0.98);
        }
        .login-link {
            margin-top: 15px;
            color: #2b5c8f;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .error-msg {
            color: #d9534f;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .success-msg {
            color: #5cb85c;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="register-container">
    
    <!-- Left panel structural visual match -->
    <div class="left-panel">
        <img src="image_46b0e5.png" alt="PawCare Layout" class="showcase-img">
        <div class="logo-box">
            <span class="logo-text">🐾 PawCare</span>
        </div>
    </div>

    <!-- Right panel registration entry flow -->
    <div class="right-panel">
        <?php if(!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <!-- Username (stores directly to customer name attribute) -->
            <div class="form-group">
                <div class="label-pill">Username</div>
                <input type="text" name="username" class="input-field" required placeholder="Your full name...">
            </div>

            <!-- Email Address Entry -->
            <div class="form-group">
                <div class="label-pill">Email</div>
                <input type="email" name="email" class="input-field" required placeholder="example@mail.com">
            </div>

            <!-- Phone Number Entry -->
            <div class="form-group">
                <div class="label-pill">Phone Number</div>
                <input type="tel" name="phone" class="input-field" required placeholder="012-3456789">
            </div>

            <!-- Password Entry -->
            <div class="form-group">
                <div class="label-pill">Password</div>
                <input type="password" name="password" class="input-field" required>
            </div>

            <!-- Confirm Password Entry -->
            <div class="form-group">
                <div class="label-pill">Confirm Password</div>
                <input type="password" name="confirm_password" class="input-field" required>
            </div>

            <!-- Home Address Entry -->
            <div class="form-group">
                <div class="label-pill">Address</div>
                <textarea name="address" class="input-field" required placeholder="Full home address..."></textarea>
            </div>

            <div class="action-container">
                <button type="submit" name="register" class="btn-submit">Create account</button>
                <a href="login.php" class="login-link">Already have an account? Login here</a>
            </div>
        </form>
    </div>

</div>

</body>
</html>