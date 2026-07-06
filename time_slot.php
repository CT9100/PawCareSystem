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
        
        /* Controls */
        .controls { display: flex; justify-content: flex-end; margin-bottom: 30px; padding: 0 10px; }
        .btn-add { background-color: #ffd966; border: none; padding: 12px 30px; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 16px; color: #333; }
        .btn-add:hover { background-color: #f2cc50; }

        /* Data Grid Styles */
        .grid-headers { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; }
        .header-cell { background-color: #ffc059; padding: 15px; border-radius: 8px; font-weight: bold; }
        .grid-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; align-items: center; }
        .data-cell { background-color: #1ccbf2; padding: 15px; border-radius: 8px; color: #333; font-weight: 500; box-sizing: border-box; }
        
        /* Status Colors */
        .status-available { background-color: #8be763; font-weight: bold; }
        .status-unavailable { background-color: #ff6666; font-weight: bold; color: white; }

        /* Action Buttons - UPDATED FOR 50/50 SPLIT */
        .action-btns { 
            display: flex; 
            gap: 10px; /* Space between Edit and Delete */
            width: 100%; 
        }
        .btn-edit, .btn-delete { 
            flex: 1; /* This forces them to split the space 50/50 */
            padding: 15px 0; /* Matches the vertical height of the other data cells */
            border-radius: 8px; 
            font-weight: bold; 
            cursor: pointer; 
            border: none;
            text-align: center;
            text-decoration: none;
            font-size: 16px; /* Ensure font matches */
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        .btn-edit { background-color: #8be763; color: #333; }
        .btn-delete { background-color: #ff6666; color: white; }
        .btn-edit:hover { background-color: #72cc4d; }
        .btn-delete:hover { background-color: #e65555; }

        /* Modal Styles */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000;
        }
        .modal-content {
            background: rgba(184, 134, 11, 0.95); /* Semi-transparent brown */
            padding: 40px; border-radius: 20px; width: 450px; position: relative;
            display: flex; flex-direction: column; align-items: center; color: white;
        }
        .modal-title { background: #ffc059; padding: 10px 40px; border-radius: 8px; font-weight: bold; margin-bottom: 30px; margin-top: -60px; color: #333; font-size: 18px; }
        .form-group { display: flex; align-items: center; width: 100%; margin-bottom: 20px; }
        .form-label { background: #1ccbf2; padding: 10px; border-radius: 8px; width: 100px; text-align: center; font-weight: bold; margin-right: 20px; color: #333; }
        .form-input { flex-grow: 1; background: transparent; border: none; border-bottom: 2px dashed white; color: white; padding: 10px; font-size: 16px; outline: none; }
        .form-input option { background: #333; color: white; } 
        .form-input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; } 
        .btn-save { background: #ccff66; border: none; padding: 12px 50px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 16px; color: #333; width: 100%; }
        .btn-save:hover { background: #bbf052; }
        .close-btn { position: absolute; top: 15px; right: 20px; color: white; font-size: 28px; cursor: pointer; font-weight: bold; text-decoration: none; }
        .btn-logout { color: red; text-decoration: none; margin: 0 15px; font-size: 18px; font-weight: bold; }   
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
        <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
        <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
    </div>
</div>
    <div class="container">
        
        <div class="controls">
            <button class="btn-add" onclick="window.location.href='add_slot.php'">Add slot</button>
        </div>

        <div class="grid-headers">
            <div class="header-cell">Slot ID</div>
            <div class="header-cell">Date</div>
            <div class="header-cell">Time</div>
            <div class="header-cell">Availability</div>
            <div class="header-cell">Action</div>
        </div>

        <div id="timeslot-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Format Date & Time visually for the grid
                    $formattedDate = date("d F Y", strtotime($row['slotDate']));
                    $formattedTime = date("h:i A", strtotime($row['slotTime']));
                    
                    // Raw data needed for the edit inputs (inputs need YYYY-MM-DD and HH:MM format)
                    $rawDate = $row['slotDate'];
                    $rawTime = date("H:i", strtotime($row['slotTime'])); 
                    
                    // Determine CSS class based on availability
                    $statusClass = (strtolower($row['availability']) == 'available') ? 'status-available' : 'status-unavailable';

                    echo '<div class="grid-row">';
                    echo '<div class="data-cell">' . htmlspecialchars($row['slotID']) . '</div>';
                    echo '<div class="data-cell">' . $formattedDate . '</div>';
                    echo '<div class="data-cell">' . $formattedTime . '</div>';
                    echo '<div class="data-cell ' . $statusClass . '">' . htmlspecialchars($row['availability']) . '</div>';
                    
                    // Action Buttons (Now 50/50 block)
                    echo '<div class="action-btns">';
                    echo '<button type="button" class="btn-edit" onclick="openEditModal(\'' . $row['slotID'] . '\', \'' . $rawDate . '\', \'' . $rawTime . '\', \'' . $row['availability'] . '\')">Edit</button>';
                    echo '<a href="delete_slot.php?id=' . $row['slotID'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete Slot ' . $row['slotID'] . '?\')">Delete</a>';
                    echo '</div>';
                    
                    echo '</div>';
                }
            } else {
                echo '<div style="color: white; text-align: center; margin-top: 20px;">No time slots found.</div>';
            }
            ?>
        </div>
    </div>

    <div id="editSlotModal" class="modal-overlay">
        <form class="modal-content" action="update_slot.php" method="POST">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <div class="modal-title">Edit slot <span id="displaySlotID"></span></div>
            
            <input type="hidden" name="slotID" id="editSlotID">
            
            <div class="form-group">
                <div class="form-label">Date :</div>
                <input type="date" name="slotDate" id="editDate" class="form-input" required>
            </div>
            
            <div class="form-group">
                <div class="form-label">Start time :</div>
                <input type="time" name="slotTime" id="editTime" class="form-input" required>
            </div>

            <div class="form-group">
                <div class="form-label">Status :</div>
                <select name="availability" id="editAvailability" class="form-input" required>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            
            <button type="submit" class="btn-save">Update Slot</button>
        </form>
    </div>

    <script>
        function openEditModal(id, date, time, availability) {
            // Fill the hidden ID input and the title display
            document.getElementById('editSlotID').value = id;
            document.getElementById('displaySlotID').innerText = "(" + id + ")";
            
            // Pre-fill the inputs with the original data
            document.getElementById('editDate').value = date;
            document.getElementById('editTime').value = time;
            
            // Set the dropdown to match current availability
            let availSelect = document.getElementById('editAvailability');
            for(let i = 0; i < availSelect.options.length; i++) {
                if(availSelect.options[i].value.toLowerCase() === availability.toLowerCase()) {
                    availSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Show the modal
            document.getElementById('editSlotModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editSlotModal').style.display = 'none';
        }
    </script>

</body>
</html>