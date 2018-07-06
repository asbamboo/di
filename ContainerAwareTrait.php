<?php
namespace asbamboo\di;

/**
 * 在service中使用 [在 new asbamboo\di\Container($serviceMapping)时被调用]。
 *
 * @example
 * class service
 * {
 *   use asbamboo\di\ContainerAwareTrait;
 * }
 * $ServiceMapping              = new asbamboo\di\ServiceMapping(['id' => 'service', 'class'=> service::class]);
 * $ServiceMappingCollection    = new asbamboo\di\ServiceMappingCollection;
 * $ServiceMappingCollection    = $ServiceMappingCollection->add($ServiceMapping);
 * $Container                   = new asbamboo\di\Container($ServiceMappingCollection); //这时会调用setContainer
 *
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $Container;

    /**
     * 在 [asbamboo\di\Container($serviceMapping)]时会被调用
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $Container) : void
    {
        $this->Container = $Container;
    }
}