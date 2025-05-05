<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ajouter un apprenant</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="?page=dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="?page=apprenants">Apprenants</a></li>
                        <li class="breadcrumb-item active">Ajouter</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informations de l'apprenant</h3>
                        </div>
                        
                        <?php if (isset($flash) && !empty($flash)): ?>
                            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?= $flash['message'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="?page=add-apprenant-process" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prenom">Prénom</label>
                                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom">Nom</label>
                                            <input type="text" class="form-control" id="nom" name="nom" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_naissance">Date de naissance</label>
                                            <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lieu_naissance">Lieu de naissance</label>
                                            <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="adresse">Adresse</label>
                                            <input type="text" class="form-control" id="adresse" name="adresse" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telephone">Téléphone</label>
                                            <input type="text" class="form-control" id="telephone" name="telephone" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="referentiel_id">Référentiel</label>
                                            <select class="form-control" id="referentiel_id" name="referentiel_id" required>
                                                <option value="">Sélectionner un référentiel</option>
                                                <?php 
                                                // Récupérer les référentiels de la promotion active
                                                $promotion_active = null;
                                                foreach ($data['promotions'] ?? [] as $promotion) {
                                                    if (($promotion['status'] ?? '') === 'active') {
                                                        $promotion_active = $promotion;
                                                        break;
                                                    }
                                                }

                                                // Filtrer les référentiels disponibles
                                                $referentiels_disponibles = array_filter($referentiels, function($ref) use ($promotion_active) {
                                                    return $promotion_active && in_array($ref['id'], $promotion_active['referentiels'] ?? []);
                                                });

                                                foreach ($referentiels_disponibles as $referentiel): 
                                                ?>
                                                    <option value="<?= $referentiel['id'] ?>"><?= htmlspecialchars($referentiel['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">Seuls les référentiels de la promotion active sont affichés</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="photo">Photo</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="photo" name="photo">
                                                    <label class="custom-file-label" for="photo">Choisir un fichier</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                <h4>Informations du tuteur</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tuteur_nom">Nom du tuteur</label>
                                            <input type="text" class="form-control" id="tuteur_nom" name="tuteur_nom" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tuteur_lien">Lien de parenté</label>
                                            <input type="text" class="form-control" id="tuteur_lien" name="tuteur_lien" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tuteur_adresse">Adresse du tuteur</label>
                                            <input type="text" class="form-control" id="tuteur_adresse" name="tuteur_adresse" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tuteur_telephone">Téléphone du tuteur</label>
                                            <input type="text" class="form-control" id="tuteur_telephone" name="tuteur_telephone" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="?page=apprenants" class="btn btn-default">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<link rel="stylesheet" href="assets/css/apprenant-form.css">
<script>
// Afficher le nom du fichier sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('photo');
    const fileLabel = document.querySelector('.custom-file-label');
    
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileLabel.textContent = this.files[0].name;
            } else {
                fileLabel.textContent = 'Choisir un fichier';
            }
        });
    }
});
</script>