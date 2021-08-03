# Currency Converter


## Installation

Install **CurrencyConverter** through [Composer](http://getcomposer.org)

Create a composer.json file in your project with this content : 

```
{
    "require": {
        "dini108/currency-converter": "~1.0"
    } 
}
```

## Usage
Instantiate CurrencyConverter with exchangeRates or CurrencyConverter Api key
```
$currencyConverter = new \Dini108\CurrencyConverter\CurrencyConverter($apiKey);
```
## Examples

Get avaliable currencies

```
$currencies = $currencyConverter
    ->currencies();
```

Get rates for currency 

```
$rates = $currencyConverter
    ->rate('HUF');
```

Convert currency
```
$convertedValue = $currencyConverter
    ->from('EUR')
    ->to('HUF')
    ->convert(100);

```
