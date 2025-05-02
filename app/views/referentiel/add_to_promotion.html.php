<?php
    $is_promotion_en_cours = false;
    $has_apprenants = false;
    if (isset($active_promotion['date_fin'])) {
        $current_year = date('Y');
        $promotion_end_year = date('Y', strtotime($active_promotion['date_fin']));
        $is_promotion_en_cours = ($promotion_end_year == $current_year);
    }
    $has_apprenants = !empty($active_promotion['apprenants']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une référentiel</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        
        .back-button {
            display: flex;
            align-items: center;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-button svg {
            margin-right: 8px;
        }
        
        .promotion-info {
            margin-bottom: 20px;
            background-color: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2196F3;
        }
        
        .promotion-info h2 {
            font-size: 18px;
            color: #333;
            font-weight: 500;
        }
        
        .promotion-info span {
            font-weight: 600;
            color: #2196F3;
        }
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .referentiels-management {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .referentiels-column {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .referentiels-column h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .referentiels-list {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            min-height: 300px;
            max-height: 400px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        
        .referentiel-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #eee;
            transition: all 0.2s ease;
        }
        
        .referentiel-item:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .referentiel-item .ref-name {
            flex: 1;
            font-size: 14px;
        }
        
        .move-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .add-btn {
            background-color: #4CAF50;
            color: white;
            margin-right: 10px;
        }
        
        .remove-btn {
            background-color: #F44336;
            color: white;
            margin-left: 10px;
        }
        
        .move-btn:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }
        
        .referentiels-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 15px;
        }
        
        .referentiels-actions button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .referentiels-actions button:hover:not(:disabled) {
            background-color: #f1f1f1;
        }
        
        .referentiels-actions button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .empty-message {
            color: #999;
            text-align: center;
            padding: 20px 0;
            font-style: italic;
        }
        
        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }
        
        .cancel-button {
            padding: 10px 20px;
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .submit-button {
            padding: 10px 20px;
            background-color: #009688;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gérer les référentiels de la promotion active</h1>
            <a href="?page=referentiels" class="back-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Retour aux référentiels
            </a>
        </div>

        <div class="promotion-info">
            <h2>Promotion active: <span><?= htmlspecialchars($active_promotion['name']) ?></span></h2>
        </div>

        <div class="form-container">
            <?php if ($is_promotion_en_cours && $has_apprenants): ?>
                <div class="error-message" style="color:#c0392b; background:#fdecea; border:1px solid #e17055; padding:10px; border-radius:5px; margin-bottom:15px;">
                    Vous ne pouvez pas désaffecter de référentiels car cette promotion est en cours et possède déjà des apprenants.
                </div>
            <?php endif; ?>
            <form action="?page=update-promotion-referentiels" method="POST">
                <input type="hidden" name="promotion_id" value="<?= $active_promotion['id'] ?>">
                <div class="referentiels-management">
                    <div class="referentiels-column">
                        <h3>Référentiels affectés</h3>
                        <div class="referentiels-list assigned-list" id="assigned-referentiels">
                            <?php if (!empty($assigned_referentiels)): ?>
                                <?php foreach ($assigned_referentiels as $ref): ?>
                                    <div class="referentiel-item" data-id="<?= $ref['id'] ?>">
                                        <span class="ref-name"><?= htmlspecialchars($ref['name']) ?></span>
                                        <button type="button" class="move-btn remove-btn"
                                            onclick="moveReferentiel('<?= $ref['id'] ?>', 'unassign')"
                                            <?= ($is_promotion_en_cours && $has_apprenants) ? 'disabled title="Impossible de désaffecter"' : '' ?>>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                        <input type="hidden" name="assigned_referentiels[]" value="<?= $ref['id'] ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-message">Aucun référentiel affecté</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="referentiels-actions">
                        <button type="button" id="assign-all-btn" onclick="moveAllReferentiels('assign')" <?= empty($unassigned_referentiels) ? 'disabled' : '' ?>>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <polyline points="5 12 12 19 19 12"></polyline>
                            </svg>
                        </button>
                        <button type="button" id="unassign-all-btn"
                            onclick="moveAllReferentiels('unassign')"
                            <?= ($is_promotion_en_cours && $has_apprenants) ? 'disabled title="Impossible de désaffecter"' : (empty($assigned_referentiels) ? 'disabled' : '') ?>>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <polyline points="19 12 12 5 5 12"></polyline>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="referentiels-column">

                        <h3>Référentiels disponibles</h3>
                        <div class="referentiels-list unassigned-list" id="unassigned-referentiels">
                            <?php if (!empty($unassigned_referentiels)): ?>
                                <?php foreach ($unassigned_referentiels as $ref): ?>
                                    <div class="referentiel-item" data-id="<?= $ref['id'] ?>">
                                        <button type="button" class="move-btn add-btn" onclick="moveReferentiel('<?= $ref['id'] ?>', 'assign')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                        <span class="ref-name"><?= htmlspecialchars($ref['name']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-message">Aucun référentiel disponible</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <a href="?page=referentiels" class="cancel-button">Annuler</a>
                    <button type="submit" class="submit-button">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<script>
    // Fonction pour déplacer un référentiel individuel
    function moveReferentiel(refId, action) {
        const referentielItem = document.querySelector(`.referentiel-item[data-id="${refId}"]`);
        
        if (!referentielItem) return;
        
        // Cloner l'élément pour le déplacer
        const clonedItem = referentielItem.cloneNode(true);
        
        if (action === 'assign') {
            // Modifier le bouton pour qu'il devienne un bouton de suppression
            const buttonHtml = `
                <button type="button" class="move-btn remove-btn" onclick="moveReferentiel('${refId}', 'unassign')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
                <input type="hidden" name="assigned_referentiels[]" value="${refId}">
            `;
            
            // Récupérer le nom du référentiel
            const refName = referentielItem.querySelector('.ref-name').textContent;
            
            // Créer le nouvel élément
            clonedItem.innerHTML = `
                <span class="ref-name">${refName}</span>
                ${buttonHtml}
            `;
            
            // Ajouter à la liste des référentiels affectés
            document.getElementById('assigned-referentiels').appendChild(clonedItem);
        } else {
            // Modifier le bouton pour qu'il devienne un bouton d'ajout
            const buttonHtml = `
                <button type="button" class="move-btn add-btn" onclick="moveReferentiel('${refId}', 'assign')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            `;
            
            // Récupérer le nom du référentiel
            const refName = referentielItem.querySelector('.ref-name').textContent;
            
            // Créer le nouvel élément
            clonedItem.innerHTML = `
                ${buttonHtml}
                <span class="ref-name">${refName}</span>
            `;
            
            // Ajouter à la liste des référentiels non affectés
            document.getElementById('unassigned-referentiels').appendChild(clonedItem);
        }
        
        // Supprimer l'élément original
        referentielItem.remove();
        
        // Mettre à jour l'état des boutons "Tout affecter" et "Tout désaffecter"
        updateAllButtonsState();
        
        // Vérifier si les listes sont vides et afficher un message si nécessaire
        checkEmptyLists();
    }
    
    // Fonction pour déplacer tous les référentiels
    function moveAllReferentiels(action) {
        if (action === 'assign') {
            // Déplacer tous les référentiels non affectés vers les affectés
            const unassignedItems = document.querySelectorAll('#unassigned-referentiels .referentiel-item');
            unassignedItems.forEach(item => {
                const refId = item.getAttribute('data-id');
                moveReferentiel(refId, 'assign');
            });
        } else {
            // Déplacer tous les référentiels affectés vers les non affectés
            const assignedItems = document.querySelectorAll('#assigned-referentiels .referentiel-item');
            assignedItems.forEach(item => {
                const refId = item.getAttribute('data-id');
                moveReferentiel(refId, 'unassign');
            });
        }
    }
    
    // Fonction pour vérifier si les listes sont vides et afficher un message si nécessaire
    function checkEmptyLists() {
        const assignedList = document.getElementById('assigned-referentiels');
        const unassignedList = document.getElementById('unassigned-referentiels');
        
        // Vérifier la liste des référentiels affectés
        if (!assignedList.querySelector('.referentiel-item')) {
            // Si aucun élément n'existe, ajouter le message "vide"
            if (!assignedList.querySelector('.empty-message')) {
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'empty-message';
                emptyMessage.textContent = 'Aucun référentiel affecté';
                assignedList.appendChild(emptyMessage);
            }
        } else {
            // Sinon, supprimer le message "vide" s'il existe
            const emptyMessage = assignedList.querySelector('.empty-message');
            if (emptyMessage) {
                emptyMessage.remove();
            }
        }
        
        // Vérifier la liste des référentiels non affectés
        if (!unassignedList.querySelector('.referentiel-item')) {
            // Si aucun élément n'existe, ajouter le message "vide"
            if (!unassignedList.querySelector('.empty-message')) {
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'empty-message';
                emptyMessage.textContent = 'Aucun référentiel disponible';
                unassignedList.appendChild(emptyMessage);
            }
        } else {
            // Sinon, supprimer le message "vide" s'il existe
            const emptyMessage = unassignedList.querySelector('.empty-message');
            if (emptyMessage) {
                emptyMessage.remove();
            }
        }
    }
    
    // Fonction pour mettre à jour l'état des boutons "Tout affecter" et "Tout désaffecter"
    function updateAllButtonsState() {
        const assignAllBtn = document.getElementById('assign-all-btn');
        const unassignAllBtn = document.getElementById('unassign-all-btn');
        
        // Vérifier s'il y a des référentiels non affectés
        const hasUnassigned = document.querySelectorAll('#unassigned-referentiels .referentiel-item').length > 0;
        assignAllBtn.disabled = !hasUnassigned;
        
        // Vérifier s'il y a des référentiels affectés
        const hasAssigned = document.querySelectorAll('#assigned-referentiels .referentiel-item').length > 0;
        unassignAllBtn.disabled = !hasAssigned;
    }
    
    // Initialiser l'état des boutons au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        updateAllButtonsState();
    });
    
    // Ajoutez ce bloc pour bloquer la désaffectation côté JS aussi
    const isPromotionEnCours = <?= ($is_promotion_en_cours && $has_apprenants) ? 'true' : 'false' ?>;
    if (isPromotionEnCours) {
        // Sauvegarder la fonction d'origine
        const originalMoveReferentiel = window.moveReferentiel;
        window.moveReferentiel = function(refId, action) {
            if (action === 'unassign') {
                alert("Impossible de désaffecter un référentiel : cette promotion est en cours et possède des apprenants.");
                return;
            }
            // Pour 'assign', on appelle la fonction normale
            originalMoveReferentiel(refId, action);
        };

        const originalMoveAllReferentiels = window.moveAllReferentiels;
        window.moveAllReferentiels = function(action) {
            if (action === 'unassign') {
                alert("Impossible de désaffecter un référentiel : cette promotion est en cours et possède des apprenants.");
                return;
            }
            // Pour 'assign', on appelle la fonction normale
            originalMoveAllReferentiels(action);
        };
    }
</script>