<?php
namespace asbamboo\di;

use asbamboo\di\exception\NotFoundException;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * 通过这个容器管理系统内各个服务service
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月16日
 */
class Container implements ContainerInterface
{
    /**
     * @var ServiceMappingCollectionInterface
     */
    private $ServiceMappings;

    /**
     * @var array
     */
    protected $services;

    /**
     *
     * @param ServiceMappingCollectionInterface $ServiceMapping
     */
    public function __construct(ServiceMappingCollectionInterface $ServiceMappings)
    {
        $this->ServiceMappings   = $ServiceMappings;
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ContainerInterface::get()
     */
    public function get(string $id) : object
    {
        /*
         * 服务不存在
         */
        if($this->has($id) == false){
            throw new NotFoundException('找不到服务。');
        }

        /*
         * 第一次初始化服务
         */
        if(!isset($this->services[$id])){
            $ServiceMapping         = $this->ServiceMappings->get($id);
            $id                     = $ServiceMapping->getId();
            $class                  = $ServiceMapping->getClass();
            $init_params            = $ServiceMapping->getInitParams();
            $ReflectionClass        = new \ReflectionClass($class);

            // 排序$init_params
            // 如果 [$init_param[$key]] 等于 constructor方法的参数名称[name]。 那么这个[name]的值为[$init_param[$key]]
            // constructor方法的参数按照上一条参数注入方式没有匹配到，那么继续从$init_params剩下的参数中，按照先后顺序注入。
            // constructor方法的参数按照上一条参数注入方式没有匹配到，并且已经没有$init_params可用，那么这个参数的值等于null
            $ordered_init_params    = [];
            foreach($ReflectionClass->getConstructor()->getParameters() AS $index => $ReflectionParameter){
                $name           = $ReflectionParameter->getName();
                if(isset($init_params[$name])){
                    $ordered_init_params[$index]    = $this->filterServiceParamValue($init_params[$name]);
                    unset($init_params[$name]);
                }
            }
            foreach($ReflectionClass->getConstructor()->getParameters() AS $index => $ReflectionParameter){
                if(!isset( $ordered_init_params[$index] )){
                    $ordered_init_params[$index]    = $this->filterServiceParamValue(array_shift($init_params));
                }
            }
            ksort($ordered_init_params);

            $this->services[$id]    = new $class(...$ordered_init_params);
            $this->ServiceMappings->remove($id);

            if(method_exists($this->services[$id], 'setContainer')){
                $this->services[$id]->setContainer($this);
            }
        }

        /*
         * return service
         */
        return $this->services[$id];
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ContainerInterface::has()
     */
    public function has(string $id) : bool
    {
        return isset($this->services[$id]) || $this->ServiceMappings->has($id);
    }
    
    /**
     * service 参数的每个值的过滤器
     * 
     * 如果参数的值是另一个service，那么该值是'@' + $service_id, 本方法将这种参数转换为service object
     * 
     * @return $value 过滤以后的 $value
     */
    private function filterServiceParamValue($value)
    {
        if(strncmp($value, '@', 1) === 0){
            $server_id  = substr($value, 1);
            $value = $this->get($server_id);
        }
        return $value;
    }
}