# StarCraftApiClient

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nimmneun/StarCraftApiClient/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/StarCraftApiClient/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/nimmneun/StarCraftApiClient/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nimmneun/StarCraftApiClient/build-status/master)

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
