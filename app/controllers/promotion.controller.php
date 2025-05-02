<?php

namespace App\Controllers;

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/validator.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/file.service.php';
require_once __DIR__ . '/../translate/fr/error.fr.php';
require_once __DIR__ . '/../translate/fr/message.fr.php';
require_once __DIR__ . '/../enums/profile.enum.php';
require_once __DIR__ . '/../enums/status.enum.php'; // Ajout de cette ligne
require_once __DIR__ . '/../enums/messages.enum.php';

use App\Models;
use App\Services;
use App\Translate\fr;
use App\Enums;
use App\Enums\Status; // Ajout de cette ligne
use App\Enums\Messages;

// Affichage de la liste des promotions
function list_promotions() {
    global $model, $session_services;
    
    // Vérification si l'utilisateur est connecté
    $user = check_auth();
    
    // S'assurer qu'une promotion est active
    $model['ensure_active_promotion']();
    
    // Récupérer les statistiques
    $stats = $model['get_statistics']();
    
    // Récupérer le terme de recherche depuis GET
    $search = $_GET['search'] ?? '';
    
    // Récupérer le filtre de statut depuis GET
    $status_filter = $_GET['status'] ?? 'all';
    
    // Récupérer la page courante et le nombre d'éléments par page
    $current_page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    $items_per_page = 8; // 8 éléments par page
    
    // Récupérer toutes les promotions
    $promotions = $model['get_all_promotions']();
    
    // Filtrer les promotions selon le statut sélectionné
    if ($status_filter === 'active') {
        // Si on filtre sur "actif", n'afficher que les promotions actives
        $promotions = array_filter($promotions, function($promotion) {
            return $promotion['status'] === 'active';
        });
    } elseif ($status_filter === 'inactive') {
        // Si on filtre sur "inactif", n'afficher que les promotions inactives
        $promotions = array_filter($promotions, function($promotion) {
            return $promotion['status'] === 'inactive';
        });
    }
    
    // Filtrer les promotions selon le terme de recherche
    if (!empty($search)) {
        $promotions = array_filter($promotions, function($promotion) use ($search) {
            return stripos($promotion['name'], $search) !== false;
        });
    }
    
    // Extraire la promotion active (si elle est incluse dans les résultats filtrés)
    $active_promotion = null;
    $other_promotions = [];
    
    foreach ($promotions as $key => $promotion) {
        if ($promotion['status'] === 'active' && ($status_filter === 'all' || $status_filter === 'active')) {
            $active_promotion = $promotion;
            // Ne pas inclure la promotion active dans les autres promotions
            // car nous la traiterons séparément
            unset($promotions[$key]);
        } else {
            $other_promotions[] = $promotion;
        }
    }
    
    // Calculer la pagination pour les promotions autres que l'active
    $total_other = count($other_promotions);
    $items_per_page_adjusted = $active_promotion ? $items_per_page - 1 : $items_per_page;
    $items_per_page_adjusted = max(1, $items_per_page_adjusted); // Éviter division par zéro
    
    $total_pages = ceil($total_other / $items_per_page_adjusted);
    
    // S'assurer que la page courante est valide
    $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
    
    // Calculer l'offset pour la pagination
    $offset = ($current_page - 1) * $items_per_page_adjusted;
    
    // Récupérer les promotions pour la page courante (sauf l'active)
    $paginated_other = array_slice($other_promotions, $offset, $items_per_page_adjusted);
    
    // Combiner la promotion active (si elle existe) avec les autres promotions paginées
    $paginated_promotions = [];
    if ($active_promotion) {
        $paginated_promotions[] = $active_promotion;
    }
    $paginated_promotions = array_merge($paginated_promotions, $paginated_other);
    
    // Calculer le nombre total d'éléments pour l'affichage de la pagination
    $total_items = $total_other + ($active_promotion ? 1 : 0);
    
    // Rendu de la vue avec les statistiques
    render('admin.layout.php', 'promotion/list.html.php', [
        'user' => $user,
        'promotions' => $paginated_promotions,
        'search' => $search,
        'status_filter' => $status_filter,
        'active_menu' => 'promotions',
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'total_items' => $total_items,
        'stats' => $stats
    ]);
}
// Affichage du formulaire d'ajout d'une promotion
function add_promotion_form() {
    global $model;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Affichage de la vue
    render('admin.layout.php', 'promotion/add.html.php', [
        'user' => $user,
        'active_menu' => 'promotions'
    ]);
}

