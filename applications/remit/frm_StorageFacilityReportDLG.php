<?php 


	class Tfrm_StorageFacilityReportDLG extends TForm
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
			$this->ID = $ID;
			$this->ID_StorageReport = $ID_StorageReport;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshControls()
		{							
			$context = $this->getContext();
			if($context != null){
				
				$db = $context->application->Reader;				
				
				$ds = $db->select('REMIT', 'getStorageFacilityReports',
											 array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport,
											 							'ID_StorageFacilityReport' => $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$GasDayStart = explode('T', $row['GasDayStart']);
				$GasDayEnd = explode('T', $row['GasDayEnd']);
				
				$this->dp_GasDayStart->Text = $GasDayStart[0];
				$this->ed_GasDayStart->Text = $GasDayStart[1];
				
				$this->dp_GasDayEnd->Text = $GasDayEnd[0];
				$this->ed_GasDayEnd->Text = $GasDayEnd[1];
				
				$this->ed_StorageFacilityIdentifier->Text = $row['StorageFacilityIdentifier'];
				$this->ed_StorageFacilityOperatorIdentifier->Text = $row['StorageFacilityOperatorIdentifier'];
				$this->ed_Storage->Text = $row['Storage'];
				
				$this->cb_StorageType->Text = $row['StorageType'];
				$this->ed_Injection->Text = $row['Injection'];
				$this->ed_Withdrawal->Text = $row['Withdrawal'];
				$this->ed_TechnicalCapacity->Text = $row['TechnicalCapacity'];
				$this->ed_ContractedCapacity->Text = $row['ContractedCapacity'];
				$this->ed_AvailableCapacity->Text = $row['AvailableCapacity'];
				
				$this->cb_StorageFacilityIdentifierType->ItemIndex = $row['StorageFacilityIdentifierType'];
				$this->cb_StorageFacilityOperatorIdentifierType->ItemIndex = $row['StorageFacilityOperatorIdentifierType'];
				
				$this->cb_StorageMU->Text = $row['StorageMU'];
				$this->cb_InjectionMU->Text = $row['InjectionMU'];
				$this->cb_WithdrawalMU->Text = $row['WithdrawalMU'];
				$this->cb_TechnicalCapacityMU->Text = $row['TechnicalCapacityMU'];
				$this->cb_ContractedCapacityMU->Text = $row['ContractedCapacityMU'];
				$this->cb_AvailableCapacityMU->Text = $row['AvailableCapacityMU'];				
			}							
		}
		
		function SaveData()
		{
			$context = $this->getContext();
			if ($context != null)
			{
				$GasDayStart = $this->dp_GasDayStart->Text .'T'. $this->ed_GasDayStart->Text .':00';
				$GasDayEnd = $this->dp_GasDayEnd->Text .'T'. $this->ed_GasDayEnd->Text .':00';
				
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('ID_StorageReport' => (int) $this->ID_StorageReport,
															  'GasDayStart' => $GasDayStart,
															  'GasDayEnd' => $GasDayEnd,
															  'StorageFacilityIdentifier' => $this->ed_StorageFacilityIdentifier->Text,
															  'StorageFacilityOperatorIdentifier' => $this->ed_StorageFacilityOperatorIdentifier->Text,
															  'StorageType' => $this->cb_StorageType->Text,
															  'Storage' => $this->ed_Storage->Text,
															  'Injection' => $this->ed_Injection->Text,
															  'Withdrawal' => $this->ed_Withdrawal->Text,
															  'TechnicalCapacity' => $this->ed_TechnicalCapacity->Text,
															  'ContractedCapacity' => $this->ed_ContractedCapacity->Text,
															  'AvailableCapacity' => $this->ed_AvailableCapacity->Text,
															  
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'StorageMU' => $this->cb_StorageMU->Text /*== null ? 0 : $this->cb_StorageMU->SelectedKey*/,
															  'InjectionMU' => $this->cb_InjectionMU->Text /*== null ? 0 : $this->InjectionMU->SelectedKey*/,
															  'WithdrawalMU' => $this->cb_WithdrawalMU->Text /*== null ? 0 : $this->cb_WithdrawalMU->SelectedKey*/,
															  'TechnicalCapacityMU' => $this->cb_TechnicalCapacityMU->Text /*== null ? 0 : $this->cb_TechnicalCapacityMU->SelectedKey*/,
															  'ContractedCapacityMU' => $this->cb_ContractedCapacityMU->Text /*== null ? 0 : $this->cb_ContractedCapacityMU->SelectedKey*/,
															  'AvailableCapacityMU' => $this->cb_AvailableCapacityMU->Text /*== null ? 0 : $this->cb_AvailableMU->SelectedKey*/
															  ));
						
						if($db->execute('REMIT', 'addStorageFacilityReport', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
					default:
						$objects = array('Arguments' => array('ID_StorageReport' => (int) $this->ID_StorageReport,
															  'ID_StorageFacilityReport' => (int) $this->ID,
															  'GasDayStart' => $GasDayStart,
															  'GasDayEnd' => $GasDayEnd,
															  'StorageFacilityIdentifier' => $this->ed_StorageFacilityIdentifier->Text,
															  'StorageFacilityOperatorIdentifier' => $this->ed_StorageFacilityOperatorIdentifier->Text,
															  'StorageType' => $this->cb_StorageType->Text,
															  'Storage' => $this->ed_Storage->Text,
															  'Injection' => $this->ed_Injection->Text,
															  'Withdrawal' => $this->ed_Withdrawal->Text,
															  'TechnicalCapacity' => $this->ed_TechnicalCapacity->Text,
															  'ContractedCapacity' => $this->ed_ContractedCapacity->Text,
															  'AvailableCapacity' => $this->ed_AvailableCapacity->Text ,
															  
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'StorageMU' => $this->cb_StorageMU->Text /*== null ? 0 : $this->cb_StorageMU->SelectedKey*/,
															  'InjectionMU' => $this->cb_InjectionMU->Text /*== null ? 0 : $this->InjectionMU->SelectedKey*/,
															  'WithdrawalMU' => $this->cb_WithdrawalMU->Text /*== null ? 0 : $this->cb_WithdrawalMU->SelectedKey*/,
															  'TechnicalCapacityMU' => $this->cb_TechnicalCapacityMU->Text /*== null ? 0 : $this->cb_TechnicalCapacityMU->SelectedKey*/,
															  'ContractedCapacityMU' => $this->cb_ContractedCapacityMU->Text /*== null ? 0 : $this->cb_ContractedCapacityMU->SelectedKey*/,
															  'AvailableCapacityMU' => $this->cb_AvailableCapacityMU->Text /*== null ? 0 : $this->cb_AvailableMU->SelectedKey*/
															  
															  ));
						
						$result = $db->execute('REMIT', 'editStorageFacilityReport', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_StorageReportDLG')->RefreshStorageFacilityReports();
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