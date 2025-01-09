<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des emprunts - Librairie XYZ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .container {
            margin-top: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-custom {
            background-color: #007bff;
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function confirmReturn() {
            return confirm("Êtes-vous sûr de vouloir retourner cet emprunt ?");
        }
    </script>
</head>

<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Liste des Livres - Librairie XYZ</h1>
    </header>

    <div class="container">
        <!-- Affichage des livres depuis la base de données -->
        <?php
        require('config.php');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit();
        }

        try {
            $query = "SELECT emprunts.*, livres.titre FROM emprunts JOIN livres ON emprunts.book_id = livres.id WHERE emprunts.user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array(':user_id' => $_SESSION['user_id']));

            if ($stmt) {
                echo "<table class='table table-striped'>";
                echo "<thead class='thead-dark'>";
                echo "<tr><th>Livre</th><th>Date d'emprunt</th><th>Date de retour</th><th>Date de retour prévu</th><th>Nombre de jours</th><th>Action</th></tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['titre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_emprunt']) . "</td>";
                    if (empty($row['date_retour'])) {
                        echo "<td>Non retourné</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($row['date_retour']) . "</td>";
                    }
                    echo "<td>" . htmlspecialchars($row['date_retour_prevu']) . "</td>";

                    // Calculer le nombre de jours emprunté
                    $date_emprunt = new DateTime($row['date_emprunt']);
                    $date_retour = empty($row['date_retour']) ? new DateTime() : new DateTime($row['date_retour']);
                    $interval = $date_emprunt->diff($date_retour);
                    $nb_jours = $interval->days;

                    echo "<td>" . htmlspecialchars($nb_jours) . "</td>";
                    echo "<td>";
                    if (empty($row['date_retour'])) {
                        echo "<form method='post' action='return_emprunt.php' onsubmit='return confirmReturn()'>";
                        echo "<input type='hidden' name='emprunt_id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<button type='submit' class='btn btn-custom'>Retourner l'emprunt</button>";
                        echo "</form>";
                    } else {
                        echo "Aucune action";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de la récupération des emprunts.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
        <!-- Bouton "Ajouter un livre" visible uniquement pour les utilisateurs -->
        <div class="row justify-content-between mt-3">
            <button onclick="window.location.href = 'index.php'" class="btn btn-secondary">Retour à l'accueil</button>
            <?php if ($_SESSION['role'] === 'utilisateur') : ?>
                <button onclick="window.location.href = 'add_emprunt.php'" class="btn btn-primary">Emprunter un livre</button>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>