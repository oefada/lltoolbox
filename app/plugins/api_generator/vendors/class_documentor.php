<?php
App::import('Vendor', 'ApiGenerator.Introspector');

/**
 * Documentation Extractor
 *
 * Retrieves Documentation using PHP ReflectionClass
 *
 * @package api_generator
 */
class ClassDocumentor extends ReflectionClass {
/**
 * class Information
 *
 * @var array
 **/
	public $classInfo;
/**
 * properties
 *
 * @var array
 **/
	public $properties;
/**
 * methods in consumed class
 *
 * @var array
 **/
	public $methods;
/**
 * getClassInfo
 *
 * Get Basic classInfo about the current class
 *
 * @return array Information about Class
 **/
	public function getClassInfo(){
		$this->classInfo['name'] = $this->getName();
		$this->classInfo['fileName'] = $this->getFileName();

		$desc = '';
		if ($this->isFinal()) {
			$desc .= 'final ';
		}
		if ($this->isAbstract()) {
			$desc .= 'abstract ';
		}
		if ($this->isInterface()) {
			$desc .= 'interface ';
		} else {
			$desc .= 'class ';
		}
		$desc .= $this->getName() . ' ';
		
		$parents = array();
		if ($this->getParentClass()) {
			$desc .= 'extends ' . $this->getParentClass()->getName();
			$object = $this->getParentClass();
			$parents[] = $object->getName();
			while ($parent = $object->getParentClass()) {
				$parents[] = $parent->getName();
				$object = $parent;
			}
		}

		$interfaces = $this->getInterfaces();
		$interfaceNames = array();
		$number = count($interfaces);
		if ($number > 0){
			$desc .= ' implements ';
			foreach ($interfaces as $int) {
				$desc .= $int->getName() . ' ';
				$interfaceNames[] = $int->getName();
			}
		}	

		$this->classInfo['classDescription'] = $desc;
		$this->classInfo['parents'] = $parents;
		$this->classInfo['interfaces'] = $interfaceNames;
		$this->classInfo['comment'] = $this->_parseComment($this->getDocComment());

		return $this->classInfo;
	}
	
/**
 * getProperties
 *
 * gets All current properties for object with documentation
 * split by access level
 *
 * @return array All properties separated by access type
 **/
	public function getProperties(){
		$public = $protected = $private = array();
		$properties = parent::getProperties();
		
		foreach ($properties as $property) {
			$name = $property->getName();
			$doc = $this->_parseComment($property->getDocComment());

			$prop = array(
				'name' => $name,
				'comment' => $doc,
				'declaredInClass' => $property->getDeclaringClass()->name
			);

			if ($property->isPublic()) {
				$prop['access'] = 'public';
				if (isset($doc['tags']['access'])) {
					$prop['access'] = $doc['tags']['access'];
				}
			}
			if ($property->isPrivate()) {
				$prop['access'] = 'private';
			}
			if ($property->isProtected()) {
				$prop['access'] = 'protected';
			}
			if ($property->isStatic()) {
				$prop['access'] .= ' static';
			}
			$this->properties[] = $prop;
		}
		return $this->properties;
	}
/**
 * getMethods
 *
 * Gets all current methods for object with documentation. 
 * Returns an array with the following structure
 * 'name' => name of the method
 * 'access' => level of access to the method
 * 'args' => array of arguments that the method takes. Args are removed from comment['tags'] as they don't need to be repeated
 * 'comment' => keyed array see cleanComment
 *
 * @see $this->_parseComment
 * @return array multi-dimensional array of methods and their attributes 
 **/
	public function getMethods() {
		$methods = parent::getMethods();
		
		foreach ($methods as $method) {
			$doc = $this->_parseComment($method->getDocComment());

			if (isset($doc['tags']['param'])) {
				$doc['tags']['param'] = (array)$doc['tags']['param'];
			}

			$met = array(
				'name' => $method->getName(), 
				'comment' => $doc,
				'startLine' => $method->getStartLine(),
				'declaredInClass' => $method->getDeclaringClass()->getName(),
				'declaredInFile' => $method->getDeclaringClass()->getFileName(),
				'signature' => Introspector::makeFunctionSignature($method),
				'isStatic' => $method->isStatic()
			);

			$params = $method->getParameters();
			$args = array();
			foreach ($params as $k => $param) {
				$type = $description = null;
				if (isset($met['comment']['tags']['param'][$param->name])) {
					extract($met['comment']['tags']['param'][$param->name]);
				}
				
				$args[$param->name] = array(
					'optional' => $param->isOptional(),
					'default' => null,
					'hasDefault' => false,
					'position' => $k //$param->getPosition() can be used from PHP 5.2.3
					'type' => $type,
					'comment' => $description
				);
				if ($param->isDefaultValueAvailable()) {
					$args[$param->name]['default'] = $param->getDefaultValue();
					$args[$param->name]['hasDefault'] = true;
				}
			}
			unset($met['comment']['tags']['param']);
			$met['args'] = $args;

			if ($method->isPublic()) {
				$met['access'] = 'public';
				if (isset($doc['tags']['access'])) {
					$met['access'] = $doc['tags']['access'];
				}
			}
			if ($method->isPrivate()) {
				$met['access'] = 'private';
			}
			if ($method->isProtected()) {
				$met['access'] = 'protected';
			}
			$this->methods[] = $met;
		}
		return $this->methods;
	}
/**
 * _parseComment
 *
 * Cleans input comments of stars and /'s so it is more readable.
 * Creates a multi dimensional array. That contains semi parsed comments
 * 
 * Returns an array with the following
 * 'title' contains the title / first line of the doc-block
 * 'desc' contains the remainder of the doc block
 * 'tags' contains all the doc-blocks @tags.
 * 
 * @param string $comments The comment block to be cleaned
 * @return array Array of Filtered and separated comments
 **/
	protected function _parseComment($comments){
		return Introspector::parseDocBlock($comments);
	}
/**
 * Get all docs for the reflected class
 *
 * @return void
 **/
	public function getAll() {
		$this->getClassInfo();
		$this->getMethods();
		$this->getProperties();
	}
}
?>