<?php
/**
 * PHP version 5
 *
 * @category Testing
 * @package  Snotra
 * @author   florianajir <florian@1001pharmacies.com>
 * @license  BSD Licence
 * @link     https://github.com/1001Pharmacies/snotra
 */
namespace Meup\Bundle\SnotraBundle\Model;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;

/**
 * Class GenericEntity
 * Data container model for any data message type
 *
 * @category Testing
 * @package  Snotra
 * @author   florianajir <florian@1001pharmacies.com>
 * @license  BSD Licence
 * @link     https://github.com/1001Pharmacies/snotra
 */
class GenericEntity implements GenericEntityInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Data array only
     *
     * @var array
     */
    protected $data;

    /**
     * Associative array ( field => value )
     *
     * @var array
     */
    protected $identifier;

    /**
     * Many to many relations array
     *
     * @var array
     */
    protected $manyToMany;

    /**
     * One to many relations array
     *
     * @var array
     */
    protected $oneToMany;

    /**
     * Many to one relations array
     *
     * @var array
     */
    protected $manyToOne;

    /**
     * One to one relations array
     *
     * @var array
     */
    protected $oneToOne;

    /**
     * Constructor, set the attributes from array
     *
     * @param string $table table name
     * @param array  $data  entity prepared data from DataTransformer
     */
    public function __construct($table, array $data)
    {
        $this->table = $table;
        $this->identifier = null;
        if (array_key_exists(DataTransformer::IDENTIFIER_KEY, $data)) {
            $identifierKey = $data[DataTransformer::IDENTIFIER_KEY];
            if (isset($data[$identifierKey])) {
                $this->identifier = array(
                    $identifierKey => $data[$identifierKey]
                );
            }
            unset($data[DataTransformer::IDENTIFIER_KEY]);
        }
        $this->oneToOne = array();
        $this->manyToOne = array();
        $this->oneToMany = array();
        $this->manyToMany = array();
        if (array_key_exists(DataTransformer::RELATED_KEY, $data)) {
            $related = $data[DataTransformer::RELATED_KEY];
            if (isset($related[DataMapper::RELATION_ONE_TO_ONE])) {
                $this->oneToOne = $related[DataMapper::RELATION_ONE_TO_ONE];
            }
            if (isset($related[DataMapper::RELATION_MANY_TO_ONE])) {
                $this->manyToOne = $related[DataMapper::RELATION_MANY_TO_ONE];
            }
            if (isset($related[DataMapper::RELATION_ONE_TO_MANY])) {
                $this->oneToMany = $related[DataMapper::RELATION_ONE_TO_MANY];
            }
            if (isset($related[DataMapper::RELATION_MANY_TO_MANY])) {
                $this->manyToMany = $related[DataMapper::RELATION_MANY_TO_MANY];
            }
            unset($data[DataTransformer::RELATED_KEY]);
        }
        $this->data = $data;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the entity identifier => value array
     *
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the entity identifier array
     *
     * @param array $identifier entity identifier associative array (id => value)
     *
     * @return self
     */
    public function setIdentifier(array $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get the entity data set
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the entity manyToOne relations array
     *
     * @return array
     */
    public function getManyToOneRelations()
    {
        return $this->manyToOne;
    }

    /**
     * Get the entity manyToMany relations array
     *
     * @return array
     */
    public function getManyToManyRelations()
    {
        return $this->manyToMany;
    }

    /**
     * Get the entity oneToMany relations array
     *
     * @return array
     */
    public function getOneToManyRelations()
    {
        return $this->oneToMany;
    }

    /**
     * Get the entity oneToOne relations array
     *
     * @return array
     */
    public function getOneToOneRelations()
    {
        return $this->oneToOne;
    }

    /**
     * Merge new data on current entity data
     *
     * @param array $data data set to merge
     *
     * @return self
     */
    public function addDataSet(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Get an entity property from data
     *
     * @param string $property data index to return
     *
     * @return string
     */
    public function getProperty($property)
    {
        $value = null;
        if (array_key_exists($property, $this->data)) {
            $value = $this->data[$property];
        }

        return $value;
    }
}
