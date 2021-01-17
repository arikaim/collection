<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Traits;

use Arikaim\Core\Collection\Properties;
use Arikaim\Core\Collection\PropertiesFactory;

/**
 * Config properties 
*/
trait ConfigProperties 
{  
    /**
     * Config properties collection
     *
     * @var Properties|null
     */
    protected $configProperties = null;

    /**
     * Create config properties array
     *    
     * @return array
     */
    public function createConfigProperties(): array
    {
        $properties = new Properties([],false);   
        $callback = function() use($properties) {
            $this->initConfigProperties($properties);           
            return $properties;
        };
      
        return $callback()->toArray();     
    }
 
    /**
     * Get config properties collection
     *
     * @return Properties
     */
    public function getConfigProperties(): Properties
    {
        return (empty($this->configProperties) == true) ? new Properties([],false) : $this->configProperties;
    }

    /**
     * Get config properties collection
     *
     * @param Properties|array|string $properties
     * @return void
     */
    public function setConfigProperties($properties): void
    {
        if (\is_string($properties) == true) {
            $properties = \json_decode($properties,true);
        }
        if (\is_array($properties) == true) {
            $properties = PropertiesFactory::createFromArray($properties); 
        }
       
        $this->configProperties = $properties;
    }
}
