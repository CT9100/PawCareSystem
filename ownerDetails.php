<?php
    session_start();
    include("database/connection.php");
    $customerID = $_SESSION['customerID'];
    $sql = "SELECT * FROM customer WHERE customerID='$customerID'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Details</title>
    <link rel="stylesheet" href="css/ownerDetails.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="background">
        <div class="card">
            <h2>🐾 Owner Details</h2>
            <form action="updateOwner.php" method="POST">
                <input type="hidden" name="customerID" value="<?php echo $row['customerID']; ?>">

                <div class="form-group">
                    <label>Owner Name</label>
                    <input class="editable" type="text" name="name" value="<?php echo $row['name']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input class="editable" type="text" name="phone" value="<?php echo $row['phone']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input class="editable" type="email" name="email" value="<?php echo $row['email']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input class="editable" id="password" type="password" name="password" value="<?php echo $row['password']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea class="editable" name="address" readonly><?php echo $row['address']; ?></textarea>
                </div>

                <div class="buttons">
                    <button type="button" id="editBtn" onclick="enableEdit()"> Edit </button>
                    <button type="submit" id="saveBtn" style="display:none;"> Save </button>
                    <button type="button" onclick="history.back();"> Back </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function enableEdit(){
            let fields=document.querySelectorAll(".editable");
            fields.forEach(function(field){
            field.removeAttribute("readonly");
            });
            document.getElementById("editBtn").style.display="none";
            document.getElementById("saveBtn").style.display="inline-block";
        }
    </script>
</body>
</html>