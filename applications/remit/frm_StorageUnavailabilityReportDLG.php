<?php 


	class Tfrm_StorageUnavailabilityReportDLG extends TForm
	{
		var $ID = 0;
		var $ID_StorageReport = 0;
		
		function newEntry($ID_StorageReport)
		{
			$this->ID_StorageReport = $ID_StorageReport;
			$this->showModal();
		}
		
		function editEntry($ID_StorageReport, $ID)
		{
			$this->ID_StorageReport = $ID_StorageReport;
			$this->ID = $ID;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshControls()
		{							
			$context = $this->getContext();
			if($context != null){
				
				$db = $context->application->Reader;				
				
				$ds = $db->select('REMIT', 'getStorageUnavailabilityReports', 
								            array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport,
								            						   'ID_StorageUnavailabilityReport' => $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;

				$row = $ds->Rows[0];
				
				$UnavailabilityNotificationTimestamp = explode('T', $row['UnavailabilityNotificationTimestamp']);
				$UnavailabilityStart = explode('T', $row['UnavailabilityStart']);
				$UnavailabilityEnd = explode('T', $row['UnavailabilityEnd']);
				
				$this->dp_UnavailabilityNotificationTimestamp->Text = $UnavailabilityNotificationTimestamp[0];
				$this->ed_UnavailabilityNotificationTimestamp->Text = $UnavailabilityNotificationTimestamp[1];
				
				$this->dp_UnavailabilityStart->Text = $UnavailabilityStart[0];
				$this->ed_UnavailabilityStart->Text = $UnavailabilityStart[1];
				
				$this->dp_UnavailabilityEnd->Text = $UnavailabilityEnd[0];
				$this->ed_UnavailabilityEnd->Text = $UnavailabilityEnd[1];
				
				$this->ckb_UnavailabilityEndFlag->Text = $row['ID_UnavailabilityEndFlag'];
				
				$this->ed_StorageFacilityOperatorIdentifier->Text = $row['StorageFacilityOperatorIdentifier'];
				$this->ed_StorageFacilityIdentifier->Text = $row['StorageFacilityIdentifier'];
				$this->ed_UnavailableVolume->Text = $row['UnavailableVolume'];
				$this->ed_UnavailableInjection->Text = $row['UnavailableInjection'];
				$this->ed_UnavailableWithdrawal->Text = $row['UnavailableWithdrawal'];
				$this->ed_UnavailabilityType->Text = $row['UnavailabilityType'];
				$this->tm_UnavailabilityDescription->Text = $row['UnavailabilityDescription'];
				
				$this->cb_StorageFacilityIdentifierType->Text = $row['StorageFacilityIdentifierType'];
				$this->cb_StorageFacilityOperatorIdentifierType->Text = $row['StorageFacilityOperatorIdentifierType'];
				
				$this->cb_UnavailableVolumeMU->Text = $row['UnavailableVolumeMU'];
				$this->cb_UnavailableWithdrawalMU->Text = $row['UnavailableWithdrawalMU'];
				$this->cb_UnavailableInjectionMU->Text = $row['UnavailableInjectionMU'];
			}							
		}
		
		function SaveData()
		{
			$context = $this->getContext();
			if ($context != null)
			{
				$UnavailabilityNotificationTimestamp = $this->dp_UnavailabilityNotificationTimestamp->Text .'T'. $this->ed_UnavailabilityNotificationTimestamp->Text .':00';
				$UnavailabilityStart = $this->dp_UnavailabilityStart->Text .'T'. $this->ed_UnavailabilityStart->Text .':00';
				$UnavailabilityEnd = $this->dp_UnavailabilityEnd->Text .'T'. $this->ed_UnavailabilityEnd->Text .':00';
				
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('ID_StorageReport' => (int) $this->ID_StorageReport,
															  'UnavailabilityNotificationTimestamp' => $UnavailabilityNotificationTimestamp,
															  'UnavailabilityStart' => $UnavailabilityStart,
															  'UnavailabilityEnd' => $UnavailabilityEnd,
															  'StorageFacilityIdentifier' => $this->ed_StorageFacilityIdentifier->Text, 	
															  'StorageFacilityOperatorIdentifier' => $this->ed_StorageFacilityOperatorIdentifier->Text,
															  'UnavailabilityEndFlag' => $this->cb_UnavailabilityEndFlag->Text,
															  'UnavailableVolume' => $this->ed_UnavailableVolume->Text,
															  'UnavailableInjection' => $this->ed_UnavailableInjection->Text,
															  'UnavailableWithdrawal' => $this->ed_UnavailableWithdrawal->Text,
															  'UnavailabilityDescription' => $this->tm_UnavailabilityDescription->Text,
															  'UnavailabilityType' => $this->cb_UnavailabilityType->Text,
															  
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'UnavailableWithdrawalMU' => $this->cb_UnavailableWithdrawalMU->Text /*== null ? 0 : $this->cb_UnavailableWithdrawalMU->SelectedKey*/,
															  'UnavailableVolumeMU' => $this->cb_UnavailableVolumeMU->Text /*== null ? 0 : $this->cb_UnavailableVolumeMU->SelectedKey*/,
															  'UnavailableInjectionMU' => $this->cb_UnavailableInjectionMU->Text /*== null ? 0 : $this->cb_UnavailableInjectionMU->SelectedKey*/,
															  	
															  ));
						
						if($db->execute('REMIT', 'addStorageUnavailabilityReport', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
					default:
						$objects = array('Arguments' => array('ID_StorageReport' => (int) $this->ID_StorageReport,
															  'ID_StorageUnavailabilityReport' => (int) $this->ID,
															  'UnavailabilityNotificationTimestamp' => $UnavailabilityNotificationTimestamp,
															  'UnavailabilityStart' => $UnavailabilityStart,
															  'UnavailabilityEnd' => $UnavailabilityEnd,
															  'StorageFacilityIdentifier' => $this->ed_StorageFacilityIdentifier->Text, 	
															  'StorageFacilityOperatorIdentifier' => $this->ed_StorageFacilityOperatorIdentifier->Text,
															  'UnavailabilityEndFlag' => $this->cb_UnavailabilityEndFlag->Text,
															  'UnavailableVolume' => $this->ed_UnavailableVolume->Text,
															  'UnavailableInjection' => $this->ed_UnavailableInjection->Text,
															  'UnavailableWithdrawal' => $this->ed_UnavailableWithdrawal->Text,
															  'UnavailabilityDescription' => $this->tm_UnavailabilityDescription->Text,
															  'UnavailabilityType' => $this->cb_UnavailabilityType->Text,
															  
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'UnavailableWithdrawalMU' => $this->cb_UnavailableWithdrawalMU->Text /*== null ? 0 : $this->cb_UnavailableWithdrawalMU->SelectedKey*/,
															  'UnavailableVolumeMU' => $this->cb_UnavailableVolumeMU->Text /*== null ? 0 : $this->cb_UnavailableVolumeMU->SelectedKey*/,
															  'UnavailableInjectionMU' => $this->cb_UnavailableInjectionMU->Text /*== null ? 0 : $this->cb_UnavailableInjectionMU->SelectedKey*/
															  ));
						$result = $db->execute('REMIT', 'editStorageUnavailabilityReport', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_StorageReportDLG')->RefreshStorageUnavailabilityReports();
				return true;
			}
		}
		
		
		function btn_SaveOnClick()
		{
			if ($this->SaveData()) $this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>