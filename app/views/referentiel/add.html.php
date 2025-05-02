<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau référentiel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            height: 100vh;
        }
        .container {
            background-color: white;
            width: 100%;
            height: 100%;
            max-width: 100%;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .page-header {
            margin-bottom: 20px;
        }
        .form-content {
            max-width: 500px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .required:after {
            content: "*";
            color: #e53e3e;
        }
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
        }
        .image-upload-icon {
            color: #aaa;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .image-upload-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-cancel {
            background-color: transparent;
            color: #666;
        }
        .btn-create {
            background-color: #9cd1d1;
            color: white;
        }
        .file-input {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Créer un nouveau référentiel</h2>
        </div>
        <div class="form-content">
            <form action="?page=add-referentiel-process" method="POST" enctype="multipart/form-data">
                <div class="image-upload">
                    <div class="image-upload-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    <div class="image-upload-text">Sélectionnez une image pour le référentiel</div>
                    <input type="file" id="referentiel_image" name="referentiel_image" class="file-input" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label class="required" for="referentiel_name">Nom</label>
                    <input type="text" id="referentiel_name" name="referentiel_name" required>
                </div>
                
                <div class="form-group">
                    <label for="referentiel_details">Description</label>
                    <textarea id="referentiel_details" name="referentiel_details"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="required" for="capacity">Capacité</label>
                        <input type="number" id="capacity" name="capacity" value="30" required>
                    </div>
                    <div class="form-group">
                        <label class="required" for="sessions">Nombre de sessions</label>
                        <select id="sessions" name="sessions" required>
                            <option value="1" selected>1 session</option>
                            <option value="2">2 sessions</option>
                            <option value="3">3 sessions</option>
                            <option value="4">4 sessions</option>
                            <option value="5">5 sessions</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="?page=all-referentiels" class="btn btn-cancel">Annuler</a>
                    <button type="submit" class="btn btn-create">Créer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>