<?php
// Configuration de la base de données (à adapter selon votre configuration)
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "tableau jeu";

// Connexion à la base de données
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    sendResponse(false, "La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Configuration AWS SDK
require 'vendor/autoload.php';

use Aws\Ses\SesClient;

$sesClient = new SesClient([
    'version' => 'latest',
    'region' => 'eu-west-3', // Change this to your desired AWS region
    'credentials' => [
        'key' => 'AKIAQX4W3KXOSQ6UTFH6',
        'secret' => 'VbUK/qLMV8w8ajW5xlhLDg4WdgUngA+MvtXhLWls',
    ],
]);

// Traitement du formulaire de réinitialisation du mot de passe
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Valider et nettoyer l'adresse e-mail
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    // Vérifier si l'adresse e-mail existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Générer un token de réinitialisation sécurisé
        $resetToken = bin2hex(random_bytes(32));
        $resetLink = "http://localhost/ECF-GAMESOFT-main/verification.html?token=" . $resetToken;
        
        // Enregistrez le token dans la base de données avec une date d'expiration
        $expiration = date("Y-m-d H:i:s", strtotime("+1 hour")); // Exemple : lien expiré après 1 heure
        $stmt = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_token_expiration = ? WHERE email = ?");
        $stmt->bind_param("sss", $resetToken, $expiration, $email);
        $stmt->execute();
        
        // Personnalisez le sujet et le contenu de l'e-mail
        $subject = "Réinitialisation de votre mot de passe";
        $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : " . $resetLink;

        // Utilisez votre adresse e-mail vérifiée comme expéditeur
$senderEmail = "mohamedbenyoussef93@outlook.fr";

// Envoie de l'e-mail via Amazon SES
$result = $sesClient->sendEmail([
    'Source' => $senderEmail,
    'Destination' => [
        'ToAddresses' => [$email], // L'adresse e-mail du destinataire
    ],
    'Message' => [
        'Subject' => [
            'Data' => $subject,
            'Charset' => 'UTF-8',
        ],
        'Body' => [
            'Html' => [
                'Data' => $message,
                'Charset' => 'UTF-8',
            ],
        ],
    ],
]);


        if ($result['MessageId']) {
            // Réinitialisation réussie
            sendResponse(true, "Un e-mail de réinitialisation a été envoyé à votre adresse.");
        } else {
            // Erreur lors de l'envoi de l'e-mail
            sendResponse(false, "Erreur lors de l'envoi de l'e-mail. Veuillez réessayer.");
        }
    } else {
        // Adresse e-mail non trouvée dans la base de données
        http_response_code(400); // Définissez le code d'erreur HTTP approprié
        echo "Adresse e-mail invalide. Veuillez réessayer.";
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
