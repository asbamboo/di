<?php
namespace asbamboo\di;

use asbamboo\di\exception\NotExistedServiceMappingException;

/**
 * 用于[asbamboo\di\ServiceMapping(ServiceMappingCollectionInterface  $mappings)]
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月16日
 */
class ServiceMappingCollection implements ServiceMappingCollectionInterface
{
    /**
     * @var array
     */
    protected $service_mappings = [];

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingCollectionInterface::add()
     */
    public function add(ServiceMappingInterface $ServiceMapping): ServiceMappingCollectionInterface
    {
        $this->service_mappings[$ServiceMapping->getId()]  = $ServiceMapping;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Countable::count()
     */
    public function count()
    {
        return count($this->service_mappings);
    }

    /**
     * {@inheritDoc}
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->service_mappings);
    }


    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingCollectionInterface::remove()
     */
    public function remove(string $id): ServiceMappingCollectionInterface
    {
        unset($this->service_mappings[$id]);

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingCollectionInterface::get()
     */
    public function get(string $id): ServiceMappingInterface
    {
        if(!$this->has($id)){
            throw new NotExistedServiceMappingException(sprintf('唯一标识符为%s的服务配置信息不存在。', $id));
        }
        return $this->service_mappings[$id];
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingCollectionInterface::has()
     */
    public function has(string $id): bool
    {
        return isset($this->service_mappings[$id]);
    }
}