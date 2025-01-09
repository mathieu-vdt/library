<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emprunt_id = $_POST['emprunt_id'];

    try {
        $pdo->beginTransaction();

        // Modifier l'emprunt et mettre date emprunt rendu
        $query = "UPDATE emprunts SET date_retour = NOW() WHERE id = :emprunt_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':emprunt_id' => $emprunt_id));

        // Récupérer le book_id de l'emprunt
        $query = "SELECT book_id FROM emprunts WHERE id = :emprunt_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':emprunt_id' => $emprunt_id));
        $book_id = $stmt->fetchColumn();

        // Mettre à jour le statut du livre
        $query = "UPDATE livres SET statut = 'disponible' WHERE id = :book_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':book_id' => $book_id));

        $pdo->commit();

        header('Location: emprunts.php');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erreur lors du retour de l'emprunt : " . htmlspecialchars($e->getMessage());
    }
}
?>