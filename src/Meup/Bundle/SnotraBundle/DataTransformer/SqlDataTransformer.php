<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

use Exception;

/**
 * Class SqlDataTransformer
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlDataTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Prepare data from mapping rules
     *
     * @param array  $data
     * @param string $type
     *
     * @return array
     *
     * @throws Exception
     */
    public function prepare(array $data, $type)
    {
        $return = array();
        if (isset($this->mapping[$type])) {
            $tableName = $this->mapping[$type]['table'];
            $return[$tableName] = array();
            foreach ($data as $field => $value) {
                if (isset($this->mapping[$type]['fields'][$field])) {
                    $fieldMapping = $this->mapping[$type]['fields'][$field];
                    // length check
                    if (isset($fieldMapping['length']) && strlen($value) > $fieldMapping['length']) {
                        throw new Exception($type . ' value length exceed database max length.');
                    }
                    $return[$tableName][$fieldMapping['column']] = $value;
                }
            }
        }

        return $return;
    }
}
