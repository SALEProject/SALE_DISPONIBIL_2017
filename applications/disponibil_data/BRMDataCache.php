<?php

	class TBRMDataCache extends TStructFile
	{
		static $contextID = 0;
		
		static function instance($contextID)
		{
			if (static::$FInstance != null) return static::$FInstance;

			static::$contextID = $contextID;
			if ($contextID == 0) static::$FInstance = new static();
			else
			{
				$context = TQuark::instance()->getContext($contextID);
				if (!isset($context) || !isset($context->application)) return null;
			
				//  assume that application has a webservice property
				switch (static::who())
				{
					case 'TAlerts':
						static::$FInstance = $context->application->Alerts;
						break;
					case 'TMessages':
						static::$FInstance = $context->application->Messages;
						break;
					case 'TAssets':
						static::$FInstance = $context->application->Assets;
						break;
					case 'TTransactions':
						static::$FInstance = $context->application->Transactions;
						break;
					case 'TTranslations':
						static::$FInstance = $context->application->Translations;
						break;
				}
			}

			if (static::$FInstance != null)
			{
				static::$FInstance->checkRevisionNumber();
				static::$FInstance->buildLatest();
			}
			return static::$FInstance;
		}
		
		static function setInstance($inst, $contextID)
		{
			static::$FInstance = $inst;
			static::$contextID = $contextID;
			if ($inst != null) static::$FInstance->latest = null;
		}

	}

?>
