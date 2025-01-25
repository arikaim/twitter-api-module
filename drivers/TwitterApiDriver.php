<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\TwitterApi\Drivers;

use Abraham\TwitterOAuth\TwitterOAuth;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;

/**
 * Twitter api driver class
 */
class TwitterApiDriver implements DriverInterface
{   
    use Driver;

    /**
     * Api client
     *
     * @var object|null
     */
    protected $apiClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('twitter','api','Twitter Api','Twitter Api driver');      
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {
        $this->apiClient = new TwitterOAuth(
            $properties->getValue('consumer_key'),
            $properties->getValue('consumer_key_secret'),
            $properties->getValue('access_token'),
            $properties->getValue('access_token_secret')
        );         
        
        $this->apiClient->setApiVersion('1.1');
    }

    /**
     * Upload media
     * @param string $fileName
     * @param array $options
     * @return object|mixed
     */
    public function uploadMedia(string $fileName, array $options = [])
    {
        $this->apiClient->setApiVersion(1.1);

        $media = $this->apiClient->upload('media/upload',[
            'media' => $fileName
        ], $options);

        $this->apiClient->setApiVersion(2);

        return $media;
    }

    /**
     * Set api version
     * @param mixed $version
     * @return object|null
     */
    public function version($version): object
    {
        $this->apiClient->setApiVersion($version);

        return $this->apiClient;
    }

    /**
     * Get api client
     * @return object|null
     */
    public function client(): object|null
    {
        return $this->apiClient;
    }

    /**
     * Has error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        if (\in_array($this->apiClient->getLastHttpCode(), [200,201])) {
            return false;
        } 

        return true;
    }

    /**
     * Get error message
     * @param mixed $response
     * @return string
     */
    public function getError($response): string
    {
        if ($response == null) {
            return 'Reseponse error';
        }

        $messge = $response->detail ?? null;

        return (empty($messge) == true) ? $response->errors[0]->message : '';
    }

    /**
     * Convert response to array
     * @param mixed $response
     * @return array
     */
    public function toArray($response): array
    {
        return ($response == null) ? [] : \json_decode(\json_encode($response),true);
    }

    /**
     * Create driver config properties array
     *
     * @param \Arikaim\Core\Collection\Properties $properties
     * @return void
     */
    public function createDriverConfig($properties)
    {
        $properties->property('consumer_key',function($property) {
            $property
                ->title('Consumer Key')
                ->type('text')   
                ->required(true)           
                ->value('');                         
        }); 

        $properties->property('consumer_key_secret',function($property) {
            $property
                ->title('Consumer Key Secret')
                ->type('key')   
                ->required(true)           
                ->value('');                         
        }); 

        $properties->property('access_token',function($property) {
            $property
                ->title('Access Token')
                ->type('text')   
                ->required(true)           
                ->value('');                         
        }); 

        $properties->property('access_token_secret',function($property) {
            $property
                ->title('Access Token Secret')
                ->type('key')   
                ->required(true)                    
                ->value('');                         
        }); 
    }
}
