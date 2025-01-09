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

$errors = [];
$success = false;

// Récupérer la liste des livres qui ne sont pas actuellement empruntés
$books = [];
try {
    $stmt = $pdo->query("SELECT id, titre FROM livres WHERE statut != 'emprunté'");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Erreur lors de la récupération des livres : " . htmlspecialchars($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collectez les données du formulaire
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    // Effectuez des validations (assurez-vous que les données sont correctes)
    if (empty($book_id)) {
        $errors[] = "L'ID du livre est requis.";
    }
    if (empty($user_id)) {
        $errors[] = "L'ID de l'utilisateur est requis.";
    }

    // Vérifiez si le book_id existe dans la table livres
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM livres WHERE id = :book_id");
    $stmt->execute([':book_id' => $book_id]);
    if ($stmt->fetchColumn() == 0) {
        $errors[] = "L'ID du livre n'existe pas.";
    }

    // Ajoutez d'autres validations ici...

    // Si aucune erreur de validation n'est présente
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Insérer l'emprunt
            $query = "INSERT INTO emprunts (book_id, user_id) VALUES (:book_id, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array(
                ':book_id' => $book_id,
                ':user_id' => $user_id,
            ));

            // Mettre à jour le statut du livre
            $query = "UPDATE livres SET statut = 'emprunté' WHERE id = :book_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':book_id' => $book_id]);

            $pdo->commit();

            // Indiquez que l'ajout de l'emprunt a réussi
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Erreur lors de l'ajout de l'emprunt : " . htmlspecialchars($e->getMessage());
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Emprunt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .container {
            margin-top: 20px;
        }

        .btn-custom {
            background-color: #007bff;
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header class="bg-primary text-white text-center py-3">
        <img class="logo" src="image/logo.png" alt="Logo Librairie XYZ">
        <h1>Ajouter un emprunt - Librairie XYZ</h1>
    </header>

    <div class="container">
        <?php if ($success) : ?>
            <div class="alert alert-success" role="alert">
                L'emprunt a été ajouté avec succès.
            </div>
            <button onclick="window.location.href = 'emprunts.php'" class="btn btn-secondary">Retour à la gestion des emprunts</button>
        <?php else : ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="book_id">Livre :</label>
                    <select name="book_id" class="form-control" required>
                        <option value="">Sélectionnez un livre</option>
                        <?php foreach ($books as $book) : ?>
                            <option value="<?= htmlspecialchars($book['id']) ?>"><?= htmlspecialchars($book['titre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom">Ajouter l'Emprunt</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>