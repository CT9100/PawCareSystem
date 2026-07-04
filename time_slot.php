<?php
session_start();
include 'connection.php'; // Make sure to include your database connection

// if (!isset($_SESSION['staffID'])) {
//     header("Location: login.php");
//     exit();
// }

// Fetch time slots from the database
$query = "SELECT slotID, slotDate, slotTime, availability FROM TimeSlot ORDER BY slotDate ASC, slotTime ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Time Slots</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: #333; }
        .navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; font-size: 18px; }
        .nav-links a.active { border-bottom: 2px solid white; padding-bottom: 5px; }
        .user-profile { display: flex; align-items: center; gap: 10px; font-weight: bold; }
        
        .container { padding: 40px; min-height: 80vh; position: relative; }
        .controls { display: flex; justify-content: space-between; margin-bottom: 30px; padding: 0 50px; }
        .btn-add { background-color: #ffd966; border: none; padding: 12px 30px; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 16px; right: 0; color: #333; }

        /* Data Grid Styles */
        .grid-headers { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; }
        .header-cell { background-color: #ffc059; padding: 15px; border-radius: 8px; font-weight: bold; }
        .grid-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; }
        .data-cell { background-color: #1ccbf2; padding: 15px; border-radius: 8px; color: #333; }
        .status-available { background-color: #8be763; font-weight: bold; }
        .status-booked { background-color: #ff6666; font-weight: bold; color: white; }

        /* Modal Overlay Styles */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000;
        }
        .modal-content {
            background: rgba(184, 134, 11, 0.85); /* Semi-transparent brown */
            padding: 40px; border-radius: 20px; width: 500px; position: relative;
            display: flex; flex-direction: column; align-items: center;
        }
        .modal-title { background: #ffc059; padding: 10px 40px; border-radius: 8px; font-weight: bold; margin-bottom: 30px; margin-top: -60px; }
        .form-group { display: flex; align-items: center; width: 100%; margin-bottom: 20px; }
        .form-label { background: #1ccbf2; padding: 10px; border-radius: 8px; width: 100px; text-align: center; font-weight: bold; margin-right: 20px; }
        .form-input { flex-grow: 1; background: transparent; border: none; border-bottom: 2px dashed white; color: white; padding: 10px; font-size: 16px; outline: none; }
        .form-input::-webkit-calendar-picker-indicator { filter: invert(1); }
        .btn-save { background: #ccff66; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 16px; }
        .close-btn { position: absolute; top: 10px; right: 20px; color: white; font-size: 24px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
        <div class="nav-links">
            <a href="admin_dashboard.php">Booking record</a>
            <a href="time_slot.php" class="active">Time Slots</a>
            <a href="report.php">Report</a>
        </div>
        <div class="user-profile">
            <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
        </div>
    </div>

    <div class="container">
        <div class="controls">
            <button class="btn-add" onclick="document.getElementById('addSlotModal').">Add slot</button>
        </div>

        <div class="grid-headers">
            <div class="header-cell">Slot ID</div>
            <div class="header-cell">Date</div>
            <div class="header-cell">Time</div>
            <div class="header-cell">Availability</div>
        </div>

        <div id="timeslot-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Format time to 12-hour AM/PM format
                    $formattedTime = date("h:i A", strtotime($row['slotTime']));
                    
                    // Determine CSS class based on availability
                    $statusClass = (strtolower($row['availability']) == 'available') ? 'status-available' : 'status-booked';

                    echo '<div class="grid-row">';
                    echo '<div class="data-cell">' . htmlspecialchars($row['slotID']) . '</div>';
                    echo '<div class="data-cell">' . htmlspecialchars($row['slotDate']) . '</div>';
                    echo '<div class="data-cell">' . $formattedTime . '</div>';
                    echo '<div class="data-cell ' . $statusClass . '">' . htmlspecialchars($row['availability']) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div style="color: white; text-align: center; margin-top: 20px;">No time slots found.</div>';
            }
            ?>
        </div>
    </div>

    <div id="addSlotModal" class="modal-overlay">
        <form class="modal-content" action="add_slot.php" method="POST">
            <span class="close-btn" onclick="document.getElementById('addSlotModal').style.display='none'">&times;</span>
            <div class="modal-title">Add slot</div>
            
            <div class="form-group">
                <div class="form-label">Date :</div>
                <input type="date" name="slotDate" class="form-input" required>
            </div>
            <div class="form-group">
                <div class="form-label">Start time :</div>
                <input type="time" name="slotTime" class="form-input" required>
            </div>
            
            <button type="submit" class="btn-save">Save</button>
        </form>
    </div>

</body>
</html>