<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu | Ink & Solace</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400&family=Montserrat:wght@600&display=swap');

        :root {
            --bg-color: #dbdbdb; 
            --border-color: #999; 
            --text-color: #1a1a1a;
            --font-sans: 'Montserrat', sans-serif;
            --accent-color: #000;
            /* Button Color */
            --btn-return: #3b4559; 
        }

        body, html {
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            font-family: var(--font-sans);
            color: var(--text-color);
            overflow-x: hidden;
            /* Ensures the footer stays at the bottom relative to the viewport height */
            min-height: 100vh; 
            position: relative; 
        }

        /* --- HEADER --- */
        .header {
            display: flex;
            justify-content: center; 
            align-items: flex-start; 
            padding: 0px 40px; 
            position: relative;
            min-height: 150px;
        }

        .logo-img {
            width: 100px;  
            height: auto;
            margin-top: 45px;
            position: absolute; 
            left: 40px;
            z-index: 10;
        }

        .title-container {
            text-align: center;
            line-height: 0;
            margin-top: 110px;
        }

        .title-main-menu {
            height: 160px; 
            width: auto;
            display: inline-block;
        }

        /* --- MENU GRID --- */
        .menu-grid {
            max-width: 1200px;
            margin: 60px auto 0; 
            /* Added padding-bottom to ensure content doesn't overlap the button */
            padding: 0 40px 100px 40px; 
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .grid-item {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            cursor: pointer;
        }

        .menu-box {
            border: 1px solid var(--border-color);
            height: 140px; 
            padding: 20px;
            display: flex;
            align-items: center; 
            justify-content: flex-start;
            transition: all 0.3s ease;
            background-color: transparent;
        }
        
        .grid-item:hover .menu-box {
            background-color: rgba(0,0,0,0.05);
            border-color: var(--accent-color);
        }

        .menu-box img {
            max-width: 90%;
            max-height: 70%;
            object-fit: contain;
        }

        /* --- PROCEED LINK --- */
        .proceed-link {
            margin-top: 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #444;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            transition: color 0.3s ease;
        }

        .proceed-link svg {
            width: 18px;
            height: 10px;
            margin-left: 8px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            transition: transform 0.3s ease;
        }

        .grid-item:hover .proceed-link {
            color: var(--accent-color);
        }

        .grid-item:hover .proceed-link svg {
            transform: translateX(8px);
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .full-width .menu-box {
            height: 100px;
        }

        /* ================= RETURN BUTTON (Bottom Right) ================= */
        .return-footer {
            position: absolute;
            bottom: 30px;
            right: 5%;
            z-index: 100;
        }

        .btn-return-wrap {
            background-color: var(--btn-return);
            padding: 15px 35px; /* Adjusted padding for text */
            border-radius: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: transform 0.2s ease;
            
            /* Text Styling added here to replace image */
            color: #ffffff;
            font-family: var(--font-sans);
            font-weight: 700;
            font-size: 16px;
        }
        
        .btn-return-wrap:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.35);
        }

        @media (max-width: 900px) {
            .header { padding: 0 20px; }
            .logo-img { width: 60px; left: 20px; margin-top: 20px; }
            .title-main-menu { height: 100px; }
            .title-container { margin-top: 80px; }
            
            .menu-grid { 
                grid-template-columns: repeat(3, 1fr); 
                padding: 0 20px 40px 20px;
                gap: 15px;
            }
            .menu-box { height: 100px; padding: 10px; }
            .proceed-link { font-size: 9px; }
        }

        @media (max-width: 768px) {
            .return-footer { 
                position: static; 
                margin-top: 30px; 
                margin-bottom: 40px; 
                display: flex; 
                justify-content: center; 
                width: 100%; 
                right: auto; 
            }
            
            .menu-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            .title-main-menu { height: 60px; }
        }
    </style>
</head>
<body>

    <header class="header">
        <img src="assets/text/logo-img.png" alt="Logo" class="logo-img">
        
        <div class="title-container">
            <img src="assets/text/title-main-menu.png" alt="Main Menu" class="title-main-menu">
        </div>
    </header>

    <main class="menu-grid">
        <a href="view_publisher_title.php" class="grid-item">
            <div class="menu-box">
                <img src="assets/text/text-publishers-titles.png" alt="Publishers and Titles">
            </div>
            <div class="proceed-link">
                PROCEED <svg viewBox="0 0 24 12"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
            </div>
        </a>

        <a href="view_publisher_employee.php" class="grid-item">
            <div class="menu-box">
                <img src="assets/text/text-publishers-employees.png" alt="Publishers and Employees">
            </div>
            <div class="proceed-link">
                PROCEED <svg viewBox="0 0 24 12"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
            </div>
        </a>

        <a href="view_authors_titles.php" class="grid-item">
            <div class="menu-box">
                <img src="assets/text/text-authors-titles.png" alt="Authors and Titles">
            </div>
            <div class="proceed-link">
                PROCEED <svg viewBox="0 0 24 12"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
            </div>
        </a>

        <a href="view_report.php" class="grid-item full-width">
            <div class="menu-box">
                <img src="assets/text/text-report.png" alt="Report">
            </div>
            <div class="proceed-link">
                PROCEED <svg viewBox="0 0 24 12"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
            </div>
        </a>
    </main>

    <div class="return-footer">
        <a href="index.php" style="text-decoration:none;">
            <div class="btn-return-wrap">
                Return to Main Menu
            </div>
        </a>
    </div>

</body>
</html>