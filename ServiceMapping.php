<?php
namespace asbamboo\di;

use asbamboo\di\exception\ServiceMappingException;
use PHPUnit\Framework\MockObject\Matcher\AnyParameters;

/**
 * 容器[container]的服务配置信息的一个单元 [asbamboo\di\ServiceMappingInterface]的实现
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月15日
 */
class ServiceMapping implements ServiceMappingInterface
{
    /**
     * 一个服务的唯一标识符
     * @var string
     */
    protected $id;

    /**
     * 一个服务的类名
     * @var string
     */
    protected $class;

    /**
     * @var AnyParameters
     */
    protected $init_params;

    /**
     * @param array $option
     */
    public function __construct(array $option)
    {
        foreach($option as $prop => $value){
            if(property_exists($this, $prop)){
                $this->{$prop}  = $value;
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingInterface::getClass()
     */
    public function getClass(): string
    {
        if(empty($this->class)){
            throw new ServiceMappingException('容器[container]的服务配置信息，必须设置类名[class name]。');
        }
        return $this->class;
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingInterface::getInitParams()
     */
    public function getInitParams(): array
    {
        return $this->init_params;
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ServiceMappingInterface::getId()
     */
    public function getId(): string
    {
        if(!$this->id){
            $this->id   = $this->getClass();
        }
        return $this->id;
    }
}