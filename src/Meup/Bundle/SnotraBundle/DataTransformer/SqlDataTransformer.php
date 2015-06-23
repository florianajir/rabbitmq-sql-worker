<?php
namespace Meup\Bundle\SnotraBundle\DataTransformer;

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
     * @param array $data
     *
     * @return array
     */
    public function transform(array $data)
    {

    }
}
