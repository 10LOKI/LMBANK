<?php
namespace Entity;
use DateTime;
use InvalidArgumentException;
use Repository\clientRepository;

require_once __DIR__ . '/../Config/dataBase.php';
class Client
{
    private ?int $id = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private DateTime $created_at;

    public function __construct(string $nom,string $prenom,string $email)
    {
        $this -> set_nom($nom);
        $this -> set_prenom($prenom);
        $this -> set_email($email);
        $this -> created_at = new DateTime();
    }
    // Des getters
    public function get_nom():string
    {
        return $this -> nom;
    }
    public function get_prenom():string
    {
        return $this -> prenom;
    }
    public function get_email():string
    {
        return $this -> email;
    }
    public function get_id(): ?int
    {
        return $this -> id;
    }
    public function get_created_at(): DateTime
    {
        return $this -> created_at;
    }
    // Des setters
    public function set_nom(string $nom): void
    {
        // if(strlen($nom) > 10 || empty($nom))
        // {
        //     echo $this -> nom . "est non valide";
        // }
        // else 
        // {
        //     $this -> nom = $nom;
        // }
        if(empty(trim($nom)))
        {
            throw new InvalidArgumentException("le nom ne peut pas etre vide");
        }
        if (strlen($nom) > 20)
        {
            throw new InvalidArgumentException("le nom ne peut pas depasser 20 caracteres");
        }
        $this -> nom = $nom;
    }
    public function set_prenom(string $prenom) : void
    {
        // if(strlen($prenom) > 10 || empty($prenom))
        // {
        //     echo $this -> prenom . "est non valide";
        // }
        // else 
        // {
        //     $this -> prenom = $prenom;
        // }
        if(empty(trim($prenom)))
        {
            throw new InvalidArgumentException("il faut entrer le nom");
        }
        if(strlen($prenom) > 20)
        {
            throw new InvalidArgumentException("Il faut pas entrer un prenom qui depasse 20 caracteres");
        }
        $this -> prenom = $prenom;
    }
    public function set_email(string $email): void
    {
        if (!self::emailValide($email))
        {
            throw new InvalidArgumentException("Email invalide");
        }
        $this -> email = $email;
    }
    public static function emailValide(string $email):bool
    {
        return (bool) filter_var($email,FILTER_VALIDATE_EMAIL);
    }
    public function set_id(int $id) :void
    {
        if($id<= 0)
        {
            throw new InvalidArgumentException("ID invalide");
        }
        $this -> id = $id;
    }
    public function get_nom_complet() : string
    {
        return $this -> nom . " " . $this -> prenom;
    }
    public function afficher_infos():string
    {
        return sprintf(
            "%s %s (%s) - cree le %s",
            $this -> nom,
            $this -> prenom,
            $this -> email,
            $this -> created_at -> format('d/m/Y H:i')
        );
    }
}
?>