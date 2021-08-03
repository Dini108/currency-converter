<?php
namespace Dini108\CurrencyConverter\Provider;

interface ProviderInterface
{
    /**
     *
     * get currency list
     *
     * @return self
     */
    public function currencies() : array;

    /**
     * convert currency to another currency
     *
     * @param float $fromValue
     * @return mixed
     */
    public function convert(float $fromValue);


    /**
     * return the rates from the currency list
     * @param string $fromRate
     * @return mixed
     */
    public function rate(string $fromRate);

}
