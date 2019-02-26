<?php
namespace asbamboo\di\exception;

use Psr\Container\ContainerExceptionInterface AS PsrContainerExceptionInterface;

/**
 * 继承遵守psr规则的ContainerInterface，并在此基础上扩展
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface ContainerExceptionInterface extends PsrContainerExceptionInterface
{

}