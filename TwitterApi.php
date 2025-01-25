<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\TwitterApi;

use Arikaim\Core\Extension\Module;

/**
 * Twitter Api client module class
 */
class TwitterApi extends Module
{
    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\TwitterApi\\Drivers\\TwitterApiDriver');
        $this->registerConsoleCommand('TwitterAccountInfo');
    }
}
