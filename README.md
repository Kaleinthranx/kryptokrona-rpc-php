# Kryptokrona RPC PHP

A simple PHP JSON-RPC wrapper for Kryptokrona. No composer or external libraries required.

Currently supports:
* Kryptokrona Daemon (kryptokronad)
* Kryptokrona Wallet API (wallet-api)
* Kryptokrona Service (kryptokrona-service)

And also includes a KryptokronaHelper class to help with atomic unit conversion and such.

| Sections |
| ----------------------- |
| 1. [Install](#install) |
| 2. [Examples](#examples)</li> |
| 3. [Documentation](#documentation)</li> |
| 4. [License](#license)</li> |

## Install

This library requires PHP >=7.0.0.

Simply download the library and include the wrapper(s) you want.

```php
require_once '/path/to/kryptokrona-rpc-php/KryptokronaHelper.php'
require_once '/path/to/kryptokrona-rpc-php/Kryptokronad.php'
require_once '/path/to/kryptokrona-rpc-php/KryptokronaService.php'
require_once '/path/to/kryptokrona-rpc-php/KryptokronaWalletAPI.php'
```

## Examples

All calls accept the XKR units in atomic units, there are functions in the KryptokronaHelper class to help convert to and from atomic units.

```php
require_once '/path/to/kryptokrona-rpc-php/Kryptokronad.php'

$daemon = new Kryptokronad('http://127.0.0.1', 11898);
$daemon->getBlockCount(); //["count"=>1322743, "status"=>"OK"]
``` 

```php
require_once '/path/to/kryptokrona-rpc-php/KryptokronaService.php'

$service = new KryptokronaService('http://127.0.0.1', 8070, 'service_password');
$service->getBalance(); //["availableBalance"=>250000, "lockedAmount"=>0]
```

```php
require_once '/path/to/kryptokrona-rpc-php/KryptokronaWalletAPI.php'

$walletapi = new KryptokronaWalletAPI('http://127.0.0.1', 8070, 'api_password');
$walletapi->balances(); //["address"=>"WALLET_ADDRESS", "locked"=>0, "unlocked"=>200000]
```

And some KryptokronaHelper example usages:
```php
require_once '/path/to/kryptokrona-rpc-php/KryptokronaHelper.php'
require_once '/path/to/kryptokrona-rpc-php/KryptokronaService.php'

$helper = new KryptokronaHelper();
$service = new KryptokronaService('http://127.0.0.1', 8070, 'service_password');
$balance = $service->getBalance(); //["availableBalance"=>250000, "lockedAmount"=>0]

$helper->fromAtomicUnits($balance['availableBalance']); //Will return: 2.5
$helper->fromAtomicUnitsRecursive($balance, 'availableBalance'); //Will modify the original array to: ["availableBalance"=>2.5, "lockedAmount"=>0]
```


You can also retrieve information from the RPC call:

```php
$response = $service->getBalance();

$response->result();
$response->getStatusCode();
$response->getHeaders();
$response->hasHeader($header);
$response->addHeader($header);
$response->getBody();
``` 

## Documentation

Documentation of the Kryptokrona API can be found [here](https://docs.kryptokrona.org/developer/kryptokrona/api).

## License

Kryptokrona RPC PHP is open-source and licensed under the [MIT License](/LICENSE).
