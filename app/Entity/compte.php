<?php
namespace Entity;

use InvalidArgumentException;

abstract class compte
{
    protected ?int $id = null;
    protected int $client_id;
    protected string $numero;
    protected float $solde;
    protected string $type_compte;

    public function __construct(int $client_id, string $numero, string $type_compte, float $solde = 0)
    {
        $this -> client_id = $client_id;
        $this -> set_numero($numero);
        $this -> type_compte = $type_compte;
        $this -> solde = $solde;
    }

    public function get_id(): ?int 
    {
        return $this->id;
    }
    public function get_client_id():int { return $this -> client_id;}
    // Changez le type de retour en string
    public function get_numero(): string 
    {
        return (string)$this->numero;
    }
    public function get_solde():float { return $this -> solde;}
    public function get_type_compte():string { return $this -> type_compte;}

    public function set_id(int $id): void
    {
        if($id <= 0) throw new InvalidArgumentException("id du compte invalide");
        $this -> id = $id;
    }

    public function set_numero(string $numero):void
    {
        if(empty(trim($numero)))
            throw new InvalidArgumentException("il faut entre un numero de compte");
            $this -> numero = $numero;
    }

    //les methodes metiers
    public function deposer (float $montant): bool
    {
        if($montant <= 0)
        {
            throw new InvalidArgumentException("tu dois deposer quelque chose positive");
        }
        $this -> solde += $montant;
        return true;
    }

    abstract public function retirer (float $montant): bool;
}

?>