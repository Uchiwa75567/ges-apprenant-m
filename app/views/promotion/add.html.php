<!-- Ajoutez ceci au début du fichier -->
<?php $additional_css = '<link rel="stylesheet" href="assets/css/promotion-form.css">'; ?>
<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Créer une nouvelle promotion</h1>
            <div class="header-subtitle">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</div>
        </div>
        
    </div>
    
    <div class="form-container">
        <form class="promotion-form" action="?page=add-promotion-process" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="promotion-name">Nom de la promotion</label>
                <input type="text" id="promotion-name" name="name" placeholder="Ex: Promotion 2025" value="<?= htmlspecialchars($name ?? '') ?>">
                <?php if (isset($errors) && isset($errors['name'])): ?>
                    <div class="error-message"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="start-date">Date de début (jj/mm/aaaa)</label>
                    <div class="date-input-container">
                        <input type="text" id="start-date" name="date_debut" 
                               placeholder="jj/mm/aaaa"
                               value="<?= htmlspecialchars($date_debut ?? '') ?>">
                        <span class="calendar-icon"></span>
                    </div>
                    <?php if (isset($errors) && isset($errors['date_debut'])): ?>
                        <div class="error-message"><?= $errors['date_debut'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="end-date">Date de fin (jj/mm/aaaa)</label>
                    <div class="date-input-container">
                        <input type="text" id="end-date" name="date_fin" 
                               placeholder="jj/mm/aaaa"
                               value="<?= htmlspecialchars($date_fin ?? '') ?>">
                        <span class="calendar-icon"></span>
                    </div>
                    <?php if (isset($errors) && isset($errors['date_fin'])): ?>
                        <div class="error-message"><?= $errors['date_fin'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Photo de la promotion</label>
                <div class="file-upload-container">
                    <input type="file" id="promotion-image" name="image" accept="image/png,image/jpeg" hidden>
                    <button type="button" class="upload-button" onclick="document.getElementById('promotion-image').click()">
                        Ajouter
                    </button>
                    <span class="upload-text">ou glisser</span>
                    <span id="selected-file-name"></span>
                </div>
                <p class="file-restrictions">Format JPG, PNG. Taille max 2MB</p>
                <?php if (isset($errors) && isset($errors['image'])): ?>
                    <div class="error-message"><?= $errors['image'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Référentiels</label>
                <div class="search-container">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" 
                           id="referentiel-search" 
                           placeholder="Rechercher un référentiel..."
                           onkeyup="searchReferentiels(this.value)">
                </div>
                <div id="referentiels-list" class="referentiels-container">
                    <!-- Les référentiels seront affichés ici -->
                </div>
                <input type="hidden" name="referentiels" id="selected-referentiels" value="<?= isset($referentiels) ? htmlspecialchars(json_encode($referentiels)) : '' ?>">
                <?php if (isset($errors) && isset($errors['referentiels'])): ?>
                    <div class="error-message"><?= $errors['referentiels'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-buttons">
                <a href="?page=promotions" class="cancel-button">Annuler</a>
                <button type="submit" class="submit-button">Créer la promotion</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Afficher le nom du fichier sélectionné
    document.getElementById('promotion-image').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '';
        document.getElementById('selected-file-name').textContent = fileName;
    });
    
    // Recherche des référentiels
    async function searchReferentiels(query) {
        const response = await fetch(`?page=search_referentiels&q=${encodeURIComponent(query)}`);
        const referentiels = await response.json();
        
        const container = document.getElementById('referentiels-list');
        container.innerHTML = referentiels.map(ref => `
            <div class="referentiel-item">
                <input type="checkbox" 
                       name="referentiels[]" 
                       value="${ref.id}"
                       onchange="updateSelectedReferentiels()">
                <span>${ref.name}</span>
            </div>
        `).join('');
        
        // Retourner une promesse résolue pour permettre le chaînage
        return Promise.resolve();
    }
    
    function updateSelectedReferentiels() {
        const selected = Array.from(document.querySelectorAll('input[name="referentiels[]"]:checked'))
                             .map(cb => cb.value);
        document.getElementById('selected-referentiels').value = JSON.stringify(selected);
    }
    
    // Charger les référentiels au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        searchReferentiels('').then(() => {
            // Restaurer les référentiels sélectionnés s'ils existent
            const selectedReferentiels = document.getElementById('selected-referentiels').value;
            if (selectedReferentiels) {
                try {
                    const referentielsArray = JSON.parse(selectedReferentiels);
                    
                    // Cocher les référentiels qui étaient sélectionnés
                    referentielsArray.forEach(refId => {
                        const checkbox = document.querySelector(`input[name="referentiels[]"][value="${refId}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                } catch (e) {
                    console.error('Erreur lors de la restauration des référentiels:', e);
                }
            }
        });
    });
</script>

<style>
/* Styles supplémentaires pour les champs de date personnalisés */
.date-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.date-input-container input {
    width: 100%;
    padding: 12px 16px;
    padding-right: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    color: #1e293b;
    transition: border-color 0.2s;
}

.date-input-container input:focus {
    border-color: #f97316;
    outline: none;
}

.calendar-icon {
    position: absolute;
    right: 12px;
    display: flex;
    align-items: center;
    color: #94a3b8;
}

.calendar-icon svg {
    width: 18px;
    height: 18px;
    stroke: #94a3b8;
}

.error-message {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
}

#selected-file-name {
    margin-left: 10px;
    font-size: 14px;
    color: #64748b;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>