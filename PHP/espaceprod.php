<?php
// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=tableau jeu;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}

// Afficher les données POST pour le débogage
var_dump($_POST);

// Récupération des données du formulaire
$titre = $_POST['titre'];
$budget = $_POST['budget'];
$statut = $_POST['statut'];
$date_fin = $_POST['date_fin'];
$commentaire = $_POST['commentaire'];

// Vérification et définition de la date de fin par défaut
if (empty($date_fin)) {
    $date_fin = date('Y-m-d', strtotime('+1 year', strtotime(date('Y-m-d')))); // Date de fin = 31 décembre de l'année suivante
}

// Requête SQL pour mettre à jour les informations du jeu
$sqlUpdate = "UPDATE jeux_video SET budget = :budget, statut = :statut, date_fin = :date_fin, commentaire = :commentaire WHERE titre = :titre";

// Préparation de la requête de mise à jour
$stmtUpdate = $bdd->prepare($sqlUpdate);

// Liaison des paramètres avec les types appropriés
$stmtUpdate->bindParam(':budget', $budget, PDO::PARAM_INT); // Utilisez PDO::PARAM_INT si le budget est un entier
$stmtUpdate->bindParam(':statut', $statut, PDO::PARAM_STR);
$stmtUpdate->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
$stmtUpdate->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
$stmtUpdate->bindParam(':titre', $titre, PDO::PARAM_STR);

// Exécution de la requête de mise à jour
$updateSuccess = $stmtUpdate->execute();

// Requête SQL pour récupérer les colonnes "titre", "budget" et "statut" des jeux vidéo
$sqlSelect = "SELECT titre, budget, statut FROM jeux_video";

// Préparation de la requête de sélection
$stmtSelect = $bdd->prepare($sqlSelect);

if ($stmtSelect->execute()) {
    $donnees = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($donnees); // Renvoie les données au format JSON
} else {
    echo "Erreur lors de la récupération des données : " . $stmtSelect->errorInfo()[2];
}

// Vérifie si la mise à jour a réussi et retourne une réponse appropriée
if ($stmtSelect->rowCount() > 0) {
    echo "Mise à jour réussie.";
} else {
    echo "Erreur lors de la mise à jour : " . $stmtSelect->errorInfo()[2];
}

// Fermeture de la connexion
$bdd = null;
?>

