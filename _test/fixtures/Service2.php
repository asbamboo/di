<?php
namespace asbamboo\di\_test\fixtures;

use asbamboo\di\ContainerAwareTrait;

/**
 * 测试自动注册service并且自动注入的时候用
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
class Service2
{
    use ContainerAwareTrait;

    public $Service1;

    public function __construct(Service1 $Service1)
    {
        $this->Service1  = $Service1;
    }
}