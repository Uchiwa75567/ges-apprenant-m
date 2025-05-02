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
use Exception; // Ajoutez cette ligne

// Affichage de la liste des référentiels de la promotion en cours
function list_referentiels() {
    global $model, $session_services;
    
    try {
        // Vérifier l'authentification
        $user = $session_services['get_current_user']();
        if (!$user || !in_array($user['profile'], ['Admin', 'Attache'])) {
            redirect('?page=forbidden');
            return;
        }
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Si aucune promotion n'est active
        if (!$current_promotion) {
            $session_services['set_flash_message']('info', 'Aucune promotion active');
            redirect('?page=promotions');
            return;
        }
        
        // Récupérer uniquement les référentiels de la promotion courante
        $referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
        
        // Compter les apprenants par référentiel dans la promotion courante
        foreach ($referentiels as &$referentiel) {
            $referentiel['apprenants'] = [];
            
            // Parcourir les apprenants de la promotion
            if (isset($current_promotion['apprenants']) && is_array($current_promotion['apprenants'])) {
                foreach ($current_promotion['apprenants'] as $apprenant) {
                    // Vérifier si l'apprenant est associé à ce référentiel
                    if (isset($apprenant['referentiel_id']) && 
                        (string)$apprenant['referentiel_id'] === (string)$referentiel['id']) {
                        $referentiel['apprenants'][] = $apprenant;
                    }
                }
            }
        }
        
        render('admin.layout.php', 'referentiel/list.html.php', [
            'user' => $user,
            'referentiels' => $referentiels,
            'current_promotion' => $current_promotion
        ]);
        
    } catch (Exception $e) {
        $session_services['set_flash_message']('danger', 'Une erreur est survenue');
        redirect('?page=dashboard');
    }
}

// Affichage de la liste de tous les référentiels
function list_all_referentiels() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération de tous les référentiels
    $referentiels = $model['get_all_referentiels']();
    
    // Filtrage des référentiels selon le critère de recherche
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $referentiels = array_filter($referentiels, function ($referentiel) use ($search) {
            return stripos($referentiel['name'], $search) !== false || 
                   stripos($referentiel['description'], $search) !== false;
        });
    }
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/list-all.html.php', [
        'user' => $user,
        'referentiels' => $referentiels,
        'search' => $search,
        'active_menu' => 'referentiels'
    ]);
}

// Affichage du formulaire d'ajout d'un référentiel
function add_referentiel_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/add.html.php', [
        'user' => $user,
        'active_menu' => 'referentiels'
    ]);
}

// Traitement de l'ajout d'un référentiel
function add_referentiel_process() {
    global $model, $validator_services, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $name = $_POST['referentiel_name'] ?? '';
    $description = $_POST['referentiel_details'] ?? '';
    $capacity = $_POST['capacity'] ?? 30;
    $sessions = $_POST['sessions'] ?? 1;
    
    // Validation des données essentielles
    $errors = [];
    
    if ($validator_services['is_empty']($name)) {
        $errors['name'] = 'Le nom du référentiel est obligatoire';
    } elseif ($model['referentiel_name_exists']($name)) {
        $errors['name'] = 'Un référentiel avec ce nom existe déjà';
    }
    
    if ($validator_services['is_empty']($description)) {
        $errors['description'] = 'La description est obligatoire';
    }
    
    // S'il y a des erreurs, affichage du formulaire avec les erreurs
    if (!empty($errors)) {
        render('admin.layout.php', 'referentiel/add.html.php', [
            'user' => $user,
            'errors' => $errors,
            'name' => $name,
            'description' => $description,
            'capacity' => $capacity,
            'sessions' => $sessions,
            'active_menu' => 'referentiels'
        ]);
        return;
    }
    
    // Traitement de l'image si elle est fournie
    $image_path = "assets/images/default-referentiel.jpg";  // Image par défaut
    if (isset($_FILES['referentiel_image']) && $_FILES['referentiel_image']['error'] == 0) {
        $upload_dir = 'assets/images/uploads/referentiels/';
        $filename = uniqid() . '_' . basename($_FILES['referentiel_image']['name']);
        $upload_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['referentiel_image']['tmp_name'], $upload_file)) {
            $image_path = $upload_file;
        }
    }
    
    // Définir les données du référentiel
    $referentiel_data = [
        'name' => $name,
        'description' => $description,
        'capacite' => $capacity,
        'sessions' => $sessions,
        'image' => $image_path
    ];
    
    // Création du référentiel
    $result = $model['create_referentiel']($referentiel_data);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', 'Erreur lors de la création du référentiel');
        render('admin.layout.php', 'referentiel/add.html.php', [
            'user' => $user,
            'name' => $name,
            'description' => $description,
            'capacity' => $capacity,
            'sessions' => $sessions,
            'active_menu' => 'referentiels'
        ]);
        return;
    }
    
    // Redirection avec message de succès
    $session_services['set_flash_message']('success', 'Référentiel créé avec succès');
    redirect('?page=all-referentiels');
}

