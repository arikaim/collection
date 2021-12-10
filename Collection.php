<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection;

use Arikaim\Core\Collection\Interfaces\CollectionInterface;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Number;
use Traversable;

/**
 * Collection base class
 */
class Collection implements CollectionInterface, \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * Collection items data
     *
     * @var array
     */
    protected $data = [];
    
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = []) 
    {  
        $this->data = $data;
    }

    /**
     * Create colleciton
     *
     * @param array $data
     * @return Collection
     */
    public static function create(array $data)
    {
        return new Self($data);
    } 

    /**
     * Create colection form json file
     *
     * @param string $fileName
     * @param string|null $root
     * @param array|null $vars
     * @return Collection
     */
    public static function createFromFile(string $fileName, ?string $root = null, ?array $vars = null) 
    {      
        $data = File::readJsonFile($fileName,$vars);
       
        $data = (\is_array($data) == true) ? $data : [];
        $data = $data[$root] ?? $data;
        
        return new Self($data);
    }

    /**
     * Return iterator
     *
     * @return ArrayIterator
     */
    public function getIterator(): Traversable 
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Return true if key exists in collection 
     *
     * @param string $key
     * @return bool
    */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Return true if key exists in collection 
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return \array_key_exists($key,$this->data);
    }

    /**
     * Union arrays
     *
     * @param array $data
     * @return Collection
     */
    public function union(array $data)
    {
        $this->data = $this->data + $data;

        return $this;
    }

    /**
     * Replace array
     *
     * @param array $replacement
     * @return Collection
     */
    public function replace(array $replacement)
    {
        $this->data = \array_replace($this->data,$replacement);

        return $this;
    }

    /**
     * Get item 
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key) 
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set item
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Remove item
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Remove item
     *
     * @param string $key
     * @return Collection
     */
    public function remove(string $key)
    {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * Remove empy items 
     *
     * @return void
     */
    public function removeEmptyItems(): void
    {
        $this->data = \array_filter($this->data,function($value) { 
            return (!is_null($value) && $value !== ''); 
        });
    }

    /**
     * Return collection items count.
     *
     * @return integer
     */
    public function count(): int
    {
        return \count($this->data);
    }

    /**
     * Set bool value
     *
     * @param string $path
     * @param integer|string $value
     * @return void
     */
    public function setBooleanValue(string $path, $value)
    {
        if (\is_numeric($value) == true) {
            $value = (\intval($value) > 0);
        }
        if (\is_string($value) == true) {
            $value = ($value === 'true');
        }

        $this->setValue($path,$value);
    }

    /**
     * Set item value by path
     *
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function setValue(string $path, $value): void
    {
        $this->data = Arrays::setValue($this->data,$path,$value);
    }

    /**
     * Merge collection items
     *
     * @param string $key
     * @param array $data
     * @param boolean $recursive
     * @return void
     */
    public function merge(string $key, array $data, bool $recursive = false): void
    {
        if (isset($this->data[$key]) == false) {
            $this->data[$key] = [];
        }       
        $this->data[$key] = ($recursive == false) ? \array_merge($this->data[$key],$data) : \array_merge_recursive($this->data[$key],$data);
    }

    /**
     * Merge all collection items
     *
     * @param array $data
     * @param boolean $recursive
     * @return void
     */
    public function mergeItems(array $data, bool $recursive = false): void
    {            
        $this->data = ($recursive == false) ? \array_merge($this->data,$data) : \array_merge_recursive($this->data,$data);
    }

    /**
     * Set item value in collection
     *
     * @param string $key Key Name
     * @param mixed $value Value
     * @return Collection
     */
    public function set(string $key, $value) 
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Add item to collection
     *
     * @param string $key key name
     * @param mixed $value
     * @return void
     */
    public function add(string $key, $value): void 
    {
        if (isset($this->data[$key]) == false) {
            $this->data[$key] = [];
        }       
        \array_push($this->data[$key],$value);
        $this->data[$key] = \array_values(\array_unique($this->data[$key]));
    }
    
    /**
     * Push value to collection
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $subKey   
     * @return boolean
     */
    public function push(string $key, $value, ?string $subKey = null): bool 
    {
        if ($subKey != null) {
            if (isset($this->data[$key][$subKey]) == false) {
                $this->data[$key][$subKey] = [];
            } 
            \array_push($this->data[$key][$subKey],$value);  
            $this->data[$key][$subKey] = \array_unique($this->data[$key][$subKey],SORT_REGULAR);
        } else {
            if (isset($this->data[$key]) == false) {
                $this->data[$key] = [];
            }    
            \array_push($this->data[$key],$value);  
            $this->data[$key] = \array_unique($this->data[$key],SORT_REGULAR);
        }
        
        return true;
    }

    /**
     * Add value to begining of collection array
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $subKey   
     * @return boolean
     */
    public function prepend(string $key, $value, ?string $subKey = null): bool 
    {
        if ($subKey != null) {
            if (isset($this->data[$key][$subKey]) == false) {
                $this->data[$key][$subKey] = [];
            } 
            \array_unshift($this->data[$key][$subKey],$value);  
            $this->data[$key][$subKey] = \array_unique($this->data[$key][$subKey],SORT_REGULAR);
        } else {
            if (isset($this->data[$key]) == false) {
                $this->data[$key] = [];
            }    
            \array_unshift($this->data[$key],$value);  
            $this->data[$key] = \array_unique($this->data[$key],SORT_REGULAR);
        }
        return true;
    }
    
    /**
     * Set collection data
     *
     * @param array $data
     * @return Collection
     */
    public function withData(array $data)
    {
        $this->data = $data;

        return $this;        
    }

    /**
     * Slice collecion by keys
     *
     * @param array|string $keys
     * @return array
     */
    public function slice($keys)
    {
        return Arrays::sliceByKeys($this->data,$keys);
    }

    /**
     * Return collection array 
     *
     * @return array
     */
    public function toArray(): array
    {
        return \is_array($this->data) ? $this->data : [];
    }

    /**
     * Return true if key exists and value not empty in collection
     *
     * @param string $key Name
     * @return boolean
     */
    public function isEmpty(string $key): bool
    {
        return (isset($this->data[$key]) == false) ? true : empty($this->data[$key]);      
    }

    /**
     * Get collection item
     *
     * @param string $key
     * @return Collection
     */
    public function getCollection(string $key)
    {
        return new Self($this->get($key,[]));
    }

    /**
     * Get value from collection
     *
     * @param string $key Name
     * @param mixed $default If key not exists return default value
     * @return mixed
     */
    public function get(string $key, $default = null)
    {       
        return $this->data[$key] ?? $default;          
    }

    /**
     * Get boolean value
     *
     * @param string $key
     * @param bool|null $default
     * @return bool
     */
    public function getBool(string $key, ?bool $default = null): bool
    {
        return Number::toBoolean($this->get($key,$default));
    }

    /**
     * Get text value
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getString(string $key, ?string $default = null): ?string
    {
        $value = $this->get($key,$default);
    
        return (\trim($value) == '') ? $default : (string)$value;
    }

    /**
     * Get int value
     *
     * @param string $key
     * @param int|null $default
     * @return integer|null
     */
    public function getInt(string $key, ?int $default = null): ?int
    {
        $value = $this->get($key,$default);
    
        return (empty($value) == true) ? $default : (int)$value;
    }

    /**
     * Get float value
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public function getFloat(string $key, ?float $default = null): ?float
    {
        $value = $this->get($key,$default);
    
        return (empty($value) == true) ? $default : (float)$value;
    }

    /**
     * Return array values
     *
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function getArray(string $key, $default = null): array
    {
        $result = $this->get($key,$default);

        return (\is_array($result) == false) ? [] : $result;
    }

    /**
     * Clear collection data
     *
     * @return void
     */
    public function clear(): void 
    {
        $this->data = [];
    }

    /**
     * Clone object
     *
     * @return Self
     */
    public function copy()     
    {
        return clone $this;
    }

    /**
     * Get value
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set value
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        return $this->set($key,$value);
    }

    /**
     * Get value by path
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function getByPath(string $path, $default = null)
    {
        $value = Arrays::getValue($this->data,$path);
        
        return $value ?? $default;
    }
    
    /**
     * Add value
     *
     * @param string $path
     * @param mixed $value
     * @return bool
     */
    public function addField(string $path, $value): bool
    {
        foreach ($this->data as $key => $item) {
            if (\is_array($item) == true) {
                $currentValue = Arrays::getValue($item,$path);
                if ($currentValue === null) {
                    $this->data[$key] = Arrays::setValue($item,$path,$value);
                }
            }
        }

        return true;
    }

    /**
     * Get collection items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->data;
    }
}
