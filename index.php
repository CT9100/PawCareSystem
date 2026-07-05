<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PawCare Booking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Poppins',sans-serif;
            background:
                linear-gradient(rgba(0,0,0,.45),rgba(0,0,0,.45)),
                url("images/index.jpg");
            background-size:cover;
            background-position:center;
            background-attachment:fixed;
            color:#333;
            display:flex;
            flex-direction:column;
            min-height:100vh;
        }

        header{
            background:#5EC6D8;
            padding:18px 50px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            backdrop-filter:blur(10px);
            box-shadow:0 8px 20px rgba(0,0,0,.25);
        }

        header h2{
            color:white;
        }

        .login-btn{
            text-decoration:none;
            background:#8BC34A;
            color:white;
            padding:12px 25px;
            border-radius:30px;
            font-weight:600;
            transition:.3s;
        }

        .login-btn:hover{
            background:#6DA62F;
            transform:translateY(-2px);
        }

        .container{
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:60px 20px;
        }

        h1{
            color:white;
            font-size:52px;
            font-weight:700;
            margin-bottom:20px;
            text-shadow:2px 2px 12px rgba(0,0,0,.4);
        }

        .intro{
            max-width:800px;
            color:white;
            font-size:18px;
            line-height:1.8;
            margin-bottom:50px;
            text-shadow:1px 1px 5px rgba(0,0,0,.3);
        }

        .services{
            display:flex;
            justify-content:center;
            flex-wrap:wrap;
            gap:25px;
        }

        .service-card{
            width:260px;
            background:rgba(255,255,255,.92);
            backdrop-filter:blur(12px);
            border-radius:18px;
            padding:25px;
            transition:.35s;
            box-shadow:0 10px 25px rgba(0,0,0,.2);
        }

        .service-card:hover{
            transform:translateY(-10px) scale(1.03);
            box-shadow:0 20px 35px rgba(0,0,0,.3);
        }

       .image-container{ 
            width:180px; 
            height:140px; 
            margin:0 auto 20px; 
            overflow:hidden; 
            border-radius:12px; 
        } 
        
        .image-container img{ 
            width:100%; 
            height:100%; 
            object-fit:cover; 
        }

        .service-card h3{
            color:#5EC6D8;
            margin-bottom:10px;
        }

        .service-card p{
            font-size:15px;
            color:#666;
        }

        footer{
            background:rgba(0,0,0,.55);
            color:white;
            padding:18px;
            backdrop-filter:blur(10px);
            text-align:center;
        }
    </style>
</head>

<body>

<header>
    <h2>🐾 PawCare Pet Care Management System</h2>

    <a href="login.php" class="login-btn">Login</a>
</header>

<div class="container">

    <h1>Welcome to PawCare</h1>

    <p class="intro">
        Book professional pet care services quickly and easily.
        PawCare helps pet owners schedule appointments for grooming,
        veterinary check-ups, vaccinations, and pet boarding—all in one place.
    </p>

    <div class="services">

        <div class="service-card">
            <div class="image-container">
                <img src="images/grooming.jpg" alt="Pet Grooming">
            </div>
                <h3>Pet Grooming</h3>
                <p>Professional grooming services to keep your pets clean, healthy, and happy.</p>
        </div>

        <div class="service-card">
            <div class="image-container">
                <img src="images/vet.jpg" alt="Veterinary">
            </div>
                <h3>Veterinary Check-up</h3>
                <p>Book appointments with experienced veterinarians for regular health check-ups.</p>
        </div>

        <div class="service-card">
            <div class="image-container">
                <img src="images/vaccine.jpg" alt="Vaccination">
            </div>
                <h3>Vaccination</h3>
                <p>Schedule vaccination appointments to protect your pets from common diseases.</p>
        </div>    

        <div class="service-card">
            <div class="image-container">
                <img src="images/boarding.jpg" alt="Pet Boarding">
            </div>
                <h3>Pet Boarding</h3>
                <p>Safe and comfortable boarding services while you're away from home.</p>
        </div>

    </div>

</div>

<footer>
    &copy; 2026 PawCare Booking System. All Rights Reserved.
</footer>

</body>
</html>
