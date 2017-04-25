<?php 


	class Tfrm_StorageParticipantActivityReportDLG extends TForm
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
				
				$ds = $db->select('REMIT', 'getStorageParticipantActivityReports', 
										array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport,
											                       'ID_StorageParticipantActivityReport' => $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;

				$row = $ds->Rows[0];
				
				$GasDayStart = explode('T', $row['GasDayStart']);
				$GasDayEnd = explode('T', $row['GasDayEnd']);
				
				$this->dp_GasDayStart->Text = $GasDayStart[0];
				$this->ed_GasDayStart->Text = $GasDayStart[1];
				
				$this->dp_GasDayEnd->Text = $GasDayEnd[0];
				$this->ed_GasDayEnd->Text = $GasDayEnd[1];
				
				$this->ed_Storage->Text = $row['Storage'];
				$this->ed_StorageFacilityIdentifier->Text = $row['StorageFacilityIdentifier'];
				$this->ed_StorageFacilityOperatorIdentifier->Text = $row['StorageFacilityOperatorIdentifier'];
				$this->ed_MarketParticipantIdentifier->Text = $row['MarketParticipantIdentifier'];
				$this->ed_ReportingEntityReferenceID->Text = $row['ReportingEntityReferenceID'];
				
				$this->cb_StorageFacilityIdentifierType->Text = $row['StorageFacilityIdentifierType'];
				$this->cb_StorageFacilityOperatorIdentifierType->Text = $row['StorageFacilityOperatorIdentifierType'];
				$this->cb_MarketParticipantIdentifierType->Text = $row['MarketParticipantIdentifierType'];
		
				$this->cb_StorageMU->Text = $row['StorageMU'];
				
				//$this->ed_ContractName->Text = $row['Name'];
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
															  'MarketParticipantIdentifier' => $this->ed_MarketParticipantIdentifier->Text,
															  'Storage' => $this->ed_Storage->Text,
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'MarketParticipantIdentifierType' => $this->cb_MarketParticipantIdentifierType->Text,
															  'StorageMU' => $this->cb_StorageMU->Text /*== null ? 0 : $this->cb_StorageMU->SelectedKey*/
															  ));
						
						if($db->execute('REMIT', 'addStorageParticipantActivityReport', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
					default:
						$objects = array('Arguments' => array('ID_StorageReport' => (int) $this->ID_StorageReport,
															  'ID_StorageParticipantActivityReport' => (int) $this->ID,
															  'GasDayStart' => $GasDayStart,
															  'GasDayEnd' => $GasDayEnd,
															  'StorageFacilityIdentifier' => $this->ed_StorageFacilityIdentifier->Text,
															  'StorageFacilityOperatorIdentifier' => $this->ed_StorageFacilityOperatorIdentifier->Text,
															  'MarketParticipantIdentifier' => $this->ed_MarketParticipantIdentifier->Text,
															  'Storage' => $this->ed_Storage->Text,
															  'StorageFacilityIdentifierType' => $this->cb_StorageFacilityIdentifierType->Text,
															  'StorageFacilityOperatorIdentifierType' => $this->cb_StorageFacilityOperatorIdentifierType->Text,
															  'MarketParticipantIdentifierType' => $this->cb_MarketParticipantIdentifierType->Text,
															  'StorageMU' => $this->cb_StorageMU->Text /*== null ? 0 : $this->cb_StorageMU->SelectedKey*/
															  ));
						$result = $db->execute('REMIT', 'editStorageParticipantActivityReport', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_StorageReportDLG')->RefreshStorageParticipantActivityReports();
				return true;
			}
		}
		
		
		function btn_SaveOnClick()
		{
			if($this->SaveData()) $this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>