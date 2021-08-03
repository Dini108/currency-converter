<?php
namespace Dini108\CurrencyConverter\Provider;

use Dini108\CurrencyConverter\Model\Currency;
use Dini108\CurrencyConverter\Exception\CurrencyNotFoundException;

abstract class BaseProvider implements ProviderInterface
{

    protected $from;

    protected $to;

    public $apiKey;

    public array $currencyIds = ['HUF','USD','EUR','GBP'];

    /**
     *
     * The origin currency
     *
     * @param string $value
     * @return self
     */
    public function from(string $value)
    {
        $this->from = $value;
        return $this;
    }

    /**
     *
     * the currency desired
     *
     * @param string $value
     * @return self
     */
    public function to(string $value)
    {
        $this->to = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function currencies(): array
    {
        $currencies = [];
        foreach ($this->currencyIds as $currencyId){
            $currencies[] = new Currency($currencyId);
        }
        return $currencies;
    }

    /**
     * @return bool
     * @throws CurrencyNotFoundException
     */
    public function currencyInPredefinedList($fromRate = null){
        if ($this->to){
            if (in_array($this->to, $this->currencyIds,true)){
                return true;
            } else {
                throw new CurrencyNotFoundException($this->to, 'Currency not in predefined list');
            }
        }
        if ($this->from){
            if (in_array($this->from, $this->currencyIds,true)){
                return true;
            } else {
                throw new CurrencyNotFoundException($this->from,'Currency not in predefined list');
            }
        }
        if ($fromRate){
            if (in_array($fromRate, $this->currencyIds,true)){
                return true;
            } else {
                throw new CurrencyNotFoundException($fromRate,'Currency not in predefined list');
            }
        }
    }
}
