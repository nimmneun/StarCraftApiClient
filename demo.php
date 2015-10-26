<?php
/**
 * @author neun
 * @since  2015-10-26
 */
require_once 'vendor/autoload.php';

use StarCraftApiClient\Client;

$apiToken = file_get_contents('conf.txt');
$client = new Client($apiToken);

$client->addAllProfileRequests(2778901, 'Mamba', 'eu');
$client->addAllProfileRequests(1283855, 'PuPu', 'eu');
// ...
$client->run();

// Iterate over all request objects and return the JSON response ...
foreach ($client->getRequests() as $id => $request) {
    echo $request->response();
}

// Access specific request object
echo $client->getRequests('profile.1283855.matches')[0]->response();
echo $client->getRequests('profile.1283855.matches')[0]->info()['http_code'];

// Directly access a specific profile sub-object (profile.1283855.matches)
echo $client->get('profile.1283855.matches')->matches[0]->map;

// Iterate over all profile-type result objects.
foreach ($client->get('profile') as $playerId => $profile) {
    var_dump($profile['matches']);
    var_dump($profile['ladders']);
    // ...
}
