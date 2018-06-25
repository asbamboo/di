<?php
namespace asbamboo\di\_test;

use asbamboo\di\exception\NotExistedServiceMappingException;
use PHPUnit\Framework\TestCase;
use asbamboo\di\ServiceMappingCollection;
use asbamboo\di\ServiceMapping;
use asbamboo\di\_test\fixtures\Service1;

/**
 * test asbamboo\di\ServiceMappingCollection
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月16日
 */
class ServiceMappingCollectionTest extends TestCase
{
    static $Collection;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp()
    {
        if(! static::$Collection){
            static::$Collection = new ServiceMappingCollection();
        }
    }

    /**
     *
     */
    public function testGetNotExisted()
    {
        $this->expectException(NotExistedServiceMappingException::class);

        static::$Collection->get('id');
    }

    /**
     *
     */
    public function testAdd()
    {
        $ServiceMapping     = new ServiceMapping(['id' => 'id', 'class' => Service1::class, 'init_params' => ['1','2']]);
        static::$Collection = static::$Collection->add($ServiceMapping);

        $GetServiceMapping      = static::$Collection->get($ServiceMapping->getId());
        $has_service_mapping    = static::$Collection->has($ServiceMapping->getId());
        $count                  = static::$Collection->count();

        $this->assertTrue($has_service_mapping);
        $this->assertEquals(1, $count);
        $this->assertEquals($GetServiceMapping->getId(), $ServiceMapping->getId());
        $this->assertEquals($GetServiceMapping->getClass(), $ServiceMapping->getClass());
        $this->assertEquals($GetServiceMapping->getInitParams(), $ServiceMapping->getInitParams());

        return $ServiceMapping;
    }

    /**
     * @depends testAdd
     */
    public function testRemove($ServiceMapping)
    {
        static::$Collection->remove($ServiceMapping->getId());

        $this->assertCount(0, static::$Collection->getIterator());
    }
}