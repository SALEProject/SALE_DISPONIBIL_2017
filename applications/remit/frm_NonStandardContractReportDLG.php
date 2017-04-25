<?php 


	class Tfrm_NonStandardContractReportDLG extends TForm
	{
		var $ID = 0;
		var $ID_Table2Report = 0;
		var $ID_VolumeOptionality = 0;
		var $ID_FixingIndex = 0;
		var $volumeRows = 0;
		var $currentVolumeRowID = -1;
		var $currentVolumeRowIndex = -1;
		var $fixingRows = 0;
		var $currentFixingRowID = -1;
		var $currentFixingRowIndex = -1;
		var $ds_Volumes = null;
		var $ds_Fixings = null;
		
		function OnLoad()
		{
			$this->dg_Volumes->Columns = array(
				array('Caption' => 'Capacity', 'DataType' => 'double', 'DataField' => 'Capacity'),
				array('Caption' => 'MU', 'DataType' => 'string', 'DataField' => 'CapacityMU'),
				array('Caption' => 'Start Date', 'DataType' => 'date', 'DataField' => 'StartDate'),
				array('Caption' => 'End Date', 'DataType' => 'date', 'DataField' => 'EndDate'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' =>'', 'KeyField' => 'RowID', 'Text' => 'edit', 'OnClick' => 'btn_EditVolumeOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' =>'', 'KeyField' => 'RowID', 'Text' => 'delete', 'OnClick' => 'btn_DeleteVolumeOnClick')
			);
			
			$this->dg_Fixings->Columns = array(
				array('Caption' => 'Fixing Index', 'DataType' => 'string', 'DataField' => 'FixingIndex'),
				array('Caption' => 'Index Type', 'DataType' => 'string', 'DataField' => 'FixingIndexType'),
				array('Caption' => 'Index Source', 'DataType' => 'string', 'DataField' => 'FixingIndexSource'),
				array('Caption' => 'First Date', 'DataType' => 'date', 'DataField' => 'FirstFixingDate'),
				array('Caption' => 'Last Date', 'DataType' => 'date', 'DataField' => 'LastFixingDate'),
				array('Caption' => 'Frequency', 'DataType' => 'string', 'DataField' => 'FixingFrequency'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' =>'', 'KeyField' => 'RowID', 'Text' => 'edit', 'OnClick' => 'btn_EditFixingOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' =>'', 'KeyField' => 'RowID', 'Text' => 'delete', 'OnClick' => 'btn_DeleteFixingOnClick')
			);
		}
		
		function newEntry()
		{
			$this->RefreshControls();
			$this->showModal();
		}
		
		function editEntry($ID_Table2Report)
		{
			$this->ID_Table2Report = $ID_Table2Report;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshContractTypes()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getContractTypes', array('Arguments' => array('enableTable2' => true)));
				
			$this->cb_ContractType->DataSet = $ds;	
			$this->cb_FixingIndexType->DataSet = $ds;
		}
		
		function RefreshContractNames()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getContractNames', array('Arguments' => array('none' => '')));
		
			$this->cb_ContractName->DataSet = $ds;
		}
		
		function RefreshCurrencies()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
		
			$db = $context->application->Reader;
			$ds = $db->select('Nomenclators', 'getCurrencies', array('Arguments' => array('none' => '')));
			
			$this->cb_PriceCurrency->DataSet = $ds;	
			$this->cb_NotionalAmountCurrency->DataSet = $ds;
		}
		
		
		function RefreshMeasuringUnits()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
		
			$db = $context->application->Reader;
			$ds = $db->select('Nomenclators', 'getMeasuringUnits', array('Arguments' => array('none' => '')));
			
			$this->cb_VolumeMU->DataSet = $ds;
			$this->cb_NotionaQuantityMU->DataSet = $ds;
		}
		
		function RefreshLoadTypes()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getLoadTypes', array('Arguments' => array('none' => '')));
		
			$this->cb_LoadType->DataSet = $ds;
		}
		
		/*		function RefreshParticipantIDs()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getExistingParticipantIDs', array('Arguments' => array('none' => '')));
			
			$this->cb_ExistingPartIDs->DataSet = $ds;
		}
		
		function RefreshCounterpartIDs()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getExistingCounterpartIDs', array('Arguments' => array('none' => '')));
			
			$this->cb_ExistingCounterpartIDs->DataSet = $ds;
		}*/
		
		function CreateVolumesDS()
		{
			$ds = new TDataSet($this);
			$ds->FieldDefs = array('RowID', 'ID', 'Capacity', 'CapacityMU', 'StartDate', 'EndDate');
			return $ds;
		}
		
		function CreateFixingsDS()
		{
			$ds = new TDataSet($this);
			$ds->FieldDefs = array('RowID', 'ID', 'FixingIndex', 'ID_FixingIndexContractType', 'FixingIndexType', 'FixingIndexSource', 'FirstFixingDate', 'LastFixingDate', 'FixingFrequency');
			return $ds;
		}
		
		function RefreshControls()
		{	
			$this->RefreshContractTypes();
			//$this->RefreshContractNames();
			$this->RefreshCurrencies();
			//$this->RefreshMeasuringUnits();
			$this->RefreshLoadTypes(); 
			//$this->RefreshParticipantIDs();
			//$this->RefreshCounterpartIDs();
			
			if ($this->ID_Table2Report == 0) 
			{
				$this->ds_Volumes = $this->CreateVolumesDS();
				$this->ds_Fixings = $this->CreateFixingsDS();
				
				$this->dg_Volumes->Dataset = $this->ds_Volumes;
				$this->dg_Fixings->Dataset = $this->ds_Fixings;
				return;
			}
			else
			{
				$context = $this->getContext();
				if($context == null) return;				
				$db = $context->application->Reader;
				
				//  Refresh Table2Report data
				$ds = $db->select('REMIT', 'getTable2ReportDetails', array('Arguments' => array('ID_Table2Report' => (int) $this->ID_Table2Report )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;				
				$row = $ds->Rows[0];				
			
				$this->ed_ContractID->Text = $row['ContractID'];				
				$this->cb_ContractName->SelectedKey = $row['ID_ContractName'];
				switch ($row['ActionType'])
				{
					case 'N': $this->cb_ActionType->ItemIndex = 0; break;
					case 'M': $this->cb_ActionType->ItemIndex = 1; break;
					case 'E': $this->cb_ActionType->ItemIndex = 2; break;
					case 'C': $this->cb_ActionType->ItemIndex = 3; break;
				}
				$this->cb_ContractType->SelectedKey = $row['ID_ContractType'];
				$this->ed_DeliveryPointOrZone->Text = $row['DeliveryPoint'];
				$this->ed_ParticipantID->Text = $row['ParticipantIdentifier'];
				$this->cb_ParticipantIDType->Text = $row['ParticipantIdentifierType'];
				$this->ed_CounterpartID->Text = $row['OtherParticipantIdentifier'];
				$this->cb_CounterpartIDType->Text = $row['OtherParticipantIdentifierType'];
				$this->ed_BeneficiaryID->Text = $row['BeneficiaryIdentifier'];
				$this->cb_BeneficiaryIDType->Text = $row['BeneficiaryIdentifierType'];
				$this->ed_Price->Text = $row['Price'];
				$this->cb_PriceCurrency->SelectedKey = $row['ID_Currency'];
				$this->ed_PriceFormula->Text = $row['PriceFormula'];
				$this->ed_NotionalAmount->Text = $row['EstimatedNotionalAmount'];
				$this->cb_NotionalAmountCurrency->SelectedKey = $row['ID_NotionalCurrency'];
				//'Volume' => (Float) $this->ed_Volume->Text,
				//'ID_VolumeMU' => $this->cb_VolumeMU->SelectedKey == null ? 0 : $this->cb_VolumeMU->SelectedKey,
				$this->ed_TotalNotionalQuantity->Text = $row['TotalNotionalQuantity'];
				$this->cb_NotionalQuantityMU->Text = $row['TotalNotionalQuantityMU'];
				$this->ed_TradingCapacity->Text = $row['TradingCapacity'];
				$this->cb_TradingCapacityType->Text = $row['TradingCapacityType'];
				switch ($row['BuySellIndicator'])
				{
					case 'B': $this->cb_BuySell->ItemIndex = 0; break;
					case 'S': $this->cb_BuySell->ItemIndex = 1; break;
					case 'C': $this->cb_BuySell->ItemIndex = 2; break;
				}
				
				$this->dp_ContractDate->Text = $row['ContractDate'];
				$this->dp_DeliveryStartDate->Text = $row['DeliveryStartDate'];
				$this->dp_DeliveryEndDate->Text = $row['DeliveryEndDate'];
					
				//'ID_EnergyCommodityType' => $this->cb_EnergyCommodityType->ItemIndex,
					
				$this->cb_VolumeOptionality->Text = $row['VolumeOptionality'];
				$this->cb_VolumeOptionalityFrequency->Text = $row['VolumeOptionalityFrequency'];
				$this->cb_TypeOfIndexPrice->Text = $row['TypeOfIndexPrice'];
				$this->cb_SettlementMethod->Text = $row['SettlementMethod'];
				$this->cb_LoadType->SelectedKey = $row['ID_LoadType'];				
				
				
				//  refresh Volume Optionalities 
				$this->ds_Volumes = $db->select('REMIT', 'getTable2VolumeOptionalities', array('Arguments' => array('ID_Table2Report' => (int) $this->ID_Table2Report )));
				$this->dg_Volumes->Dataset = $this->ds_Volumes;
				/*if($ds == null) return;
				
				if($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];

				$this->ed_Capacity->Text = $row['Capacity'];
				$this->cb_CapacityMU->ItemIndex = $row['CapacityMU'];
				$this->dp_StartDate->Text = $row['StartDate'];
				$this->dp_EndDate->Text = $row['EndDate'];*/
				
				
				//  refresh Price fixing indeces
				$this->ds_Fixings = $db->select('REMIT', 'getTable2FixingIndexDetails', array('Arguments' => array('ID_Table2Report' => (int) $this->ID_Table2Report )));
				$this->dg_Fixings->Dataset = $this->ds_Fixings;
				/*if($ds == null) return;
				if($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_FixingIndex->Text = $ds['FixingIndex'];
				$this->dp_FirstFixingDate->Text = $ds['FirstFixingDate'];
				$this->cb_FixingIndexTypeType->ItemIndex = $ds['FixingIndexTypeType'];
				$this->dp_LastFixingDate->Text = $ds['LastFixingDate'];
				$this->ed_FixingIndexSource->Text = $ds['FixingIndexSource'];
				$this->cb_FixingFrequency->ItemIndex = $ds['FixingFrequency'];*/
			}
													
		}
		
		function SaveData()
		{					
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				
				$ActionType = 'N';
				switch ($this->cb_ActionType->ItemIndex)
				{
					case 0: $ActionType = 'N'; break;	
					case 1: $ActionType = 'M'; break;	
					case 2: $ActionType = 'E'; break;	
					case 3: $ActionType = 'C'; break;	
				}
				
				switch ($this->ID_Table2Report)
				{
					case 0:
						$objects = array('Arguments' => array(
							'ContractID' => $this->ed_ContractID->Text,
							'ID_ContractName' => $this->cb_ContractName->SelectedKey == null ? 0 : $this->cb_ContractName->SelectedKey,
							'ActionType' => $ActionType,
							'ID_ContractType' => $this->cb_ContractType->SelectedKey == null ? 0 : $this->cb_ContractType->SelectedKey,
							'DeliveryPoint' => $this->ed_DeliveryPointOrZone->Text,
							'ParticipantIdentifier' => $this->ed_ParticipantID->Text,
							'ParticipantIdentifierType' => $this->cb_ParticipantIDType->Text,															  
							'OtherParticipantIdentifier' => $this->ed_CounterpartID->Text,
							'OtherParticipantIdentifierType' => $this->cb_CounterpartIDType->Text,															  
							'BeneficiaryIdentifier' => $this->ed_BeneficiaryID->Text,
							'BeneficiaryIdentifierType' => $this->cb_BeneficiaryIDType->Text,
							'Price' => (Float) $this->ed_Price->Text,
							'ID_Currency' => $this->cb_PriceCurrency->SelectedKey == null ? 0 : $this->cb_PriceCurrency->SelectedKey,
							'PriceFormula' => $this->ed_PriceFormula->Text,
							'EstimatedNotionalAmount' => $this->ed_NotionalAmount->Text == '' ? 'none' : (Float) $this->ed_NotionalAmount->Text,
							'ID_NotionalCurrency' => $this->cb_NotionalAmountCurrency->SelectedKey == null ? 0 : $this->cb_NotionalAmountCurrency->SelectedKey,
							//'Volume' => (Float) $this->ed_Volume->Text,
							//'ID_VolumeMU' => $this->cb_VolumeMU->SelectedKey == null ? 0 : $this->cb_VolumeMU->SelectedKey,
							'TotalNotionalQuantity' => $this->ed_TotalNotionalQuantity->Text == '' ? 'none' : (Float) $this->ed_TotalNotionalQuantity->Text,
							'TotalNotionalQuantityMU' => $this->cb_NotionalQuantityMU->Text,// == null ? 0 : $this->cb_NotionalQuantityMU->SelectedKey, 
							'TradingCapacity' => (Float) $this->ed_TradingCapacity->Text,
							'TradingCapacityType' => $this->cb_TradingCapacityType->Text,
							'BuySellIndicator' => $this->cb_BuySell->ItemIndex == 0 ? 'B' : ($this->cb_BuySell->ItemIndex == 1 ? 'S' : 'C'),
							  	
							'ContractDate' => $this->dp_ContractDate->Text,
							'DeliveryStartDate' => $this->dp_DeliveryStartDate->Text,
							'DeliveryEndDate' => $this->dp_DeliveryEndDate->Text,
							  
							//'ID_EnergyCommodityType' => $this->cb_EnergyCommodityType->ItemIndex,
							
							'VolumeOptionality' => $this->cb_VolumeOptionality->Text,
							'VolumeOptionalityFrequency' => $this->cb_VolumeOptionalityFrequency->Text,
							'TypeOfIndexPrice' => $this->cb_TypeOfIndexPrice->Text,
							'SettlementMethod' => $this->cb_SettlementMethod->Text,
							'ID_LoadType' => (Int) $this->cb_LoadType->SelectedKey == null ? 0 : $this->cb_LoadType->SelectedKey,
							'VolumeOptionalities' => $this->ds_Volumes->Rows,
							'FixingIndexDetails' => $this->ds_Fixings->Rows
						));
						
						if($db->execute('REMIT', 'addTable2Report', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
					default:
						$objects = array('Arguments' => array(
							'ID_Table2Report' => $this->ID_Table2Report,
							'ContractID' => $this->ed_ContractID->Text,
							'ID_ContractName' => $this->cb_ContractName->SelectedKey == null ? 0 : $this->cb_ContractName->SelectedKey,
							'ActionType' => $ActionType,
							'ID_ContractType' => $this->cb_ContractType->SelectedKey == null ? 0 : $this->cb_ContractType->SelectedKey,
							'DeliveryPoint' => $this->ed_DeliveryPointOrZone->Text,
							'ParticipantIdentifier' => $this->ed_ParticipantID->Text,
							'ParticipantIdentifierType' => $this->cb_ParticipantIDType->Text,															  
							'OtherParticipantIdentifier' => $this->ed_CounterpartID->Text,
							'OtherParticipantIdentifierType' => $this->cb_CounterpartIDType->Text,															  
							'BeneficiaryIdentifier' => $this->ed_BeneficiaryID->Text,
							'BeneficiaryIdentifierType' => $this->cb_BeneficiaryIDType->Text,
							'Price' => (Float) $this->ed_Price->Text,
							'ID_Currency' => $this->cb_PriceCurrency->SelectedKey == null ? 0 : $this->cb_PriceCurrency->SelectedKey,
							'PriceFormula' => $this->ed_PriceFormula->Text,
							'EstimatedNotionalAmount' => $this->ed_NotionalAmount->Text == '' ? 'none' : (Float) $this->ed_NotionalAmount->Text,
							'ID_NotionalCurrency' => $this->cb_NotionalAmountCurrency->SelectedKey == null ? 0 : $this->cb_NotionalAmountCurrency->SelectedKey,
							//'Volume' => (Float) $this->ed_Volume->Text,
							//'ID_VolumeMU' => $this->cb_VolumeMU->SelectedKey == null ? 0 : $this->cb_VolumeMU->SelectedKey,
							'TotalNotionalQuantity' => $this->ed_TotalNotionalQuantity->Text == '' ? 'none' : (Float) $this->ed_TotalNotionalQuantity->Text,
							'TotalNotionalQuantityMU' => $this->cb_NotionalQuantityMU->Text, //== null ? 0 : $this->cb_NotionalQuantityMU->SelectedKey, 
							'TradingCapacity' => (Float) $this->ed_TradingCapacity->Text,
							'TradingCapacityType' => $this->cb_TradingCapacityType->Text,
							'BuySellIndicator' => $this->cb_BuySell->ItemIndex == 0 ? 'B' : ($this->cb_BuySell->ItemIndex == 1 ? 'S' : 'C'),
							  	
							'ContractDate' => $this->dp_ContractDate->Text,
							'DeliveryStartDate' => $this->dp_DeliveryStartDate->Text,
							'DeliveryEndDate' => $this->dp_DeliveryEndDate->Text,
							  
							//'ID_EnergyCommodityType' => $this->cb_EnergyCommodityType->ItemIndex,
							
							'VolumeOptionality' => $this->cb_VolumeOptionality->Text,
							'VolumeOptionalityFrequency' => $this->cb_VolumeOptionalityFrequency->Text,
							'TypeOfIndexPrice' => $this->cb_TypeOfIndexPrice->Text,
							'SettlementMethod' => $this->cb_SettlementMethod->Text,
							'ID_LoadType' => (Int) $this->cb_LoadType->SelectedKey == null ? 0 : $this->cb_LoadType->SelectedKey,
							'VolumeOptionalities' => $this->ds_Volumes->Rows,
							'FixingIndexDetails' => $this->ds_Fixings->Rows
						));
						
						if($db->execute('REMIT', 'editTable2Report', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
				}
				
				TQuark::instance()->getForm('frm_NonStandardContractReports')->refreshData();
				return true;
			}
		}
		
		function ValidateForm()
		{
			if ($this->cb_ActionType->ItemIndex != 0 && !$this->ed_ContractID->Matches) { TQuark::instance()->browserAlert('Invalid Contract ID value'); return false; }
			if (!$this->ed_DeliveryPointOrZone->Matches) { TQuark::instance()->browserAlert('Invalid Delivery Zone value'); return false; }
			if (!$this->ed_ParticipantID->Matches) { TQuark::instance()->browserAlert('Invalid Participant ID value'); return false; }
			//if (!$this->ed_BeneficiaryID->Matches) { TQuark::instance()->browserAlert('Invalid Beneficiary ID value'); return false; }
			if (!$this->ed_Price->Matches && !$this->ed_PriceFormula->Matches) { TQuark::instance()->browserAlert('Invalid Price or Price Formula values'); return false; }
			if (!$this->ed_NotionalAmount->Matches) { TQuark::instance()->browserAlert('Invalid Notional Amount value'); return false; }
			//if (!$this->ed_Volume->Matches) { TQuark::instance()->browserAlert('Invalid Volume value'); return false; }
			if (!$this->ed_TotalNotionalQuantity->Matches) { TQuark::instance()->browserAlert('Invalid Notional Quantity value'); return false; }
			//if (!$this->ed_TradingCapacity->Matches) { TQuark::instance()->browserAlert('Invalid Trading Capacity value'); return false; }
			
			return true;
		}
		
		function btn_NewVolumeOnClick()
		{
			$this->currentVolumeRowID = -1;
			$this->currentFixingRowIndex = -1;
			$this->ID_VolumeOptionality = 0;
			
			$this->ed_Capacity->Text = $this->ed_Capacity->Hint;
			$this->cb_CapacityMU->ItemIndex = 0;
			$this->dp_StartDate->Text = $this->dp_StartDate->Hint;
			$this->dp_EndDate->Text = $this->dp_EndDate->Hint;
			
			$this->ed_Capacity->resetClassName();
			$this->dp_StartDate->resetClassName();
			$this->dp_EndDate->resetClassName();
			
			$this->pnl_VolumeOptionality->Visible = true;
		}
		
		function btn_NewFixingOnClick()
		{
			$this->currentFixingRowID = -1;
			$this->currentFixingRowIndex = -1;
			$this->ID_FixingIndex = 0;
			
			$this->ed_FixingIndex->Text = $this->ed_FixingIndex->Hint;
			$this->cb_FixingIndexType->ItemIndex = 0;
			$this->ed_FixingIndexSource->Text = $this->ed_FixingIndexSource->Hint;
			$this->dp_LastFixingDate->Text = $this->dp_LastFixingDate->Hint;
			$this->dp_FirstFixingDate->Text = $this->dp_FirstFixingDate->Hint;
			$this->cb_FixingFrequency->ItemIndex = 0;
			
			$this->ed_FixingIndex->resetClassName();
			$this->ed_FixingIndexSource->resetClassName();
			$this->dp_LastFixingDate->resetClassName();
			$this->dp_FirstFixingDate->resetClassName();
				
			$this->pnl_FixingDetails->Visible = true;
		}
		
		function ValidateVolume()
		{
			if($this->ed_Capacity->Matches && 
			   $this->ed_Capacity->Text != $this->ed_Capacity->Hint && 
			   $this->dp_StartDate->Text != $this->dp_StartDate->Hint && 
			   $this->dp_EndDate->Text != $this->dp_EndDate->Hint )
					return true;	
					
			return false;
		}
		
		function ValidateFixing()
		{
			if($this->ed_FixingIndex->Matches && $this->ed_FixingIndexSource->Matches && 
			   $this->ed_FixingIndex->Text != $this->ed_FixingIndex->Hint &&
			   $this->ed_FixingIndexSource->Text != $this->ed_FixingIndexSource->Hint && 
			   $this->dp_FirstFixingDate->Text != $this->dp_FirstFixingDate->Hint && 
			   $this->dp_LastFixingDate->Text != $this->dp_LastFixingDate->Hint )
			   		return true;
				
			return false;
		}
		
		function btn_SaveVolumeOnClick()
		{	
			if(!$this->ValidateVolume()) return;
			
			if($this->currentVolumeRowID == -1)
			{	
				$maxRowID = 0;
				$rows = $this->ds_Volumes->Rows;
				for ($i = 0; $i < count($rows); $i++)
					if ($rows[$i]['RowID'] > $maxRowID) $maxRowID = $rows[$i]['RowID'];
				
				$row = array('RowID' => $maxRowID + 1,
						'ID' => $this->ID_VolumeOptionality,
						'Capacity' => $this->ed_Capacity->Text,
						'CapacityMU' => $this->cb_CapacityMU->Text,
						'StartDate' => $this->dp_StartDate->Text,
						'EndDate' => $this->dp_EndDate->Text);
				
				$this->ds_Volumes->addRow($row);
				
				$this->volumeRows++;
			}
			else
			{
				$row = array('RowID' => $this->currentVolumeRowID,
						'ID' => $this->ID_VolumeOptionality,
						'Capacity' => $this->ed_Capacity->Text,
						'CapacityMU' => $this->cb_CapacityMU->Text,
						'StartDate' => $this->dp_StartDate->Text,
						'EndDate' => $this->dp_EndDate->Text);
				
				$this->ds_Volumes->editRow($this->currentVolumeRowIndex, $row);
			}
			
			$this->dg_Volumes->Dataset = $this->ds_Volumes;
				
			$this->ed_Capacity->Text = '';
			$this->cb_CapacityMU->ItemIndex = 0;
			$this->dp_StartDate->Text = '';
			$this->dp_EndDate->Text = '';
					
			$this->currentVolumeRowID = -1;
			$this->currentFixingRowIndex = -1;
			$this->pnl_VolumeOptionality->Visible = false;
		}
		
		
		function btn_SaveFixingOnClick()
		{
			if(!$this->ValidateFixing()) return;
				
			if($this->currentFixingRowID == -1)
			{
				$maxRowID = 0;
				$rows = $this->ds_Fixings->Rows;
				for ($i = 0; $i < count($rows); $i++)
					if ($rows[$i]['RowID'] > $maxRowID) $maxRowID = $rows[$i]['RowID'];
				
				$row = array('RowID' => $maxRowID + 1,
							'ID' => $this->ID_FixingIndex,
							'FixingIndex' => $this->ed_FixingIndex->Text,
							'ID_FixingIndexContractType' => $this->cb_FixingIndexType->SelectedKey,
							'FixingIndexType' => $this->cb_FixingIndexType->Text,
							'FixingIndexSource' => $this->ed_FixingIndexSource->Text,
							'LastFixingDate' => $this->dp_LastFixingDate->Text,
							'FirstFixingDate' => $this->dp_FirstFixingDate->Text, 
							'FixingFrequency' => $this->cb_FixingFrequency->Text
				);				
		
				$this->ds_Fixings->addRow($row);
		
				$this->fixingRows++;
			}
			else
			{
				$row = array('RowID' => $this->currentFixingRowID,
							'ID' => $this->ID_FixingIndex,
							'FixingIndex' => $this->ed_FixingIndex->Text,
							'ID_FixingIndexContractType' => $this->cb_FixingIndexType->SelectedKey,
							'FixingIndexType' => $this->cb_FixingIndexType->Text,
							'FixingIndexSource' => $this->ed_FixingIndexSource->Text,
							'LastFixingDate' => $this->dp_LastFixingDate->Text,
							'FirstFixingDate' => $this->dp_FirstFixingDate->Text, 
							'FixingFrequency' => $this->cb_FixingFrequency->Text
				);				
		
				$this->ds_Fixings->editRow($this->currentFixingRowIndex, $row);
			}
				
			$this->dg_Fixings->Dataset = $this->ds_Fixings;
		
			$this->ed_FixingIndex->Text = '';
			$this->cb_FixingIndexType->ItemIndex = 0;
			$this->ed_FixingIndexSource->Text = '';
			$this->dp_LastFixingDate->Text = '';
			$this->dp_FirstFixingDate->Text = '';
			$this->cb_FixingFrequency->ItemIndex = 0;
				
			$this->currentFixingRowID = -1;
			$this->currentFixingRowIndex = -1;
			$this->pnl_FixingDetails->Visible = false;
		}
		
		function btn_EditVolumeOnClick($sender, $varname, $varvalue)
		{
			$ds = $this->ds_Volumes;
		
			if($ds == null) return;
			if($ds->RowsCount == 0) return;
			
			$this->currentVolumeRowID = $varvalue;
			
			$rows = $ds->Rows;
			$b = false;
			$i = -1;
			while (!$b && $i < count($rows) - 1)
			{
				$i++;
				
				if ($rows[$i]['RowID'] == $varvalue) $b = true;
			}
			
			if ($b)
			{
				$row = $rows[$i];
			
				$this->currentVolumeRowIndex = $i;
				$this->ID_VolumeOptionality = $row['ID'];
				$this->ed_Capacity->Text = $row['Capacity'];
				$this->cb_CapacityMU->ItemIndex = $row['CapacityMU'];
				$this->dp_StartDate->Text = $row['StartDate'];
				$this->dp_EndDate->Text = $row['EndDate'];
			
				$this->ed_Capacity->AddClassName(' verified');
				$this->dp_StartDate->AddClassName(' verified');
				$this->dp_EndDate->AddClassName(' verified');
			
				$this->pnl_VolumeOptionality->Visible = true;
			}
		}
		
		function btn_EditFixingOnClick($sender, $varname, $varvalue)
		{
			$ds = $this->ds_Fixings;
		
			if($ds == null) return;
			if($ds->RowsCount == 0) return;
				
			$this->currentFixingRowID = $varvalue;
				
			$rows = $ds->Rows;
			$b = false;
			$i = -1;
			while (!$b && $i < count($rows) - 1)
			{
				$i++;
				
				if ($rows[$i]['RowID'] == $varvalue) $b = true;
			}
			
			if ($b)
			{
				$row = $rows[$i];
				
				$this->currentFixingRowIndex = $i;
				$this->ID_FixingIndex = $row['ID'];
				$this->ed_FixingIndex->Text = $row['FixingIndex'];
				$this->cb_FixingIndexType->SelectedKey = $row['ID_FixingIndexContractType'];
				$this->ed_FixingIndexSource->Text = $row['FixingIndexSource'];
				$this->dp_LastFixingDate->Text = $row['LastFixingDate'];
				$this->dp_FirstFixingDate->Text = $row['FirstFixingDate'];
				$this->cb_FixingFrequency->ItemIndex = $row['FixingFrequency'];
				
				$this->ed_FixingIndex->AddClassName(' verified');
				$this->ed_FixingIndexSource->AddClassName(' verified');
				$this->dp_LastFixingDate->AddClassName(' verified');
				$this->dp_FirstFixingDate->AddClassName(' verified');
					
				$this->pnl_FixingDetails->Visible = true;
			}
		}
		
		function btn_DeleteVolumeOnClick($sender, $varname, $varvalue)
		{	
			$rows = $this->ds_Volumes->Rows;
			$b = false;
			$i = -1;
			while (!$b && $i < count($rows) - 1)
			{
				$i++;
				
				if ($rows[$i]['RowID'] == $varvalue) $b = true;
			}
			
			if ($b)
			{			
				$this->currentVolumeRowID = $varvalue;
				$this->currentVolumeRowIndex = $i;
				TQuark::instance()->MessageDlg('Are you sure you want to delete the item?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteVolumeOnConfirmation');
			}
		}
		
		function btn_DeleteVolumeOnConfirmation($sender, $varname, $varvalue)
		{				
			if ($varvalue != 'mrYes') return;
			$this->ds_Volumes->deleteRow($this->currentVolumeRowIndex);
			$this->dg_Volumes->Dataset = $this->ds_Volumes;
		}
		
		function btn_DeleteFixingOnClick($sender, $varname, $varvalue)
		{
			$rows = $this->ds_Fixings->Rows;
			$b = false;
			$i = -1;
			while (!$b && $i < count($rows) - 1)
			{
				$i++;
				
				if ($rows[$i]['RowID'] == $varvalue) $b = true;
			}
			
			if ($b)
			{			
				$this->currentFixingRowID = $varvalue;
				$this->currentFixingRowIndex = $i;
				TQuark::instance()->MessageDlg('Are you sure you want to delete the item?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteFixingOnConfirmation');
			}
		}
		
		function btn_DeleteFixingOnConfirmation($sender, $varname, $varvalue)
		{
			if ($varvalue != 'mrYes') return;
			$this->ds_Fixings->deleteRow($this->currentFixingRowIndex);
			$this->dg_Fixings->Dataset = $this->ds_Fixings;
		}
		
		function btn_CancelVolumeOnClick()
		{
			$this->pnl_VolumeOptionality->Visible = false;
		}
		
		function btn_CancelFixingOnClick()
		{
			$this->pnl_FixingDetails->Visible = false;
		}
		
		function btn_SaveOnClick()
		{					
			if(!$this->ValidateForm()) return;
			if ($this->SaveData()) $this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>