// Traitement de l'ajout d'une promotion
function add_promotion_process() {
    global $model, $validator_services, $session_services, $error_messages, $success_messages;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['name'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $image = $_FILES['image'] ?? null;
    
    // S'assurer que referentiels est un tableau valide
    $referentiels = [];
    if (isset($_POST['referentiels']) && !empty($_POST['referentiels'])) {
        $decoded = json_decode($_POST['referentiels'], true);
        if (is_array($decoded)) {
            $referentiels = $decoded;
        }
    }
    
    // Validation des données
    $errors = [];
    
    // Validation du nom
    if ($validator_services['is_empty']($name)) {
        $errors['name'] = 'Le nom de la promotion est obligatoire';
    } elseif ($model['promotion_name_exists']($name)) {
        $errors['name'] = 'Ce nom de promotion existe déjà';
    }
    
    // Validation de la date de début - OBLIGATOIRE
    if ($validator_services['is_empty']($date_debut)) {
        $errors['date_debut'] = 'La date de début est obligatoire';
    } elseif (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_debut, $matches)) {
        $errors['date_debut'] = 'Format de date invalide. Utilisez le format jj/mm/aaaa';
    } else {
        // Vérifier si la date est valide
        $jour = (int)$matches[1];
        $mois = (int)$matches[2];
        $annee = (int)$matches[3];
        
        if (!checkdate($mois, $jour, $annee)) {
            $errors['date_debut'] = 'Date invalide. Veuillez entrer une date existante.';
        }
    }
    
    // Validation de la date de fin - OBLIGATOIRE
    if ($validator_services['is_empty']($date_fin)) {
        $errors['date_fin'] = 'La date de fin est obligatoire';
    } elseif (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_fin, $matches)) {
        $errors['date_fin'] = 'Format de date invalide. Utilisez le format jj/mm/aaaa';
    } else {
        // Vérifier si la date est valide
        $jour = (int)$matches[1];
        $mois = (int)$matches[2];
        $annee = (int)$matches[3];
        
        if (!checkdate($mois, $jour, $annee)) {
            $errors['date_fin'] = 'Date invalide. Veuillez entrer une date existante.';
        }
    }
    
    // Vérification que la date de fin est postérieure à la date de début
    if (!isset($errors['date_debut']) && !isset($errors['date_fin'])) {
        // Convertir les dates au format timestamp pour comparaison
        $date_debut_ts = convertirDateEnTimestamp($date_debut);
        $date_fin_ts = convertirDateEnTimestamp($date_fin);
        
        if ($date_fin_ts <= $date_debut_ts) {
            $errors['date_fin'] = 'La date de fin doit être postérieure à la date de début';
        }
    }
    
    // Validation de l'image
    if (empty($image) || empty($image['tmp_name'])) {
        $errors['image'] = 'L\'image est obligatoire';
    } elseif (!$validator_services['is_valid_image']($image)) {
        $errors['image'] = 'Le fichier doit être une image valide (JPG ou PNG) de moins de 2MB';
    }
    
    // S'il y a des erreurs, affichage du formulaire avec les erreurs
    if (!empty($errors)) {
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'errors' => $errors,
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels' => $referentiels
        ]);
        return;
    }
    
    // Convertir les dates au format YYYY-MM-DD pour la base de données
    $date_debut_db = convertirDateFormatBD($date_debut);
    $date_fin_db = convertirDateFormatBD($date_fin);
    
    // Téléchargement de l'image
    $image_path = upload_image($image, 'promotions');
    
    if ($image_path === false) {
        $session_services['set_flash_message']('danger', 'Erreur lors du téléchargement de l\'image');
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels' => $referentiels
        ]);
        return;
    }
    
    // Création de la promotion
    $promotion_data = [
        'name' => $name,
        'date_debut' => $date_debut_db,
        'date_fin' => $date_fin_db,
        'image' => $image_path
    ];
    
    // Ajouter les référentiels seulement s'ils existent
    if (!empty($referentiels)) {
        $promotion_data['referentiels'] = $referentiels;
    }
    
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création de la promotion');
        render('admin.layout.php', 'promotion/add.html.php', [
            'user' => $user,
            'name' => $name,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'referentiels' => $referentiels
        ]);
        return;
    }
    
    // Redirection vers la liste des promotions avec un message de succès
    $session_services['set_flash_message']('success', 'Promotion créée avec succès');
    redirect('?page=promotions');
}

