<?php 


	class Tfrm_StorageReportDLG extends TForm
	{
		var $ID_StorageReport = 0;
		
		function OnLoad()
		{
			$this->dg_StorageFacilityReports->Columns = array(
					array('Caption' => 'Gas Day Start', 'DataType' => 'datetime', 'DataField' => 'GasDayStart'),
					array('Caption' => 'Gas Day End', 'DataType' => 'datetime', 'DataField' => 'GasDayEnd'),
					array('Caption' => 'Facility Identifier', 'DataType' => 'string', 'DataField' => 'StorageFacilityIdentifier'),
					array('Caption' => 'Storage', 'DataType' => 'double', 'DataField' => 'Storage'),
					array('Caption' => 'edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'edit', 'OnClick' => 'btn_EditStorageFacilityReportOnClick')
			);
				
			$this->dg_StorageParticipantActivityReports->Columns = array(
					array('Caption' => 'Gas Day Start', 'DataType' => 'datetime', 'DataField' => 'GasDayStart'),
					array('Caption' => 'Gas Day End', 'DataType' => 'datetime', 'DataField' => 'GasDayEnd'),
					array('Caption' => 'Facility Identifier', 'DataType' => 'string', 'DataField' => 'StorageFacilityIdentifier'),
					array('Caption' => 'Storage', 'DataType' => 'double', 'DataField' => 'Storage'),
					array('Caption' => 'edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'edit', 'OnClick' => 'btn_EditStorageParticipantActivityReportOnClick')
			);
				
			$this->dg_StorageUnavailabilityReports->Columns = array(
					array('Caption' => 'Notification Timestamp', 'DataType' => 'datetime', 'DataField' => 'UnavailabilityNotificationTimestamp'),
					array('Caption' => 'Facility Identifier', 'DataType' => 'string', 'DataField' => 'StorageFacilityIdentifier'),
					array('Caption' => 'Unavailable Volume', 'DataType' => 'double', 'DataField' => 'UnavailableVolume'),
					array('Caption' => 'edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'edit', 'OnClick' => 'btn_EditStorageUnavailabilityReportOnClick')
			);
				
		}
		
		function editEntry($ID_StorageReport)
		{
			$this->ID_StorageReport = $ID_StorageReport;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshStorageFacilityReports()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getStorageFacilityReports', array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport)));

			$this->dg_StorageFacilityReports->DataSet = $ds;
		}

		function RefreshStorageParticipantActivityReports()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getStorageParticipantActivityReports', array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport)));
		
			$this->dg_StorageParticipantActivityReports->DataSet = $ds;
		}
		
		function RefreshStorageUnavailabilityReports()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getStorageUnavailabilityReports', array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport)));
		
			$this->dg_StorageUnavailabilityReports->DataSet = $ds;
		}
				
		function RefreshControls()
		{							
			$this->RefreshStorageFacilityReports();
			$this->RefreshStorageParticipantActivityReports();
			$this->RefreshStorageUnavailabilityReports();			
		}		
		
		function btn_NewStorageFacilityReportOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_StorageFacilityReportDLG.xml');
			if ($frm != null) $frm->newEntry($this->ID_StorageReport);
		}
		
		
		function btn_NewStorageParticipantActivityReportOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_StorageParticipantActivityReportDLG.xml');
			if ($frm != null) $frm->newEntry($this->ID_StorageReport);
		}
		
		function btn_NewStorageUnavailabilityReportOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
		
			$frm = $context->application->CreateForm('frm_StorageUnavailabilityReportDLG.xml');
			if ($frm != null) $frm->newEntry($this->ID_StorageReport);
		}
		
		
		function btn_EditStorageFacilityReportOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_StorageFacilityReportDLG.xml');
			if ($frm != null) $frm->editEntry($this->ID_StorageReport, $varvalue);
		}
		
		function btn_EditStorageParticipantActivityReportOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_StorageParticipantActivityReportDLG.xml');
			if ($frm != null) $frm->editEntry($this->ID_StorageReport, $varvalue);
		}
		
		function btn_EditStorageUnavailabilityReportOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_StorageUnavailabilityReportDLG.xml');
			if ($frm != null) $frm->editEntry($this->ID_StorageReport, $varvalue);
		}
		
		
		function btn_SaveOnClick()
		{
			$this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>