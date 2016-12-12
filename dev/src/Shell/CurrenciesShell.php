<?php

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;
use Psy\Shell as PsyShell;
use App\Shell\TableRegistry;

/**
 * Simple console wrapper around Psy\Shell.
 */
class CurrenciesShell extends Shell
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Currencies');
    }

    public function main()
    {
        
    }

    function updateCurrencies()
    {
        $this->Currencies->updateCurrencies();
        $this->out("Valor de indicadores actualizados");
    }
}
