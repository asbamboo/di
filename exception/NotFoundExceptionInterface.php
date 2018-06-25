<?php
namespace asbamboo\di\exception;

use asbamboo\di\psr\exception\PsrNotFoundExceptionInterface;

/**
 * 在容器[container]中找不到对象object的时候抛出的异常
 *
 * 基于psr11规范[https://github.com/php-fig/container]
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface NotFoundExceptionInterface extends ContainerExceptionInterface,PsrNotFoundExceptionInterface
{

}