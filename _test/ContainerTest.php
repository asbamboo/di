<?php
namespace asbamboo\di\_test;

use PHPUnit\Framework\TestCase;
use asbamboo\di\Container;
use asbamboo\di\ServiceMapping;
use asbamboo\di\ServiceMappingCollection;
use asbamboo\di\_test\fixtures\Service1;
use asbamboo\di\exception\NotFoundException;

/**
 * test [\asbamboo\di\Container]
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月16日
 */
class ContainerTest extends TestCase
{
    /**
     *
     * @return \asbamboo\di\Container
     */
    public function testGet()
    {
        $option             = ['id' => 'id', 'class' => Service1::class, 'init_params' => ['prop2' => '1', '2']];
        $ServiceMapping     = new ServiceMapping($option);
        $ServiceMappings    = new ServiceMappingCollection();
        $ServiceMappings    = $ServiceMappings->add($ServiceMapping);
        $Container          = new Container($ServiceMappings);
        $Service1           = $Container->get($ServiceMapping->getId());

        $this->assertInstanceOf(Service1::class, $Service1);
        $this->assertEquals($option['init_params'][0], $Service1->prop1);
        $this->assertEquals($option['init_params']['prop2'], $Service1->prop2);

        return $Container;
    }
    
    /**
     * @depends testGet
     */
    public function testFilterServiceParamValue()
    {
        $option             = ['id' => 'id', 'class' => Service1::class, 'init_params' => ['prop2' => '1', '2']];
        $ServiceMapping1    = new ServiceMapping($option);
        
        $option             = ['id' => 'id2', 'class' => Service1::class, 'init_params' => ['prop2' => '@id']];
        $ServiceMapping2    = new ServiceMapping($option);
        
        $ServiceMappings    = new ServiceMappingCollection();
        $ServiceMappings    = $ServiceMappings->add($ServiceMapping1);
        $ServiceMappings    = $ServiceMappings->add($ServiceMapping2);
        $Container          = new Container($ServiceMappings);
        $Service1           = $Container->get($ServiceMapping1->getId());
        $Service2           = $Container->get($ServiceMapping2->getId());

        $this->assertEquals($Service1, $Service2->prop2);
    }

    /**
     * @depends testGet
     */
    public function testGetNotFoundException($Container)
    {
        $this->expectException(NotFoundException::class);
        $Container->get('not-existed');
    }
}