<?php
require 'connexion.php';

// Données fictives pour le test
$_POST['username'] = 'denkisenshi'; // Remplacez par le nom d'utilisateur que vous voulez tester
$_POST['password'] = 'Kevadam1'; // Remplacez par le mot de passe que vous voulez tester

// Appeler la fonction de connexion
$conn = new mysqli("localhost", "root", "", "tableau jeu");

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Le reste de votre code pour vérifier les résultats du test ici.

// Fermeture de la connexion à la base de données
$conn->close();


?>

