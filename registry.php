<?php

	require_once 'structfile.php';

	class TRegistry
	{
		static private $Finstance = null;
		var $DataObject = null;
		
		static function instance()
		{
			if (self::$Finstance == null) self::$Finstance = TQuarkOS::instance()->registry;
			if (self::$Finstance != null) return self::$Finstance;
			
			self::$Finstance = new TRegistry();
			return self::$Finstance;
		}
		
		function __construct()
		{
			$this->DataObject = new TStructFile('registry');
		}
		
		function __destruct()
		{
			//$this->DataObject->save();
		}
		
		function openKey($key, $write = false)
		{
			$this->DataObject->setPath($key, $write);
		}
		
		function keyName()
		{
			return $this->DataObject->keyName();
		}
		
		function keyPath()
		{
			return $this->DataObject->keyPath();
		}
		
		function hasChildren()
		{
			return $this->DataObject->hasChildren();
		}
		
		function first()
		{
			return $this->DataObject->first();
		}
		
		function next()
		{
			return $this->DataObject->next();
		}
	
		function prev()
		{
			return $this->DataObject->prev();
		}
		
		function last()
		{
			return $this->DataObject->last();
		}
		
		function name()
		{
			return $this->DataObject->getName();
		}
		
		function openCurrent()
		{
			return $this->DataObject->open();
		}
		
		function closeCurrent()
		{
			return $this->DataObject->close();
		}
		
		function read($key = '')
		{
			return $this->DataObject->get($key);
		}
		
		function write($key, $value)
		{
			return $this->DataObject->set($key, $value, true);
		}
		
		function export($key)
		{
			
		}
		
		function import()
		{
			
		}	
		
	}
	
?>