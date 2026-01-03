<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ink & Solace Library</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap');

        :root {
            --bg-color: #1c202a;
            --overlay-tint: rgba(32, 37, 45, 0.6); 
            --card-bg: #2a2e38;
            --text-white: #ffffff;
            --gap-size: 24px;
            --radius: 20px;
            --transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: var(--bg-color);
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
            color: var(--text-white);
            overflow-x: hidden;
        }

        *, *::before, *::after { box-sizing: inherit; }

        .img-text {
            display: block;
            object-fit: contain;
            height: auto;
        }

        /* --- HERO SECTION --- */
        .hero {
            position: relative;
            height: 100vh;
            width: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.3) 60%, var(--bg-color) 100%), 
                        url('assets/text/Background.png') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .script-overlay {
            position: absolute;
            inset: 0;
            z-index: 1; 
            pointer-events: none; 
            background: url('assets/text/Solace_Text_BG.png') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: flex-end; 
            justify-content: flex-start; 
        }

        .script-overlay::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: var(--overlay-tint);
            z-index: -1; 
        }

        .overlay-solace-img { 
            width: 55vw; 
            max-width: 850px; 
            filter: drop-shadow(5px 5px 25px rgba(0,0,0,0.7));
            opacity: 0.95;
            margin-left: -40px; 
            margin-bottom: -10px;
            transition: var(--transition);
        }

        /* --- UI ELEMENTS --- */
        .subtitle-underline {
            width: 100%;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            position: absolute;
            top: 105px;
            z-index: 5;
        }

        .hero-top-center {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 30;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .subtitle-img { 
            height: 50px; 
            max-width: 80vw;
        } 

        .nav-top {
            position: absolute;
            top: 140px; 
            width: 100%;
            padding: 0 5%; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 20;
        }

        .nav-img { 
            height: 32px; 
            max-width: 40vw; 
            transition: var(--transition);
        } 

        .nav-top a:hover .nav-img {
            transform: translateY(-3px);
            opacity: 0.7;
        }

        .proceed-link {
            position: absolute;
            bottom: 40px;
            right: 40px;
            z-index: 20;
            transition: var(--transition);
        }
        .proceed-img { 
            height: 50px; 
            width: auto;
        } 
        .proceed-link:hover { transform: scale(1.08); }

        /* --- BENTO GALLERY --- */
        .gallery-container {
            width: 90%; 
            max-width: 1800px;
            margin: 80px auto;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: 24% 1fr; 
            gap: var(--gap-size); 
            height: 850px; 
        }

        .grid-img {
            background-color: var(--card-bg);
            border-radius: var(--radius);
            background-size: cover;
            background-position: center;
            transition: var(--transition);
        }

        .grid-img:hover { transform: scale(1.01); }
        
        .g-item-1 { background-image: url('assets/text/tall_left.png'); }
        .g-item-2 { height: 45%; background-image: url('assets/text/mid_level.png'); }
        .g-item-3 { background-image: url('assets/text/medium_bottom.png'); }
        .g-item-4 { flex: 1; background-image: url('assets/text/small_top.png'); }
        .g-item-5 { flex: 1; background-image: url('assets/text/small_bottom.png'); }

        .right-col { display: flex; flex-direction: column; gap: var(--gap-size); }
        .bottom-row { flex: 1; display: grid; grid-template-columns: 1.5fr 1fr; gap: var(--gap-size); }
        .stacked-col { display: flex; flex-direction: column; gap: var(--gap-size); }

        /* --- RESPONSIVE ADJUSTMENTS --- */
        @media (max-width: 1024px) {
            .bento-grid { height: 700px; }
            .nav-top { top: 120px; }
            .nav-img { height: 28px; }
            .overlay-solace-img { width: 65vw; margin-left: -20px; }
            .proceed-img { height: 45px; }
        }

        @media (max-width: 768px) {
            .hero { height: 80vh; min-height: 500px; }
            .bento-grid { grid-template-columns: 1fr; height: auto; }
            .g-item-1 { height: 400px; }
            .right-col { height: 1000px; }
            
            .script-overlay { justify-content: center; align-items: center; }
            .overlay-solace-img { width: 80vw; margin-left: 0; margin-bottom: 0; }

            .nav-top { top: 110px; padding: 0 30px; }
            .nav-img { height: 22px; } 
            .subtitle-img { height: 35px; }
            .subtitle-underline { top: 90px; }

            .proceed-img { height: 40px; }
            .proceed-link { bottom: 30px; right: 30px; }
        }

        @media (max-width: 480px) {
            .nav-top { 
                flex-direction: column; 
                gap: 15px; 
                top: 105px; 
            }
            .nav-img { 
                height: 18px; 
            }

            .overlay-solace-img { width: 85vw; }

            .proceed-link { 
                right: 50%; 
                transform: translateX(50%); 
                bottom: 25px; 
                width: 100%;
                display: flex;
                justify-content: center;
            }
            .proceed-img { 
                height: 32px; 
                max-width: 160px; 
            }
            .proceed-link:hover { transform: translateX(50%) scale(1.05); }
        }
    </style>
</head>
<body>

    <?php
        // PHP variables for easy management
        $login_page = "login.php";
        $menu_page = "menu.php";
    ?>

    <header class="hero">
        <div class="subtitle-underline"></div>

        <div class="hero-top-center">
            <img src="assets/text/text-bespoke-solitude.png" alt="Bespoke Solitude" class="subtitle-img img-text">
        </div>

        <nav class="nav-top">
            <a href="#"><img src="assets/text/text-metro-manila.png" alt="Metro Manila" class="nav-img img-text"></a>
            <a href="<?php echo $login_page; ?>"><img src="assets/text/text-admin-login.png" alt="Admin Login" class="nav-img img-text"></a>
        </nav>

        <div class="script-overlay">
            <img src="assets/text/text-overlay-solace.png" alt="Solace" class="overlay-solace-img img-text">
        </div>

        <a href="<?php echo $menu_page; ?>" class="proceed-link">
            <img src="assets/text/text-proceed-button.png" alt="Proceed" class="proceed-img img-text">
        </a>
    </header>

    <section class="gallery-container">
        <div class="bento-grid">
            <div class="grid-img g-item-1"></div>   
            
            <div class="right-col">
                <div class="grid-img g-item-2"></div>
                <div class="bottom-row">
                    <div class="grid-img g-item-3"></div>
                    <div class="stacked-col">
                        <div class="grid-img g-item-4"></div>
                        <div class="grid-img g-item-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>

