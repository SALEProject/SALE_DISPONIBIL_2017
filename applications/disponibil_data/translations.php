<?php

	require_once 'BRMWebService.php';
	require_once 'BRMDataCache.php';
	
	class TTranslations extends TBRMDataCache
	{
		protected static $FInstance = null;
		var $language = 'RO';
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/translations');
		}
		
		function update($operation, $object)
		{
			$label = $object['Label'];
			$syslabel = strpos($label, 'fld_') === false;
			
			switch ($syslabel)
			{
				case false:
					$this->setPath('.syslabels.'.$label);
					$this->set('Value_EN', $object['Value_EN']);
					$this->set('Value_RO', $object['Value_RO']);
					break;
				case true:
					$this->setPath('.fields.'.$label);
					$this->set('Value_EN', $object['Value_EN']);
					$this->set('Value_RO', $object['Value_RO']);
					break;
			}
		}
		
		function translate($label)
		{
			$translation = $label;
			
			$syslabel = strpos($label, 'fld_') === false;
			if ($syslabel) $this->setPath('.syslabels', true);
			else $this->setPath('.fields', true);
			
			if ($this->locate($label))
			{
				$this->open();
				switch ($this->language)
				{
					case 'EN':
						$translation = $this->get('Value_EN');
						break;
					case 'RO':
						$translation = $this->get('Value_RO');
						break;
				}
				$this->close();
			}
			else 
			{
				//  attempt to read it from webservice and put it in the cache
				$webservice = TBRMWebService::instance(static::$contextID);
				if ($webservice->Login())
				{
					$this->setPath($label);
					
					$ds = $webservice->Reader->select('Nomenclators', 'getTranslations', array('Arguments' => array('Label' => $label)));
					if ($ds != null && $ds->RowsCount > 0)
					{
						$row = $ds->Rows[0];
						$this->set('Value_EN', $row['Value_EN']);
						$this->set('Value_RO', $row['Value_RO']);
						
						switch ($this->language)
						{
							case 'EN':
								$translation = $row['Value_EN'];
								break;
							case 'RO':
								$translation = $row['Value_RO'];
								break;
						}
					}
					else
					{
						$this->set('Value_EN', $label);
						$this->set('Value_RO', $label);
						
						$translation = $label;
					}
				}
			}
			
			return $translation;
		}
	}

?>
