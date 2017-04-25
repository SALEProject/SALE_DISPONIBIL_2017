<?php

	require_once 'BRMWebService.php';
	
	class TMessages extends TBRMDataCache
	{
		protected static $FInstance = null;
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/messages');
		}
	
		function update($operation, $object)
		{
			$this->setPath('.');
			$b = $this->keyExists($object['ID']);
	
			if (!$b)
			{
				$this->setPath($object['ID']);
				$this->set('Date', $object['Date']);
				$this->set('ID_User', $object['ID_User']);
				$this->set('Message', $object['Message']);
				$this->set('ID_Bursary', $object['ID_Bursary']);
			}
		}
	}
	
?>