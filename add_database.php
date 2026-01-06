
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Database | Ink & Solace</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        :root {
            --light-bg: #dbdbdb; 
            --dark-bg: #20252d; 
            --text-white: #ececec;
            --btn-blue: #3c4862; 
            --font-sans: 'Montserrat', sans-serif;
            --banner-tint: rgba(143, 137, 137, 0.4); 
        }

        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: var(--font-sans);
            display: flex;
            flex-direction: column;
            background-color: var(--dark-bg);
            overflow-x: hidden; 
        }

        /* --- TOP SECTION --- */
        .top-section {
            background-color: var(--light-bg);
            height: 22vh; 
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            padding: 0 5%;
        }

        .nav-bar {
            position: absolute;
            top: 2vh; 
            left: 5%;
            right: 5%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start; 
            z-index: 10;
        }

        .custom-logo {
            width: 8vw;
            min-width: 80px;
            max-width: 120px;
            height: auto;
        }

        .btn-return {
            margin-top: 13vh; 
            background-color: var(--btn-blue);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-return:hover { 
            background-color: #4e5e7a;
            opacity: 0.9;
        }

        .title-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .img-main-title {
            height: 12vh;
            max-width: 80%;
            object-fit: contain;
        }

        /* --- BOTTOM SECTION --- */
        .bottom-section {
            background-color: var(--dark-bg);
            flex: 1;
            padding: 4vh 5% 5vh 5%;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .banner-container {
            width: 100%;
            max-width: 1100px;
            height: 20vh; 
            margin-bottom: 6vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: visible; 
            border-radius: 20px;
            background-color: var(--banner-tint);
        }

        .banner-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/text/banner-background-bubble.png'); 
            background-size: cover;
            background-position: center;
            opacity: 0.15; 
            border-radius: 20px;
        }

        .img-banner-label {
            position: relative;
            z-index: 2; 
            height: 60%;
            width: auto;
            object-fit: contain;
            /* Changed scale from 2.0 to 1.5 to make it smaller */
            transform: scale(1.5); 
            filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.4));
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 1100px;
            margin-bottom: 30px;
            margin-top: 2vh;
        }

        .option-item {
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .option-item:hover { transform: translateY(-5px); }

        .option-box {
            border: 1px solid rgba(255, 255, 255, 0.8); 
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: flex-start; 
            padding-left: 20px;
            text-decoration: none;
            box-sizing: border-box;
        }

        /* Removed .report-item CSS */

        .img-option-text {
            max-height: 70%;
            max-width: 90%;
            object-fit: contain;
        }

        .proceed-link {
            margin-top: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-white);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            text-decoration: none;
        }

        .proceed-link svg {
            width: 18px;
            margin-left: 8px;
        }

        @media (max-width: 800px) {
            .options-grid { grid-template-columns: 1fr; }
            .img-banner-label { transform: scale(1.4); }
            .nav-bar { top: 2vh; } 
            .btn-return { margin-top: 20px; padding: 8px 15px; font-size: 11px; }
        }
    </style>
</head>
<body>

    <div class="top-section">
        <div class="nav-bar">
            <img src="assets/text/logo-img.png" alt="Ink & Solace" class="custom-logo">
            <a href="main_menu.php" class="btn-return">Return to Main Menu</a>
        </div>

        <div class="title-container">
            <img src="assets/text/title-main-menu.png" alt="Main Menu" class="img-main-title">
        </div>
    </div>

    <div class="bottom-section">
        <div class="banner-container">
            <div class="banner-bg"></div> 
            <img src="assets/text/label_add_database.png" alt="Add Database" class="img-banner-label">
        </div>

        <div class="options-grid">
            <div class="option-item">
                <a href="add_publisher_title.php" class="option-box">
                    <img src="assets/text/text-opt-publishers-titles.png" alt="Publishers & Titles" class="img-option-text">
                </a>
                <a href="add_publisher_title.php" class="proceed-link">
                    PROCEED <svg viewBox="0 0 24 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
                </a>
            </div>

            <div class="option-item">
                <a href="add_publisher_employee.php" class="option-box">
                    <img src="assets/text/text-opt-publishers-employees.png" alt="Publishers & Employees" class="img-option-text">
                </a>
                <a href="add_publisher_employee.php" class="proceed-link">
                    PROCEED <svg viewBox="0 0 24 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
                </a>
            </div>

            <div class="option-item">
                <a href="add_authors_titles.php" class="option-box">
                    <img src="assets/text/text-opt-authors-titles.png" alt="Authors & Titles" class="img-option-text">
                </a>
                <a href="add_authors_titles.php" class="proceed-link">
                    PROCEED <svg viewBox="0 0 24 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M0 6L22 6M22 6L17 1M22 6L17 11"></path></svg>
                </a>
            </div>
        </div>
        
        </div>

</body>
</html>
