<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Interfaces;

/**
 * Collection interface
 */
interface CollectionInterface
{    
    /**
     * Delete all collection items
     *
     * @return void
     */
    public function clear();

    /**
     * Copy collection 
     *
     * @return void
     */
    public function copy();

    /**
     * Return true if collection item is empty
     *
     * @param string $key
     * @return boolean
     */
    public function isEmpty($key);

    /**
     * Convert collection to array
     *
     * @return array
     */
    public function toArray();

    /**
     * Get value from collection
     *
     * @param string $key Name
     * @param mixed $default If key not exists return default value
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get value by path
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function getByPath($path, $default = null);

    /**
     * Set item value in collection
     *
     * @param string $key Key Name
     * @param mixed $value Value
     * @return Collection
     */
    public function set($key, $value);
}
