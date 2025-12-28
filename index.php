<?php
// CHARGEMENT DES DÉPENDANCES (Gardez vos require existants)
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

$clientRepo = new clientRepository();
$compteRepo = new CompteRepository();
$transRepo = new TransactionRepository();

$message = "";

// --- LOGIQUE DE TRAITEMENT DES ACTIONS (POST & GET) ---

// 1. GESTION DES CLIENTS (CRUD)
if (isset($_POST['action_client'])) {
    try {
        if ($_POST['action_client'] === 'creer') {
            $client = new Client($_POST['nom'], $_POST['prenom'], $_POST['email']);
            $clientRepo->sauvegarder($client);
            $message = "✅ Client créé avec succès !";
        } elseif ($_POST['action_client'] === 'modifier') {
            $client = $clientRepo->trouver_par_id((int)$_POST['id']);
            if ($client) {
                $client->set_nom($_POST['nom']);
                $client->set_prenom($_POST['prenom']);
                $client->set_email($_POST['email']);
                $clientRepo->sauvegarder($client);
                $message = "✅ Client mis à jour !";
            }
        }
    } catch (Exception $e) { $message = "❌ Erreur : " . $e->getMessage(); }
}

if (isset($_GET['supprimer_client'])) {
    $clientRepo->supprimer((int)$_GET['supprimer_client']);
    $message = "🗑️ Client supprimé.";
}

// 2. GESTION DES COMPTES
if (isset($_POST['action_compte']) && $_POST['action_compte'] === 'creer') {
    try {
        $clientId = (int)$_POST['client_id'];
        $num = "CB-" . rand(1000, 9999);
        if ($_POST['type'] === 'courant') {
            $compte = new CompteCourant($clientId, $num, (float)$_POST['solde'], (float)$_POST['decouvert']);
        } else {
            $compte = new CompteEpargne($clientId, $num, (float)$_POST['solde'], (float)$_POST['taux']);
        }
        $compteRepo->sauvegarder($compte);
        $message = "✅ Compte créé avec succès !";
    } catch (Exception $e) { $message = "❌ Erreur : " . $e->getMessage(); }
}

// 3. GESTION DES VIREMENTS
if (isset($_POST['action_virement'])) {
    if ($transRepo->effectuerVirement((int)$_POST['source'], (int)$_POST['dest'], (float)$_POST['montant'])) {
        $message = "💸 Virement effectué !";
    } else {
        $message = "❌ Échec du virement (Solde insuffisant ou erreur).";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; margin: 20px; background: #f4f4f4; }
        .container { display: flex; gap: 20px; flex-wrap: wrap; }
        section { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; min-width: 300px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .msg { padding: 10px; background: #e7f3fe; border-left: 6px solid #2196F3; margin-bottom: 20px; }
    </style>
    <title>Console LM BANK</title>
</head>
<body>
    <h1>🏦 LM BANK - Console d'Administration</h1>

    <?php if($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <div class="container">
        <section>
            <h2>👥 Gestion Clients</h2>
            <form method="POST">
                <input type="hidden" name="action_client" value="creer">
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Ajouter Client</button>
            </form>
            
            <table>
                <tr><th>ID</th><th>Nom</th><th>Email</th><th>Action</th></tr>
                <?php foreach($clientRepo->trouver_tous() as $c): ?>
                <tr>
                    <td><?= $c->get_id() ?></td>
                    <td><?= $c->get_nom_complet() ?></td>
                    <td><?= $c->get_email() ?></td>
                    <td><a href="?supprimer_client=<?= $c->get_id() ?>">❌</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section>
            <h2>💳 Créer un Compte</h2>
            <form method="POST">
                <input type="hidden" name="action_compte" value="creer">
                <select name="client_id" required>
                    <option value="">-- Choisir Client --</option>
                    <?php foreach($clientRepo->trouver_tous() as $c): ?>
                        <option value="<?= $c->get_id() ?>"><?= $c->get_nom_complet() ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="type" id="typeCompte" onchange="toggleFields()">
                    <option value="courant">Courant</option>
                    <option value="epargne">Épargne</option>
                </select>
                <input type="number" name="solde" placeholder="Solde Initial" step="0.01">
                <div id="fieldCourant">
                    <input type="number" name="decouvert" placeholder="Découvert autorisé" value="500">
                </div>
                <div id="fieldEpargne" style="display:none;">
                    <input type="number" name="taux" placeholder="Taux (ex: 0.02)" step="0.01" value="0.02">
                </div>
                <button type="submit">Ouvrir Compte</button>
            </form>

            <h3>Liste des Comptes</h3>
            <table>
                <tr><th>ID</th><th>N°</th><th>Solde</th><th>Type</th></tr>
                <?php foreach($compteRepo->findAll() as $compte): ?>
                <tr>
                    <td><?= $compte->get_id() ?></td>
                    <td><?= $compte->get_numero() ?></td>
                    <td><?= $compte->get_solde() ?> DH</td>
                    <td><?= $compte->get_type_compte() ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section>
            <h2>💸 Effectuer un Virement</h2>
            <form method="POST">
                <input type="hidden" name="action_virement" value="1">
                <input type="number" name="source" placeholder="ID Compte Source" required>
                <input type="number" name="dest" placeholder="ID Compte Destinataire" required>
                <input type="number" name="montant" placeholder="Montant" step="0.01" required>
                <button type="submit">Transférer</button>
            </form>
        </section>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('typeCompte').value;
            document.getElementById('fieldCourant').style.display = type === 'courant' ? 'block' : 'none';
            document.getElementById('fieldEpargne').style.display = type === 'epargne' ? 'block' : 'none';
        }
    </script>
</body>
</html>
