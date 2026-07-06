<?php
session_start();
include("connection.php"); // Include verbatim connection script

if (!isset($_SESSION['customerID'])) {
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['customerID'];

// 1. Fetch current logged-in owner details for greeting matching image header
$owner_sql = "SELECT * FROM customer WHERE customerID='$customerID'";
$owner_result = mysqli_query($conn, $owner_sql);
$owner = mysqli_fetch_assoc($owner_result);

// 2. Fetch distinct dates that have available slots in the timeslot table
$date_sql = "SELECT DISTINCT slotDate FROM timeslot WHERE availability = 'Available' AND slotDate >= CURDATE() ORDER BY slotDate ASC";
$date_result = mysqli_query($conn, $date_sql);

// 3. Handle the AJAX request inline if a date is selected dynamically
if (isset($_GET['ajax_date'])) {
    $selectedDate = mysqli_real_escape_string($conn, $_GET['ajax_date']);
    $query = "SELECT slotID, slotTime FROM timeslot WHERE slotDate = '$selectedDate' AND availability = 'Available'";
    $result = mysqli_query($conn, $query);
    
    $slots = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $slots[] = [
            'slotID' => $row['slotID'],
            'slotTime' => date("g:i A", strtotime($row['slotTime']))
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($slots);
    exit(); // Stop execution here for AJAX requests so it doesn't render the HTML layout
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Booking</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=1200') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        
        /* Top Navigation Header Matching image_47a522.png Layout Exactly */
        .navbar {
            background-color: #8cd3e6;
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo-container {
            background: white;
            padding: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding-bottom: 5px;
        }
        .nav-links a.active {
            border-bottom: 3px solid white;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            color: white;
        }
        .avatar-icon {
            width: 35px;
            height: 35px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            font-weight: bold;
        }

        /* Center Glassmorphism Booking Container matching image_47a522.png */
        .booking-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            min-height: calc(100vh - 75px);
        }
        .booking-card {
            background-color: rgba(210, 165, 109, 0.75); /* Warm brown semi-transparent overlay */
            width: 100%;
            max-width: 750px;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }
        .card-header-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fcae68;
            color: #2b5c8f;
            padding: 6px 30px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Visual Structure Forms matching Blue Labels and Dashed Lines */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px 40px;
            margin-top: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .label-badge {
            background-color: #61cae3;
            color: #1a426e;
            padding: 8px 15px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
            width: fit-content;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .input-underlined {
            background: transparent;
            border: none;
            border-bottom: 2px dashed white;
            color: white;
            font-size: 16px;
            padding: 5px 0;
            width: 100%;
            outline: none;
        }
        .input-underlined option {
            background-color: #d2a56d;
            color: white;
        }
        
        .date-picker-custom {
            background: transparent;
            border: 2px solid white;
            border-radius: 4px;
            color: white;
            padding: 10px;
            font-size: 14px;
            width: 100%;
            outline: none;
        }

        /* Dynamic Slot Selection Area */
        .slots-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: white;
            max-height: 150px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .slot-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            border-bottom: 1px dashed rgba(255,255,255,0.3);
        }
        .slot-row input[type="radio"] {
            accent-color: #c9f281;
            transform: scale(1.2);
        }

        /* Big Green Save Action Button */
        .submit-container {
            grid-column: span 2;
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .save-btn {
            background-color: #c9f281;
            color: #2b5c8f;
            border: none;
            padding: 10px 60px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: background 0.2s;
        }
        .save-btn:hover {
            background-color: #b3df6b;
        }
    </style>
</head>
<body>

    <!-- Main Navigation Bar Block Elements matching image_47a522.png -->
    <div class="navbar">
        <div class="logo-container">
            <span style="color:#2b5c8f; font-weight:bold; font-size:18px;">🐾 PawCare</span>
        </div>
        <div class="nav-links">
            <a href="booking.php" class="active">Booking</a>
            <a href="ownerDetails.php">Owner details</a>
            <a href="petDetails.php">Pet details</a>
        </div>
        <div class="user-profile">
            <span>Welcome, <?php echo htmlspecialchars($owner['name'] ?? 'USER'); ?></span>
            <div class="avatar-icon">👤</div>
        </div>
    </div>

    <!-- Interactive Centralized Dynamic Booking Structure -->
    <div class="booking-wrapper">
        <div class="booking-card">
            <div class="card-header-badge">Booking details</div>
            
            <form action="saveBooking.php" method="POST">
                <div class="form-grid">
                    
                    <!-- Pet Name Selection options from DB -->
                    <div class="form-group">
                        <label class="label-badge">Pet Name :</label>
                        <select name="petID" class="input-underlined" required>
                            <option value="">-- Choose registered pet --</option>
                            <?php
                            $pet_sql = "SELECT petID, petName FROM pet WHERE customerID = '$customerID' ORDER BY petName ASC";
                            $pet_result = mysqli_query($conn, $pet_sql);
                            while ($pet = mysqli_fetch_assoc($pet_result)) {
                                echo "<option value='".htmlspecialchars($pet['petID'])."'>".htmlspecialchars($pet['petName'])."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Additional Field matching image details context -->
                    <div class="form-group">
                        <label class="label-badge">Pet Size :</label>
                        <select name="petSize" class="input-underlined" required>
                            <option value="Small">Small (0-10kg)</option>
                            <option value="Medium">Medium (11-25kg)</option>
                            <option value="Large">Large (Above 25kg)</option>
                        </select>
                    </div>

                    <!-- Date Picker sourcing fields directly from DB timeslots -->
                    <div class="form-group">
                        <label class="label-badge">choose date :</label>
                        <select id="bookingDate" name="bookingDate" class="date-picker-custom" onchange="fetchTimeslots(this.value)" required>
                            <option value="">-- Select an Available Date --</option>
                            <?php
                            while ($date_row = mysqli_fetch_assoc($date_result)) {
                                $formattedDate = $date_row['slotDate'];
                                echo "<option value='$formattedDate'>".date("d-m-Y", strtotime($formattedDate))."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dynamic Real-Time Time Slot Option container -->
                    <div class="form-group">
                        <label class="label-badge">choose Time :</label>
                        <div id="slots-target" class="slots-container">
                            <span style="color: #eee; font-style: italic; font-size: 13px;">Please select a date first...</span>
                        </div>
                    </div>

                    <!-- Submit Action Button -->
                    <div class="submit-container">
                        <button type="submit" class="save-btn">Save</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Asynchronous Javascript requests targeting THIS same file natively via "?ajax_date=" -->
    <script>
    function fetchTimeslots(dateVal) {
        const slotsContainer = document.getElementById('slots-target');
        if (!dateVal) {
            slotsContainer.innerHTML = '<span style="color: #eee; font-style: italic; font-size: 13px;">Please select a date first...</span>';
            return;
        }

        slotsContainer.innerHTML = '<span style="color: #eee; font-style: italic; font-size: 13px;">Loading times...</span>';

        // Targets booking.php directly using url query parameters
        fetch(`booking.php?ajax_date=${dateVal}`)
            .then(response => response.json())
            .then(data => {
                slotsContainer.innerHTML = '';
                if (data.length === 0) {
                    slotsContainer.innerHTML = '<span style="color: #ffb3b3; font-weight: bold; font-size: 13px;">No available slots for this date.</span>';
                    return;
                }

                data.forEach(slot => {
                    const row = document.createElement('div');
                    row.className = 'slot-row';
                    row.innerHTML = `
                        <input type="radio" id="slot_${slot.slotID}" name="slotID" value="${slot.slotID}" required>
                        <label for="slot_${slot.slotID}">${slot.slotTime}</label>
                    `;
                    slotsContainer.appendChild(row);
                });
            })
            .catch(err => {
                slotsContainer.innerHTML = '<span style="color: #ffb3b3; font-size: 13px;">Error processing timeslots.</span>';
                console.error(err);
            });
    }
    </script>
</body>
</html>