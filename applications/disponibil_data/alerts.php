<?php

	require_once 'BRMDataCache.php';

	class TAlerts extends TBRMDataCache
	{
		protected static $FInstance = null;
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/alerts');
		}
		
		function update($operation, $object)
		{
			$this->setPath('.');
			$b = $this->keyExists($object['ID']);
				
			if (!$b)
			{
				$this->setPath($object['ID']);
				$this->set('Date', $object['Date']);
				$this->set('ID_Market', $object['ID_Market']);
				$this->set('ID_Ring', $object['ID_Ring']);
				$this->set('ID_Asset', $object['ID_Asset']);
				$this->set('Message', $object['Message']);
				$this->set('Message_RO', $object['Message_RO']);
				$this->set('Message_EN', $object['Message_EN']);
			}
		}
	}
	
?>