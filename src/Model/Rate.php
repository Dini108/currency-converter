<?php
namespace Dini108\CurrencyConverter\Model;

class Rate
{
    public $name;

    public array $rates;

    /**
     * Rate constructor.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @param array $rates
     */
    public function setRates(array $rates)
    {
        $this->rates = $rates;
    }

    public function toArray()
    {
        /** @var Rate $this */
        return [
            'name' => $this->name,
            'rates' => $this->rates,
        ];
    }
}
