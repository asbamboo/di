<?php
namespace asbamboo\di;

/**
 * 容器[container]的服务配置信息的的集合
 *
 * 用于[asbamboo\di\ServiceMapping(ServiceMappingCollectionInterface  $mappings)]
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface ServiceMappingCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * 将某个容器[container]的服务配置信息[ServiceMapping]添加到集合[ServiceMappingCollection]
     *
     * @param ServiceMappingInterface $ServiceMapping
     *
     * @return self
     */
    public function add(ServiceMappingInterface $ServiceMapping) : self;

    /**
     * 获取唯一标识符为[$id]的配置信息。
     *
     * @param string $id
     * @return ServiceMappingInterface
     */
    public function get(string $id) : ServiceMappingInterface;

    /**
     * 判断容器[container]的服务配置信息的的集合中是否存在唯一标识符为[$id]的配置信息。
     *
     * @param string $id [asbamboo\di\ServiceMappingInterface::getId()]
     * @return bool
     */
    public function has(string $id) : bool;

    /**
     * 从集合[ServiceMappingCollection]删除容器[container]的服务配置信息[ServiceMapping]
     *
     * @param string $id [asbamboo\di\ServiceMappingInterface::getId()]
     * @return self
     */
    public function remove(string $id) : self;
}