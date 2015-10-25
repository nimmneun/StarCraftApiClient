# StarCraftApiClient
Simple API client to fetch results from battle.net's StarCraft API in parallel.

```php
$client= new StarCraftApiClient\Client('fancyToken...n3t7n893...')

$client->addProfileMatchesRequest(123321, 'SomeGuy', 'eu');
$client->addProfileLaddersRequest(456654, 'AnotherGuy', 'kr');
$client->addAllProfileRequests(789987, 'SomeOtherGuy', 'us');

$client->run();

echo $client->get('profile.123321.matches')->matches[0]->map
```
See [demo.php] for more examples.
