<?php
namespace Repository;
require_once __DIR__. "/../Config/dataBase.php";
require_once __DIR__ . "/../Entity/client.php";

use Entity\Client;
use PDO;
class clientRepository
{
    private $db;
    private $pdo;
    public function __construct()
    {
        $this -> db = new db();
        $this -> pdo = $this -> db -> creer_conn();
    }

    public function 
}

?>