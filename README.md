XRPhp - XRP Ledger Library
==========================

This is a PHP 7.1+ library for communication with the XRP Ledger.

The intention is to provide PHP developers with an easy way to interact/explore
with the [rippled API](https://developers.ripple.com/rippled-api.html).

The [Ripple Developer Portal](https://developers.ripple.com/) is a great resource
to use along side this project to study basic and advanced concepts of the XRP ledger.

As I learn the API better myself, I'll work on making the library more than just
the wrapper.

Installation
------------

```
composer require mikemilano/xrphp
```

## Instantiating With Connection Data

Create a Connection object from a url string:
```
$con = new Connection('https://s1.ripple.com:51234');
```

Create a `Connection` objecct from a config array:
```
$con = new \XRPhp\Connection([
    'scheme' => 'https',
    'host' => 's1.ripple.com',
    'port' => 51234
]);
```

## Sending Commands

This example calls the `account_info` command. You can see a full
list of commands available in the [rippled api](https://developers.ripple.com/rippled-api.html)
documentation.

```
$resp = $con->send('account_info', [
    'account' => 'rG1QQv2nh2gr7RCZ1P8YYcBUKCCN633jCn',
    'strict' => true,
    'ledger_index' => 'current',
    'queue' => true
]);
```

Response data is the result of `json_decode($json, true)`, so any
objects have been converted to associative arrays.

Output of `print_r($resp)`:
```
(
    [result] => Array
        (
            [account_data] => Array
                (
                    [Account] => rG1QQv2nh2gr7RCZ1P8YYcBUKCCN633jCn
                    [Balance] => 6
                    [Flags] => 65536
                    [LedgerEntryType] => AccountRoot
                    [OwnerCount] => 0
                    [PreviousTxnID] => E9A2BAA7B310BA3C52FDFBDEC404D1339E7547E8CD769D6CD40AD0EFABF337F8
                    [PreviousTxnLgrSeq] => 36405347
                    [RegularKey] => rU4DpLWAzs3ECf8SVkeJGeLt9KBRGhxpQg
                    [Sequence] => 192218
                    [index] => 92FA6A9FC8EA6018D5D16532D7795C91BFB0831355BDFDA177E86C8BF997985F
                )

            [ledger_current_index] => 38726789
            [queue_data] => Array
                (
                    [txn_count] => 0
                )

            [status] => success
            [validated] => 
        )
)
```
