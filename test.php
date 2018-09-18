<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__ .'/ElasticSearch.php';
require __DIR__ .'/ElasticQuery.php';
require __DIR__ .'/ElasticMapping.php';

use App\Service\ElasticSearch\ElasticSearch;
use App\Service\ElasticSearch\ElasticMapping;
use App\Service\ElasticSearch\ElasticQuery;

$elastic = new ElasticSearch();

// wipe index if it exists
$elastic->deleteIndex('item');

$settings = [
    'analysis' => ElasticMapping::ANALYSIS
];

// index mappings
$mapping = [
    'search' => [
        'dynamic' => true,
        '_source'    => [ 'enabled' => true ],
    ],
];

// create index
$elastic->addIndex('item', $mapping, $settings);

//////////////////////////////////////////////////////////////////

$data = '{
    "1675": {
        "ID": 1675,
        "ItemUICategory.Name": "Gladiator\'s Arm",
        "LevelItem": 70,
        "Name": "Curtana"
    },
    "1676": {
        "ID": 1676,
        "ItemUICategory.Name": "Gladiator\'s Arm",
        "LevelEquip": 50,
        "LevelItem": 90,
        "Name": "Behemoth Knives"
    },
    "1677": {
        "ID": 1677,
        "ItemUICategory.Name": "Gladiator\'s Arm",
        "LevelEquip": 50,
        "Name": "Zantetsuken"
    },
    "1678": {
        "ID": 1678,
        "ItemUICategory.Name": "Gladiator\'s Arm",
        "LevelEquip": 45,
        "LevelItem": 120,
        "Name": "Test"
    },
    "1500": {
        "ID": 1500,
        "ItemUICategory.Name": "Other\'s Arm",
        "LevelEquip": 50,
        "LevelItem": 140,
        "Col": "X",
        "Name": "Zantetsuken Test"
    }   
}';

$elastic->bulkDocuments('item', 'search', json_decode($data, true));

// wait for eventual consistency
sleep(2);

$query = (new ElasticQuery())
    ->queryWildcard('Name', 'tet')
    ->filterRange('LevelEquip', 40, 'gte')
    ->filterTerm('Col', 'X');

print_r(
    json_encode(
        $query->getQuery(),
        JSON_PRETTY_PRINT
    )
);

echo "\n\n\n";

$results = $elastic->search('item', 'search', $query);

print_r($results);


