<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;

// Fonction pour afficher les détails d'un apprenant
function apprenant_detail() {
    global $model, $session_services;
    
    // Vérifier si l'utilisateur est connecté
    if (!$session_services['is_logged_in']()) {
        header('Location: ?page=login');
        exit;
    }
    
    // Récupérer le matricule de l'apprenant depuis l'URL
    $matricule = $_GET['id'] ?? null;
    
    if (!$matricule) {
        header('Location: ?page=apprenants');
        exit;
    }
    
    // Récupérer l'apprenant par son matricule
    $apprenant = $model['get_apprenant_by_matricule']($matricule);
    
    if (!$apprenant) {
        header('Location: ?page=apprenants');
        exit;
    }
    
    // Récupérer le référentiel associé à l'apprenant
    $referentiel = null;
    if (isset($apprenant['referentiel_id']) && !empty($apprenant['referentiel_id'])) {
        $referentiel = $model['get_referentiel_by_id']($apprenant['referentiel_id']);
    }
    
    // Données de présence pour les statistiques
    $presence_data = [
        'presences' => 20,
        'retards' => 5,
        'absences' => 1
    ];
    
    // Charger la vue avec les données
    render('admin.layout.php', 'apprenant/detail.html.php', [
        'apprenant' => $apprenant,
        'referentiel' => $referentiel,
        'presence_data' => $presence_data
    ]);
}

function list_apprenants() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Définir le nombre d'apprenants par page
    $apprenantsParPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // Récupérer la page courante
    $pageCourante = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    
    // Récupérer les apprenants et référentiels
    $apprenants = $model['get_all_apprenants']();
    $referentiels = $model['get_all_referentiels']();
    
    // Récupérer la promotion active
    $promotions = $model['get_all_promotions']();
    $promotion_active = null;
    foreach ($promotions as $promotion) {
        if (($promotion['status'] ?? '') === 'active') {
            $promotion_active = $promotion;
            break;
        }
    }

    // Filtrer les référentiels pour n'avoir que ceux de la promotion active
    $referentiels_disponibles = array_filter($referentiels, function($ref) use ($promotion_active) {
        return $promotion_active && in_array($ref['id'], $promotion_active['referentiels'] ?? []);
    });

    // Appliquer le filtre par référentiel
    if (isset($_GET['referentiel']) && !empty($_GET['referentiel'])) {
        $referentiel_id = $_GET['referentiel'];
        $apprenants = array_filter($apprenants, function($apprenant) use ($referentiel_id) {
            return ($apprenant['referentiel_id'] ?? '') === $referentiel_id;
        });
    }
    
    // Appliquer le filtre par statut
    if (isset($_GET['statut']) && !empty($_GET['statut'])) {
        $statut = $_GET['statut'];
        $apprenants = array_filter($apprenants, function($apprenant) use ($statut) {
            return ($apprenant['statut'] ?? '') === $statut;
        });
    }

    // Recalculer la pagination après le filtrage
    $totalApprenants = count($apprenants);
    
    // Éviter la division par zéro
    if ($apprenantsParPage < 1) {
        $apprenantsParPage = 10;
    }
    
    $totalPages = max(1, ceil($totalApprenants / $apprenantsParPage));
    $pageCourante = min(max(1, $pageCourante), $totalPages);
    $debut = ($pageCourante - 1) * $apprenantsParPage;
    $apprenantsPage = array_slice($apprenants, $debut, $apprenantsParPage);
    
    render('admin.layout.php', 'apprenant/list.html.php', [
        'apprenants' => $apprenantsPage,
        'referentiels' => $referentiels_disponibles, // Passer uniquement les référentiels disponibles
        'totalApprenants' => $totalApprenants,
        'totalPages' => $totalPages,
        'pageCourante' => $pageCourante,
        'apprenantsParPage' => $apprenantsParPage,
        'debut' => $debut
    ]);
}

