<?php

	require_once 'BRMDataCache.php';

	class TSysLog extends TBRMDataCache
	{
		protected static $FInstance = null;
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/syslog');
		}
		
		function update($operation, $object)
		{
			$this->setPath('.');
			$b = $this->keyExists($object['ID']);
			
			if (!$b)
			{
				$this->setPath($object['ID']);
				$this->set('Date', $object['Date']);
				$this->set('Priority', $object['Priority']);
				$this->set('Resource', $object['Resource']);
				$this->set('EventType', $object['EventType']);
				$this->set('ID_Resource', $object['ID_Resource']);
				$this->set('ID_LinkedResource', $object['ID_LinkedResource']);
			}
		}
	}

?>