// Fonction pour convertir une date au format jj/mm/aaaa en timestamp
function convertirDateEnTimestamp($date) {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        $jour = (int)$matches[1];
        $mois = (int)$matches[2];
        $annee = (int)$matches[3];
        
        return mktime(0, 0, 0, $mois, $jour, $annee);
    }
    
    return false;
}

// Fonction pour convertir une date au format jj/mm/aaaa en format YYYY-MM-DD pour la BD
function convertirDateFormatBD($date) {
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        $jour = $matches[1];
        $mois = $matches[2];
        $annee = $matches[3];
        
        return "$annee-$mois-$jour";
    }
    
    return $date;
}

// Modification du statut d'une promotion (activation/désactivation)
function toggle_promotion_status() {
    global $model, $session_services;
    
    // Vérification de l'authentification
    check_auth();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?page=promotions');
        return;
    }
    
    $promotion_id = filter_input(INPUT_POST, 'promotion_id', FILTER_VALIDATE_INT);
    if (!$promotion_id) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    $result = $model['toggle_promotion_status']($promotion_id);
    
    if ($result) {
        $message = $result['status'] === Status::ACTIVE->value ? 
                  Messages::PROMOTION_ACTIVATED->value : 
                  Messages::PROMOTION_INACTIVE->value;
        $session_services['set_flash_message']('success', $message);
    } else {
        $session_services['set_flash_message']('error', Messages::PROMOTION_ERROR->value);
    }
    
    redirect('?page=promotions');
}

// Ajout d'une promotion
function add_promotion() {
    global $model, $session_services, $validator_services, $file_services;
    
    // Vérification de l'authentification
    $user = check_auth();
    
    // Vérification de la méthode POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $session_services['set_flash_message']('error', Messages::INVALID_REQUEST->value);
        redirect('?page=promotions');
        return;
    }
    
    // Validation des données
    $validation = $validator_services['validate_promotion']($_POST, $_FILES);
    
    if (!$validation['valid']) {
        $session_services['set_flash_message']('error', $validation['errors'][0]);
        redirect('?page=promotions');
        return;
    }
    
    // Traitement de l'image avec le service
    $image_path = $file_services['handle_promotion_image']($_FILES['image']);
    if (!$image_path) {
        $session_services['set_flash_message']('error', Messages::IMAGE_UPLOAD_ERROR->value);
        redirect('?page=promotions');
        return;
    }
    
    // Préparation des données
    $promotion_data = [
        'name' => htmlspecialchars($_POST['name']),
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'image' => $image_path,
        'status' => 'inactive',
        'apprenants' => []
    ];
    
    // Création de la promotion
    $result = $model['create_promotion']($promotion_data);
    
    if (!$result) {
        $session_services['set_flash_message']('error', Messages::PROMOTION_CREATE_ERROR->value);
        redirect('?page=promotions');
        return;
    }

    $session_services['set_flash_message']('success', Messages::PROMOTION_CREATED->value);
    redirect('?page=promotions');
}

// Recherche des référentiels
function search_referentiels() {
    global $model;
    
    // Vérification si l'utilisateur est connecté
    check_auth();
    
    $query = $_GET['q'] ?? '';
    $referentiels = $model['search_referentiels']($query);
    
    // Retourner les résultats en JSON
    header('Content-Type: application/json');
    echo json_encode(array_values($referentiels));
    exit;
}

// Cette fonction peut être ajoutée à votre contrôleur de promotion
function identifier_promotion_en_cours($promotions) {
    // Obtenir l'année actuelle du système
    $annee_courante = date('Y');
    
    // Identifier les promotions en cours
    $promotions_en_cours = [];
    foreach ($promotions as $promotion) {
        if (isset($promotion['annee']) && $promotion['annee'] == $annee_courante) {
            $promotion['est_en_cours'] = true;
            $promotions_en_cours[] = $promotion;
        } else {
            $promotion['est_en_cours'] = false;
        }
    }
    
    return [
        'promotions_mises_a_jour' => $promotions,
        'promotions_en_cours' => $promotions_en_cours
    ];
}
