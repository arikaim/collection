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
 * Property interface
 */
interface PropertyInterface
{    
    /**
     * Get property name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get property value.
     *
     * @return mixed|null
     */
    public function getValue();

    /**
     * Get property display name.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Get property description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Get property type.
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get property default value.
     *
     * @return mixed|null
     */
    public function getDefault();

    /**
     * Get property required attribute.
     *
     * @return boolean
     */
    public function getRequired(): bool;

    /**
     * Get property help
     *
     * @return string|null
    */
    public function getHelp(): ?string;

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function isReadonly(): bool;

    /**
     * Get hidden attribute
     *
     * @return boolean
    */
    public function isHidden(): bool;
}
