<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Ink & Solace</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        :root {
            --bg-color: #dbdbdb; 
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            background-color: var(--bg-color);
            font-family: 'Montserrat', sans-serif;
            overflow: hidden; 
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER --- */
        .header {
            padding: 4vh 5vw;
            display: flex;
            justify-content: flex-start;
            z-index: 10;
        }

        .logo-img {
            width: 10vw;
            min-width: 80px;
            height: auto;
            display: block;
        }

        /* --- MAIN CONTENT (MOVED UP) --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Changed from center to flex-start to move it to the top */
            justify-content: flex-start; 
            /* Adjust this value to move it higher or lower from the top */
            padding-top: 5vh; 
            text-align: center;
            padding-left: 5vw;
            padding-right: 5vw;
        }

        .welcome-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1vh; 
        }

        /* Scalable Images */
        .img-welcome {
            height: 22vh;
            width: auto;
            max-width: 90vw;
            object-fit: contain;
        }

        .img-subtitle {
            height: 7vh;
            width: auto;
            max-width: 80vw;
            object-fit: contain;
        }

        /* --- BOTTOM RIGHT LINK --- */
        .proceed-container {
            position: absolute;
            bottom: 6vh;
            right: 6vw;
        }

        .proceed-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.2s ease, opacity 0.3s;
        }

        .proceed-link:hover {
            opacity: 0.7;
            transform: translateY(-2px);
        }

        .img-proceed-text {
            height: 4vh;
            width: auto;
            max-width: 40vw;
        }

        /* Mobile Adjustments */
        @media (max-height: 500px) or (max-width: 480px) {
            .main-content { padding-top: 2vh; }
            .img-welcome { height: 16vh; }
            .img-subtitle { height: 5vh; }
            .proceed-container { 
                right: 50%; 
                transform: translateX(50%); 
                bottom: 4vh; 
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <img src="assets/text/logo-img.png" alt="Ink & Solace" class="logo-img">
    </header>

    <main class="main-content">
        <div class="welcome-container">
            <img src="assets/text/text-welcome.png" alt="Welcome," class="img-welcome">
        </div>
        <img src="assets/text/text-subtitle.png" alt="What would you like to do today?" class="img-subtitle">
    </main>

    <div class="proceed-container">
        <a href="main_menu.php" class="proceed-link">
            <img src="assets/text/btn-proceed-menu.png" alt="PROCEED TO MAIN MENU" class="img-proceed-text">
        </a>
    </div>

</body>
</html>