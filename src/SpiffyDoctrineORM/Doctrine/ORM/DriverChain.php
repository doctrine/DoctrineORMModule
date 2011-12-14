<?php
namespace SpiffyDoctrineORM\Doctrine\ORM;
use ReflectionClass,
    Doctrine\Common\Cache\Cache,
	Doctrine\Common\Annotations\AnnotationReader,
	Doctrine\Common\Annotations\CachedReader,
	Doctrine\Common\Annotations\IndexedReader,
	Doctrine\ORM\Mapping\Driver\DriverChain as DoctrineDriverChain,
    SpiffyDoctrine\Doctrine\Instance;

class DriverChain extends Instance
{
	const ANNOTATION_DRIVER = 'Doctrine\ORM\Mapping\Driver\AnnotationDriver';
	const DRIVER_CHAIN      = 'Doctrine\ORM\Mapping\Driver\DriverChain';
	
	/**
	 * @var array
	 */
	protected $driverChainDefinition = array(
        'required' => array(
            'class' 	=> 'string',
            'namespace' => 'string',
            'paths' 	=> 'array',
        )
    );
	
	/**
	 * @var Doctrine\Common\Annotations\CachedReader
	 */
	protected static $cachedReader;
	
	/**
	 * @var Doctrine\Common\Cache\Cache
	 */
	protected $cache;
	
	/**
	 * Constructor.
	 * 
	 * @param array $drivers
	 * @param Cache $cache
	 */
	public function __construct(array $drivers = array(), Cache $cache)
	{
		$this->cache = $cache;
		parent::__construct($drivers);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see SpiffyDoctrineORM\Instance.Instance::loadInstance()
	 */
	protected function loadInstance()
	{
		$drivers = $this->getOptions();
		
        $wrapperClass = self::DRIVER_CHAIN;
        if (isset($opts['wrapperClass'])) {
            if (is_subclass_of($opts['wrapperClass'], $wrapperClass)) {
               $wrapperClass = $opts['wrapperClass'];
            } else {
                throw \InvalidArgumentException(sprintf(
                	'wrapperClass must be an instance of %s, %s given',
                	self::DRIVER_CHAIN,
                	$wrapperClass
                ));
            }
        }
		
        $chain = new $wrapperClass;
        
        foreach($drivers as $driverOpts) {
            $this->validateOptions($driverOpts, $this->driverChainDefinition);
            
            if (($driverOpts['class'] == self::ANNOTATION_DRIVER) ||
            	(is_subclass_of($driverOpts['class'], self::ANNOTATION_DRIVER))
			) {
                $cachedReader = $this->getCachedReader();
                $driver = new $driverOpts['class']($cachedReader, $driverOpts['paths']);
            } else {
                $driver = new $driverOpts['class']($driverOpts['paths']);
            }
            $chain->addDriver($driver, $driverOpts['namespace']);
        }

        $this->instance = $chain; 
    }
    
    /**
     * Get the cached reader instance for annotation readers.
     * 
     * @todo investigate use cases for indexed reader
     * @return Doctrine\Common\Annotations\CachedReader
     */
    protected function getCachedReader()
    {
    	if (null === self::$cachedReader) {
	    	$annotationReader   = new AnnotationReader;
			//$indexedReader 	    = new IndexedReader($annotationReader);
			//self::$cachedReader = new CachedReader($indexedReader, $this->cache);
			self::$cachedReader = new CachedReader($annotationReader, $this->cache);
    	}
    	return self::$cachedReader;
    }
}