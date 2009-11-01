<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage LazyLoad
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Generates LazyLoad Proxies from metadata
 *
 * @uses       Zend_Entity_Definition_MappingVisitor
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage LazyLoad
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_LazyLoad_GeneratorAbstract implements Zend_Entity_Definition_MappingVisitor
{
    /**
     * @throws Zend_Entity_LazyLoad_GenerateProxyException
     * @param Zend_Config|Array $options
     * @return Zend_Entity_LazyLoad_GeneratorAbstract
     */
    static public function create($options)
    {
        if($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        
        if(isset($options['type'])) {
            switch(strtolower($options['type'])) {
                case 'dynamic':
                    $class = "Zend_Entity_LazyLoad_DynamicGenerator";
                    break;
                case 'cachefile':
                    $class = "Zend_Entity_LazyLoad_CacheFileGenerator";
                    break;
                default:
                    throw new Zend_Entity_LazyLoad_GenerateProxyException(
                        "Type only accepts the options 'dynamic' or 'cachefile'."
                    );
            }
        } elseif(isset($options['class'])) {
            $class = $options['class'];
        } else {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "Either option 'type' (dynamic, cachefile) or 'class' has to ".
                "be given to determine how to generate lazy load proxies."
            );
        }

        if(!class_exists($class)) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "The lazy load proxy generator class '".$class."' does not exist."
            );
        }

        $generatorOptions = array();
        if(isset($options['generatorOptions'])) {
            $generatorOptions = $options['generatorOptions'];
        }
        $proxyGenerator = new $class($generatorOptions);

        if(!($proxyGenerator instanceof Zend_Entity_LazyLoad_GeneratorAbstract)) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "The given lazy load generator class '".$class."' does not implement ".
                "'Zend_Entity_LazyLoad_GeneratorAbstract'."
            );
        }

        return $proxyGenerator;
    }

    /**
     * @var array
     */
    protected $_classes = array();

    /**
     * @var string
     */
    protected $_metadataVersion = null;

    /**
     * @var array
     */
    protected $_entityProxies = array();

    /**
     * @param array|Zend_Config $options
     */
    public function __construct($options=array())
    {
        if($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        if(is_array($options)) {
            foreach($options AS $key => $value) {
                $method = "set".ucfirst($key);
                if(method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * Execute the Proxy Generation with the collected data on the entities.
     *
     * @throws Zend_Entity_LazyLoad_GenerateProxyException
     * @return void
     */
    abstract public function generate();

    /**
     *
     * @param string $entityName
     * @param Zend_Entity_Manager_Interface $entityManager
     * @param int $id
     */
    public function instantiate($entityName, $entityManager, $id)
    {
        if(!isset($this->_entityProxies[$entityName])) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException("No lazyload proxy found for entity '".$entityName."'.");
        }
        $proxyClass = $this->_entityProxies[$entityName];
        return new $proxyClass($entityManager, $entityName, $id);
    }

    /**
     * Accept an entity definition
     *
     * @param Zend_Entity_Definition_Entity $entity
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptEntity(Zend_Entity_Definition_Entity $entity, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {
        // @todo Maybe extend to use an additional acceptMetadataFactory() ?
        if($this->_metadataVersion == null) {
            $this->_metadataVersion = $metadataFactory->getCurrentVersionHash();
        }

        $this->_classes[] = $this->generateLazyLoadProxyClass($entity);
        $this->_entityProxies[$entity->class] = $entity->proxyClass;
    }
    
    /**
     * @param Zend_Entity_Definition_Entity $entityDef
     * @return Zend_CodeGenerator_Php_Class
     */
    public function generateLazyLoadProxyClass(Zend_Entity_Definition_Entity $entityDef)
    {
        $proxyClassName = $entityDef->proxyClass;

        if(!class_Exists($entityDef->class)) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "Entity Class to generate proxy from '".$entityDef->class."' does not exist!"
            );
        }

        $reflectionClass = new Zend_Reflection_Class($entityDef->class);
        $entityHasConstructor = ($reflectionClass->getConstructor()!==null);

        $proxyClass = $this->_generateLazyLoadProxyClassBase($proxyClassName, $entityDef->class);

        $entityManagerProperty = $this->_generateLazyLoadProxyEntityManagerProperty();
        $proxyClass->setProperty($entityManagerProperty);

        if($entityHasConstructor == true && $reflectionClass->getConstructor()->isFinal()) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(
                "Constructor on entity '".$entityDef->class."' is ".
                "not allowed to be final! No valid proxy can be generated!"
            );
        }

        $constructor = $this->_generateLazyLoadProxyConstructor($entityHasConstructor);
        $proxyClass->setMethod($constructor);

        $lazyLoadMethod = $this->_generateLazyLoadProxyMethod();
        $proxyClass->setMethod($lazyLoadMethod);

        $entityWasLoadedMethod = $this->_generateEntityWasLoadedMethod();
        $proxyClass->setMethod($entityWasLoadedMethod);

        $this->_generateLazyLoadProxyOverwriteMethods($proxyClass, $reflectionClass);

        return $proxyClass;
    }

    /**
     *
     * @return Zend_CodeGenerator_Php_Property
     */
    protected function _generateLazyLoadProxyEntityManagerProperty()
    {
        $entityManagerProperty = new Zend_CodeGenerator_Php_Property();
        $entityManagerProperty->setName('_entityManager');
        $entityManagerProperty->setVisibility(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE);

        return $entityManagerProperty;
    }

    /**
     *
     * @param  string $proxyClassName
     * @param  string $entityClassName
     * @return  generateLazyLoadProxyClassBase
     */
    protected function _generateLazyLoadProxyClassBase($proxyClassName, $entityClassName)
    {
        $proxyClass = new Zend_CodeGenerator_Php_Class();
        $proxyClass->setName($proxyClassName);
        $proxyClass->setExtendedClass($entityClassName);
        $proxyClass->setImplementedInterfaces(array('Zend_Entity_LazyLoad_Proxy'));
        $proxyClass->setDocblock("THIS CODE WAS AUTOMATICALLY CREATED AND MIGHT BE AUTOMATICALLY REGENERATED\nCHANGES TO THIS CODE CAN BE LOST!");
        
        return $proxyClass;
    }

    /**
     *
     * @param  bool $entityHasConstructor
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _generateLazyLoadProxyConstructor($entityHasConstructor)
    {
        $constructor = new Zend_CodeGenerator_Php_Method();
        $constructor->setName('__construct');
        $constructor->setFinal(true);
        $constructor->setVisibility(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC);
        if($entityHasConstructor == true) {
            $constructor->setBody($this->_getConstructorMethodBody_WithParentConstructor());
        } else {
            $constructor->setBody($this->_getConstructorMethodBody_WithoutParentConstructor());
        }

        $entityManagerParam = new Zend_CodeGenerator_Php_Parameter();
        $entityManagerParam->setName('entityManager');
        $entityManagerParam->setType('Zend_Entity_Manager_Interface');

        $entityNameParam = new Zend_CodeGenerator_Php_Parameter();
        $entityNameParam->setName('entityName');

        $idParam = new Zend_CodeGenerator_Php_Parameter();
        $idParam->setName('id');

        $constructor->setParameter($entityManagerParam);
        $constructor->setParameter($entityNameParam);
        $constructor->setParameter($idParam);

        return $constructor;
    }

    /**
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _generateLazyLoadProxyMethod()
    {
        $lazyLoadMethod = new Zend_CodeGenerator_Php_Method();
        $lazyLoadMethod->setName('__lazyLoad');
        $lazyLoadMethod->setVisibility(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE);
        $lazyLoadMethod->setBody($this->_getLazyLoadMethodBody());
        $lazyLoadMethod->setFinal(true);

        return $lazyLoadMethod;
    }

    /**
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _generateEntityWasLoadedMethod()
    {
        $entityWasLoadedMethod = new Zend_CodeGenerator_Php_Method();
        $entityWasLoadedMethod->setName('entityWasLoaded');
        $entityWasLoadedMethod->setVisibility(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC);
        $entityWasLoadedMethod->setBody($this->_getEntityWasLoadedMethodBody());
        $entityWasLoadedMethod->setFinal(true);

        return $entityWasLoadedMethod;
    }

    /**
     * @return string
     */
    protected function _getEntityWasLoadedMethodBody()
    {
        $body = <<<EWL
return (\$this->_entityManager===null);
EWL;
        return $body;
    }

    /**
     * @param Zend_CodeGenerator_Php_Class $proxyClass
     * @param Zend_Reflection_Class $reflectionClass
     */
    protected function _generateLazyLoadProxyOverwriteMethods(Zend_CodeGenerator_Php_Class $proxyClass, Zend_Reflection_Class $reflectionClass)
    {
        $methods = $reflectionClass->getMethods();
        foreach($methods AS $method) {
            if($method->isConstructor() || $method->isStatic()) {
                continue;
            }

            if($method->isFinal()) {
                throw new Zend_Entity_LazyLoad_GenerateProxyException(
                    "Method '".$method->getName()."' is not allowed to be final! No valid proxy can be generated!"
                );
            }

            // Array StateTransformer may habe problems if this method triggers a lazy load :-)
            if($method->name == "setState") {
                continue;
            }

            /* @var $method ReflectionMethod */
            if($method->isPublic()) {
                $overwriteMethod = $this->_generateLazyLoadProxyOverwriteMethod($method);
                $proxyClass->setMethod($overwriteMethod);
            }
        }
    }

    /**
     * @param Zend_Reflection_Method $method
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _generateLazyLoadProxyOverwriteMethod(Zend_Reflection_Method $method)
    {
        try {
            $overwriteMethod = Zend_CodeGenerator_Php_Method::fromReflection($method);
        } catch(Zend_Reflection_Exception $e) {
            throw new Zend_Entity_LazyLoad_GenerateProxyException(sprintf(
                "An error occured while generating the proxy method %s::%s(): %s",
                $method->getDeclaringClass()->getName(), $method->getName(), $e->getMessage()
            ));
        }

        $params = $overwriteMethod->getParameters();
        $parentParams = array();
        foreach($params AS $param) {
            $parentParams[] = '$'.$param->getName();
        }
        $returnParent = "return parent::".$method->getName()."(".implode(", ", $parentParams).");\n";
        $methodBody = '$this->__lazyLoad();'."\n".$returnParent;

        $overwriteMethod->setFinal(true);
        $overwriteMethod->setBody($methodBody);

        return $overwriteMethod;
    }

    protected function _getConstructorMethodBody_WithParentConstructor()
    {
        $body = <<<COM
\$this->_entityManager = \$entityManager;
\$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
parent::__construct();
COM;
        return $body;
    }

    protected function _getConstructorMethodBody_WithoutParentConstructor()
    {
        $body = <<<COM
\$this->_entityManager = \$entityManager;
\$entityManager->getIdentityMap()->addObject(\$entityName, \$id, \$this);
COM;
        return $body;
    }

    protected function _getLazyLoadMethodBody()
    {
        $body = <<<LLM
if(\$this->_entityManager !== null) {
    \$this->_entityManager->refresh(\$this);
    \$this->_entityManager = null;
}
LLM;
        return $body;
    }

    /**
     * Accept a property definition
     *
     * @param Zend_Entity_Definition_Property $property
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property $property, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory)
    {

    }

    public function finalize()
    {
        
    }
}