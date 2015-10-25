<?php
/**
 * You should probably use an autoloader =)
 */
require_once "StarCraftApiClient/Client.php";
require_once "StarCraftApiClient/Requests/Request.php";
require_once "StarCraftApiClient/Requests/DataRequest.php";
require_once "StarCraftApiClient/Requests/LadderRequest.php";
require_once "StarCraftApiClient/Requests/ProfileRequest.php";

$client = new StarCraftApiClient\Client('...tokenTOKENtoken...');

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
