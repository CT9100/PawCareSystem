<?php
session_start();
include 'connection.php'; // Ensure your database connection file is included

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slotDate = $_POST['slotDate'];
    $slotTime = $_POST['slotTime'];
    $availability = 'Available'; // A newly created slot should be available by default

    // 1. GENERATE THE NEW SLOT ID
    // We fetch the current highest slotID to figure out what the next one should be
    $idQuery = "SELECT slotID FROM TimeSlot ORDER BY slotID DESC LIMIT 1";
    $idResult = $conn->query($idQuery);
    
    if ($idResult && $idResult->num_rows > 0) {
        $row = $idResult->fetch_assoc();
        $lastId = $row['slotID']; // e.g., "T005"
        
        // Extract the numbers after the 'T' and add 1
        $numPart = intval(substr($lastId, 1)); 
        $nextNum = $numPart + 1;
        
        // Format it back to T followed by 3 digits (e.g., T006)
        $newSlotID = 'T' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    } else {
        // If there are no slots in the database yet, start with T001
        $newSlotID = 'T001';
    }

    // 2. INSERT INTO DATABASE (Now including the generated $newSlotID)
    $insertQuery = "INSERT INTO TimeSlot (slotID, slotDate, slotTime, availability) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    // Bind 4 strings ("ssss")
    $stmt->bind_param("ssss", $newSlotID, $slotDate, $slotTime, $availability);

    if ($stmt->execute()) {
        // Redirect back to the time slot dashboard after saving successfully
        header("Location: time_slot.php");
        exit();
    } else {
        $error = "Failed to add time slot: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Add Time Slot</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: white; display: flex; flex-direction: column; height: 100vh; }
        .navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
        
        .container { flex-grow: 1; display: flex; justify-content: center; align-items: center; }
        
        .modal-content {
            background: rgba(184, 134, 11, 0.85); 
            padding: 40px; border-radius: 20px; width: 450px; position: relative;
            display: flex; flex-direction: column; align-items: center;
        }
        .modal-title { background: #ffc059; padding: 10px 40px; border-radius: 8px; font-weight: bold; margin-bottom: 30px; margin-top: -60px; color: #333; font-size: 18px; }
        
        .form-group { display: flex; align-items: center; width: 100%; margin-bottom: 20px; }
        .form-label { background: #1ccbf2; padding: 10px; border-radius: 8px; width: 100px; text-align: center; font-weight: bold; margin-right: 20px; color: #333; }
        .form-input { flex-grow: 1; background: transparent; border: none; border-bottom: 2px dashed white; color: white; padding: 10px; font-size: 16px; outline: none; }
        .form-input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; } 
        
        .btn-save { background: #ccff66; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 16px; color: #333; width: 100%; }
        .btn-cancel { background: #ff6666; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; font-size: 16px; color: white; text-decoration: none; text-align: center; width: 100%; box-sizing: border-box; }
        
        .btn-save:hover { background: #bbf052; }
        .btn-cancel:hover { background: #e65555; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
    </div>

    <div class="container">
        <form class="modal-content" action="add_slot.php" method="POST">
            <div class="modal-title">Add slot</div>
            
            <?php if(isset($error)) echo "<p style='color: #ffcccc; font-weight: bold; margin-bottom: 15px;'>$error</p>"; ?>
            
            <div class="form-group">
                <div class="form-label">Date :</div>
                <input type="date" name="slotDate" class="form-input" required>
            </div>
            
            <div class="form-group">
                <div class="form-label">Start time :</div>
                <input type="time" name="slotTime" class="form-input" required>
            </div>
            
            <button type="submit" class="btn-save">Save Slot</button>
            <a href="time_slot.php" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>