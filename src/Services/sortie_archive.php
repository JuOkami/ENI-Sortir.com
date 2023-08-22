<?php
// Inclure les dépendances (Doctrine, etc.)
require 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

// Configuration de la base de données
$dbParams = [
    'dbname' => 'sortir',
    'user' => 'root',
    'password' => 'your_db_password',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$connection = DriverManager::getConnection($dbParams);

// Sélectionner les sorties passées depuis plus d'un mois
$oneMonthAgo = new DateTime();
$oneMonthAgo->modify('-1 month');

$queryBuilder = $connection->createQueryBuilder();
$queryBuilder
    ->select('*')
    ->from('sortie')
    ->where('date < :oneMonthAgo')
    ->setParameter('oneMonthAgo', $oneMonthAgo->format('Y-m-d H:i:s'));

$sortiesToArchive = $queryBuilder->execute()->fetchAllAssociative();

// Déplacer les sorties dans la table sortie_archive
foreach ($sortiesToArchive as $sortie) {
    $connection->insert('sortie_archive', $sortie);
    $connection->delete('sortie', ['id' => $sortie['id']]);
}

echo count($sortiesToArchive) . ' sorties ont été archivées.';
?>
