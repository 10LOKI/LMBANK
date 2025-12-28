<?php
namespace Entity;

class Transaction {
    private ?int $id;
    private int $compte_id;
    private string $type;
    private float $montant;
    private string $date_transaction;

    public function __construct(int $compte_id, string $type, float $montant, ?string $date_transaction = null, ?int $id = null) {
        $this->id = $id;
        $this->compte_id = $compte_id;
        $this->type = $type;
        $this->montant = $montant;
        $this->date_transaction = $date_transaction ?? date("Y-m-d H:i:s");
    }

    // Getters
    public function get_id(): ?int { return $this->id; }
    public function get_compte_id(): int { return $this->compte_id; }
    public function get_type(): string { return $this->type; }
    public function get_montant(): float { return $this->montant; }
    public function get_date(): string { return $this->date_transaction; }

    // Setters
    public function set_id(int $id): void { $this->id = $id; }
}
?>