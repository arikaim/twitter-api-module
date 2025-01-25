<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Modules\TwitterApi\Console;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Twitter account info command
 */
class TwitterAccountInfo extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('twitter:users:me');
        $this->setDescription('Twitter account info');               
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input,$output)
    {
        global $arikaim;

        $this->showTitle();

        $driver = $arikaim->get('driver')->create('twitter');
        $response = $driver->version(2)->get('users/me');
        
        if ($driver->hasError() == false) {
            $this->showCompleted();

            print_r($driver->toArray($response));
            return;
        } 
        
        $this->showError('Error run api');  
        $this->writeLn($driver->getError($response)); 
    }
}
