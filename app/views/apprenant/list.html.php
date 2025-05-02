<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Apprenants</title>
    <link rel="stylesheet" href="assets/css/apprenant-list.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="app-title">
                <h1>Apprenants</h1>
                <?php 
                // Compter le nombre d'apprenants dans la promotion active
                $nombre_apprenants = isset($apprenants) && is_array($apprenants) ? count($apprenants) : 0;
                ?>
                <span><?= $nombre_apprenants ?> apprenant<?= $nombre_apprenants > 1 ? 's' : '' ?></span>
            </div>
        </div>
        
        <div class="search-filters">
            <div class="search-input">
                <input type="text" placeholder="Rechercher...">
            </div>
            <div class="filter-dropdown">
                <form method="get" action="">
                    <input type="hidden" name="page" value="apprenants">
                    <input type="hidden" name="limit" value="<?= $apprenantsParPage ?>">
                    <select name="referentiel" onchange="this.form.submit()">
                        <option value="">Tous les référentiels</option>
                        <?php foreach ($referentiels as $referentiel): ?>
                            <option value="<?= htmlspecialchars($referentiel['id']) ?>" 
                                <?= (isset($_GET['referentiel']) && $_GET['referentiel'] == $referentiel['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($referentiel['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="filter-dropdown">
                <form method="get" action="">
                    <input type="hidden" name="page" value="apprenants">
                    <input type="hidden" name="limit" value="<?= $apprenantsParPage ?>">
                    <!-- Conserver le filtre référentiel si présent -->
                    <?php if (isset($_GET['referentiel']) && !empty($_GET['referentiel'])): ?>
                        <input type="hidden" name="referentiel" value="<?= htmlspecialchars($_GET['referentiel']) ?>">
                    <?php endif; ?>
                    <select name="statut" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="Actif" <?= (isset($_GET['statut']) && $_GET['statut'] === 'Actif') ? 'selected' : '' ?>>Actif</option>
                        <option value="Inactif" <?= (isset($_GET['statut']) && $_GET['statut'] === 'Inactif') ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </form>
            </div>
            <div class="action-buttons">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="downloadDropdown">
                        <span>Télécharger la liste</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 16L12 8M12 16L8 12M12 16L16 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 17V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="dropdown-content download-options">
                        <a href="?page=download-list&format=pdf" class="download-option">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#FF5252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 2V8H20" stroke="#FF5252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 18V12" stroke="#FF5252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 15L12 18L15 15" stroke="#FF5252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>PDF</span>
                        </a>
                        <a href="?page=download-list&format=excel" class="download-option">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 2V8H20" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 13H8" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 17H8" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 9H9H8" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Excel</span>
                        </a>
                    </div>
                </div>
                <button class="btn btn-secondary" onclick="window.location.href='?page=import-apprenants'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 8V16M12 8L8 12M12 8L16 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 17V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Importer un fichier</span>
                </button>
                <button class="btn btn-primary" onclick="window.location.href='?page=add-apprenant'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4V12M12 12V20M12 12H20M12 12H4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Ajouter apprenant</span>
                </button>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active">Liste des retenues</div>
            <div class="tab">Liste d'attente</div>
        </div>

        <?php if (!isset($apprenants) || !is_array($apprenants)) $apprenants = []; ?>

        <?php
        // Pagination
        $apprenantsParPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $pageCourante = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $totalApprenants = count($apprenants);
        $totalPages = max(1, ceil($totalApprenants / $apprenantsParPage));
        $debut = ($pageCourante - 1) * $apprenantsParPage;
        $apprenantsPage = array_slice($apprenants, $debut, $apprenantsParPage);

        // Fonction utilitaire pour le nom du référentiel
        if (!function_exists('getReferentielName')) {
            function getReferentielName($referentiels, $id) {
                if (empty($id)) {
                    return 'Non assigné';
                }
                
                foreach ($referentiels as $ref) {
                    if ($ref['id'] == $id) {
                        return $ref['name'];
                    }
                }
                return 'Référentiel #' . $id;  // Affiche l'ID si le nom n'est pas trouvé
            }
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th>Photo</th>

                    <th>Matricule</th> <!-- Changement ici -->
                    <th>Nom complet</th> <!-- Changement ici -->
                    <th>Email</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Référentiel</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apprenantsPage as $apprenant): ?>
                    <tr>
                        <td>
                            <?php if (!empty($apprenant['photo'])): ?>
                                <img src="<?= htmlspecialchars(!empty($apprenant['photo']) ? $apprenant['photo'] : 'assets/images/default-avatar.png') ?>" alt="Photo" class="avatar">
                            <?php else: ?>
                                <span style="color:#ccc;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($apprenant['matricule'] ?? '') ?></td>
                        <td><?= htmlspecialchars(($apprenant['prenom'] ?? '') . ' ' . ($apprenant['nom'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($apprenant['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($apprenant['adresse'] ?? '') ?></td>
                        <td><?= htmlspecialchars($apprenant['telephone'] ?? '') ?></td>
                        <td>
                            <?= htmlspecialchars(getReferentielName($referentiels, $apprenant['referentiel_id'] ?? '')) ?>
                        </td>
                        <td>
                            <?php if (($apprenant['statut'] ?? '') === 'Actif'): ?>
                                <span class="badge badge-green">Actif</span>
                            <?php elseif (($apprenant['statut'] ?? '') === 'Inactif'): ?>
                                <span class="badge badge-red">Inactif</span>
                            <?php else: ?>
                                <?= htmlspecialchars($apprenant['statut'] ?? '') ?>
                            <?php endif; ?>
                        </td>
                        <td class="action-menu">
                            <div class="dropdown">
                                <span class="options-icon">
                                    <!-- Icône trois points verticaux -->
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="1.5"/>
                                        <circle cx="12" cy="12" r="1.5"/>
                                        <circle cx="12" cy="19" r="1.5"/>
                                    </svg>
                                </span>
                                <div class="dropdown-content">
                                    <a href="?page=apprenant-detail&id=<?= htmlspecialchars($apprenant['matricule']) ?>">Détail</a>
                                    <a href="#">Modifier</a>
                                    <a href="#">Supprimer</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <div class="pagination-info">
                <span>Apprenants/page</span>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="page" value="apprenants">
                    <?php if (isset($_GET['referentiel']) && !empty($_GET['referentiel'])): ?>
                        <input type="hidden" name="referentiel" value="<?= htmlspecialchars($_GET['referentiel']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['statut']) && !empty($_GET['statut'])): ?>
                        <input type="hidden" name="statut" value="<?= htmlspecialchars($_GET['statut']) ?>">
                    <?php endif; ?>
                    <select name="limit" onchange="this.form.submit()">
                        <?php foreach ([5,10,20,50] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $apprenantsParPage == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <span>
                    <?= $debut + 1 ?> à <?= min($debut + $apprenantsParPage, $totalApprenants) ?> apprenants pour <?= $totalApprenants ?>
                </span>
            </div>
            <div class="pagination-controls">
                <?php 
                // Construire les paramètres d'URL pour conserver les filtres
                $url_params = '?page=apprenants&limit=' . $apprenantsParPage;
                if (isset($_GET['referentiel']) && !empty($_GET['referentiel'])) {
                    $url_params .= '&referentiel=' . htmlspecialchars($_GET['referentiel']);
                }
                if (isset($_GET['statut']) && !empty($_GET['statut'])) {
                    $url_params .= '&statut=' . htmlspecialchars($_GET['statut']);
                }
                ?>
                
                <?php if ($pageCourante > 1): ?>
                    <a href="<?= $url_params ?>&p=<?= $pageCourante-1 ?>"><button class="page-btn"><</button></a>
                <?php else: ?>
                    <button class="page-btn" disabled><</button>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $pageCourante): ?>
                        <button class="page-btn active"><?= $i ?></button>
                    <?php else: ?>
                        <a href="<?= $url_params ?>&p=<?= $i ?>"><button class="page-btn"><?= $i ?></button></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pageCourante < $totalPages): ?>
                    <a href="<?= $url_params ?>&p=<?= $pageCourante+1 ?>"><button class="page-btn">></button></a>
                <?php else: ?>
                    <button class="page-btn" disabled>></button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>