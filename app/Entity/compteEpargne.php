<?php
namespace Entity;
use InvalidArgumentException;

class CompteEpargne extends Compte
{
    private float $taux_interet;

    public function __construct(int $client_id, string $numero, float $solde = 0 ,float $taux_interet = 0.02 )
    {
        parent::__construct($client_id, $numero, "Epargne" , $solde);
        $this -> setTauxInteret($taux_interet);
    }
    public function retirer(float $montant): bool
    {
        if($montant <= 0)
        {
            throw new InvalidArgumentException("le montant doit etre positive");
        }
        if($montant > $this -> solde)
        {
            throw new InvalidArgumentException("il faut retirer une valeur que vous avez");
            echo "<br>";
        }
            $this->solde -= $montant;
            return true;
    }
    public function appliquerInterets($solde)
    {
        $this -> solde += $this -> solde * $this -> taux_interet;
    }
    public function getTauxInteret():float
    {
        return $this -> taux_interet;
    }
    public function setTauxInteret(float $taux_interet) :void
    {
        if($taux_interet < 0)
        {
            throw new InvalidArgumentException("le taux d'interet ne peut pas etre negatif");
        }
        $this -> taux_interet = $taux_interet;
    }
}
?>