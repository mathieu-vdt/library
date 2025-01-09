<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des emprunts - Librairie XYZ</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            color: #fff;
            text-align: center;
            padding: 1em 0;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            color: #fff;
        }

        .book-image {
            max-width: 100px;
            height: auto;
        }

        button {
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>

    <style>
        @media (max-width: 768px) {
            .container {
                width: 100%;
            }

            table {
                font-size: 14px;
            }

            .book-image {
                max-width: 50px;
            }
        }
    </style>
</head>

<body>
    <header>
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
                echo "<table>";
                echo "<tr><th>Livre</th><th>Date d'emprunt</th><th>Date de retour</th><th>Date de retour prévu</th><th>Nombre de jours</th><th>Action</th></tr>";
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
                        echo "<form method='post' action='return_emprunt.php'>";
                        echo "<input type='hidden' name='emprunt_id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<button type='submit'>Retourner l'emprunt</button>";
                        echo "</form>";
                    } else {
                        echo "Aucune action";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Erreur lors de la récupération des emprunts.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
        }
        ?>
        <!-- Bouton "Ajouter un livre" visible uniquement pour les utilisateurs -->
        <?php if ($_SESSION['role'] === 'utilisateur') : ?>
            <button onclick="window.location.href = 'add_emprunt.php'">Emprunter un livre</button><br>
        <?php endif; ?>
        <button onclick="window.location.href = 'index.php'">Retour à l'accueil</button>

    </div>
</body>

</html>