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

use Arikaim\Core\Arikaim;
use Arikaim\Modules\Api\AbstractApiClient;
use Arikaim\Modules\Api\Interfaces\ApiClientInterface;
use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Exception;

/**
 * Twitter api driver class
 */
class TwitterApiDriver extends AbstractApiClient implements DriverInterface, ApiClientInterface
{   
    use Driver;

    /**
     * Consumer key
     *
     * @var string|null
     */
    protected $consumerKey;

    /**
     * Consumer key secret
     *
     * @var string|null
     */
    protected $consumerKeySecret;

    /**
     * Access token
     *
     * @var string|null
     */
    protected $accessToken;

    /**
     * Access token secret
     *
     * @var string|null
     */
    protected $accessTokenSecret;

    /**
     * Bearer token
     *
     * @var string|null
     */
    protected $bearerToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('twitter-api','api','Twitter Api','Twitter Api driver');      
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {
        $this->setBaseUrl($properties->getValue('baseUrl'));    
        $this->consumerKey = $properties->getValue('consumer_key');
        $this->consumerKeySecret = $properties->getValue('consumer_key_secret');
        $this->accessToken = $properties->getValue('access_token');
        $this->accessTokenSecret = $properties->getValue('access_token_secret');
        $this->bearerToken = $properties->getValue('bearer_token');

        $this->setFunctionsNamespace('Arikaim\\Modules\\TwitterApi\\Functions\\');       
    }

    /**
     * Get authorization header or false if api not uses header for auth
     *
     * @return array|null
    */
    public function getAuthHeaders(?array $params = null): ?array
    {
        $url = $params['base_url'] . $params['url_path'];
        $method = $params['method'] ?? null;
        $apiData = $params['params'] ?? [];
        $postFields = $params['post_fields'] ?? [];
        
        if (empty($url) == true || empty($method) == true) {
            throw new Exception('Not vlaid request url or method.', 1);    
            return null;     
        }

        $keys = [
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_nonce'            => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token'            => $this->accessToken,
            'oauth_timestamp'        => time(),
            'oauth_version'          => '1.0'
        ];
        ksort($keys);
        $dataFields = \array_merge($keys,$apiData);  
        $dataFields = \array_merge($dataFields,$postFields);  

        $keys['oauth_signature'] = $this->createSignature($url,$method,$dataFields);

        foreach($keys as $key => $value) {
            $fields[] = "$key=\"" . \rawurlencode($value) . "\"";
        }

        return [
            'Authorization: OAuth ' . \implode(', ',$fields)
        ];
    }

    /**
     * Create sha1 signature
     *
     * @param string $url
     * @param string $method
     * @param array $fields
     * @return string
     */
    public function createSignature(string $url, string $method, array $fields): string
    {
        $keys = [];      
        foreach ($fields as $key => $value) {
            $keys[] = \rawurlencode($key) . '=' . \rawurlencode($value);
        }

        $data = $method . "&" . \rawurlencode($url) . '&' . \rawurlencode(\implode('&',$keys));
        $key = \rawurlencode($this->consumerKeySecret) . '&' . rawurlencode($this->accessTokenSecret);

        return \base64_encode(\hash_hmac('sha1',$data,$key,true));
    }

    /**
     * Get error field name
     *
     * @return string|null
    */
    public function getErrorFieldName(): ?string
    {
        return 'errors';
    }

    /**
     * Get error
     *
     * @param mixed $response
     * @return string|null
     */
    public function getError($response): ?string
    {
        if (\is_array($response) == true) {
            return $response[$this->getErrorFieldName()] ?? null;
        }

        return $response->getError();
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return void
     */
    public function createDriverConfig($properties)
    {
        $properties->property('baseUrl',function($property) {
            $property
                ->title('Base Url')
                ->type('text')
                ->default('https://api.twitter.com/1.1/')
                ->value('https://api.twitter.com/1.1/')
                ->readonly(true);              
        }); 
        
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
                ->type('text')   
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
                ->type('text')   
                ->required(true)                    
                ->value('');                         
        }); 

        $properties->property('bearer_token',function($property) {
            $property
                ->title('Bearer Token')
                ->type('text')                     
                ->value('');                         
        }); 
    }
}
