<?php
namespace Dini108\CurrencyConverter;

use Dini108\CurrencyConverter\Provider\BaseProvider;
use Dini108\CurrencyConverter\Provider\ProviderInterface;
use Dini108\CurrencyConverter\Provider\ExchangeRatesProvider;
use Dini108\CurrencyConverter\Provider\CurrencyConverterProvider;

use Dini108\CurrencyConverter\Exception\MissingProviderApiKeyException;
use Dini108\CurrencyConverter\Exception\NoProvidersAvailableException;
use Dini108\CurrencyConverter\Exception\ProviderNotAvailableException;
use Dini108\CurrencyConverter\Exception\CurrencyNotFoundException;

/**
 * Class Currency
 */
class CurrencyConverter
{

    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @var string
     */
    protected string $from;

    /**
     * @var string
     */
    protected string $to;

    /**
     * @var string
     */
    private $providerName;


    public $provider;

    /**
     * @var int
     */
    private int $providerChange;

    /**
     * CurrencyConverter constructor.
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey,$providerName = "CurrencyConverter")
    {
        $this->apiKey = $apiKey;
        $this->providerName = $providerName;
        $this->providerChange = 0;

        switch ($this->providerName) {
            case "CurrencyConverter":
                $this->provider = new CurrencyConverterProvider($this->apiKey);
                break;
            case "ExchangeRates":
                $this->provider = new ExchangeRatesProvider($this->apiKey);
                break;
        }
    }

    /**
     * @param ProviderInterface $provider
     * @return self
     */
    private function setProviderInterface(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $value
     * @return self
     */
    public function from(string $value)
    {
        $this->from = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return self
     */
    public function to(string $value)
    {
        $this->to = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function currencies()
    {
        return $this->provider->currencies();
    }

    /**
     *
     * convert a currency from one to another one
     *
     * @param float $number
     * @return array
     */
    public function convert(float $number)
    {
        try {
            return $this->getConversion($number);
        } catch (ProviderNotAvailableException $e) {
            //Nem annyira szép megoldás
            try {
                $this->changeProvider();
            } catch (NoProvidersAvailableException $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
            return $this->convert($number);
        }
    }

    /**
     * @param float $number
     * @return array
     * @throws \Dini108\CurrencyConverter\Exception\ProviderNotAvailableException
     * @throws \JsonException
     */
    public function getConversion(float $number)
    {
        try {
            $this->validate();
            $this->provider->from($this->from);
            $this->provider->to($this->to);
            $this->provider->currencyInPredefinedList();
            return $this->provider->convert($number);
        } catch (MissingProviderApiKeyException|CurrencyNotFoundException $e ) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param string $fromRate
     * @return Rate
     */
    public function rate(string $fromRate)
    {
        try {
            return $this->getRates($fromRate);
        } catch (ProviderNotAvailableException $e) {
            //Nem annyira szép megoldás
            try {
                $this->changeProvider();
            } catch (NoProvidersAvailableException $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }

            return $this->rate($fromRate);
        }
    }

    /**
     * @param string $fromRate
     * @return array|\Dini108\CurrencyConverter\Model\Rate|mixed
     * @throws \Dini108\CurrencyConverter\Exception\ProviderNotAvailableException
     * @throws \JsonException
     */
    public function getRates(string $fromRate)
    {
        try {
            $this->validate();
            $this->provider->currencyInPredefinedList($fromRate);
            return $this->provider->rate($fromRate);
        } catch (MissingProviderApiKeyException|CurrencyNotFoundException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     *
     * @throws MissingProviderApiKeyException thrown if the apiKey is missing
     */
    private function validate(): void
    {
        if ($this->apiKey === null || empty($this->apiKey)) {
            throw new MissingProviderApiKeyException('Valid API key must be provided');
        }

    }

    private function changeProvider(): void
    {
        if ($this->providerChange < 1){
            if ($this->provider instanceof CurrencyConverterProvider){
                $this->setProviderInterface(new ExchangeRatesProvider($this->apiKey));
            } else {
                $this->setProviderInterface(new CurrencyConverterProvider($this->apiKey));
            }

            $this->providerChange++;
        } else {
            throw new NoProvidersAvailableException('No provider available');
        }
    }
}
