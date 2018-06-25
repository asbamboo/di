<?php
namespace asbamboo\di;

/**
 * 容器[container]的服务配置信息的一个单元
 * [asbamboo\di\ServiceMapping::addMappingInfo]接受的参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月14日
 */
interface ServiceMappingInterface
{
    /**
     * 获取这个容器[container]内的对象[object]的id与class映射关系的唯一标识符
     *
     * 当配置信息没有设置id时，默认将使用[$this->getClass()]作为Id
     *
     * @return string
     */
    public function getId() : string;

    /**
     * 获取这个容器[container]内的对象[object]的id与class映射关系的类名
     *
     * @throws \asbamboo\di\exception\ServiceMappingException 当配置信息中没有设置类名[class]时抛出异常
     *
     * @return string
     */
    public function getClass() : string;

    /**
     * 获取这个容器[container]内的对象[object]的id与class映射关系的类，实例化[__construct]时需要使用的参数
     * 如果 [$init_param[$key]] 等于 constructor方法的参数名称[name]。 那么这个[name]的值为[$init_param[$key]]
     * constructor方法的参数按照上一条参数注入方式没有匹配到，那么继续从$init_params剩下的参数中，按照先后顺序注入。
     * constructor方法的参数按照上一条参数注入方式没有匹配到，并且已经没有$init_params可用，那么这个参数的值等于null
     *
     * @return array
     */
    public function getInitParams() : array;
}