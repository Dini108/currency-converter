<?php
namespace Dini108\CurrencyConverter\Exception;

/**
 * Class CurrencyNotFoundException
 */
class CurrencyNotFoundException extends \RuntimeException
{

    /**
     */
    public function __construct($currency,$message = null, $code = 0, \Exception $previous = null)
    {
        $this->currency = $currency;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string return the currency unknown
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
