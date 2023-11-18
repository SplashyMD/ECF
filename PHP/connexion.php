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
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validation des données (vérifiez également la longueur, les caractères spéciaux, etc.)
    if (empty($username) || empty($password)) {
        redirectToError("Nom d'utilisateur et mot de passe requis.");
    } else {
        // Préparation et exécution de la requête de vérification de la connexion
        $stmt = $conn->prepare("SELECT username, password FROM utilisateurs WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row["password"];

            if (password_verify($password, $hashed_password)) {
                // Connexion réussie
                sendResponse(true, "Connexion réussie!");
            }
        }

        // Connexion échouée
sendResponse(false, "Nom d'utilisateur ou mot de passe incorrect.");
    }
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
