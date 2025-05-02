<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les référentiels</title>
    <link rel="stylesheet" href="/assets/css/referentiels.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-actions {
            display: flex;
            gap: 10px;
        }
        .btn-create {
            background-color: #4CAF50;
            height: 35px;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-back {
            background-color: #f5f5f5;
            color: #333;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }
        .search-section {
            margin-bottom: 20px;
        }
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #f5f5f5;
            border-radius: 4px;
            padding: 8px 12px;
            max-width: 400px;
        }
        .search-bar input {
            border: none;
            background: transparent;
            flex: 1;
            padding: 8px;
            outline: none;
        }
        .search-icon {
            margin-right: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tous les Référentiels</h1>
            <div class="header-actions">
                <a href="?page=add-referentiel" class="btn-create">Créer un nouveau référentiel</a>
                <a href="?page=referentiels" class="btn-back">Retour</a>
            </div>
        </div>

        <div class="search-section">
            <form action="" method="GET">
                <input type="hidden" name="page" value="all-referentiels">
                <div class="search-bar">
                    <div class="search-icon">🔍</div>
                    <input type="text" name="search" placeholder="      Rechercher un référentiel..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </form>
        </div>

        <div class="cards-container">
            <?php if (!empty($referentiels)): ?>
                <?php foreach ($referentiels as $ref): ?>
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= $ref['image'] ?? 'assets/images/uploads/referentiels/devweb.png' ?>" 
                                 alt="<?= htmlspecialchars($ref['name']) ?>">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($ref['name']) ?></h3>
                            <p class="card-subtitle"><?= count($ref['modules'] ?? []) ?> modules</p>
                            <p class="card-description"><?= htmlspecialchars($ref['description']) ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="card-avatars">
                                <?php for($i = 0; $i < min(3, count($ref['apprenants'] ?? [])); $i++): ?>
                                    <div class="avatar"></div>
                                <?php endfor; ?>
                            </div>
                            <div class="card-learners">
                                <?= count($ref['apprenants'] ?? []) ?> apprenants
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">Aucun référentiel trouvé</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>