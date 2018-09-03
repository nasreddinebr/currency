<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/2/18
 * Time: 3:47 PM
 */
namespace App\Command;

use App\Service\CurrencyGenerator;

class CreateUserCommand extends \Symfony\Component\Console\Command\Command
{
    private $currencyGenerator;

    public function __construct(CurrencyGenerator $_currencyGenerator){
        $this->currencyGenerator = $_currencyGenerator;

        parent::__construct();
    }

    protected function configure(){
        //Configure the command
        $this
            ->setName('app:currencies')   // the name of command
            ->setDescription('Exchange rate')   //Description of command
            ->setHelp('This command allows you to list you a list of exchange rate')   // "--help" option of the command
        ;

    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
        $output->writeln([
            'Exchange rate:',
            '==============',
            '',
        ]);
        $xml=simplexml_load_file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");

        $dateExchenge = new \DateTime($xml->Cube->Cube["time"]);
        $output->writeln([
            '',
            'Euro foreign exchange reference rates: ' . $dateExchenge->format('d M Y'),
            '====================================================',
            '',
        ]);
        $output->writeln([
            '',
            '-------------------------------------------------------',
            'All currencies quoted against the euro (base currency)',
            '-------------------------------------------------------',
            '',
        ]);

        $curecies = $this->currencyGenerator->getExchangeRate();
        foreach($curecies as $currency){
            $output->writeln($currency->getCurrency() . ' <====> '
                . number_format($currency->getRate(), 3,'.',' ') . ' => '
                . $currency->getGrowth() . '  '
                . ($currency->getAnomaly())?:$currency->getAnomaly());
        }


    }

}