<?php
namespace Dini108\CurrencyConverter\Model;

class Items
{
    public $currency;

    public $rate;

    /**
     * Rate constructor.
     */
    public function __construct($currency, $rate)
    {
        $this->currency = $currency;
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }


    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

}
