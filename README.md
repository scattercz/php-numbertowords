# php-numbertowords
Simple PHP number and currency to words converter for czech language.

### Basic usage
```php
<?php
use scattercz;

$numberToWords = new NumberToWords;
$numberAsWords = $numberToWords->convertNumber(17);

echo $numberAsWords; //outputs "sedmnáct"
```

## Numbers conversion
Convert integer / float into string with optional or none words delimiter. Supported are numbers in range from -999999999.999 to 999999999.999 with up to 3 decimals.

### Examples
```php
// whole numbers
$numberToWords->convertNumber(38); // "třicet osm"
$numberToWords->convertNumber(2564); // "dva tisíce pět set šedesát čtyři"

// negative numbers
$numberToWords->convertNumber(-64); // "mínus šedesát čtyři"

// decimals
$numberToWords->convertNumber(0.255); // "žádná celá dvě stě padesát pět"
$numberToWords->convertNumber(4.037); // "čtyři celé třicet sedm tisícin"

// custom separators
$numberToWords->convertNumber(345, ''); // "třistačtyřicetpět"
$numberToWords->convertNumber(56, ' a '); // "pět a šest"
```

## Currency conversion
Currency can be coverted in the same way as numbers, including currency decimals and negative numbers.

### Examples
```php
// whole numbers
$numberToWords->convertCurrency(1); // "jedna koruna"
$numberToWords->convertCurrency(102); // "jedno sto dvě koruny"

// negative numbers
$numberToWords->convertCurrency(-646); // "mínus šest set čtyřicet šest korun"

// decimals
$numberToWords->convertCurrency(3.14); // "tři koruny čtrnáct haléřů"
$numberToWords->convertCurrency(5.02); // "pět korun dva haléře"

// custom separators
$numberToWords->convertCurrency(345, ''); // "třistačtyřicetpětkorun"
```
When converting currency with decimals, decimals are rounded to 2 numbers.