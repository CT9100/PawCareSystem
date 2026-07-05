<?php
session_start();
include("connection.php"); // Include verbatim connection script

// Redirect to login if user session is missing
if (!isset($_SESSION['customerID'])) {
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['customerID'];

// 1. Fetch current logged-in owner details for greeting
$owner_sql = "SELECT * FROM customer WHERE customerID='$customerID'";
$owner_result = mysqli_query($conn, $owner_sql);
$owner = mysqli_fetch_assoc($owner_result);

// 2. Count registered pets for metrics card display
$pet_count_sql = "SELECT COUNT(*) as total_pets FROM pet WHERE customerID='$customerID'";
$pet_count_result = mysqli_query($conn, $pet_count_sql);
$pet_count_data = mysqli_fetch_assoc($pet_count_result);
$total_pets = $pet_count_data['total_pets'];

// 3. Count total active appointments for metrics card display
$app_count_sql = "SELECT COUNT(*) as total_apps FROM appointment a 
                  JOIN pet p ON a.petID = p.petID 
                  WHERE p.customerID='$customerID' AND a.status != 'Cancelled'";
$app_count_result = mysqli_query($conn, $app_count_sql);
$app_count_data = mysqli_fetch_assoc($app_count_result);
$total_appointments = $app_count_data['total_apps'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
        }
        /* Top Navigation Header matching your styles context */
        .navbar {
            background-color: #8cd3e6;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #2b5c8f;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .logo {
            font-weight: bold;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .logout-link {
            color: #d9534f;
            text-decoration: none;
            font-weight: bold;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .welcome-section {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-text h1 {
            margin: 0 0 5px 0;
            color: #2b5c8f;
        }
        .welcome-text p {
            margin: 0;
            color: #666;
        }

        /* Action Nav Buttons mapping to requested script locations */
        .action-buttons {
            display: flex;
            gap: 15px;
        }
        .nav-btn {
            background-color: #f3be6b;
            color: #333;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        /* Styling for the new primary action booking button */
        .nav-btn.booking-btn {
            background-color: #8cd3e6;
            color: #2b5c8f;
        }
        .nav-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Information Grid Grid Metrics Cards layouts */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .card-icon {
            font-size: 40px;
            background-color: #fcf1de;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-info h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #888;
            text-transform: uppercase;
        }
        .card-info p {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        /* Data Display Tables formatting templates */
        .table-section {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .table-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2b5c8f;
            border-bottom: 2px solid #f4f7f6;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th {
            background-color: #8cd3e6;
            color: #2b5c8f;
            padding: 12px 15px;
            font-weight: bold;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f4f7f6;
        }
        tr:hover {
            background-color: #fafafa;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .status-pending { background-color: #fcf1de; color: #e0ae5e; }
        .status-confirmed { background-color: #dff0d8; color: #3c763d; }
        .status-completed { background-color: #d9edf7; color: #31708f; }
        .status-cancelled { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>

    <!-- Header Panel matching navigation contexts -->
    <div class="navbar">
        <div class="logo">🐾 PawCare Dashboard</div>
        <div>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>

    <div class="main-container">
        
        <!-- Welcome banner containing path configuration links -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Hello, <?php echo htmlspecialchars($owner['name']); ?>! 👋</h1>
                <p>Welcome back to your PawCare dashboard management terminal.</p>
            </div>
            <div class="action-buttons">
                <!-- New Booking Button Added Here -->
                <a href="booking.php" class="nav-btn booking-btn">➕ New Booking</a>
                <a href="ownerDetails.php" class="nav-btn">👤 Owner Details</a>
                <a href="petDetails.php" class="nav-btn">🐶 Pet Details</a>
            </div>
        </div>

        <!-- Metric summaries showing system status counts -->
        <div class="metrics-grid">
            <div class="card">
                <div class="card-icon">🐾</div>
                <div class="card-info">
                    <h3>My Registered Pets</h3>
                    <p><?php echo $total_pets; ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">📅</div>
                <div class="card-info">
                    <h3>Active Bookings</h3>
                    <p><?php echo $total_appointments; ?></p>
                </div>
            </div>
        </div>

        <!-- Appointment Schedule Log showing exact system mapped attributes -->
        <div class="table-section">
            <h2>Upcoming Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Booking Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Select appointment and matching pet parameters from database schema
                    $app_sql = "SELECT a.appointmentID, p.petName, p.petType, a.bookingDate, a.status 
                                FROM appointment a
                                JOIN pet p ON a.petID = p.petID
                                WHERE p.customerID = '$customerID'
                                ORDER BY a.bookingDate DESC";
                    $app_result = mysqli_query($conn, $app_sql);

                    if (mysqli_num_rows($app_result) > 0) {
                        while ($row = mysqli_fetch_assoc($app_result)) {
                            // Assign specific styling layout components depending on status configurations
                            $status_class = "status-pending";
                            if (strtolower($row['status']) == 'confirmed') $status_class = "status-confirmed";
                            if (strtolower($row['status']) == 'completed') $status_class = "status-completed";
                            if (strtolower($row['status']) == 'cancelled') $status_class = "status-cancelled";

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['appointmentID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['petName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['petType']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['bookingDate']) . "</td>";
                            echo "<td><span class='status-badge {$status_class}'>" . htmlspecialchars($row['status']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; color: #888;'>No scheduled appointments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>