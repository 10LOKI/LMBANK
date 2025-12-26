<?php
require_once __DIR__ . '/app/Entity/client.php';
require_once __DIR__ . '/app/Repository/clientRepository.php';
use Entity\Client;
use Repository\clientRepository;

$repo = new clientRepository();
// test dial l'affichage 
try
{
    echo "liste des clients";
    $tousLesClients = $repo -> trouver_tous();
    if (empty($tousLesClients))
    {
        echo "ya pas de client";
    }
    else
    {
        foreach($tousLesClients as $unClient)
        {
            echo $unClient -> afficher_infos();
        }
    }
}
catch (Exception $e)
{
    echo "erreur d'affichage" . $e -> getMessage();
}
// test dial securite de la creation
try
{
    echo "test de securite";
    $client1 = new Client("Loki","chwiyaMalade", "m9awd33@gmail.com");
    if($repo -> sauvegarder($client1))
    {
        echo "succes";
    }
}
catch (InvalidArgumentException $e)
{
    echo "erreur" . $e -> getMessage();
}
catch (Exception $e)
{
    echo "erreur generale" . $e -> getMessage();
}
?>