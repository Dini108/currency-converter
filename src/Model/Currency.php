<?php
namespace Dini108\CurrencyConverter\Model;

class Currency
{
    public $name;

    /**
     * Currency constructor.
     *
     * @param $name
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
}
