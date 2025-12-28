<?php
namespace Repository;

require_once __DIR__ . "/../Config/dataBase.php";
require_once __DIR__ . "/../Entity/transaction.php";
require_once __DIR__ . "/compteRepository.php";

use Config\Database;
use Entity\Transaction;
use PDO;
use Exception;

class TransactionRepository {
    private $pdo;
    private $compteRepo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->creer_conn();
        $this->compteRepo = new CompteRepository();
    }

    public function sauvegarder(Transaction $transaction): bool {
    $sql = "INSERT INTO transaction (compte_id, type_transaction, montant, date_transaction) 
            VALUES (:compte_id, :type, :montant, :date_t)";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':compte_id' => $transaction->get_compte_id(),
        ':type'      => $transaction->get_type(),
        ':montant'   => $transaction->get_montant(),
        ':date_t'    => $transaction->get_date()
    ]);
}
    public function effectuerVirement(int $idSource, int $idDest, float $montant): bool {
        try {
            $this->pdo->beginTransaction();

            $source = $this->compteRepo->findById($idSource);
            $dest = $this->compteRepo->findById($idDest);

            if (!$source || !$dest) throw new Exception("Compte(s) introuvable(s)");
            if ($source->get_solde() < $montant) throw new Exception("Solde insuffisant");

            // 1. Mise à jour des objets Compte
            $source->deposer($source->get_solde() - $montant);
            $dest->deposer($dest->get_solde() + $montant);

            // 2. Persistance des comptes
            $this->compteRepo->sauvegarder($source);
            $this->compteRepo->sauvegarder($dest);

            // 3. Création et sauvegarde des entités Transaction
            $tSource = new Transaction($idSource, 'virement_debit', $montant);
            $tDest = new Transaction($idDest, 'virement_credit', $montant);

            $this->sauvegarder($tSource);
            $this->sauvegarder($tDest);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Récupère l'historique pour un compte sous forme d'objets Transaction
     */
    public function findByCompte(int $compteId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM transaction WHERE compte_id = ? ORDER BY date_transaction DESC");
        $stmt->execute([$compteId]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Transaction(
                $row['compte_id'],
                $row['type_transaction'],
                $row['montant'],
                $row['date_transaction'],
                $row['id']
            );
        }
        return $results;
    }
}

?>