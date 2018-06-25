<?php
namespace asbamboo\di\_test;

use asbamboo\di\exception\ServiceMappingException;
use PHPUnit\Framework\TestCase;
use asbamboo\di\ServiceMapping;
use asbamboo\di\_test\fixtures\Service1;

/**
 * Test asbamboo\di\ServiceMapping
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
class ServiceMappingTest extends TestCase
{
    /**
     * 测试没有设置class的的时候抛出异常
     */
    public function testGetClassException()
    {
        $this->expectException(ServiceMappingException::class);

        $option            = ['id' => 'id'];

        $ServiceMapping    = new ServiceMapping($option);
        $ServiceMapping->getClass();
    }

    /**
     * 测试没有设置id时获取id
     */
    public function testGetIdNotSet()
    {
        $option            = ['class' => Service1::class];
        $ServiceMapping    = new ServiceMapping($option);
        $id                = $ServiceMapping->getId();
        $this->assertEquals($option['class'], $id);
    }

    /**
     * 正常获取各个参数
     */
    public function testGet()
    {
        $option                    = ['id' => 'id', 'class' => Service1::class, 'init_params' => ['1','2','3']];
        $ServiceMapping    = new ServiceMapping($option);
        $this->assertEquals($option['id'], $ServiceMapping->getId());
        $this->assertEquals($option['class'], $ServiceMapping->getClass());
        $this->assertEquals($option['init_params'], $ServiceMapping->getInitParams());
    }
}