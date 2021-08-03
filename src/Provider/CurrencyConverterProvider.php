<?php
namespace Dini108\CurrencyConverter\Provider;

use Dini108\CurrencyConverter\Model\Rate;
use Dini108\CurrencyConverter\Model\Items;

use Dini108\CurrencyConverter\Exception\ProviderNotAvailableException;

class CurrencyConverterProvider extends BaseProvider
{
    protected $baseUrl = "https://free.currconv.com/api/v7/";
    protected $convertUrl = "convert";

    /**
     * CurrencyConverterProvider constructor.
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

        if (isset($error_msg) && empty($data)) {
            //TODO: history for provider errors
            throw new ProviderNotAvailableException();
        }

        return json_decode($data, true);
    }

    /**
     * @param float $fromValue
     * @return array
     * @throws ProviderNotAvailableException
     * @throws \JsonException
     */
    public function convert(float $fromValue): array
    {
        $rate = $this->getEndPointResult(
            $this->convertUrl.'?q='.$this->from.'_'.$this->to.'&compact=y&apiKey='.$this->apiKey);

        return [
            'from' => $this->from,
            'to' => $this->to,
            'fromValue' => $fromValue,
            'result' => (float)number_format($rate[$this->from.'_'.$this->to]['val'] * $fromValue,3)
        ];
    }

    /**
     * @param string $fromRate
     * @return Rate
     * @throws \Dini108\CurrencyConverter\Exception\ProviderNotAvailableException
     * @throws \JsonException
     */
    public function rate(string $fromRate): Rate
    {
        $rates = [];
        $preparedCurrencyArray = $this->generateFromRateQueryString($fromRate);
        foreach($preparedCurrencyArray as $currencyId){
            $rates[] = $this->getEndPointResult(
                $this->convertUrl.'?q='.$currencyId.'&compact=y&apiKey='.$this->apiKey);
        }
        return $this->prepareRates($fromRate,$rates);
    }

    /**
     * @param $fromRate
     * @return array
     */
    private function generateFromRateQueryString($fromRate): array
    {
        $currency_array = $this->currencyIds;
        unset($currency_array[array_search($fromRate, $currency_array, true)]);
        $preparedQuery = [];

        foreach ($currency_array as $currencyId){
            $preparedQuery[] = $fromRate . '_' . $currencyId;
        }

        return $preparedQuery;
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
            foreach ($rate as $index => $ra) {
                $preparedRatesForRate[] = new Items(str_replace($rateName.'_', '', $index), $ra['val']);
            }
        }

        $rate = new Rate($rateName);
        $rate->setRates($preparedRatesForRate);

        return $rate;
    }
}
