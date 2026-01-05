<?php
// Main Menu - Ink & Solace
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu | Ink & Solace</title>
    <style>
        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d; 
            --btn-blue: #3C4862; /* Color picked from your image */
        }

        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden; 
            background-color: var(--dark-bg);
            font-family: 'Montserrat', sans-serif; 
        }

        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap');

        /* --- TOP SECTION --- */
        .top-section {
            background-color: var(--light-bg);
            height: 35vh; 
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .nav-bar {
            position: absolute;
            top: 40px;
            left: 0;
            width: 100%;
            padding: 0 60px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between; 
            align-items: center;
        }

        .custom-logo {
            width: 120px; 
            height: auto;
        }

        /* --- NEW BUTTON STYLE (Copied from your design) --- */
        .btn-return {
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700; /* Bold text like the image */
            font-size: 16px;
            color: #ffffff; /* White text */
            background-color: var(--btn-blue); /* Dark Blue Background */
            padding: 15px 35px; /* Pill shape padding */
            border-radius: 50px; /* High radius for pill shape */
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2); /* Subtle shadow */
            border: none;
            white-space: nowrap;
        }

        .btn-return:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            opacity: 0.95;
        }

        .title-img {
            width: 500px; 
            max-width: 85%;
            height: auto;
            margin-top: 30px;
        }

        /* --- BOTTOM SECTION --- */
        .bottom-section {
            flex: 1; 
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px; 
            padding: 40px;
            flex-wrap: wrap;
        }

        /* --- CARDS --- */
        .card-link {
            width: 420px; 
            height: 420px; 
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            text-decoration: none;
            transition: transform 0.3s ease;
            background-color: #8f8989; 
        }

        .card-link:hover {
            transform: scale(1.05);
        }

        .card-bg-image {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0.4; 
            transition: opacity 0.3s ease;
        }

        .card-link:hover .card-bg-image {
            opacity: 0.6;
        }

        /* Overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.4); 
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Text Image inside Cards */
        .card-title-img {
            width: 260px; 
            max-width: 80%;
            height: auto;
            z-index: 2;
        }

        .img-edit { background-image: url('assets/text/bg-edit.png'); }
        .img-view { background-image: url('assets/text/bg-view.png'); }

        @media (max-width: 1024px) {
            .card-link { width: 340px; height: 340px; }
            .title-img { width: 400px; }
        }

        @media (max-width: 768px) {
            .top-section { height: auto; padding: 100px 20px 40px; }
            .nav-bar { padding: 0 30px; top: 20px; }
            .card-link { width: 100%; max-width: 380px; height: 300px; }
            .btn-return { padding: 10px 20px; font-size: 13px; } 
            .custom-logo { width: 90px; }
        }
    </style>
</head>
<body>

    <div class="top-section">
        <div class="nav-bar">
            <img src="assets/text/logo-img.png" alt="Ink & Solace" class="custom-logo">
            
            <a href="index.php" class="btn-return">Return to Main Menu</a>
        </div>
        <img src="assets/text/title-main-menu.png" alt="Main Menu" class="title-img">
    </div>

    <div class="bottom-section">
        
        <a href="add_database.php" class="card-link">
            <div class="card-bg-image img-edit"></div>
            <div class="overlay">
                <img src="assets/text/label_add_database.png" alt="Edit Database" class="card-title-img">
            </div>
        </a>

        <a href="admin_view_database.php" class="card-link">
            <div class="card-bg-image img-view"></div>
            <div class="overlay">
                <img src="assets/text/label-view-database.png" alt="View Database" class="card-title-img">
            </div>
        </a>
        
    </div>

</body>

</html>