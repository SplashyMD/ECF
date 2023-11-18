<?php
// Configuration de la base de données
$servername = "localhost"; // Nom du serveur de base de données
$db_username = "root"; // Nom d'utilisateur MySQL
$db_password = ""; // Mot de passe MySQL
$dbname = "tableau jeu"; // Nom de la base de données

// Connexion à la base de données
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    sendResponse(false, "La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Vérifiez si le paramètre 'token' est présent dans l'URL
if (isset($_GET["token"])) {
    $token = $_GET["token"];

    // Recherchez l'utilisateur avec ce jeton dans la base de données
    $stmt = $conn->prepare("SELECT email_verified FROM utilisateurs WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Vérifiez si l'adresse e-mail a déjà été vérifiée
        if ($row["email_verified"] == 1) {
            sendResponse(false, "L'adresse e-mail a déjà été vérifiée.");
        } else {
            // Marquez l'adresse e-mail de l'utilisateur comme vérifiée
            $stmt = $conn->prepare("UPDATE utilisateurs SET email_verified = 1 WHERE verification_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            sendResponse(true, "Adresse e-mail vérifiée avec succès.");
        }
    } else {
        sendResponse(false, "La vérification de l'adresse e-mail a échoué.");
    }
} else {
    sendResponse(false, "Paramètre 'token' manquant dans l'URL.");
}

// Fermeture de la connexion à la base de données
$conn->close();

// Fonction pour envoyer une réponse JSON
function sendResponse($success, $message) {
    $response = array("success" => $success, "message" => $message);
    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}
?>



