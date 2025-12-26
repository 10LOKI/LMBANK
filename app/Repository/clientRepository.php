<?php
namespace Repository;
require_once __DIR__. "/../Config/dataBase.php";
require_once __DIR__ . "/../Entity/client.php";

use Entity\Client;
use Config\Database;
use PDO;

class clientRepository
{
    private $db;
    private $pdo;
    public function __construct()
    {
        $this -> db = new Database();
        $this -> pdo = $this -> db -> creer_conn();
    }

    public function sauvegarder(Client $client) : bool
    {
        if($client ->get_id() === null)
        {
            $sql = "INSERT INTO client (nom,prenom,email,created_at) VALUES (:nom, :prenom, :email, :created_at)";
            $stmt = $this -> pdo -> prepare($sql);

            return $stmt -> execute([
                ':nom' => $client -> get_nom(),
                ':prenom' => $client -> get_prenom(),
                ':email' => $client -> get_email(),
                ':created_at' => $client -> get_created_at() -> format('Y-m-d H:i:s')
            ]);
        }
        else
        {
            $sql = "UPDATE client SET nom = :nom , prenom = :prenom, email = :email WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            return $stmt -> execute([
                ':nom' => $client -> get_nom(),
                ':prenom' => $client->get_prenom(),
                ':email' => $client->get_email(),
                ':id' => $client->get_id()
            ]);
        }
    }
    public function trouver_par_id(int $id) 
    {
        $sql = "SELECT * FROM client WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) 
        {
            $client = new Client($row['nom'], $row['prenom'], $row['email']);
            $client->set_id($row['id']);
            return $client;
        }
        return null;
    }
     public function trouver_tous(): array
    {
        $sql = "SELECT * FROM client";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $tabClient = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $objetClient = new Client($row['nom'], $row['prenom'], $row['email']);
            $tabClient[] = $objetClient;
        }
        return $tabClient;
    }
    public function supprimer(int $id): bool
    {
        $sql = "DELETE FROM client WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>