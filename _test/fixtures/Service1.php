<?php
namespace asbamboo\di\_test\fixtures;

use asbamboo\di\ContainerAwareTrait;

/**
 * 测试注册服务时设置参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
class Service1
{
    use ContainerAwareTrait;

    public $prop1;

    public $prop2;

    public function __construct($prop1, $prop2)
    {
        $this->prop1  = $prop1;
        $this->prop2  = $prop2;
    }
}