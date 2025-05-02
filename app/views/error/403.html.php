<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accès interdit</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
            position: relative;
        }

        .error-code {
            font-size: 180px;
            font-weight: 900;
            background: linear-gradient(135deg, #0E8F7E, #0c7c6d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 20px;
            position: relative;
            animation: float 6s ease-in-out infinite;
        }

        .error-code::after {
            content: '403';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 182px;
            background: linear-gradient(135deg, #ff6600, #f87312);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.3;
            z-index: -1;
            filter: blur(8px);
            animation: float 6s ease-in-out infinite reverse;
        }
        
        .error-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-button {
            display: inline-block;
            background: linear-gradient(135deg, #0E8F7E, #0c7c6d);
            color: white;
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(14, 143, 126, 0.2);
        }
        
        .error-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 143, 126, 0.3);
        }

        .lock-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6600, #f87312);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        .lock-icon::before {
            content: '🔒';
            font-size: 40px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="lock-icon"></div>
        <div class="error-code">403</div>
        <h1 class="error-title">Accès interdit</h1>
        <p class="error-message">Désolé, vous n'avez pas les autorisations nécessaires pour accéder à cette page.</p>
        <a href="?page=login" class="error-button">Retour à l'accueil</a>
    </div>
</body>
</html>