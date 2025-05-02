<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Oops!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
            perspective: 1000px;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: containerFloat 6s ease-in-out infinite;
        }

        .error-code {
            font-size: 150px;
            font-weight: 900;
            position: relative;
            margin-bottom: 20px;
            display: inline-block;
        }

        .error-code .digit {
            position: relative;
            display: inline-block;
            animation: bounce 1.5s ease-in-out infinite;
        }

        .error-code .digit:nth-child(2) {
            animation-delay: 0.2s;
        }

        .error-code .digit:nth-child(3) {
            animation-delay: 0.3s;
        }

        .error-code .digit span {
            background: linear-gradient(135deg, #ff6600, #f87312);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .astronaut {
            position: absolute;
            width: 120px;
            height: 120px;
            background: url('assets/images/astronaut.svg') no-repeat center;
            background-size: contain;
            animation: float 6s ease-in-out infinite;
            top: -60px;
            right: -30px;
            transform-origin: center;
        }

        .planet {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0E8F7E, #0c7c6d);
            left: -40px;
            bottom: -20px;
            animation: planetRotate 20s linear infinite;
        }

        .planet::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            animation: starTwinkle 2s ease-in-out infinite;
        }

        .star {
            position: absolute;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }

        .error-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        .error-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff6600, #f87312);
            color: white;
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(248, 115, 18, 0.2);
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        
        .error-button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(248, 115, 18, 0.3);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(3deg); }
            50% { transform: translateY(-30px) rotate(-3deg); }
        }

        @keyframes planetRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes containerFloat {
            0%, 100% { transform: translateY(0) rotateX(0deg); }
            50% { transform: translateY(-10px) rotateX(2deg); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes starTwinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

    </style>
</head>
<body>
    <div class="stars">
        <?php for($i = 0; $i < 50; $i++): ?>
            <div class="star" style="left: <?= rand(0, 100) ?>%; top: <?= rand(0, 100) ?>%;"></div>
        <?php endfor; ?>
    </div>
    <div class="error-container">
        <div class="astronaut"></div>
        <div class="planet"></div>
        <div class="error-code">
            <div class="digit"><span>4</span></div>
            <div class="digit"><span>0</span></div>
            <div class="digit"><span>4</span></div>
        </div>
        <h1 class="error-title">Oops! Page introuvable</h1>
        <p class="error-message">Il semblerait que vous vous soyez perdu dans l'espace... 🚀</p>
        <a href="?page=login" class="error-button">
            Retour sur Terre 🌍
        </a>
    </div>
</body>
</html>