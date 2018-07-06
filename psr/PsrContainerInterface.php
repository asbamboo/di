<?php
namespace asbamboo\di\psr;

/**
 * 容器接口[Container]
 *
 * 基于psr11规范[https://github.com/php-fig/container]
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface PsrContainerInterface
{
    /**
     * 获取容器内的Id为{$id}的对象,如果对象不存在抛出异常
     *
     * @param string $id 容器[container]内的对象[object]的唯一标识符
     *
     * @throws \asbamboo\di\exception\NotFoundExceptionInterface 从容器中[$this->container]找不到$id对应的对象[object]时发生的异常
     * @throws \asbamboo\di\exception\ContainerExceptionInterface 从容器中[$this->container]获取$id对应的对象[object]时发生异常
     *
     * @return object
     */
    public function get(string $id) : object;

    /**
     * 当容器中[$this->container]存在$id对应的对象[object]时返回true
     * 当容器中[$this->container]不存在$id对应的对象[object]时返回false
     *
     * [$this->container->has($id) == true] 并不表示[$this->container->get($id)一定不会抛出异常, 但一定不会抛出[\asbamboo\di\exception\NotFoundExceptionInterface]
     *
     * @param string $id 容器[container]内的对象[object]的唯一标识符
     *
     * @return bool
     */
    public function has(string $id) : bool;
}