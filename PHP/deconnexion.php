<?php
session_start();
// Détruisez la session
session_destroy();
// Redirigez l'utilisateur vers la page de connexion
header("Location: connexion.html");
?>
