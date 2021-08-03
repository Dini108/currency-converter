<?php
namespace Dini108\CurrencyConverter\Provider;

use Dini108\CurrencyConverter\Model\Rate;
use Dini108\CurrencyConverter\Model\Items;

use Dini108\CurrencyConverter\Exception\CurrencyNotFoundException;
use Dini108\CurrencyConverter\Exception\ProviderNotAvailableException;

class ExchangeRatesProvider extends BaseProvider
{
    protected $baseUrl = "http://api.exchangeratesapi.io/v1/";
    protected $rateUrl = "latest";

    /**
     * ExchangeRatesProvider constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $endpoint
     * @return mixed
     * @throws ProviderNotAvailableException
     * @throws \JsonException
     */
    private function getEndPointResult($endpoint)
    {
        $ch = curl_init($this->baseUrl.$endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_FAILONERROR,true);
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        if (isset($error_msg)) {
            throw new ProviderNotAvailableException();
        }

        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param float $fromValue
     * @return array
     * @throws ProviderNotAvailableException
     * @throws \JsonException
     */
    public function convert(float $fromValue): array
    {
        //Cant access convert function with free account
        //workaround
        $rates = $this->getEndPointResult($this->rateUrl.'?access_key='.$this->apiKey.
            '&symbols='.implode(',',$this->currencyIds));

        $rates = $this->calculateBaseRate($this->from,$rates['rates']);

        return [
            'from' => $this->from,
            'to' => $this->to,
            'fromValue' => $fromValue,
            'result' => (float)number_format($this->calculateConvertedValueFromRate($rates,$fromValue),3)
        ];
    }

    /**
     * @param string $fromRate
     * @throws \Dini108\CurrencyConverter\Exception\ProviderNotAvailableException
     * @throws \JsonException
     */
    public function rate(string $fromRate)
    {
        $rates = $this->getEndPointResult($this->rateUrl.'?access_key='.$this->apiKey.
            '&symbols='.implode(',',$this->currencyIds));

        $rates = $this->calculateBaseRate($fromRate,$rates['rates']);

        return $this->prepareRates($fromRate,$rates);
    }

    /**
     * @param $rateName
     * @param $rates
     * @return Rate
     */
    private function prepareRates($rateName,$rates): Rate
    {
        $preparedRatesForRate = [];

        foreach ($rates as $key => $rate) {
            $preparedRatesForRate[] = new Items($key, $rate);
        }

        $rate = new Rate($rateName);
        $rate->setRates($preparedRatesForRate);

        return $rate;
    }

    /**
     * @param $rates
     * @param $fromValue
     * @return float|int
     */
    private function calculateConvertedValueFromRate($rates,$fromValue){
        $conversionRate = 1;
        foreach ($rates as $key => $rate){
            if($this->to === $key){
                $conversionRate = $rate;
            }
        }
        return $fromValue * $conversionRate;
    }

    /**
     * @param $rateName
     * @param $rates
     * @return array
     */
    private function calculateBaseRate($rateName,$rates): array
    {
        //Cant change base currency "EUR" with free account
        //workaround
        $newRates = [];
        foreach ($rates as $key => $rate) {
            if ($key !== $rateName) {
                if ($key === 'EUR') {
                    $newRates[$key] = (float)number_format($rates['EUR'] / $rates[$rateName],4);
                } else {
                    $newRates[$key] = (float)number_format($rate / $rates[$rateName],4);
                }
            }
        }
        return $newRates;
    }
}
