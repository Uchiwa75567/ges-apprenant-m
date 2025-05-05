<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
// Ajouter ces lignes au début du fichier
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;

// Fonction pour envoyer un email de bienvenue
function envoyer_email($email, $nom_complet, $matricule, $password, $promotion_nom, $referentiel_nom, $date_debut) {
    $mail = new PHPMailer(true);
    try {
        // Paramètres serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Serveur SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'pabassdiame76@gmail.com'; // Votre adresse Gmail
        $mail->Password = 'lpst qzsg eydl nuaw  '; // Mot de passe d'application Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port = 465;
        
        // Expéditeur
        $mail->setFrom('pabassdiame76@gmail.com', 'GESTION APPRENANTS');
        
        // Destinataire
        $mail->addAddress($email, $nom_complet);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Bienvenue à Sonatel Academy !';
        
        // Logo SVG intégré directement (version simplifiée du logo Sonatel)
        $logo_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 100" width="180">
            <rect x="10" y="20" width="280" height="60" rx="10" fill="#f97316"/>
            <text x="150" y="65" font-family="Arial, sans-serif" font-size="30" font-weight="bold" fill="white" text-anchor="middle">SONATEL</text>
            <text x="150" y="85" font-family="Arial, sans-serif" font-size="16" fill="white" text-anchor="middle">ACADEMY</text>
        </svg>';
        
        // Convertir le SVG en base64 pour l'intégrer dans l'email
        $logo_base64 = 'data:image/svg+xml;base64,' . base64_encode($logo_svg);
        
        // Corps de l'email avec style amélioré
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Bienvenue chez Sonatel Academy</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
                
                body {
                    font-family: 'Poppins', Arial, sans-serif;
                    line-height: 1.6;
                    color: #333333;
                    margin: 0;
                    padding: 0;
                    background-color: #f5f5f5;
                }
                
                .container {
                    max-width: 650px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    margin-top: 20px;
                    margin-bottom: 20px;
                }
                
                .header {
                    background: linear-gradient(135deg, #ff8c00, #f97316);
                    padding: 30px 20px;
                    text-align: center;
                    position: relative;
                }
                
                .header::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    height: 30px;
                    background: linear-gradient(135deg, transparent 25px, #ffffff 0);
                }
                
                .logo {
                    max-width: 180px;
                    margin: 0 auto 15px;
                    display: block;
                }
                
                .welcome-text {
                    color: white;
                    font-size: 24px;
                    font-weight: 600;
                    margin: 0;
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
                }
                
                .content {
                    padding: 30px;
                    background-color: #ffffff;
                }
                
                h1 {
                    color: #f97316;
                    margin-top: 0;
                    font-size: 28px;
                    font-weight: 600;
                    border-bottom: 2px solid #f0f0f0;
                    padding-bottom: 10px;
                }
                
                p {
                    margin-bottom: 16px;
                    color: #555;
                }
                
                .info-box {
                    background-color: #fff8f3;
                    border-left: 4px solid #f97316;
                    padding: 20px;
                    margin: 25px 0;
                    border-radius: 4px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                }
                
                .info-box h3 {
                    color: #f97316;
                    margin-top: 0;
                    font-size: 18px;
                }
                
                .info-item {
                    display: flex;
                    margin-bottom: 10px;
                    align-items: center;
                }
                
                .info-label {
                    font-weight: 600;
                    width: 140px;
                    color: #333;
                }
                
                .info-value {
                    flex: 1;
                    padding: 8px 12px;
                    background-color: #f5f5f5;
                    border-radius: 4px;
                    font-family: 'Courier New', monospace;
                    font-weight: 500;
                }
                
                .footer {
                    background-color: #333333;
                    color: #ffffff;
                    text-align: center;
                    padding: 20px;
                    font-size: 13px;
                }
                
                .btn {
                    display: inline-block;
                    background: linear-gradient(to right, #f97316, #ff8c00);
                    color: white;
                    padding: 12px 25px;
                    text-decoration: none;
                    border-radius: 30px;
                    margin-top: 20px;
                    font-weight: 500;
                    text-align: center;
                    box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
                    transition: all 0.3s ease;
                }
                
                .btn:hover {
                    background: linear-gradient(to right, #ff8c00, #f97316);
                    box-shadow: 0 6px 15px rgba(249, 115, 22, 0.4);
                    transform: translateY(-2px);
                }
                
                .highlight {
                    font-weight: 600;
                    color: #f97316;
                }
                
                .divider {
                    height: 1px;
                    background-color: #f0f0f0;
                    margin: 25px 0;
                }
                
                .social-links {
                    margin-top: 15px;
                }
                
                .social-icon {
                    display: inline-block;
                    margin: 0 5px;
                    width: 30px;
                    height: 30px;
                    background-color: #555;
                    border-radius: 50%;
                    text-align: center;
                    line-height: 30px;
                    color: white;
                    font-size: 16px;
                    text-decoration: none;
                }
                
                .note {
                    font-size: 13px;
                    color: #777;
                    font-style: italic;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='$logo_base64' alt='Sonatel Academy Logo' class='logo'>
                    <p class='welcome-text'>Bienvenue dans votre parcours d'excellence !</p>
                </div>
                
                <div class='content'>
                    <h1>Bonjour, $nom_complet !</h1>
                    
                    <p>Nous sommes ravis de vous accueillir à la <span class='highlight'>Sonatel Academy</span>. Votre parcours de formation commence maintenant, et nous sommes impatients de vous accompagner vers la réussite.</p>
                    
                    <p>Vous avez été inscrit(e) à la promotion <span class='highlight'>$promotion_nom</span> dans le référentiel <span class='highlight'>$referentiel_nom</span>.</p>
                    
                    <div class='info-box'>
                        <h3>🔐 Vos informations de connexion</h3>
                        
                        <div class='info-item'>
                            <div class='info-label'>Matricule :</div>
                            <div class='info-value'>$matricule</div>
                        </div>
                        
                        <div class='info-item'>
                            <div class='info-label'>Email :</div>
                            <div class='info-value'>$email</div>
                        </div>
                        
                        <div class='info-item'>
                            <div class='info-label'>Mot de passe :</div>
                            <div class='info-value'>$password</div>
                        </div>
                        
                        <p class='note'>Pour des raisons de sécurité, nous vous recommandons vivement de changer votre mot de passe lors de votre première connexion.</p>
                    </div>
                    
                    <div class='divider'></div>
                    
                    <p>La formation débutera le <span class='highlight'>$date_debut</span>. Connectez-vous dès maintenant pour :</p>
                    
                    <ul>
                        <li>Découvrir votre emploi du temps</li>
                        <li>Accéder aux ressources pédagogiques</li>
                        <li>Faire connaissance avec votre promotion</li>
                        <li>Préparer votre parcours d'apprentissage</li>
                    </ul>
                    
                    <div style='text-align: center;'>
                        <a href='http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "' class='btn'>Accéder à la plateforme</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Sonatel Academy. Tous droits réservés.</p>
                    <div class='social-links'>
                        <a href='#' class='social-icon'>f</a>
                        <a href='#' class='social-icon'>in</a>
                        <a href='#' class='social-icon'>t</a>
                    </div>
                    <p>Ce message est automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        error_log("Email envoyé avec succès à $email");
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : {$mail->ErrorInfo}");
        return false;
    }
}

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
            // Récupérer les informations pour l'email
            $current_promotion = $model['get_current_promotion']();
            $referentiel = $model['get_referentiel_by_id']($referentiel_id);
            
            // Générer un mot de passe temporaire
            $temp_password = substr(md5(uniqid()), 0, 8);
            
            // Préparer les données pour l'email
            $nom_complet = $prenom . ' ' . $nom;
            $promotion_nom = $current_promotion ? $current_promotion['name'] : 'Non assignée';
            $referentiel_nom = $referentiel ? $referentiel['name'] : 'Non assigné';
            $date_debut = $current_promotion ? $current_promotion['date_debut'] : 'Non définie';
            
            // Appeler la fonction d'envoi d'email
            $email_sent = envoyer_email($email, $nom_complet, $matricule, $temp_password, $promotion_nom, $referentiel_nom, $date_debut);
            
            if ($email_sent) {
                $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès et email de bienvenue envoyé.');
            } else {
                $session_services['set_flash_message']('success', 'Apprenant ajouté avec succès mais l\'email n\'a pas pu être envoyé.');
            }
            
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
        $emails_sent = 0;
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
            $referentiel_obj = null;
            foreach ($referentiels as $ref) {
                if (
                    strtolower($ref['name']) === strtolower($referentiel_name) &&
                    in_array($ref['id'], $current_promotion['referentiels'])
                ) {
                    $ref_id = $ref['id'];
                    $referentiel_obj = $ref;
                    break;
                }
            }
            if (!$ref_id) {
                $errors[] = "Ligne $i: référentiel non trouvé dans la promotion.";
                continue;
            }

            // Générer matricule unique
            $matricule = 'ODC-' . date('Y') . '-' . rand(1000, 9999);
            
            // Générer un mot de passe temporaire
            $temp_password = substr(md5(uniqid()), 0, 8);

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
                'promotion_id' => $current_promotion['id'],
                'password' => $temp_password // Ajouter le mot de passe temporaire
            ];

            // Ajouter l'apprenant
            if ($model['add_apprenant']($apprenant)) {
                $added++;
                
                // Préparer les données pour l'email
                $nom_complet = $prenom . ' ' . $nom;
                $promotion_nom = $current_promotion['name'] ?? 'Non assignée';
                $referentiel_nom = $referentiel_obj ? $referentiel_obj['name'] : 'Non assigné';
                $date_debut = $current_promotion['date_debut'] ?? 'Non définie';
                
                // Envoyer l'email de bienvenue
                if (envoyer_email($email, $nom_complet, $matricule, $temp_password, $promotion_nom, $referentiel_nom, $date_debut)) {
                    $emails_sent++;
                } else {
                    $errors[] = "Ligne $i: apprenant ajouté mais l'email n'a pas pu être envoyé à $email.";
                }
            } else {
                $errors[] = "Ligne $i: erreur lors de l'ajout de l'apprenant.";
            }
        }

        // Messages de succès et d'erreur
        if ($added) {
            $message = "$added apprenant(s) importé(s) avec succès.";
            if ($emails_sent) {
                $message .= " $emails_sent email(s) de bienvenue envoyé(s).";
            }
            $session_services['set_flash_message']('success', $message);
        }
        if ($errors) {
            $session_services['set_flash_message']('danger', implode('<br>', $errors));
        }
        redirect('?page=apprenants');
    } else {
        redirect('?page=import-apprenants');
    }
}
