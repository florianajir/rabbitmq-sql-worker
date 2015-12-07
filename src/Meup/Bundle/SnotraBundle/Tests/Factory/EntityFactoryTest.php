<?php
/**
 * This file is part of the Meup Snotra.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/snotra>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\SnotraBundle\Tests\Factory;

use Meup\Bundle\SnotraBundle\Factory\EntityFactory;

/**
 *
 */
class EntityFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $class = 'Meup\Bundle\SnotraBundle\Model\Entity';

    /**
     *
     */
    public function testFactoryInterface()
    {
        $factory = new EntityFactory($this->class);

        $this->assertInstanceOf('Meup\Bundle\SnotraBundle\Factory\EntityFactoryInterface', $factory);
    }

    /**
     *
     */
    public function testCreate()
    {
        $factory = new EntityFactory($this->class);

        $this->assertInstanceOf($this->class, $factory->create(array('_table' => 'test')));
    }

    /**
     *
     */
    public function testCreateAssumingException()
    {
        $this->setExpectedException('InvalidArgumentException');
        new EntityFactory('stdClass');
    }
}
