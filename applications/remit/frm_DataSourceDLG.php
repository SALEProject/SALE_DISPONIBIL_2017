<?php 


	class Tfrm_DataSourceDLG extends TForm
	{
		var $ID = 0;
		
		function newEntry()
		{
			$this->showModal();
		}
		
		function editEntry($ID)
		{
			$this->ID = $ID;
			$this->showModal();
		}
		
		function SaveData()
		{
			$DataSourceName = $this->ed_DataSourceName->Text;
			$DataSourceType = $this->cb_DataSourceType->Text;
			$isActive = $this->ckb_isActive->Checked;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('DataSourceName' => $DataSourceName, 'DataSourceType' => $DataSourceType, 'isActive' => $isActive));
						$result = $db->execute('REMIT', 'addDataSource', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_DataSource' => $this->ID, 'DataSourceName' => $DataSourceName, 'DataSourceType' => $DataSourceType, 'isActive' => $isActive));
						$result = $db->execute('REMIT', 'editDataSource', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_DataSources')->refreshData();
			}
		}
		
		function btn_SaveOnClick()
		{
			$this->SaveData();
			$this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>