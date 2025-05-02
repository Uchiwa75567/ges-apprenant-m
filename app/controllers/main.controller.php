// Dans votre contrôleur principal ou autre contrôleur pertinent
$data = $model['read_data']();
$promotions = $data['promotions'] ?? [];

// Identifier les promotions en cours
$resultat_promotions = identifier_promotion_en_cours($promotions);
$promotions_mises_a_jour = $resultat_promotions['promotions_mises_a_jour'];
$promotions_en_cours = $resultat_promotions['promotions_en_cours'];

// Mettre à jour les données
$data['promotions'] = $promotions_mises_a_jour;

// Sauvegarder les données mises à jour si nécessaire
$model['write_data']($data);

// Vous pouvez maintenant passer ces informations à vos vues
// mais ne pas les afficher explicitement