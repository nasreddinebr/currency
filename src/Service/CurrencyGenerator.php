<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/2/18
 * Time: 4:36 PM
 */
namespace App\Service;

use App\Entity\Money;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyGenerator
{
    private $getDoctrineManager;

    public function __construct(EntityManagerInterface $_getDoctrineManager)
    {
        $this->getDoctrineManager = $_getDoctrineManager;
    }

    /**
     * @return array
     */
    public function getExchangeRate(){
        // Recuperate all data from eurofxref in XML format, and Interprets them into an object.
        $xmlObject=simplexml_load_file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");

        $storedCurrencies = $this->getDoctrineManager
            ->getRepository(Money::class)
            ->findAll();

        // Check if the database is empty to fill it, else we update it.
        if(!$storedCurrencies){
            $currencies = $this->addCurrencies($xmlObject);

        }else{
            $currencies = $this->updateCurrencies($xmlObject, $storedCurrencies);
        }

        return $currencies;

    }

    /**
     * @param $_xml
     */
    public function addCurrencies($_xml){
        $currencies = $this->arrayGenerator($_xml);
        foreach ($currencies as $currency){
            // Instantiate and hydrate the "Money" object.
            $money =new Money();
            $money->hydrate($currency);

            $this->getDoctrineManager->persist($money);

            $addedCurencies[] = $money;
        }

        $this->getDoctrineManager->flush();

        return $addedCurencies;
    }

    /**
     * @param $_currentCurrencies
     * @param $_storedCurrencies
     * @return array
     *
     * Update the currencies.
     */
    public function updateCurrencies($_currentCurrencies, $_storedCurrencies){
        $currentCurrencies = $this->arrayGenerator($_currentCurrencies);

        /* We go through the two arrays, then we check if the value of the "currency"
         * attribute of the first array corresponds to the attribute of the second array.
         * If this is the case; we check if the rate has changed or not if yes;
         * the growth rate is calculated.
         */
        foreach ($_storedCurrencies as $storedCurrency){
            foreach ($currentCurrencies as $currentCurrency){
                if ($storedCurrency->getCurrency() == $currentCurrency["currency"]){

                    if ($storedCurrency->getRate() != $currentCurrency["rate"]){
                        // Formula: "rateOfGrowth = (currentRate - storedRate)/storedRate"
                        $rateOfGrowth = ((float)$currentCurrency["rate"] - $storedCurrency->getRate())
                            / $storedCurrency->getRate();
                        $rateOfGrowth = number_format($rateOfGrowth, 5, '.',' ')*100;

                        (($rateOfGrowth >= 20.00) || ($rateOfGrowth <= -20.00))?
                            $storedCurrency->setAnomaly('Warning!!! it\'s an anomaly'):
                            $storedCurrency->setAnomaly('');

                        $storedCurrency->setGrowth($rateOfGrowth.'%');

                    }

                    $storedCurrency->hydrate($currentCurrency);

                    break;
                }
            }

            $this->getDoctrineManager->flush($storedCurrency);
            $updatedCurencies[] = $storedCurrency;
        }
        return $updatedCurencies;

    }

    /**
     * @param $_xml
     * @return array
     *
     * Initialization of array to hydrate the object.
     */
    private function arrayGenerator($_xml){
        $money = array();
        foreach($_xml->Cube->Cube->Cube as $exchange) {
            $money[] = array("time"=>new \DateTime($_xml->Cube->Cube["time"]), "currency"=>$exchange["currency"],
                "rate"=> $exchange["rate"]);
        }

        return $money;
    }

}