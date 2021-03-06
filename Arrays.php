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

/**
 * Array helpers
 */
class Arrays 
{
    /**
     * Recursive array count
     *
     * @param mixed $array
     * @return integer
     */
    public static function recursiveCount($array): int 
    {
        if (\is_array($array) == false) {
            return 1;
        }

        $count = 0;
        foreach($array as $item) {
            $count += Self::recursiveCount($item);
        }

        return $count;
    }

    /**
     * Append values from append array to array 
     *
     * @param array $array
     * @param array $append
     * @return array
     */
    public static function arrayAppend(array $array, array $append): array
    {
        foreach($append as $key => $value) {
            if (isset($array[$key]) == false) {
                $array[$key] = (\is_array($value) == true) ? [] : $value;
            } 
            $array[$key] = (\is_array($value) == true) ? \array_unique(\array_merge($array[$key],$value)) : $value;               
        }   

        return $array;
    }

    /**
     * Unique multidimensional array
     *
     * @param array $array
     * @return array
     */
    public static function uniqueMultidimensional(array $array): array
    {
        $serialized = \array_map('serialize', $array);
        $unique = \array_unique($serialized);
        
        return \array_intersect_key($array, $unique);
    }

    /**
     * Recursive insert array
     *
     * @param array $array
     * @param array $insert
     * @return array
     */
    public static function arrayInsert(array $array,array $insert): array 
    {      
        if (\is_array($array) == true && \is_array($insert) == true) {
            foreach ($insert as $key => $value) {              
                if (isset($array[$key]) == true && \is_array($value) == true && \is_array($array[$key]) == true)  {
                    $array[$key] = Self::arrayInsert($array[$key],$value);
                }  else  {
                    $array[$key] = $value;
                }                     
            }
        }  

        return $array;
    }

    /**
     * Return array with unique values 
     *
     * @param array $array
     * @return array
     */
    public static function unique(array $array): array 
    {
        return \array_keys(\array_flip($array));
    } 

    /**
     * Set array value
     *
     * @param array $array
     * @param string $path
     * @param mixed $value
     * @param string $separator
     * @return array
     */
    public static function setValue($array, $path, $value, $separator = '/') 
    {
        if (!$path) {
            return null;
        }   
        $segments = \is_array($path) ? $path : \explode($separator,$path);
        $current = &$array;

        foreach ($segments as $segment) {
            if (isset($current[$segment]) == false) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
        $current = $value;

        return $array;
    }

    /**
     * Return true if array is associative
     *
     * @param array $array
     * @return boolean
     */
    public static function isAssociative(array $array): bool
    {
        if ([] === $array) {
            return false;
        }

        return (\array_keys($array) !== \range(0,\count($array) - 1));
    }

    /**
     * Get default value
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getDefaultValue(array $array, string $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    /**
     * Get array value by key path
     *
     * @param array $array
     * @param string|array $path
     * @param string $separator
     * @return mixed
     */
    public static function getValue($array, $path, string $separator = '/') 
    {    
        if (empty($path) == true) {
            return null;
        }

        $pathParts = \is_array($path) ? $path : \explode($separator, $path);
        $reference = &$array;
        foreach ($pathParts as $key) {           
            $reference = &$reference[$key];
        }

        return $reference;                
    }

    /**
     * Get array value
     *
     * @param array $array
     * @param string $keySearch
     * @return array|null
     */
    public static function getValues(array $array, $keySearch): ?array
    {
        if (\is_array($array) == false) {
            return null;
        }
        $len = \strlen($keySearch);
        $result = [];

        foreach ($array as $key => $value) {
            if (\substr($key,0,$len) == $keySearch) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Merge arrays
     *
     * @param array $array1
     * @param array $array2
     * @param string $prevKey
     * @param string $fullKey
     * @return array
     */
    public static function merge(array $array1, array $array2, string $prevKey = '', string $fullKey = ''): array 
    {
        $result = $array1;
        if (\is_array($array2) == false) {
            return $result;
        }

        foreach ($array2 as $key => &$value) {
            if ($fullKey != '') { 
                $fullKey .= '/'; 
            }
            $fullKey .= $key;
            if (\is_array($value) && isset($result[$key]) && \is_array($result[$key])) {     
                $result[$key] = Self::merge($result[$key],$value,$key,$fullKey);
            } else {
                $fullKey = \str_replace("0/",'',$fullKey);
                $result[$key] = $value;               
                $fullKey = \str_replace('/' . $prevKey . '/' . $key,'',$fullKey);
            }
        }

        return $result;
    }

    /**
     * Convert array to path
     *
     * @param array $array
     * @return string
     */
    public static function toPath(array $array): string 
    {    
        $path = '';
        if (count($array) > 1) {          
            for ($i = 0; $i < count($array); $i++) { 
                $path .= $array[$i] . DIRECTORY_SEPARATOR;
            }
            $result = \rtrim($path,DIRECTORY_SEPARATOR);
        } else {
            $result = \end($array);
        }

        return $result;
    }

    /**
     * Convert text to array
     *
     * @param string $text
     * @param string|null $separator
     * @return array
     */
    public static function toArray(string $text, ?string $separator = null): array 
    {
        if (\is_array($text) == true) {
            return $text;
        }

        $separator = $separator ?? PHP_EOL;
        $result = \explode($separator,\trim($text));    
        
        return (\is_array($result) == false) ? [] : $result;
    }

    /**
     * Convert array values to string 
     *
     * @param array $array
     * @param string|null $separator
     * @return string
     */
    public static function toString(array $array, ?string $separator = null): string 
    {
        if (\count($array) == 0) {
            return '';
        }
        $separator = $separator ?? PHP_EOL;

        return \implode($separator,$array);
    }

    /**
     * Convert object to array
     *
     * @param object $object
     * @return array
     */
    public static function convertToArray($object): array 
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $result = [];
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $result[$name] = $property->getValue($object);
            $property->setAccessible(false);
        }

        return $result;
    }

    /**
     * Return true if array have sub items
     *
     * @param mixed $array
     * @return bool
     */
    public static function haveSubItems($array): bool
    {
        if (\is_array($array) == false) {
            return false;
        }

        foreach ($array as $item) {           
            if (\is_array($item) == true) {               
                return true;
            }
        }

        return false;
    } 

    /**
     * Set default value if key not exist in array
     *
     * @param array $array
     * @param string|int $key
     * @param mixed $value
     * @return array
     */
    public static function setDefault(array $array, $key, $value): array
    {   
        $array[$key] = $array[$key] ?? $value;
       
        return $array;
    }

    /**
     * Slice array by keys
     *
     * @param array $array
     * @param array|string|null $keys
     * @return array
     */
    public static function sliceByKeys(array $array, $keys = null) 
    {
        $keys = (empty($keys) == true) ? \array_keys($array) : $keys;
        $keys = (\is_array($keys) == false) ? [$keys] : $keys;
    
        return \array_intersect_key($array,\array_fill_keys($keys,'1'));    
    }

    /**
     * Remove empty values from array
     *
     * @param array $array
     * @return array
     */
    public static function removeEmpty(array $array)
    {
        return \array_filter($array,function($value) {
            return !empty($value) || $value === 0;
        }); 
    }

    /**
     * Filer array columns
     *
     * @param array $data
     * @param array $keys
     * @return array
     */
    public static function arrayColumns(array $data, array $keys)
    {    
        $keys = \array_flip($keys);
        $filtered = \array_map(function($item) use($keys) {
            return \array_intersect_key($item,$keys);
        },$data);

        return $filtered;
    }
}
