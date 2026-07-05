<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['staffID'])) {
    header("Location: login.php");
    exit();
}

// 1. Capture search inputs from the URL if they exist
$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';
$searchStatus = isset($_GET['search_status']) ? $_GET['search_status'] : '';

// 2. Start building the base query
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
    WHERE 1=1 
";

// 3. Dynamically append search conditions based on user input
if (!empty($searchName)) {
    $safeName = $conn->real_escape_string($searchName); 
    // Search both Pet Name OR Owner Name
    $query .= " AND (p.petName LIKE '%$safeName%' OR c.name LIKE '%$safeName%') ";
}

if (!empty($searchDate)) {
    $safeDate = $conn->real_escape_string($searchDate);
    $query .= " AND t.slotDate = '$safeDate' ";
}

if (!empty($searchStatus)) {
    $safeStatus = $conn->real_escape_string($searchStatus);
    $query .= " AND a.status = '$safeStatus' ";
}

// 4. Add the final sorting logic
$query .= " ORDER BY t.slotDate ASC, t.slotTime ASC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Admin Dashboard</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: white; }
        .navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; font-size: 18px; }
        .nav-links a.active { border-bottom: 2px solid white; padding-bottom: 5px; }
        .user-profile { display: flex; align-items: center; gap: 10px; font-weight: bold; color: #333; }
        
        .container { padding: 40px; min-height: 80vh; position: relative; }
        
        /* Updated Top Controls for Search Form */
        .controls { display: flex; gap: 15px; margin-bottom: 40px; align-items: center; }
        .search-input { padding: 12px; border-radius: 10px; border: none; font-size: 16px; outline: none; width: 220px; }
        select.search-input { cursor: pointer; width: 160px; }
        .btn-search { background-color: #ccff66; border: none; padding: 12px 30px; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 16px; color: #333; }
        .btn-clear { color: white; text-decoration: underline; font-size: 16px; margin-left: 10px; }

        /* Data Grid Styles */
        .grid-headers { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; }
        .header-cell { background-color: #ffc059; padding: 15px; border-radius: 10px; font-weight: bold; color: #333; }
        
        .grid-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 15px; text-align: center; align-items: center; }
        .data-cell { background-color: #1ccbf2; padding: 15px; border-radius: 10px; color: #333; font-weight: 500; }
        
        .btn-update { background-color: #8be763; border: none; padding: 15px; border-radius: 10px; font-weight: bold; cursor: pointer; color: #333; width: 100%; font-size: 14px; }
        .btn-update:hover { background-color: #79d652; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
        <div class="nav-links">
            <a href="admin_dashboard.php" class="active">Booking record</a>
            <a href="time_slot.php">Time Slots</a>
            <a href="report.php">Report</a>
        </div>
        <div class="user-profile">
            <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
        </div>
    </div>

    <div class="container">
        
        <form class="controls" method="GET" action="admin_dashboard.php">
            <input type="text" name="search_name" class="search-input" placeholder="Search Pet or Owner..." value="<?php echo htmlspecialchars($searchName); ?>">
            
            <input type="date" name="search_date" class="search-input" style="width: 150px;" value="<?php echo htmlspecialchars($searchDate); ?>">
            
            <select name="search_status" class="search-input">
                <option value="">All Statuses</option>
                <option value="Completed" <?php if($searchStatus == 'Completed') echo 'selected'; ?>>Completed</option>
                <option value="Pending" <?php if($searchStatus == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Cancelled" <?php if($searchStatus == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>

            <button type="submit" class="btn-search">Search</button>
            
            <?php if (!empty($searchName) || !empty($searchDate) || !empty($searchStatus)): ?>
                <a href="admin_dashboard.php" class="btn-clear">Clear Filters</a>
            <?php endif; ?>
        </form>

        <div class="grid-headers">
            <div class="header-cell">Pet name</div>
            <div class="header-cell">Owner name</div>
            <div class="header-cell">Date</div>
            <div class="header-cell">Time</div>
            <div class="header-cell">Status</div>
            <div class="header-cell">Action</div>
        </div>

        <div id="booking-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $formattedDate = date("d F Y", strtotime($row['slotDate']));
                    $formattedTime = date("h:i A", strtotime($row['slotTime']));

                    echo '<div class="grid-row">';
                    echo '<div class="data-cell">' . htmlspecialchars($row['petName']) . '</div>';
                    echo '<div class="data-cell">' . htmlspecialchars($row['ownerName']) . '</div>';
                    echo '<div class="data-cell">' . $formattedDate . '</div>';
                    echo '<div class="data-cell">' . $formattedTime . '</div>';
                    echo '<div class="data-cell">' . htmlspecialchars($row['status']) . '</div>';
                    
                    echo '<div><button class="btn-update" onclick="window.location.href=\'update_booking.php?id=' . $row['appointmentID'] . '\'">Update</button></div>';
                    echo '</div>';
                }
            } else {
                echo '<div style="color: white; text-align: center; margin-top: 40px; grid-column: span 6;">No booking records found matching your search.</div>';
            }
            ?>
        </div>

    </div>

</body>
</html>
