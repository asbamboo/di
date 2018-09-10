<?php
namespace asbamboo\di\_test\fixtures;

use asbamboo\di\ContainerAwareTrait;

/**
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
class Service3
{
    use ContainerAwareTrait;

    public $Service4;

    public function __construct(Service4 $Service4 = null)
    {
        $this->Service4  = $Service4;
    }
}