function download_apprenants_list() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Récupérer le format demandé (pdf ou excel)
    $format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
    
    // Récupérer les apprenants
    $apprenants = $model['get_all_apprenants']();
    $referentiels = $model['get_all_referentiels']();
    
    // Fonction pour obtenir le nom du référentiel
    function getReferentielName($referentiels, $id) {
        if (empty($id)) {
            return 'Non assigné';
        }
        
        foreach ($referentiels as $ref) {
            if ($ref['id'] == $id) {
                return $ref['name'];
            }
        }
        return 'Référentiel #' . $id;
    }
    
    if ($format === 'pdf') {
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Liste des Apprenants</title>
            <style>
                body { font-family: Arial, sans-serif; }
                h1 { text-align: center; color: #0e9f6e; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background-color: #f97316; color: white; font-weight: bold; text-align: left; padding: 8px; }
                td { border-bottom: 1px solid #ddd; padding: 8px; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { text-align: center; font-size: 9pt; color: #666; margin-top: 30px; }
                .print-instructions { 
                    background-color: #f0f0f0; 
                    padding: 15px; 
                    margin-bottom: 20px; 
                    border-radius: 5px;
                    text-align: center;
                }
                @media print {
                    .print-instructions { display: none; }
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-instructions">
                <p>Pour télécharger le PDF, cliquez sur le bouton ci-dessous puis utilisez la fonction "Enregistrer sous PDF" de votre navigateur.</p>
                <button onclick="window.print()">Imprimer / Enregistrer en PDF</button>
            </div>
            
            <h1>Liste des Apprenants</h1>
            
            <table>
                <thead>
                    <tr>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Référentiel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apprenants as $apprenant): ?>
                        <tr>
                            <td><?= htmlspecialchars(($apprenant['prenom'] ?? '') . ' ' . ($apprenant['nom'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($apprenant['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($apprenant['adresse'] ?? '') ?></td>
                            <td><?= htmlspecialchars($apprenant['telephone'] ?? '') ?></td>
                            <td><?= htmlspecialchars(getReferentielName($referentiels, $apprenant['referentiel_id'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="footer">
                Document généré le <?= date('d/m/Y à H:i:s') ?> - Gestion des Apprenants ODC
            </div>
            
            <script>
                // Vous pouvez également déclencher l'impression automatiquement
                // window.onload = function() { window.print(); };
            </script>
        </body>
        </html>
        <?php
        exit;
    } elseif ($format === 'excel') {
        // Générer un fichier Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="liste_apprenants.xls"');
        
        echo '<table border="1">';
        // En-têtes
        echo '<tr>
                <th>Matricule</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Référentiel</th>
                <th>Statut</th>
              </tr>';
        
        // Données
        foreach ($apprenants as $apprenant) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($apprenant['matricule'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars(($apprenant['prenom'] ?? '') . ' ' . ($apprenant['nom'] ?? '')) . '</td>';
            echo '<td>' . htmlspecialchars($apprenant['email'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($apprenant['adresse'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($apprenant['telephone'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars(getReferentielName($referentiels, $apprenant['referentiel_id'] ?? '')) . '</td>';
            echo '<td>' . htmlspecialchars($apprenant['statut'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    } else {
        // Format non supporté
        $session_services['set_flash_message']('danger', 'Format de téléchargement non supporté');
        redirect('?page=apprenants');
    }
}

function add_apprenant_form() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Charger les données
    $data = json_decode(file_get_contents(__DIR__ . '/../data/data.json'), true);
    
    // Récupérer tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Afficher le formulaire d'ajout d'apprenant
    render('admin.layout.php', 'apprenant/add.html.php', [
        'referentiels' => $referentiels,
        'data' => $data
    ]);
}

function add_apprenant_process() {
    global $model, $session_services;
    
    // Vérifier l'authentification
    $user = check_auth();
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $prenom = $_POST['prenom'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $date_naissance = $_POST['date_naissance'] ?? '';
        $lieu_naissance = $_POST['lieu_naissance'] ?? '';
        $adresse = $_POST['adresse'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $email = $_POST['email'] ?? '';
        $referentiel_id = $_POST['referentiel_id'] ?? '';
        $statut = $_POST['statut'] ?? 'Actif';
        
        // Informations du tuteur
        $tuteur = [
            'nom' => $_POST['tuteur_nom'] ?? '',
            'lien' => $_POST['tuteur_lien'] ?? '',
            'adresse' => $_POST['tuteur_adresse'] ?? '',
            'telephone' => $_POST['tuteur_telephone'] ?? ''
        ];
        
        // Traitement de la photo
        $photo = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/assets/images/uploads/apprenants/';
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $filename = 'apprenant_' . uniqid() . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $target_path = $upload_dir . $filename;
            
            // Déplacer le fichier téléchargé
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
                $photo = 'assets/images/uploads/apprenants/' . $filename;
            }
        }
        
        // Générer un matricule unique
        $matricule = 'ODC-' . date('Y') . '-' . rand(1000, 9999);
        
        // Créer l'apprenant
        $apprenant = [
            'matricule' => $matricule,
            'prenom' => $prenom,
            'nom' => $nom,
            'date_naissance' => $date_naissance,
            'lieu_naissance' => $lieu_naissance,
            'adresse' => $adresse,
            'email' => $email,
            'telephone' => $telephone,
            'referentiel_id' => $referentiel_id,
            'date_creation' => date('Y-m-d H:i:s'),
            'photo' => $photo,
            'statut' => $statut,
            'tuteur' => $tuteur,
            'promotion_id' => null // Sera assigné plus tard à une promotion
        ];
        
        // Ajouter l'apprenant à la base de données
        $result = $model['add_apprenant']($apprenant);
        
        if ($result) {
            $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès.');
            redirect('?page=apprenants');
        } else {
            $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout de l\'apprenant.');
            redirect('?page=add-apprenant');
        }
    } else {
        // Si le formulaire n'a pas été soumis, rediriger vers le formulaire
        redirect('?page=add-apprenant');
    }
}

function import_apprenants_form() {
    // Affiche le formulaire d'import
    render('admin.layout.php', 'apprenant/import.html.php');
}

function import_apprenants_process() {
    global $model, $session_services;

    // Vérifier l'authentification
    $user = check_auth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
        $file = $_FILES['import_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['csv', 'xls', 'xlsx'];
        if (!in_array($ext, $allowed)) {
            $session_services['set_flash_message']('danger', 'Format de fichier non supporté.');
            redirect('?page=import-apprenants');
        }

        // Déplacer le fichier temporairement
        $tmpPath = $file['tmp_name'];

        // Charger les référentiels et la promo active
        $referentiels = $model['get_all_referentiels']();
        $current_promotion = $model['get_current_promotion']();
        if (!$current_promotion) {
            $session_services['set_flash_message']('danger', 'Aucune promotion active.');
            redirect('?page=import-apprenants');
        }

        // Utilisation de la librairie PhpSpreadsheet (nécessite composer require phpoffice/phpspreadsheet)
        require_once __DIR__ . '/../../vendor/autoload.php';
        if ($ext === 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setEnclosure('"');
            $reader->setInputEncoding('UTF-8');
            $firstLine = file($tmpPath, FILE_IGNORE_NEW_LINES)[0] ?? '';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $reader->setDelimiter(';');
            } elseif (substr_count($firstLine, ',') > 0) {
                $reader->setDelimiter(',');
            } elseif (substr_count($firstLine, "\t") > 0) {
                $reader->setDelimiter("\t");
            } else {
                $reader->setDelimiter(';');
            }

            if (filesize($tmpPath) === 0) {
                $session_services['set_flash_message']('danger', 'Le fichier CSV est vide.');
                redirect('?page=import-apprenants');
            }

            try {
                $spreadsheet = $reader->load($tmpPath);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                $session_services['set_flash_message']('danger', 'Erreur lors de la lecture du CSV : ' . $e->getMessage());
                redirect('?page=import-apprenants');
            }
        } else {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpPath);
        }
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $added = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // skip header

            // Vérifie que la ligne a au moins 6 colonnes
            if (count($row) < 6) {
                $errors[] = "Ligne $i: nombre de colonnes insuffisant.";
                continue;
            }

            // Remplace null par '' avant trim
            $row = array_map(function($v) { return trim((string)($v ?? '')); }, $row);
            [$prenom, $nom, $email, $adresse, $telephone, $referentiel_name] = $row;

            // Validation
            if (!$prenom || !$nom || !$email || !$adresse || !$telephone || !$referentiel_name) {
                $errors[] = "Ligne $i: champ manquant.";
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne $i: email invalide.";
                continue;
            }
            if (!preg_match('/^[\d\s\+\-\(\)]+$/', $telephone)) {
                $errors[] = "Ligne $i: téléphone invalide.";
                continue;
            }
            // Trouver le référentiel par nom dans la promo active
            $ref_id = null;
            foreach ($referentiels as $ref) {
                if (
                    strtolower($ref['name']) === strtolower($referentiel_name) &&
                    in_array($ref['id'], $current_promotion['referentiels'])
                ) {
                    $ref_id = $ref['id'];
                    break;
                }
            }
            if (!$ref_id) {
                $errors[] = "Ligne $i: référentiel non trouvé dans la promotion.";
                continue;
            }

            // Générer matricule unique
            $matricule = 'ODC-' . date('Y') . '-' . rand(1000, 9999);

            $apprenant = [
                'matricule' => $matricule,
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'referentiel_id' => $ref_id,
                'date_creation' => date('Y-m-d H:i:s'),
                'photo' => '',
                'statut' => 'Actif',
                'tuteur' => [],
                'promotion_id' => $current_promotion['id']
            ];

            $model['add_apprenant']($apprenant);
            $added++;
        }

        if ($added) {
            $session_services['set_flash_message']('success', "$added apprenant(s) importé(s) avec succès.");
        }
        if ($errors) {
            $session_services['set_flash_message']('danger', implode('<br>', $errors));
        }
        redirect('?page=apprenants');
    } else {
        redirect('?page=import-apprenants');
    }
}
