<?php
// CHARGEMENT DES FICHIERS
require_once __DIR__ . '/app/Config/dataBase.php';
require_once __DIR__ . '/app/Entity/client.php';
require_once __DIR__ . '/app/Entity/compte.php'; 
require_once __DIR__ . '/app/Entity/compteCourant.php';
require_once __DIR__ . '/app/Entity/compteEpargne.php';
require_once __DIR__ . '/app/Entity/transaction.php';
require_once __DIR__ . '/app/Repository/clientRepository.php';
require_once __DIR__ . '/app/Repository/compteRepository.php';
require_once __DIR__ . '/app/Repository/transactionRepository.php';

use Entity\Client;
use Entity\CompteCourant;
use Entity\CompteEpargne;
use Repository\clientRepository;
use Repository\CompteRepository;
use Repository\TransactionRepository;

// Initialisation
$clientRepo = new clientRepository();
$compteRepo = new CompteRepository();
$transRepo  = new TransactionRepository();

// Formatage simple pour la console ou page blanche
function logSimple($step, $msg) {
    echo "\n--- [$step] ---\n";
    echo $msg . "\n";
}

try {
    // 1. GESTION CLIENT
    $email = "test." . uniqid() . "@bank.com";
    $client = new Client("Alaoui", "Karim", $email);
    
    if($clientRepo->sauvegarder($client)) {
        logSimple("1. CLIENT", "Créé: " . $client->afficher_infos() . " (ID: " . $client->get_id() . ")");
    }

    // 2. OUVERTURE COMPTES
    $cSource = new CompteCourant($client->get_id(), "COURANT-".rand(100,999), 5000, 1000);
    $compteRepo->sauvegarder($cSource);
    
    $cDest = new CompteEpargne($client->get_id(), "EPARGNE-".rand(100,999), 0, 0.05);
    $compteRepo->sauvegarder($cDest);

    logSimple("2. COMPTES", "Source: " . $cSource->get_solde() . " DH | Dest: " . $cDest->get_solde() . " DH");

    // 3. VIREMENT
    $montant = 1500;
    echo "\n> Action: Tentative de virement de $montant DH...\n";

    if($transRepo->effectuerVirement($cSource->get_id(), $cDest->get_id(), $montant)) {
        echo "SUCCESS: Virement effectué.\n";
    } else {
        echo "ERROR: Le virement a échoué.\n";
    }

    // 4. VERIFICATION SOLDES
    $sourceUpdated = $compteRepo->findById($cSource->get_id());
    $destUpdated = $compteRepo->findById($cDest->get_id());

    logSimple("3. RESULTAT FINAL", 
        "Source: " . $sourceUpdated->get_solde() . " DH (Attendu: 3500)\n" .
        "Dest  : " . $destUpdated->get_solde() . " DH (Attendu: 1500)"
    );

    // 5. HISTORIQUE RAPIDE
    echo "\n--- 4. HISTORIQUE (SOURCE) ---\n";
    $transactions = $transRepo->findAllByCompte($cSource->get_id());
    foreach($transactions as $t) {
        echo "[{$t->get_date()}] " . strtoupper($t->get_type()) . ": {$t->get_montant()} DH\n";
    }

} catch (Exception $e) {
    echo "\n!!! EXCEPTION: " . $e->getMessage() . "\n";
}