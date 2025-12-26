<?php
namespace Entity;
use Exception;
class CompteCourant extends Compte
{
    private float $decouvert_autorise;

    public function __construct(int $client_id, string $numero, float $solde = 0, float $decouvert_autorise = 500.0)
    {
        parent::__construct($client_id, $numero, "Courant", $solde);
        $this->decouvert_autorise = $decouvert_autorise;
    }

    public function retirer(float $montant): bool
    {
        if ($this->solde - $montant < -$this->decouvert_autorise)
        {
            throw new Exception("Découvert autorisé dépassé !");
        }
        $this->solde -= $montant;
        return true;
    }
    public function get_decouvert(): float
    {
        return $this->decouvert_autorise;
    }
}
?>