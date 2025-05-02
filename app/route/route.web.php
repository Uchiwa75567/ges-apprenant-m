<?php

namespace App\Route;

require_once __DIR__ . '/../controllers/auth.controller.php';
require_once __DIR__ . '/../controllers/promotion.controller.php';
require_once __DIR__ . '/../controllers/referentiel.controller.php';
require_once __DIR__ . '/../controllers/dashboard.controller.php';
require_once __DIR__ . '/../controllers/apprenant.controller.php';

use App\Controllers;

// Définition de toutes les routes disponibles avec leur fonction controller associée
$routes = [
    // Routes pour l'authentification
    'login' => 'App\Controllers\login_page',
    'login-process' => 'App\Controllers\login_process',
    'logout' => 'App\Controllers\logout',
    'change-password' => 'App\Controllers\change_password_page',
    'change-password-process' => 'App\Controllers\change_password_process',
    'forgot-password' => 'App\Controllers\forgot_password_page',
    'forgot-password-process' => 'App\Controllers\forgot_password_process',
    'reset-password' => 'App\Controllers\reset_password_page',
    'reset-password-process' => 'App\Controllers\reset_password_process',
    
    // Routes pour les promotions
    'promotions' => 'App\Controllers\list_promotions',
    'add-promotion' => 'App\Controllers\add_promotion_form',
    'add-promotion-process' => 'App\Controllers\add_promotion_process',
    
    // Routes pour les référentiels
    'referentiels' => 'App\Controllers\list_referentiels',
    'all-referentiels' => 'App\Controllers\list_all_referentiels',
    'add-referentiel' => 'App\Controllers\add_referentiel_form',
    'add-referentiel-process' => 'App\Controllers\add_referentiel_process',
    'add-referentiel-to-promotion' => 'App\Controllers\add_referentiel_to_promotion_form',
    'add-referentiel-to-promotion-process' => 'App\Controllers\add_referentiel_to_promotion_process',
    'assign-referentiels' => 'App\Controllers\assign_referentiels_form',
    'assign-referentiels-process' => 'App\Controllers\assign_referentiels_process',
    'update-promotion-referentiels' => 'App\Controllers\update_promotion_referentiels',
    
    // Route par défaut pour le tableau de bord
    'dashboard' => 'App\Controllers\dashboard',
    
    // Route pour les erreurs
    'forbidden' => 'App\Controllers\forbidden',
    
    // Route par défaut (page non trouvée)
    '404' => 'App\Controllers\not_found',
    // Routes pour les apprenants
    'apprenants' => 'App\Controllers\list_apprenants',
    'add-apprenant' => 'App\Controllers\add_apprenant_form',
    'add-apprenant-process' => 'App\Controllers\add_apprenant_process',
    'download-list' => 'App\Controllers\download_apprenants_list',
    'import-apprenants' => 'App\Controllers\import_apprenants_form',
    'import-apprenants-process' => 'App\Controllers\import_apprenants_process'
];

/**
 * Fonction de routage qui exécute le contrôleur correspondant à la page demandée
 *
 * @param string $page La page demandée
 * @return mixed Le résultat de la fonction contrôleur
 */
function route($page) {
    global $routes, $model, $session_services, $file_services;
    
    // Vérifie si la route demandée existe
    $route_exists = array_key_exists($page, $routes);

    if ($page === 'all-referentiels') {
        \App\Controllers\list_all_referentiels();
        return;
    }
    
    // Obtient la fonction à exécuter (route demandée ou 404 si non trouvée)
    $controller_function = $route_exists ? $routes[$page] : $routes['404'];
    
    // Appelle la fonction contrôleur
    return call_user_func($controller_function);
}
