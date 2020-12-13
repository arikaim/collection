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

use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Collection\Property;
use Arikaim\Core\Collection\Interfaces\PropertyInterface;
use Arikaim\Core\Collection\Interfaces\CollectionInterface;

/**
 * Properties collection
 */
class Properties extends Collection implements CollectionInterface
{ 
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = []) 
    {
        parent::__construct($data);
    }

    /**
     * Set property 
     *
     * @param string $name
     * @param array|object|string|Callable $descriptor
     * @return Properties
     */
    public function property($name, $descriptor)
    {
        if (\is_array($descriptor) == true) {
            $property = Property::create($descriptor);
        }
        if (\is_object($descriptor) == true) {
            $property = $descriptor;
        }
        if (\is_string($descriptor) == true) {
            $property = Property::createFromText($descriptor);
        }
        if (\is_callable($descriptor) == true) {
            $property = new Property($name);
            $callback = function() use($property,$descriptor) {
                $descriptor($property);
                return $property;
            };
            $property = $callback();          
        }

        $group = $property->getGroup();
        if ($property->isGroup() == true) {
            $this->add('groups',$property->getValue());
        }
        if (empty($group) == false) {           
            $this->data[$group][$name] = $property->toArray();
        } else {           
            $this->data[$name] = $property->toArray();
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface
     */
    public function getProperty($name)
    {
        return (isset($this->data[$name]) == true) ? $this->data[$name] : new Property($name);
    }

    /**
     * Get properties, return Property objects array
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->data;
    }

    /**
     * Get property value
     *
     * @param string $key
     * @return mixed
     */
    public function getValue($key, $group = null)
    {
        if ($this->has($key) == false) {
            return null;
        }
        $property = (empty($group) == true) ? $this->get($key) : $this->data[$group][$key];
        $default = $property['default'] ?? null;

        if ($property['type'] == Property::BOOLEAN_TYPE) {
            return $property['value'] ?? $default;
        }
        $value = \trim($property['value'] ?? '');

        return (empty($value) == true) ? \trim($default) : $value;
    }

     /**
     * Get property value
     *
     * @param string $key
     * @return mixed
     */
    public function getValueAsText($key, $group = null)
    {
        $value = $this->getValue($key,$group);
        $type = $this->getType($key,$group);

        switch($type) {
            case Property::BOOLEAN_TYPE:              
                return (empty($value) == true || $value == 0 || $value == '0') ? 'false' : 'true';
            break;
        }

        return (string)$value;
    }

    /**
     * Get property type
     *
     * @param string $key
     * @return int|null
     */
    public function getType($key, $group = null)
    {
        if ($this->has($key) == false) {
            return null;
        }
        $property = (empty($group) == true) ? $this->get($key) : $this->data[$group][$key];

        return $property['type'] ?? null;        
    }

    /**
     * Get groups
     *
     * @return array
     */
    public function getGroups() 
    {
        $result = [];
        foreach ($this->data as $key => $property) {
            if (isset($property['type']) == true) {
                if ($property['type'] == Property::GROUP) {
                    $result[] = $property;
                }              
            }
        }    

        return $result;
    }

    /**
     * Get properties list
     *
     * @param boolean|null $editable
     * @param boolean|null $hidden
     * @return array
     */
    public function gePropertiesList($editable = null, $hidden = null, $group = null)
    {
        $result = [];
        $data = (empty($group) == false) ? $this->data[$group] : $this->data;
        $groups = $this->get('groups',[]);

        foreach ($data as $key => $property) {           
            if (\in_array($key,$groups) === true && empty($groups) == false) {               
                continue;
            }
            if ($key == 'groups') {
                continue;
            }
            if ($property['type'] == Property::GROUP) {              
                continue;
            }
                 
            $propertyValue = $property['value'] ?? null;
            $property['value'] = (empty($propertyValue) == true) ? $property['default'] : $propertyValue;
            
            $itemGroup = $property['group'] ?? null;
          
            if (empty($itemGroup) == false && $itemGroup != $group) {             
                continue;
            }
            if ($editable == true) {
                if ($property['readonly'] == false && $property['hidden'] == false) {
                    $result[] = $property;
                }                 
            }                
            if ($editable == false) {
                if ($property['readonly'] == true || $property['hidden'] == true) {
                    $result[] = $property;
                }                 
            }

            if (empty($hidden) == false) {
                if ($property['hidden'] == $hidden) {
                    $result[] = $property;
                } 
            }
        }    

        return $result;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        $result = [];
        $groups = $this->get('groups',[]);

        foreach ($this->data as $key => $property) {
            if ($key == 'groups') {
                continue; 
            }
            if (\in_array($key,$groups) === true) {   
                foreach ($property as $name => $item) {
                    $default = $item['default'] ?? null;
                    $value = (empty($item['value']) == true) ? $default : $item['value'];
                    $result[$key][$name] = $value;
                }
                continue;                           
            } 
            if ($property['type'] == Property::GROUP) {
                continue;
            }

            $value = (empty($property['value']) == true) ? $property['default'] : $property['value'];
            $result[$key] = $value;         
        }    

        return $result;
    }

    /**
     * Clear property values
     *
     * @return void
     */
    public function clearValues()
    {
        $groups = $this->get('groups',[]);

        foreach ($this->data as $key => $value) {
            if (\in_array($key,$groups) === true) {
                foreach ($value as $name => $item) {
                    $this->data[$key][$name]['value'] = $this->data[$key][$name]['default'] ?? null;
                }
            } else {
                $this->data[$key]['value'] = $this->data[$key]['default'] ?? null;
            }    
        }
    }

    /**
     * Set value of every property from data array
     *
     * @param array $data
     * @return void
     */
    public function setPropertyValues(array $data)
    {
        $groups = $this->get('groups',[]);

        foreach ($data as $key => $value) {
            if (\in_array($key,$groups) === true) {
                foreach ($value as $name => $item) {
                    $this->data[$key][$name]['value'] = $item;
                }
            } else {
                $this->data[$key]['value'] = $value;        
            }    
        }
    }
}
