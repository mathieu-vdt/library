<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // Durée de vie du cookie en secondes (1 jour)
        'cookie_secure' => true, // Le cookie n'est envoyé que sur des connexions HTTPS
        'cookie_httponly' => true, // Le cookie n'est accessible que par le serveur
        'use_strict_mode' => true, // Utilisation du mode strict pour les sessions
        'use_only_cookies' => true, // Utilisation uniquement des cookies pour les sessions
        'cookie_samesite' => 'Strict' // Le cookie n'est pas envoyé avec les requêtes intersites
    ]);
}


// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}
?>