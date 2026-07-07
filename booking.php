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
// Fetch all available grooming services
$service_sql = "SELECT * FROM grooming ORDER BY serviceName ASC";
$service_result = mysqli_query($conn, $service_sql);

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
        body{
            margin:0;
            padding:0;
            background:#f4f7f6;
            min-height:100vh;
            overflow-x:hidden;
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

        .nav-btn{
            background:#f3be6b;
            padding:10px 15px;
            border-radius:20px;
            border:none;
            cursor:pointer;
            font-weight:bold;
            text-decoration:none;
            color:#333;
        }

        /* Center Glassmorphism Booking Container matching image_47a522.png */
        .booking-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            min-height: calc(100vh - 75px);
        }
        .booking-card{
            width:100%;
            max-width:900px;
            background:rgba(255,255,255,.92);
            backdrop-filter:blur(12px);
            border-radius:20px;
            padding:35px;
            box-shadow:0 10px 30px rgba(0,0,0,.25);
            position:relative;
        }

        .card-header-badge{
            position:absolute;
            top:-18px;
            left:50%;
            transform:translateX(-50%);

            background:#8cd3e6;
            color:#2b5c8f;

            padding:10px 28px;

            border-radius:30px;

            font-weight:bold;

            box-shadow:0 5px 12px rgba(0,0,0,.15);
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
        
        .input-underlined,
        .date-picker-custom{
            width:100%;
            padding:12px;
            border-radius:10px;
            border:2px solid #dcdcdc;
            background:#fff;
            color:#555;
            outline:none;
            transition:.3s;
        }

        .input-underlined option{
            color:#555;
            background:#fff;
        }

        .input-underlined:focus,
        .date-picker-custom:focus{
            border-color:#8cd3e6;
        }

        /* Dynamic Slot Selection Area */
        .slots-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: #555;
            max-height: 150px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .slot-row{
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 0;
            border-bottom:1px dashed #ddd;
            color:#555;
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
       .save-btn{
            background:#8cd3e6;
            color:#2b5c8f;
            padding:12px 45px;
            border:none;
            border-radius:25px;
            font-size:16px;
            font-weight:bold;
            transition:.3s;
        }

        .save-btn:hover{
            transform:translateY(-3px);
            background:#75c7dd;
        }

        /* ===== FLOATING PAWS ===== */
        .paw{
            position:fixed;
            bottom:-50px;
            font-size:24px;
            opacity:0.35;
            z-index:1; /* IMPORTANT: above overlay */
            animation:floatUp linear infinite;
            pointer-events:none;
        }

        @keyframes floatUp{
            0%{
                transform:translateY(0) translateX(0) rotate(0deg);
                opacity:0;
            }
            10%{
                opacity:0.4;
            }
            100%{
                transform:translateY(-110vh) translateX(40px) rotate(360deg);
                opacity:0;
            }
        }

        /* VIDEO BACKGROUND */
        #bg-video{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            object-fit:cover;
            z-index:-2;
        }

        /* DARK OVERLAY */
        .overlay{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,.35);
            z-index:-1;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo-container">
            <span style="color:#2b5c8f; font-weight:bold; font-size:18px;">🐾 PawCare</span>
        </div>
        <div class="nav-links">
            <a href="booking.php" class="active">Booking</a>
            <a href="petDetails.php">My Pets</a>
            <a href="ownerDetails.php">My Profile</a>
        </div>
        <div>
            <a href="dashboard.php" class="nav-btn">← Back</a>
        </div>
    </div>
    <video autoplay muted loop playsinline id="bg-video">
        <source src="videos/grooming.mp4" type="video/mp4">
    </video>

    <div class="overlay"></div>

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

                    <!-- Grooming Service -->
                    <div class="form-group">
                        <label class="label-badge">Grooming Service :</label>
                        <select name="serviceID" class="input-underlined" required>
                            <option value="">-- Select Grooming Service --</option>
                            <?php
                                while($service = mysqli_fetch_assoc($service_result))
                                {
                                ?>
                                <option
                                    value="<?php echo $service['serviceID']; ?>"
                                    data-description="<?php echo htmlspecialchars($service['description']); ?>"
                                    data-price="<?php echo number_format($service['price'],2); ?>"
                                    data-duration="<?php echo $service['duration']; ?>">
                                    <?php
                                        echo htmlspecialchars($service['serviceName']);
                                    ?>
                                </option>
                                <?php
                                }
                            ?>
                        </select>
                        <div id="serviceDescription" style="margin-top:10px;color:#666;font-size:13px;line-height:1.6;">
                            Select a grooming service to view details.
                        </div>
                    </div>

                    <!-- Date Picker sourcing fields directly from DB timeslots -->
                    <div class="form-group">
                        <label class="label-badge">Choose date :</label>
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
                        <label class="label-badge">Choose Time :</label>
                        <div id="slots-target" class="slots-container">
                            <span style="color: #666; font-style: italic; font-size: 13px;">Please select a date first...</span>
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
            slotsContainer.innerHTML ='<span style="color:#777;font-style:italic;">Please select a date first...</span>';
            return;
        }

        slotsContainer.innerHTML ='<span style="color:#777;font-style:italic;">Loading available times...</span>';

        // Targets booking.php directly using url query parameters
        fetch(`booking.php?ajax_date=${dateVal}`)
            .then(response => response.json())
            .then(data => {
                slotsContainer.innerHTML = '';
                if (data.length === 0) {
                    slotsContainer.innerHTML = '<span style="color:#d9534f;font-weight:bold;">No available slots for this date.</span>';
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
                slotsContainer.innerHTML = '<span style="color:#d9534f;">Unable to load available times.</span>';
                console.error(err);
            });
    }
    </script>
    <script>
        const serviceSelect = document.querySelector('select[name="serviceID"]');
        const serviceDesc = document.getElementById("serviceDescription");

        serviceSelect.addEventListener("change", function(){

        if(this.selectedIndex === 0)
        {
            serviceDesc.innerHTML = "Select a grooming service to view details.";
            return;
        }

        const option = this.options[this.selectedIndex];

        serviceDesc.innerHTML =
            "<strong>Description:</strong> " + option.dataset.description +
            "<br><strong>Duration:</strong> " + option.dataset.duration + " minutes" +
            "<br><strong>Price:</strong> RM " + option.dataset.price;
        });
    </script>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <script>
    document.querySelectorAll('.paw').forEach(paw => {

        // spread across full screen width
        paw.style.left = Math.random() * 100 + "vw";

        // random size (makes it cute, not robotic)
        let size = 18 + Math.random() * 22;
        paw.style.fontSize = size + "px";

        // different speeds (important for visibility)
        let duration = 6 + Math.random() * 6;
        paw.style.animationDuration = duration + "s";

        // delay so they don't spawn at same time
        paw.style.animationDelay = Math.random() * 5 + "s";
    });
    </script>
</body>
</html>