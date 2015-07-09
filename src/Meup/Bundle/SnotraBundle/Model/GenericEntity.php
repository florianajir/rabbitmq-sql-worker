<?php
namespace Meup\Bundle\SnotraBundle\Model;

use Meup\Bundle\SnotraBundle\DataMapper\DataMapper;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformer;

/**
 * Class GenericEntity
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class GenericEntity implements GenericEntityInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $manyToMany;

    /**
     * @var array
     */
    protected $oneToMany;

    /**
     * @var array
     */
    protected $manyToOne;

    /**
     * @var array
     */
    protected $oneToOne;

    /**
     * @param string $table
     * @param array  $data
     */
    public function __construct($table, array $data)
    {
        $this->table = $table;
        $this->identifier = null;
        if (isset($data[DataTransformer::IDENTIFIER_KEY])) {
            $identifierKey = $data[DataTransformer::IDENTIFIER_KEY];
            if (isset($data[$identifierKey])) {
                $this->identifier = array(
                    $identifierKey => $data[$identifierKey]
                );
            }
            unset($data[DataTransformer::IDENTIFIER_KEY]);
        }
        $this->oneToOne = array();
        if (isset($data[DataTransformer::RELATED_KEY][DataMapper::RELATION_ONE_TO_ONE])) {
            $this->oneToOne = $data[DataTransformer::RELATED_KEY][DataMapper::RELATION_ONE_TO_ONE];
        }
        $this->manyToOne = array();
        if (isset($data[DataTransformer::RELATED_KEY][DataMapper::RELATION_MANY_TO_ONE])) {
            $this->manyToOne = $data[DataTransformer::RELATED_KEY][DataMapper::RELATION_MANY_TO_ONE];
        }
        $this->oneToMany = array();
        if (isset($data[DataTransformer::RELATED_KEY][DataMapper::RELATION_ONE_TO_MANY])) {
            $this->oneToMany = $data[DataTransformer::RELATED_KEY][DataMapper::RELATION_ONE_TO_MANY];
        }
        $this->manyToMany = array();
        if (isset($data[DataTransformer::RELATED_KEY][DataMapper::RELATION_MANY_TO_MANY])) {
            $this->manyToMany = $data[DataTransformer::RELATED_KEY][DataMapper::RELATION_MANY_TO_MANY];
        }
        if (isset($data[DataTransformer::RELATED_KEY])) {
            unset($data[DataTransformer::RELATED_KEY]);
        }
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param array $identifier
     *
     * @return self
     */
    public function setIdentifier(array $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getManyToOneRelations()
    {
        return $this->manyToOne;
    }

    /**
     * @return array
     */
    public function getManyToManyRelations()
    {
        return $this->manyToMany;
    }

    /**
     * @return array
     */
    public function getOneToManyRelations()
    {
        return $this->oneToMany;
    }

    /**
     * @return array
     */
    public function getOneToOneRelations()
    {
        return $this->oneToOne;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public function addDataSet(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * @param string $property
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
