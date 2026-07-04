<?php
session_start();
include 'connection.php';

// Check if an ID was passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$appointmentID = $_GET['id'];

// Handle the form submission to update the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newStatus = $_POST['status'];
    
    $updateQuery = "UPDATE Appointment SET status = ? WHERE appointmentID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $newStatus, $appointmentID);
    
    if ($stmt->execute()) {
        // Redirect back to dashboard after successful update
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Failed to update booking.";
    }
}

// Fetch the existing data for this appointment
$query = "
    SELECT 
        a.appointmentID, 
        a.status,
        p.petName, 
        c.name AS ownerName, 
        t.slotDate, 
        t.slotTime
    FROM Appointment a
    JOIN Pet p ON a.petID = p.petID
    JOIN Customer c ON p.customerID = c.customerID
    JOIN TimeSlot t ON a.slotID = t.slotID
    WHERE a.appointmentID = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $appointmentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Appointment not found.";
    exit();
}

$appointment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Update Booking</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: white; display: flex; flex-direction: column; height: 100vh; }
        .navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
        
        .container { flex-grow: 1; display: flex; justify-content: center; align-items: center; }
        
        /* Modal-style form container */
        .modal-content {
            background: rgba(184, 134, 11, 0.85);
            padding: 40px; border-radius: 20px; width: 500px; position: relative;
            display: flex; flex-direction: column; align-items: center;
        }
        .modal-title { background: #ffc059; padding: 10px 40px; border-radius: 8px; font-weight: bold; margin-bottom: 30px; margin-top: -60px; color: #333; font-size: 18px; }
        
        .form-group { display: flex; align-items: center; width: 100%; margin-bottom: 20px; }
        .form-label { background: #1ccbf2; padding: 10px; border-radius: 8px; width: 120px; text-align: center; font-weight: bold; margin-right: 20px; color: #333; }
        .form-input { flex-grow: 1; background: transparent; border: none; border-bottom: 2px dashed white; color: white; padding: 10px; font-size: 16px; outline: none; }
        
        select.form-input { background: #333; border: 1px solid white; border-radius: 5px; cursor: pointer; }
        
        .btn-save { background: #ccff66; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 16px; color: #333; }
        .btn-cancel { background: #ff6666; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; font-size: 16px; color: white; text-decoration: none; text-align: center; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
    </div>

    <div class="container">
        <form class="modal-content" method="POST" action="">
            <div class="modal-title">Edit Booking Status</div>
            
            <?php if(isset($error)) echo "<p style='color: #ffcccc;'>$error</p>"; ?>

            <div class="form-group">
                <div class="form-label">Pet Name :</div>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($appointment['petName']); ?>" readonly>
            </div>

            <div class="form-group">
                <div class="form-label">Owner :</div>
                <input type="text" class="form-input" value="<?php echo htmlspecialchars($appointment['ownerName']); ?>" readonly>
            </div>

            <div class="form-group">
                <div class="form-label">Date & Time :</div>
                <input type="text" class="form-input" value="<?php echo date("d M Y", strtotime($appointment['slotDate'])) . ' @ ' . date("h:i A", strtotime($appointment['slotTime'])); ?>" readonly>
            </div>

            <div class="form-group">
                <div class="form-label">Status :</div>
                <select name="status" class="form-input" required>
                    <option value="Completed" <?php if(strtolower($appointment['status']) == 'completed') echo 'selected'; ?>>Completed</option>
                    <option value="Pending" <?php if(strtolower($appointment['status']) == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="Cancelled" <?php if(strtolower($appointment['status']) == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </div>
            
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="admin_dashboard.php" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>