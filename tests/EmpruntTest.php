<?php

use PHPUnit\Framework\TestCase;

class EmpruntTest extends TestCase
{
    public function testAjouterEmprunt()
    {
        // Créer un mock de la classe PDO
        $pdoMock = $this->createMock(PDO::class);

        // Configurer le mock pour retourner un résultat spécifique
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        // Simuler le comportement de la transaction
        $pdoMock->expects($this->once())->method('beginTransaction');
        $pdoMock->expects($this->once())->method('commit');

        // Code à tester
        try {
            $pdoMock->beginTransaction();

            // Insérer l'emprunt
            $query = "INSERT INTO emprunts (book_id, user_id) VALUES (:book_id, :user_id)";
            $stmt = $pdoMock->prepare($query);
            $stmt->execute(array(
                ':book_id' => 1,
                ':user_id' => 1,
            ));

            // Mettre à jour le statut du livre
            $query = "UPDATE livres SET statut = 'emprunté' WHERE id = :book_id";
            $stmt = $pdoMock->prepare($query);
            $stmt->execute([':book_id' => 1]);

            $pdoMock->commit();

            $result = true;
        } catch (PDOException $e) {
            $pdoMock->rollBack();
            $result = false;
        }

        // Vérifier le résultat
        $this->assertTrue($result);
    }
}
?>