// Affichage du formulaire d'affectation de référentiels à une promotion
function assign_referentiels_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération de tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();
    
    // Récupération des référentiels déjà affectés à la promotion
    $assigned_referentiels = $model['get_referentiels_by_promotion']($current_promotion['id']);
    $assigned_ids = array_map(function($ref) {
        return $ref['id'];
    }, $assigned_referentiels);
    
    // Filtrer les référentiels non affectés
    $unassigned_referentiels = array_filter($all_referentiels, function($ref) use ($assigned_ids) {
        return !in_array($ref['id'], $assigned_ids);
    });
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/assign.html.php', [
        'user' => $user,
        'current_promotion' => $current_promotion,
        'unassigned_referentiels' => array_values($unassigned_referentiels)
    ]);
}

// Traitement de l'affectation de référentiels à une promotion
function assign_referentiels_process() {
    global $model, $session_services, $error_messages, $success_messages;
    
    // Vérification des droits d'accès (Admin uniquement)
    check_profile(Enums\ADMIN);
    
    // Récupération de la promotion courante
    $current_promotion = $model['get_current_promotion']();
    
    if (!$current_promotion) {
        $session_services['set_flash_message']('info', 'Aucune promotion active. Veuillez d\'abord activer une promotion.');
        redirect('?page=promotions');
        return;
    }
    
    // Récupération des référentiels sélectionnés
    $selected_referentiels = $_POST['referentiels'] ?? [];
    
    if (empty($selected_referentiels)) {
        $session_services['set_flash_message']('info', 'Aucun référentiel sélectionné.');
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Affectation des référentiels à la promotion
    $result = $model['assign_referentiels_to_promotion']($current_promotion['id'], $selected_referentiels);
    
    if (!$result) {
        $session_services['set_flash_message']('danger', $error_messages['referentiel']['update_failed']);
        redirect('?page=assign-referentiels');
        return;
    }
    
    // Redirection vers la liste des référentiels de la promotion avec un message de succès
    $session_services['set_flash_message']('success', $success_messages['referentiel']['assigned']);
    redirect('?page=referentiels');
}

function assign_referentiels_to_promotion() {
    global $model, $session_services;
    
    $promotion_id = $_POST['promotion_id'] ?? null;
    $referentiel_ids = $_POST['referentiel_ids'] ?? [];
    
    if (!$promotion_id || !is_array($referentiel_ids)) {
        $session_services['set_flash_message']('error', 'Données invalides');
        redirect('?page=referentiels');
        return;
    }
    
    $result = $model['assign_referentiels_to_promotion']($promotion_id, $referentiel_ids);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiels assignés avec succès');
    } else {
        $session_services['set_flash_message']('error', 'Erreur lors de l\'assignation');
    }
    
    redirect('?page=referentiels');
}

