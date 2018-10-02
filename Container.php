<?php
namespace asbamboo\di;

use asbamboo\di\exception\NotFoundException;
use asbamboo\di\exception\CannotInjectParamToServiceException;

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
     * 为实现服务自动注入参数的功能,设置的class 与 service id 映射关系的变量
     *  - 一个class可能会对应有多个service_id
     *  - class_implements(class)和class_parents(class)也和class一样记录在这个变量中
     *  - 如果一个class产生了多个service_id那么不允许自动注入
     * ex:
     *  - 设interface ia;
     *  - 设interface ib extends ia
     *  - 设interface ic;
     *  - 设class a implements ib;
     *  - 设class b extends a implements ic;
     *  - 设 $services['sid'] = new b();
     *  - 那么会有
     *      - $class_to_service_mapping[b] = [sid]
     *      - $class_to_service_mapping[a] = [sid]
     *      - $class_to_service_mapping[ic] = [sid]
     *      - $class_to_service_mapping[ib] = [sid]
     *      - $class_to_service_mapping[ia] = [sid]
     *
     * @var array
     */
    private $class_to_service_mapping   = [];

    /**
     * @var array
     */
    protected $services = [];

    /**
     *
     * @param ServiceMappingCollectionInterface $ServiceMapping
     */
    public function __construct(ServiceMappingCollectionInterface $ServiceMappings)
    {
        /**
         * @var ServiceMappingInterface $ServiceMapping
         * @var \asbamboo\di\Container $ServiceMappings
         */
        $this->ServiceMappings   = $ServiceMappings;
        foreach($this->ServiceMappings AS $ServiceMapping){
            $this->addClassToServiceMapping($ServiceMapping->getClass(), $ServiceMapping->getId());
        }
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ContainerInterface::get()
     */
    public function get(string $id) : object
    {

        /**
         * 服务不存在
         */
        if($this->has($id) == false){
            if(isset($this->class_to_service_mapping[$id]) && count($this->class_to_service_mapping[$id]) == 1){
                // 在一个类只被注册为服务一次的情况下，参数$id可以传递类名字，这里将类名字转换成id
                $id = current($this->class_to_service_mapping[$id]);
            }elseif(class_exists($id)){
                // 类在从来没有注册过服务时，允许自动注册
                $this->ServiceMappings->add(new ServiceMapping(['class' => $id]));
            }elseif(interface_exists($id)){
                // 类在从来没有注册过服务时，允许自动注册
                $isset_service_mapping  = false;
                foreach($this->ServiceMappings->getIterator() AS $ServiceMapping){
                    if(in_array($id, class_implements($ServiceMapping->getClass()))){
                        $id                     = $ServiceMapping->getId();
                        $isset_service_mapping  = true;
                    }
                }
                if($isset_service_mapping == false){
                    $interface_str  = substr($id, -9);
                    $class          = strcasecmp($interface_str, 'Interface') === 0 ? substr($id, 0, -9) : $id;
                    $this->ServiceMappings->add(new ServiceMapping(['id' => $id, 'class' => $class]));
                }
            }else{
                throw new NotFoundException(sprintf('找不到服务。[%s]', $id));
            }
        }

        /**
         * 第一次初始化服务
         */
        if(!isset($this->services[$id])){
            $ServiceMapping         = $this->ServiceMappings->get($id);
            $id                     = $ServiceMapping->getId();
            $class                  = $ServiceMapping->getClass();
            $init_params            = $ServiceMapping->getInitParams();
            $ReflectionClass        = new \ReflectionClass($class);

            /**
             * 设置服务的初始化参数（按下列顺序处理）
             *  - 根据init_params里面的key=>value设置。如果 [$init_param[$key]] 等于 constructor方法的参数名称[name]。 那么这个[name]的值为[$init_param[$key]]
             *  - 根据$ReflectionParameter::getClass查询应该设置的参数
             *      - 循环$ReflectionParameters，对没有设置的参数进行设置(按下面的先后顺序，判断从哪里取值)
             *      - 如果还有$init_params未分配 优先考虑$init_params
             *      - 如果$ReflectionParameter存在默认值，那么参数是该默认值
             *      - 如果参数类型是一个类，那么从$class_to_service_mapping找对应的参数
             *  - 把剩余没有分配的$init_params按照先后顺序分配。
             *  - 最后如果还有参数需要设置的话，采用自动注册服务的方式。
             *      - 如果参数类型是一个class，那么使用class name作为service id注册一个一个服务
             *      - 如果参数类型是一个interface，那么使用和interface同一个目录下实现该interface的一个class做服务（class的名字是interface一样的名字）
             *          - Interface接口的名字也应该使用Interface结尾
             *          - 如UserInterface 那么 注册一个服务User.
             *
             * @var \ReflectionParameter $ReflectionParameter
             * @var array $ordered_init_params
             */
            $ordered_init_params    = [];
            $ReflectionParameters   = $ReflectionClass->getConstructor() ? $ReflectionClass->getConstructor()->getParameters() : [];
            $seted_params_count     = 0; // 判断是不是所有参数已经设置好。
            $full_params_count      = count($ReflectionParameters); // 判断是不是所有参数已经设置好。
            foreach($ReflectionParameters AS $index => $ReflectionParameter){
                $name           = $ReflectionParameter->getName();
                if(isset($init_params[$name])){
                    $ordered_init_params[$index]    = $this->filterServiceParamValue($init_params[$name]);
                    unset($init_params[$name]);
                    $seted_params_count++;
                }
            }
            if($full_params_count != $seted_params_count){
                foreach($ReflectionParameters AS $index => $ReflectionParameter){
                    if(isset( $ordered_init_params[$index] )){
                        continue;
                    }
                    $ReflectionParameterClass   = $ReflectionParameter->getClass();
                    if(is_null($ReflectionParameterClass)){
                        continue;
                    }
                    $test_class_name    = $ReflectionParameterClass->getName();
                    $from_initparams    = [];
                    foreach($init_params AS $key => $init_param){
                        $init_param = $this->filterServiceParamValue($init_param);
                        if($init_param instanceof $test_class_name){
                            $from_initparams[$key]  = $init_param;
                        }
                    }
                    if(count( $from_initparams ) > 1){
//                      这里不再抛出异常的原因是controller的 __construct(TestInterface ...$test)
//                      这种情况下这个构造方法所有的参数都应该是TestInterface类型，而且还要传递多个参数，init_params参数还不能使用test作为参数的key
//                      这种情况发生在asbamboo/demo中 config "CheckerCollection"
                        if($ReflectionParameter->isVariadic()){
                            foreach($from_initparams AS $key => $from_initparam){
                                $ordered_init_params[$index]    = $from_initparam;
                                $index                          = $index+1;
                                unset($init_params[$key]);
                                $seted_params_count++;
                            }
                            $index  = $index-1;
                            continue;
                        }
                        throw new CannotInjectParamToServiceException('无法实现容器内服务参数的自动注入, 因为设置的参数中有两个类型一样的变量。');
                    }
                    foreach($from_initparams AS $key => $from_initparam){
                        $ordered_init_params[$index]    = $from_initparam;
                        unset($init_params[$key]);
                        $seted_params_count++;
                        continue 2;
                    }
                    if($ReflectionParameter->isDefaultValueAvailable()){
                        $ordered_init_params[$index]    = $ReflectionParameter->getDefaultValue();
                        $seted_params_count++;
                    }
                    if(empty($this->class_to_service_mapping[$test_class_name])){
                        continue;
                    }
                    if(count($this->class_to_service_mapping[$test_class_name]) > 1){
                        throw new CannotInjectParamToServiceException(sprintf('无法实现容器内服务参数的自动注入, 因为有两个类型一样的服务。[%s]', $test_class_name));
                    }
                    $auto_inject_service_id         = current($this->class_to_service_mapping[$test_class_name]);
                    $ordered_init_params[$index]    = $this->get($auto_inject_service_id);
                    $seted_params_count++;
                }
            }
            if($full_params_count != $seted_params_count && !empty( $init_params )){
                foreach($ReflectionParameters AS $index => $ReflectionParameter){
                    if(!isset( $ordered_init_params[$index] )){
                        $ordered_init_params[$index]    = $this->filterServiceParamValue(array_shift($init_params));
                        $seted_params_count++;
                    }
                }
            }
            if($full_params_count != $seted_params_count){
                foreach($ReflectionParameters AS $index => $ReflectionParameter){
                    $ReflectionParameterClass   = $ReflectionParameter->getClass();
                    if(!isset( $ordered_init_params[$index] )){
                        if(is_null($ReflectionParameterClass)){
                            if($ReflectionParameter->isDefaultValueAvailable()){
                                $ordered_init_params[$index]    = $ReflectionParameter->getDefaultValue();
                            }
                        }else{
                            $test_class             = $ReflectionParameterClass->getName();
                            $test_interface_string  = strtolower(substr($test_class, -9));
                            if($test_interface_string == 'interface'){
                                $test_class         = substr($test_class, 0, -9);
                            }
                            if(class_exists($test_class)){
                                $ordered_init_params[$index]    = $this->get($test_class);
                            }
                        }
                    }
                }
            }
            ksort($ordered_init_params);

            $this->ServiceMappings->remove($id);
            $this->set($id, new $class(...$ordered_init_params));
        }

        /*
         * return service
         */
        return $this->services[$id];
    }

    /**
     *
     * @param string $id
     * @param object $service
     */
    public function set(string $id, object $service) : ContainerInterface
    {
        $this->services[$id]    = $service;

        if(method_exists($this->services[$id], 'setContainer')){
            $this->services[$id]->setContainer($this);
        }

        $this->addClassToServiceMapping(get_class($service), $id);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\di\ContainerInterface::has()
     */
    public function has(string $id) : bool
    {
        if(isset($this->services[$id])){
            return true;
        }else if($this->ServiceMappings->has($id)){
            return true;
        }
        return false;
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
        if(is_string($value) && strncmp($value, '@', 1) === 0){
            $server_id  = substr($value, 1);
            $value = $this->get($server_id);
        }
        return $value;
    }

    /**
     * 添加一个class于service id的映射关系，后期自动注入时使用
     *
     * @param string $class
     * @param string $service_id
     * @return self
     */
    private function addClassToServiceMapping(string $class, string $service_id) : self
    {
        $parents    = class_parents($class);
        $implements = class_implements($class);
        foreach(array_merge($parents, $implements, [$class]) AS $c){
            $this->class_to_service_mapping[$c][$service_id]    = $service_id;
        }
        return $this;
    }
}