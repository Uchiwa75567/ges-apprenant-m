<h2>Importer des apprenants (Excel/CSV)</h2>
<form method="post" enctype="multipart/form-data" action="?page=import-apprenants-process">
    <input type="file" name="import_file" accept=".csv,.xls,.xlsx" required>
    <button type="submit">Importer</button>
</form>
<p>Le fichier doit contenir les colonnes : Prénom, Nom, Email, Adresse, Téléphone, Référentiel</p>