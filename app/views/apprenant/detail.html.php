<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Apprenant</title>
    <link rel="stylesheet" href="assets/css/apprenant-detail.css">
</head>
<body>
    <div class="container">
        <div class="top-nav">
            <div class="logo">
                <h1>Apprenants</h1>
                <span>/</span>
                <span class="details">Détails</span>
            </div>
        </div>

        <a href="?page=apprenants" class="back-btn">
            <span class="arrow">←</span>
            <span>Retour sur la liste</span>
        </a>

        <div class="profile-section">
            <div class="left-panel">
                <div class="profile-pic">
                    <img src="<?= $apprenant['photo'] ?? 'assets/images/default-avatar.png' ?>" alt="Photo de profil">
                </div>
                <div class="profile-name"><?= htmlspecialchars($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></div>
                <div class="profile-tag"><?= htmlspecialchars($referentiel['name'] ?? 'Non assigné') ?></div>
                <div class="status"><?= $apprenant['statut'] === 'Actif' ? 'Actif' : 'Inactif' ?></div>

                <div class="contact-info">
                    <div class="contact-item">
                        <span class="icon">📱Tel</span>
                        <span><?= htmlspecialchars($apprenant['telephone'] ?? '-') ?></span>
                    </div>
                    <div class="contact-item">
                        <span class="icon">✉️Email</span>
                        <span><?= htmlspecialchars($apprenant['email'] ?? '-') ?></span>
                    </div>
                    <div class="contact-item">
                        <span class="icon">🏠Adresse</span>
                        <span><?= htmlspecialchars($apprenant['adresse'] ?? '-') ?></span>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon green-icon">✓</div>
                        <div class="stat-info">
                            <div class="stat-value"><?= $presence_data['presences'] ?></div>
                            <div class="stat-label">Présence(s)</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange-icon">⏱</div>
                        <div class="stat-info">
                            <div class="stat-value">5</div>
                            <div class="stat-label">Retard(s)</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red-icon">⚠</div>
                        <div class="stat-info">
                            <div class="stat-value">1</div>
                            <div class="stat-label">Absence(s)</div>
                        </div>
                    </div>
                </div>

                <div class="tab-section">
                    <div class="tabs">
                        <div class="tab">Programme & Modules</div>
                        <div class="tab active">Liste de présences de l'apprenant</div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Matricule</th>
                            <th>Nom Complet</th>
                            <th>Date & Heure</th>
                            <th>Statut</th>
                            <th>Justification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058215</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-absent">Absent</span></td>
                            <td><span class="justification justified">Justifié</span></td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058218</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-absent">Absent</span></td>
                            <td><span class="justification not-justified">Non justifié</span></td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058219</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>-</td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058220</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>-</td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058221</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-absent">Absent</span></td>
                            <td><span class="justification not-justified">Non justifié</span></td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058222</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-retard">Retard</span></td>
                            <td><span class="justification not-justified">Non justifié</span></td>
                            <td class="actions-menu">•••</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="profile-pic-small">
                                    <img src="/api/placeholder/40/40" alt="Profile">
                                </div>
                            </td>
                            <td>1058223</td>
                            <td>Seydina Mouhammad Diop</td>
                            <td>10/02/2023 7:32</td>
                            <td><span class="status-badge status-active">Actif</span></td>
                            <td>-</td>
                            <td class="actions-menu">•••</td>
                        </tr>
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info">
                        <span>Apprenants/page</span>
                        <select style="margin-left: 5px; padding: 2px 5px; border: 1px solid #ddd; border-radius: 4px;">
                            <option>10</option>
                        </select>
                    </div>
                    <div style="color: #888;">1 à 10 apprenants pour 142</div>
                    <div class="pagination-controls">
                        <div class="page-btn nav"><</div>
                        <div class="page-btn active">1</div>
                        <div class="page-btn">2</div>
                        <div class="page-btn">...</div>
                        <div class="page-btn">10</div>
                        <div class="page-btn nav">></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>