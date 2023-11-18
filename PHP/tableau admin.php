<?php
// Code PHP pour récupérer les données depuis la base de données et les afficher
$servername = "localhost"; // Nom du serveur de base de données
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$dbname = "tableau jeu"; // Nom de la base de données

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Requête SQL pour récupérer les jeux vidéo
$sql = "SELECT id, titre, date_debut, score FROM jeux_video";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["titre"] . "</td>";
        echo "<td>" . $row["date_debut"] . "</td>";
        echo "<td>" . $row["score"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Aucun jeu vidéo trouvé.</td></tr>";
}

// Fermeture de la connexion à la base de données
$conn->close();
?>

