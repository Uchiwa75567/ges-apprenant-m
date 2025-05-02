<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des Apprenants</title>
    <link rel="stylesheet" href="assets/css/pdf-template.css">
</head>
<body>
    <div class="header">
        <h1>Liste des Apprenants</h1>
        <div class="promotion"><?= htmlspecialchars($promotion_name) ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Référentiel</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($apprenants as $apprenant): ?>
            <tr>
                <td><?= htmlspecialchars(($apprenant['prenom'] ?? '') . ' ' . ($apprenant['nom'] ?? '')) ?></td>
                <td><?= htmlspecialchars($apprenant['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($apprenant['telephone'] ?? '') ?></td>
                <td>
                    <?php
                    $ref_id = $apprenant['referentiel_id'] ?? '';
                    $ref = array_filter($referentiels, function($r) use ($ref_id) {
                        return ($r['id'] ?? '') === $ref_id;
                    });
                    echo htmlspecialchars(reset($ref)['nom'] ?? '');
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Document généré le <?= date('d/m/Y à H:i:s') ?>
    </div>
</body>
</html>