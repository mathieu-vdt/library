SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create the utilisateurs table
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) NOT NULL
);

-- Create the livres table
CREATE TABLE livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    date_publication DATE NOT NULL,
    isbn VARCHAR(13) NOT NULL UNIQUE,
    description TEXT,
    statut ENUM('disponible', 'emprunté') DEFAULT 'disponible',
    photo_url VARCHAR(255)
);

-- Create the emprunts table
CREATE TABLE emprunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    date_emprunt DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_retour DATE,
    date_retour_prevu DATE AS (DATE_ADD(date_emprunt, INTERVAL 30 DAY)) STORED,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES livres(id) ON DELETE CASCADE
);


INSERT INTO `livres` (`id`, `titre`, `auteur`, `date_publication`, `isbn`, `description`, `statut`, `photo_url`) VALUES
(1, 'Developpement Web mobile avec HTML, CSS et JavaScript Pour les Nuls', 'William HARREL', '2023-11-09', 'DHIDZH1374R', 'Un livre indispensable ï¿½ tous les concepteurs ou dï¿½veloppeurs de sites Web pour iPhone, iPad, smartphones et tablettes !Ce livre est destinï¿½ aux dï¿½veloppeurs qui veulent crï¿½er un site Internet destinï¿½ aux plate-formes mobiles en adoptant les standard du Web que sont HTML, XHTML, les CSS et JavaScript.', 'emprunté', 'https://cdn.cultura.com/cdn-cgi/image/width=180/media/pim/82_metadata-image-20983225.jpeg'),
(4, 'PHP et MySql pour les Nuls ', 'Janet VALADE', '2023-11-14', '23R32R2R4', 'Le livre best-seller sur PHP & MySQL !\r\n\r\n\r\nAvec cette 5e ï¿½dition de PHP et MySQL pour les Nuls, vous verrez qu\'il n\'est plus nï¿½cessaire d\'ï¿½tre un as de la programmation pour dï¿½velopper des sites Web dynamiques et interactifs.\r\n', 'disponible', ' https://cdn.cultura.com/cdn-cgi/image/width=830/media/pim/66_metadata-image-20983256.jpeg');

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `role`) VALUES
(1, 'Smith', 'John', 'john@smith.com', '$2y$10$xSEoJGdBwbJXMU3BIRD6xuLh0Be/Bz0D8FxbNbyqzHN3Ovuvfa1O2', '2023-11-09 21:54:09', 'admin'),
(2, 'Lord', 'Marc', 'marc@lord.com', '$2y$10$pY2UHGd6DcGYgmU1HgSgbOrp9g7fuFrAk1B7lIZi7anXzrFAlt5IG', '2023-11-09 21:59:23', 'utilisateur');
