<?php

namespace App\Models;

require_once __DIR__ . '/../enums/path.enum.php';
require_once __DIR__ . '/../enums/status.enum.php';
require_once __DIR__ . '/../enums/profile.enum.php';

use App\Enums;
use App\Enums\Status; // Ajout de cette ligne

// Collection de toutes les fonctions modèles pour l'application
$model = [
    // Fonctions de base pour manipuler les données
    'read_data' => function () {
        if (!file_exists(Enums\DATA_PATH)) {
            // Si le fichier n'existe pas, on renvoie une structure par défaut
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        $json_data = file_get_contents(Enums\DATA_PATH);
        $data = json_decode($json_data, true);
        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // En cas d'erreur de décodage JSON
            return [
                'users' => [],
                'promotions' => [],
                'referentiels' => [],
                'apprenants' => []
            ];
        }
        
        return $data;
    },
    
    'write_data' => function ($data) {
        // Vérifier si le dossier data existe, sinon le créer
        $data_dir = dirname(Enums\DATA_PATH);
        if (!is_dir($data_dir)) {
            mkdir($data_dir, 0777, true);
        }
        
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents(Enums\DATA_PATH, $json_data) !== false;
    },
    
    'generate_id' => function () {
        return uniqid();
    },
    
    // Fonctions d'authentification
    'authenticate' => function ($email, $password) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email, $password) {
            return $user['email'] === $email && $user['password'] === $password;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_email' => function ($email) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($email) {
            return $user['email'] === $email;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'get_user_by_id' => function ($user_id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_users = array_filter($data['users'], function ($user) use ($user_id) {
            return $user['id'] === $user_id;
        });
        
        // Si aucun utilisateur ne correspond
        if (empty($filtered_users)) {
            return null;
        }
        
        // Récupérer le premier utilisateur qui correspond
        return reset($filtered_users);
    },
    
    'change_password' => function ($user_id, $new_password) use (&$model) {
        $data = $model['read_data']();
        
        $user_indices = array_keys(array_filter($data['users'], function($user) use ($user_id) {
            return $user['id'] === $user_id;
        }));
        
        if (empty($user_indices)) {
            return false;
        }
        
        $user_index = reset($user_indices);
        
        // Mettre à jour le mot de passe (sans cryptage)
        $data['users'][$user_index]['password'] = $new_password;
        
        // Sauvegarder les modifications
        return $model['write_data']($data);
    },
    
    // Fonctions pour les promotions
    'get_all_promotions' => function () use (&$model) {
        $data = $model['read_data']();
        return $data['promotions'] ?? [];
    },
    
    'get_promotion_by_id' => function($id) use (&$model) {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if ((int)$promotion['id'] === (int)$id) {
                return $promotion;
            }
        }
        
        return null;
    },
    
    'promotion_name_exists' => function(string $name) use (&$model): bool {
        $data = $model['read_data']();
        
        foreach ($data['promotions'] as $promotion) {
            if (strtolower($promotion['name']) === strtolower($name)) {
                return true;
            }
        }
        
        return false;
    },
    
    'create_promotion' => function(array $promotion_data) use (&$model) {
        $data = $model['read_data']();
        
        // Générer un nouvel ID
        $max_id = 0;
        foreach ($data['promotions'] as $promotion) {
            $max_id = max($max_id, (int)$promotion['id']);
        }
        
        $promotion_data['id'] = $max_id + 1;
        
        // S'assurer que le statut est défini
        if (!isset($promotion_data['status'])) {
            // Si c'est la première promotion, la définir comme active
            if (empty($data['promotions'])) {
                $promotion_data['status'] = 'active';
            } else {
                $promotion_data['status'] = 'inactive'; // Statut inactif par défaut
            }
        }
        
        // S'assurer que apprenants est défini
        if (!isset($promotion_data['apprenants'])) {
            $promotion_data['apprenants'] = [];
        }
        
        // Ajouter la promotion
        $data['promotions'][] = $promotion_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    
    'update_promotion' => function ($id, $promotion_data) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($id) {
            return $promotion['id'] === $id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Mettre à jour les données de la promotion
        $data['promotions'][$promotion_index] = array_merge(
            $data['promotions'][$promotion_index],
            $promotion_data
        );
        
        if ($model['write_data']($data)) {
            return $data['promotions'][$promotion_index];
        }
        
        return null;
    },
    
    // Fonction pour s'assurer qu'une promotion est active
    'ensure_active_promotion' => function() use (&$model) {
        $data = $model['read_data']();
        
        // Vérifier s'il y a au moins une promotion active
        $active_promotions = array_filter($data['promotions'] ?? [], function($promotion) {
            return $promotion['status'] === 'active';
        });
        
        // Si aucune promotion n'est active et qu'il y a des promotions
        if (empty($active_promotions) && !empty($data['promotions'])) {
            // Activer la promotion la plus récente (par date de début)
            usort($data['promotions'], function($a, $b) {
                return strtotime($b['date_debut']) - strtotime($a['date_debut']);
            });
            
            // Activer la première promotion (la plus récente)
            $data['promotions'][0]['status'] = 'active';
            
            // Sauvegarder les modifications
            $model['write_data']($data);
        }
        
        return true;
    },
    
    // Modification de la fonction toggle_promotion_status
    'toggle_promotion_status' => function(int $promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion à modifier
        $target_promotion = null;
        $target_index = null;
        
        foreach ($data['promotions'] as $index => $promotion) {
            if ((int)$promotion['id'] === $promotion_id) {
                $target_promotion = $promotion;
                $target_index = $index;
                break;
            }
        }
        
        if ($target_index === null) {
            return false;
        }
        
        // Si la promotion est déjà active, ne rien faire
        if ($target_promotion['status'] === Status::ACTIVE->value) {
            return $target_promotion;
        }
        
        // Si la promotion est inactive, l'activer et désactiver toutes les autres
        if ($target_promotion['status'] === Status::INACTIVE->value) {
            // Désactiver toutes les promotions
            $data['promotions'] = array_map(function($p) {
                $p['status'] = Status::INACTIVE->value;
                return $p;
            }, $data['promotions']);
            
            // Activer la promotion ciblée
            $data['promotions'][$target_index]['status'] = Status::ACTIVE->value;
        }
        
        // Sauvegarder les modifications
        if ($model['write_data']($data)) {
            return $data['promotions'][$target_index];
        }
        
        return null;
    },
    
    'search_promotions' => function($search_term) use (&$model) {
        $promotions = $model['get_all_promotions']();
        
        if (empty($search_term)) {
            return $promotions;
        }
        
        return array_values(array_filter($promotions, function($promotion) use ($search_term) {
            return stripos($promotion['name'], $search_term) !== false;
        }));
    },
    
    // Fonctions pour les référentiels
    'get_all_referentiels' => function() use (&$model) {
        $data = $model['read_data']();
        return isset($data['referentiels']) ? $data['referentiels'] : [];
    },
    
    'get_referentiel_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($id) {
            return $referentiel['id'] === $id;
        });
        
        return !empty($filtered_referentiels) ? reset($filtered_referentiels) : null;
    },
    
    'referentiel_name_exists' => function ($name, $exclude_id = null) use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $filtered_referentiels = array_filter($data['referentiels'] ?? [], function ($referentiel) use ($name, $exclude_id) {
            return strtolower($referentiel['name']) === strtolower($name) && ($exclude_id === null || $referentiel['id'] !== $exclude_id);
        });
        
        return !empty($filtered_referentiels);
    },
    
    'create_referentiel' => function($referentiel_data) use (&$model) {
        $data = $model['read_data']();
        
        // Générer un ID unique pour le nouveau référentiel
        $new_id = uniqid();
        
        // Créer le nouveau référentiel avec les données fournies
        $new_referentiel = [
            'id' => $new_id,
            'name' => $referentiel_data['name'],
            'description' => $referentiel_data['description'],
            'capacite' => $referentiel_data['capacite'] ?? 30,
            'sessions' => $referentiel_data['sessions'] ?? 10,
            'modules' => []
        ];
        
        // Gérer l'image
        $image = $referentiel_data['image'] ?? null;
        
        // Si l'image est déjà un chemin (string), l'utiliser directement
        if (is_string($image)) {
            $new_referentiel['image'] = $image;
        } 
        // Sinon, utiliser une image par défaut
        else {
            $new_referentiel['image'] = "assets/images/default-referentiel.jpg";
        }
        
        // Ajouter le nouveau référentiel aux données
        $data['referentiels'][] = $new_referentiel;
        
        // Enregistrer les modifications
        if ($model['write_data']($data)) {
            return $new_referentiel;
        }
        
        return null;
    },
    
    'get_referentiels_by_promotion' => function($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion
        $promotion = null;
        foreach ($data['promotions'] as $p) {
            if ($p['id'] == $promotion_id) {
                $promotion = $p;
                break;
            }
        }
        
        if (!$promotion || empty($promotion['referentiels'])) {
            return [];
        }
        
        // Récupérer les référentiels associés
        return array_filter($data['referentiels'], function($ref) use ($promotion) {
            return in_array($ref['id'], $promotion['referentiels']);
        });
    },
    
    'assign_referentiels_to_promotion' => function ($promotion_id, $referentiel_ids) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver l'index de la promotion
        $promotion_indices = array_keys(array_filter($data['promotions'], function($promotion) use ($promotion_id) {
            return $promotion['id'] === $promotion_id;
        }));
        
        if (empty($promotion_indices)) {
            return false;
        }
        
        $promotion_index = reset($promotion_indices);
        
        // Ajouter les référentiels à la promotion
        if (!isset($data['promotions'][$promotion_index]['referentiels'])) {
            $data['promotions'][$promotion_index]['referentiels'] = [];
        }
        
        $data['promotions'][$promotion_index]['referentiels'] = array_unique(
            array_merge($data['promotions'][$promotion_index]['referentiels'], $referentiel_ids)
        );
        
        return $model['write_data']($data);
    },
    
    'update_promotion_referentiels' => function(int $promotion_id, array $referentiel_ids) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion à mettre à jour
        $promotion_found = false;
        foreach ($data['promotions'] as &$promotion) {
            if ((int)$promotion['id'] === $promotion_id) {
                // Mettre à jour les référentiels de la promotion
                $promotion['referentiels'] = $referentiel_ids;
                $promotion_found = true;
                break;
            }
        }
        
        if (!$promotion_found) {
            return false;
        }
        
        // Enregistrer les modifications
        return $model['write_data']($data);
    },
    
    'search_referentiels' => function(string $query) use (&$model) {
        $referentiels = $model['get_all_referentiels']();
        if (empty($query)) {
            return $referentiels;
        }
        
        return array_filter($referentiels, function($ref) use ($query) {
            return stripos($ref['name'], $query) !== false || 
                   stripos($ref['description'], $query) !== false;
        });
    },
    
    // Fonction pour récupérer la promotion active courante
    'get_current_promotion' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Utiliser array_filter au lieu de foreach
        $active_promotions = array_filter($data['promotions'] ?? [], function ($promotion) {
        
            return $promotion['status'] === Status::ACTIVE->value;
        });
        
        if (empty($active_promotions)) {
            return null;
        }
        
        // Trier par date de début (la plus récente d'abord)
        usort($active_promotions, function ($a, $b) {
            return strtotime($b['date_debut']) - strtotime($a['date_debut']);
        });
        
        return reset($active_promotions);
    },
    
    // Statistiques diverses pour le tableau de bord
    'get_promotions_stats' => function () use (&$model) {
        $data = $model['read_data']();
        
        // Nombre total de promotions
        $total_promotions = count($data['promotions'] ?? []);
        
        // Nombre de promotions actives
        $active_promotions = count(array_filter($data['promotions'] ?? [], function ($promotion) {
            return $promotion['status'] === Enums\ACTIVE;
        }));
        
        // Récupérer la promotion courante
        $current_promotion = $model['get_current_promotion']();
        
        // Nombre d'apprenants dans la promotion courante
        $current_promotion_apprenants = 0;
        if ($current_promotion) {
            $current_promotion_apprenants = count(array_filter($data['apprenants'] ?? [], function ($apprenant) use ($current_promotion) {
                return $apprenant['promotion_id'] === $current_promotion['id'];
            }));
        }
        
        // Nombre de référentiels dans la promotion courante
        $current_promotion_referentiels = 0;
        if ($current_promotion) {
            $current_promotion_referentiels = count($current_promotion['referentiels'] ?? []);
        }
        
        return [
            'total_promotions' => $total_promotions,
            'active_promotions' => $active_promotions,
            'current_promotion_apprenants' => $current_promotion_apprenants,
            'current_promotion_referentiels' => $current_promotion_referentiels
        ];
    },
    
    // Fonctions pour les apprenants
    'get_all_apprenants' => function() use (&$model) {
        $data = $model['read_data']();
        
        // Récupérer les apprenants de la promotion active
        $current_promotion = $model['get_current_promotion']();
        
        if ($current_promotion && isset($current_promotion['apprenants'])) {
            return $current_promotion['apprenants'];
        }
        
        // Si pas de promotion active ou pas d'apprenants dans la promotion active,
        // retourner la liste générale des apprenants
        return $data['apprenants'] ?? [];
    },
    
    'get_apprenants_by_promotion' => function ($promotion_id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par promotion_id
        return array_filter($data['apprenants'] ?? [], function ($apprenant) use ($promotion_id) {
            // Vérifier si la clé promotion_id existe avant de l'utiliser
            return isset($apprenant['promotion_id']) && $apprenant['promotion_id'] == $promotion_id;
        });
    },
    
    'get_apprenant_by_id' => function ($id) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par ID
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($id) {
            return $apprenant['id'] === $id;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'get_apprenant_by_matricule' => function ($matricule) use (&$model) {
        $data = $model['read_data']();
        
        // Filtrer les apprenants par matricule
        $filtered_apprenants = array_filter($data['apprenants'] ?? [], function ($apprenant) use ($matricule) {
            return $apprenant['matricule'] === $matricule;
        });
        
        return !empty($filtered_apprenants) ? reset($filtered_apprenants) : null;
    },
    
    'generate_matricule' => function () use (&$model) {
        $data = $model['read_data']();
        $year = date('Y');
        $count = count($data['apprenants'] ?? []) + 1;
        
        return 'ODC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    },
    
    'get_statistics' => function() use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion active
        $active_promotions = array_filter($data['promotions'], function($promotion) {
            return $promotion['status'] === 'active';
        });
        $active_promotion = reset($active_promotions);
        
        // Calculer les statistiques
        $stats = [
            'active_learners' => 0,
            'total_referentials' => 0, // Sera remplacé par le nombre de référentiels de la promotion active
            'active_promotions' => count($active_promotions),
            'total_promotions' => count($data['promotions'] ?? [])
        ];
        
        // Ajouter le nombre d'apprenants de la promotion active
        if ($active_promotion) {
            $stats['active_learners'] = count($active_promotion['apprenants'] ?? []);
        
            // Compter les référentiels de la promotion active
            $stats['total_referentials'] = count($active_promotion['referentiels'] ?? []);
        }
        
        return $stats;
    },
    // Ajouter un apprenant à une promotion
    'add_apprenant_to_promotion' => function($promotion_id, $apprenant_data) use (&$model) {
        $data = $model['read_data']();
        
        // Trouver la promotion
        $promotion_index = null;
        foreach ($data['promotions'] as $index => $promotion) {
            if ((int)$promotion['id'] === (int)$promotion_id) {
                $promotion_index = $index;
                break;
            }
        }
        
        if ($promotion_index === null) {
            return false;
        }
        
        // Initialiser le tableau des apprenants s'il n'existe pas
        if (!isset($data['promotions'][$promotion_index]['apprenants'])) {
            $data['promotions'][$promotion_index]['apprenants'] = [];
        }
        
        // Ajouter l'apprenant à la promotion
        $data['promotions'][$promotion_index]['apprenants'][] = $apprenant_data;
        
        // Ajouter l'apprenant à la liste globale des apprenants si elle existe
        if (!isset($data['apprenants'])) {
            $data['apprenants'] = [];
        }
        $data['apprenants'][] = $apprenant_data;
        
        // Sauvegarder les données
        return $model['write_data']($data);
    },
    'add_apprenant' => function($apprenant) use (&$model) {
        $data = $model['read_data']();
        
        // Ajouter l'apprenant à la liste générale des apprenants
        $data['apprenants'][] = $apprenant;
        
        // Si l'apprenant doit être ajouté à la promotion active
        $current_promotion = $model['get_current_promotion']();
        if ($current_promotion) {
            // Trouver l'index de la promotion active
            foreach ($data['promotions'] as $key => $promotion) {
                if ($promotion['id'] === $current_promotion['id']) {
                    // Ajouter l'apprenant à la promotion
                    if (!isset($data['promotions'][$key]['apprenants'])) {
                        $data['promotions'][$key]['apprenants'] = [];
                    }
                    $apprenant['promotion_id'] = $current_promotion['id'];
                    $data['promotions'][$key]['apprenants'][] = $apprenant;
                    break;
                }
            }
        }
        
        // Enregistrer les données
        return $model['write_data']($data);
    }
];