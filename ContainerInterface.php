<?php
namespace asbamboo\di;

use Psr\Container\ContainerInterface AS PsrContainerInterface;

/**
 * 继承遵守psr规则的ContainerInterface，并在此基础上扩展
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * 设置一个服务
     *
     * @param string $id 服务的唯一表示符
     * @param object $service 服务
     * @return ContainerInterface
     */
    public function set(string $id, object $service) : ContainerInterface;
}