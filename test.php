<?php
// test_client.php
require_once __DIR__ . '/../app/Entity/client.php';

try {
    // Test 1: Création d'un client valide
    echo "=== Test 1: Création client valide ===\n";
    $client = new Client("Dupont", "Jean", "jean.dupont@email.com", new DateTime());
    echo $client->afficher_infos() . "\n";
    echo "Nom complet: " . $client->get_nom_complet() . "\n";
    echo "Email: " . $client->get_email() . "\n";
    echo "Date création: " . $client->get_create_at()->format('Y-m-d') . "\n";
    
    // Test 2: Test des setters
    echo "\n=== Test 2: Modification des valeurs ===\n";
    $client->set_nom("Martin");
    $client->set_prenom("Marie");
    $client->set_email("marie.martin@test.fr");
    echo $client->afficher_infos() . "\n";
    
    // Test 3: Test validation email
    echo "\n=== Test 3: Test email invalide ===\n";
    try {
        $client->set_email("email-invalide");
        echo "ERREUR: Devrait échouer!\n";
    } catch (InvalidArgumentException $e) {
        echo "OK: Exception capturée - " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test validation nom vide
    echo "\n=== Test 4: Test nom vide ===\n";
    try {
        $client->set_nom("");
        echo "ERREUR: Devrait échouer!\n";
    } catch (InvalidArgumentException $e) {
        echo "OK: Exception capturée - " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test nom trop long
    echo "\n=== Test 5: Test nom trop long ===\n";
    try {
        $client->set_nom("UnNomTrèsLongQuiDépasseVingtCaractères");
        echo "ERREUR: Devrait échouer!\n";
    } catch (InvalidArgumentException $e) {
        echo "OK: Exception capturée - " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Tous les tests sont passés! ===\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}