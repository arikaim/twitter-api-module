<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\TwitterApi\Functions;

use Arikaim\Modules\Api\AbstractApiFunction;
use Arikaim\Modules\Api\Interfaces\ApiFunctionInterface;

/**
 * Create tweet
 */
class StatusesUpdate extends AbstractApiFunction implements ApiFunctionInterface
{
    /**
     * Initialize api funciton
     *
     * @return void
     */
    public function init(): void
    {
        $this
            ->method('POST')
            ->path('statuses/update.json')
            ->paramsType('query');    
    }
}