// Affichage du formulaire d'ajout d'un référentiel à la promotion active
function add_referentiel_to_promotion_form() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupérer la promotion active
    $active_promotion = $model['get_current_promotion']();
    
    if (!$active_promotion) {
        $session_services['set_flash_message']('danger', 'Aucune promotion active n\'est disponible');
        redirect('?page=promotions');
        return;
    }
    
    // Récupérer tous les référentiels
    $all_referentiels = $model['get_all_referentiels']();
    
    // Récupérer les IDs des référentiels déjà assignés à la promotion
    $assigned_referentiel_ids = isset($active_promotion['referentiels']) ? $active_promotion['referentiels'] : [];
    
    // Séparer les référentiels en deux groupes : assignés et non assignés
    $assigned_referentiels = [];
    $unassigned_referentiels = [];
    
    foreach ($all_referentiels as $ref) {
        if (in_array($ref['id'], $assigned_referentiel_ids)) {
            $assigned_referentiels[] = $ref;
        } else {
            $unassigned_referentiels[] = $ref;
        }
    }
    
    // Affichage de la vue
    render('admin.layout.php', 'referentiel/add_to_promotion.html.php', [
        'user' => $user,
        'active_promotion' => $active_promotion,
        'assigned_referentiels' => $assigned_referentiels,
        'unassigned_referentiels' => $unassigned_referentiels,
        'active_menu' => 'referentiels'
    ]);
}

// Traitement de l'ajout d'un référentiel à la promotion active
function add_referentiel_to_promotion_process() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $promotion_id = $_POST['promotion_id'] ?? null;
    $referentiel_id = $_POST['referentiel_id'] ?? null;
    
    if (!$promotion_id || !$referentiel_id) {
        $session_services['set_flash_message']('danger', 'Données invalides');
        redirect('?page=referentiels');
        return;
    }
    
    // Vérifier que la promotion existe
    $promotion = $model['get_promotion_by_id']($promotion_id);
    if (!$promotion) {
        $session_services['set_flash_message']('danger', 'Promotion non trouvée');
        redirect('?page=referentiels');
        return;
    }
    
    // Vérifier que le référentiel existe
    $referentiel = $model['get_referentiel_by_id']($referentiel_id);
    if (!$referentiel) {
        $session_services['set_flash_message']('danger', 'Référentiel non trouvé');
        redirect('?page=referentiels');
        return;
    }
    
    // Ajouter le référentiel à la promotion
    $result = $model['assign_referentiels_to_promotion']($promotion_id, [$referentiel_id]);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiel ajouté à la promotion avec succès');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de l\'ajout du référentiel à la promotion');
    }
    
    redirect('?page=referentiels');
}

// Traitement de la mise à jour des référentiels d'une promotion
function update_promotion_referentiels() {
    global $model, $session_services;
    
    // Vérification des droits d'accès (Admin uniquement)
    $user = check_profile(Enums\ADMIN);
    
    // Récupération des données du formulaire
    $promotion_id = $_POST['promotion_id'] ?? null;
    $assigned_referentiels = $_POST['assigned_referentiels'] ?? [];
    
    // Débogage temporaire
    error_log("Promotion ID reçu: " . print_r($promotion_id, true));
    error_log("Référentiels assignés: " . print_r($assigned_referentiels, true));
    
    if (!$promotion_id) {
        $session_services['set_flash_message']('danger', 'Promotion non spécifiée');
        redirect('?page=referentiels');
        return;
    }
    
    // Vérifier que la promotion existe
    $promotion = $model['get_promotion_by_id']($promotion_id);
    
    // Débogage temporaire
    error_log("Promotion trouvée: " . ($promotion ? 'Oui' : 'Non'));
    if ($promotion) {
        error_log("Détails de la promotion: " . print_r($promotion, true));
    }
    
    if (!$promotion) {
        $session_services['set_flash_message']('danger', 'Promotion non trouvée');
        redirect('?page=referentiels');
        return;
    }
    
    // Mettre à jour les référentiels de la promotion
    $result = $model['update_promotion_referentiels']($promotion_id, $assigned_referentiels);
    
    if ($result) {
        $session_services['set_flash_message']('success', 'Référentiels de la promotion mis à jour avec succès');
    } else {
        $session_services['set_flash_message']('danger', 'Erreur lors de la mise à jour des référentiels');
    }
    
    redirect('?page=referentiels');
}