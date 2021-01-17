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

use Arikaim\Core\Collection\Interfaces\PropertyInterface;

/**
 * Property descriptior
 */
class Property implements PropertyInterface
{
    const TEXT         = 0;
    const NUMBER       = 1;
    const CUSTOM       = 2;
    const BOOLEAN_TYPE = 3;
    const LIST         = 4;
    const PHP_CLASS    = 5;
    const PASSWORD     = 6;
    const URL          = 7;
    const TEXT_AREA    = 8;
    const GROUP        = 9;

    /**
     * Property type text names
     *
     * @var array
     */
    private $typeNames = [
        'text',
        'number',
        'custom',
        'boolean',
        'list',
        'class',
        'password',
        'url',
        'text-area',
        'group'
    ];

    /**
     * Property name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Property value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Dropdown items
     *
     * @var array
     */
    protected $items;

    /**
     * Group name
     *
     * @var string|null
     */
    protected $group = null;

    /**
     * Default value
     *
     * @var mixed
     */
    protected $default;

    /**
     * Property title
     *
     * @var string
     */
    protected $title;

    /**
     * Property type
     *
     * @var integer
     */
    protected $type;

    /**
     * Property description
     *
     * @var string
     */
    protected $description;

    /**
     * Property required atribute
     *
     * @var boolean
     */
    protected $required;

    /**
     * Property help
     *
     * @var string
     */
    protected $help;

    /**
     * Readonly attribute
     *
     * @var boolean
     */
    protected $readonly;

    /**
     * Hidden attribute
     *
     * @var boolean
     */
    protected $hidden;

    /**
     * Constructor
     *
     * @param string|null $name
     * @param mixed|null $value
     * @param mixed|null $default
     * @param string|int|null $type
     * @param string|null $title
     * @param string|null $description
     * @param boolean $required
     * @param string|null $help
     * @param array|null $items
     */
    public function __construct(
        ?string $name = null, 
        $value = null, 
        $default = null, 
        $type = Self::TEXT, 
        ?string $title = null, 
        ?string $description = null, 
        bool $required = false, 
        ?string $help = null, 
        ?array $items = null, 
        ?string $group = null
    ) 
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->title = $title;
        $this->default = $default;
        $this->description = $description;
        $this->required = $required;
        $this->help = $help;
        $this->items = $items ?? []; 
        $this->group = $group;
    }

    /**
     * Get readonly attribute
     *
     * @return boolean
     */
    public function isReadonly(): bool
    {
        return (empty($this->readonly) == true) ? false : $this->readonly;
    }

    /**
     * Return true if property is group
     *
     * @return boolean
    */
    public function isGroup(): bool
    {
        return ($this->type == Self::GROUP);
    }

    /**
     * Get hidden attribute
     *
     * @return boolean
    */
    public function isHidden(): bool
    {
        return (empty($this->hidden) == true) ? false : $this->hidden;
    }

    /**
     * Set property value
     *
     * @param mixed|null $value
     * @return Property
    */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set property items
     *
     * @param array $items
     * @return Property
    */
    public function items(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Set property type
     *
     * @param string|integer $type
     * @return Property
    */
    public function type($type)
    {
        $this->type = (\is_string($type) == true) ? $this->getTypeId($type) : $type;

        return $this;
    }

    /**
     * Set readonly attribute
     *
     * @param boolean $readonly
     * @return Property
    */
    public function readonly(bool $readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set hidden attribute
     *
     * @param boolean $hidden
     * @return Property
    */
    public function hidden(bool $hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Set property title
     *
     * @param string|null $title
     * @return Property
    */
    public function title(?string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set property required attribute
     *
     * @param boolean $required
     * @return Property
    */
    public function required(bool $required)
    {
        $this->required = (boolean)$required;
        return $this;
    }

    /**
     * Set property default
     *
     * @param mixed|null $default
     * @return Property
    */
    public function default($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Set property description
     *
     * @param string|null $description
     * @return Property
     */
    public function description(?string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set property help
     *
     * @param string $help
     * @return Property
     */
    public function help(?string $help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Set property name
     *
     * @param string $name
     * @return Property
     */
    public function name(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set property group
     *
     * @param string $name
     * @return Property
     */
    public function group(string $name)
    {
        $this->group = $name;
        return $this;
    }

    /**
     * Get type id
     *
     * @param string|int $type
     * @return int|null
     */
    public function getTypeId($type): ?int
    {
        $key = \array_search($type,$this->typeNames);       
        return ($key !== false) ? $key : null;
    }

    /**
     * Return property name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Return property items.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Return property required attribute.
     *
     * @return boolean
     */
    public function getRequired(): bool
    {
        return (empty($this->required) == true) ? false : $this->required;
    }

    /**
     * Return property group.
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Return property value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return (\is_null($this->value) == true) ? $this->getDefault() : $this->value;
    }

    /**
     * Return property version.
     *
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Return property display name.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return (empty($this->title) == true) ? $this->name : $this->title;
    }

    /**
     * Get property description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get property type
     *
     * @return integer|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * Get property type as text
     *
     * @return string
     */
    public function getTypeText()
    {
        $type = $this->getType();
        return (isset($this->typeNames[$type]) == true) ? $this->typeNames[$type] : 'unknow';
    }

    /**
     * Get property help
     *
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->getName(),
            'value'       => $this->getValue(),
            'title'       => $this->getTitle(),
            'description' => $this->description,
            'default'     => $this->getDefault(),
            'type'        => $this->getType(),
            'required'    => $this->required,
            'readonly'    => $this->isReadonly(),
            'hidden'      => $this->isHidden(),
            'items'       => $this->getItems(),
            'group'       => $this->group,
            'help'        => $this->help
        ];
    }
    
    /**
     * Create property obj from text
     *
     * @param string $text
     * @return Property
     */
    public static function createFromText(string $text)
    {
        $result = [];
        $tokens = \explode('|',$text);
        foreach ($tokens as $param) {
            $token = \explode('=',$param);
            $result[$token[0]] = $token[1];
        }
        
        return Self::create($result);
    }

    /**
     * Create property obj from array
     *
     * @param array $data
     * @return Property
     */
    public static function create(array $data)
    {
        $name = $data['name'] ?? null;
        $value = $data['value'] ?? null;
        $required = $data['required'] ?? false;
        $default = $data['default'] ?? null;
        $type = $data['type'] ?? Self::TEXT;
        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;
        $help = $data['help'] ?? null;
        $readonly = $data['readonly'] ?? false;
        $hidden = $data['hidden'] ?? false;
        $items = $data['items'] ?? null;
        $group = $data['group'] ?? null;

        $property = new Self($name,$value,$default,$type,$title,$description,$required,$help,$items,$group);
        
        return $property->readonly($readonly)->hidden($hidden);
    }
}
