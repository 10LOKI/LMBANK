<?php
namespace Repository;

require_once __DIR__ . "/../Config/dataBase.php";
require_once __DIR__ . "/../Entity/compte.php";
require_once __DIR__ . "/../Entity/compteCourant.php";
require_once __DIR__ . "/../Entity/compteEpargne.php";

use Entity\Database;
use Entity\Compte;
use Entity\CompteCourant;
use Entity\CompteEpargne; // Correction de la casse si nécessaire
use PDO;

class CompteRepository
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->creer_conn();
    }

    /**
     * Sauvegarde ou met à jour un compte
     */
    public function sauvegarder(Compte $compte): bool
    {
        $decouvert = ($compte instanceof CompteCourant) ? $compte->get_decouvert() : 0;
        $taux = ($compte instanceof CompteEpargne) ? $compte->getTauxInteret() : 0;

        if ($compte->get_id() === null) {
            // INSERTION
            $sql = "INSERT INTO compte (client_id, numero, solde, type_compte, decouvert_autorise, taux_interet) 
                    VALUES (:client_id, :numero, :solde, :type, :decouvert, :taux)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':client_id' => $compte->get_client_id(),
                ':numero'    => $compte->get_numero(),
                ':solde'     => $compte->get_solde(),
                ':type'      => $compte->get_type_compte(),
                ':decouvert' => $decouvert,
                ':taux'      => $taux
            ]);
        } else {
            // MISE À JOUR (UPDATE)
            $sql = "UPDATE compte 
                    SET client_id = :client_id, 
                        numero = :numero, 
                        solde = :solde, 
                        decouvert_autorise = :decouvert, 
                        taux_interet = :taux 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':client_id' => $compte->get_client_id(),
                ':numero'    => $compte->get_numero(),
                ':solde'     => $compte->get_solde(),
                ':decouvert' => $decouvert,
                ':taux'      => $taux,
                ':id'        => $compte->get_id()
            ]);
        }
    }

    /**
     * Récupère un compte par son ID et retourne l'objet spécifique (Courant ou Epargne)
     */
    public function findById(int $id): ?Compte
    {
        $stmt = $this->pdo->prepare("SELECT * FROM compte WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return $this->transformerEnObjet($data);
    }

    /**
     * Récupère tous les comptes
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM compte");
        $comptes = [];

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comptes[] = $this->transformerEnObjet($data);
        }

        return $comptes;
    }

    /**
     * Supprime un compte
     */
    public function supprimer(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM compte WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Méthode interne pour transformer un tableau SQL en objet CompteCourant ou CompteEpargne
     */
    private function transformerEnObjet(array $data): Compte
    {
        // On suppose que votre BDD a une colonne 'type_compte' avec des valeurs comme 'courant' ou 'epargne'
        if ($data['type_compte'] === 'courant') {
            $compte = new CompteCourant(
                $data['client_id'],
                $data['numero'],
                $data['solde'],
                $data['decouvert_autorise']
            );
        } else {
            $compte = new CompteEpargne(
                $data['client_id'],
                $data['numero'],
                $data['solde'],
                $data['taux_interet']
            );
        }

        // On n'oublie pas de réinjecter l'ID généré par la BDD
        $compte->set_id($data['id']); 
        return $compte;
    }
}
?>