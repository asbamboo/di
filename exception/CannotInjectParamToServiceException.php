<?php
namespace asbamboo\di\exception;

/**
 * 无法实现容器中某个服务的参数自动注入
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年9月7日
 */
class CannotInjectParamToServiceException extends \InvalidArgumentException implements ContainerExceptionInterface{}