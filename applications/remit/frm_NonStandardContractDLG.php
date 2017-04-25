<?php 


	class Tfrm_NonStandardContractDLG extends TForm
	{
		var $ID = 0;
		
		function newEntry()
		{
			$this->RefreshControls();
			$this->showModal();
		}
		
		function editEntry($ID)
		{
			$this->ID = $ID;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshContractTypes()
		{
			$context = $this->getContext();
			if($context == null) return;
			if($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getContractTypes', array('Arguments' => array('none' => '')));
				
			$this->cb_ContractType->DataSet = $ds;			
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
		
		function RefreshParticipantIDs()
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
		}
		
		function RefreshControls()
		{	
			$this->RefreshContractTypes();
			$this->RefreshContractNames();
			$this->RefreshCurrencies();
			//$this->RefreshMeasuringUnits();
			$this->RefreshLoadTypes(); 
			//$this->RefreshParticipantIDs();
			//$this->RefreshCounterpartIDs();
			
			if ($this->ID == 0) return;
									
			$context = $this->getContext();
			if($context != null){
				
				$db = $context->application->Reader;				
				
				$ds = $db->select('REMIT', 'getTable1ReportDetails', array('Arguments' => array('ID_Table1Report' => (int) $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				//$this->ed_ContractID->Text = $row['ContractID'];
				switch ($row['ActionType'])
				{
					case 'N': $this->cb_ActionType->ItemIndex = 0; break;
					case 'M': $this->cb_ActionType->ItemIndex = 1; break;
					case 'E': $this->cb_ActionType->ItemIndex = 2; break;
					case 'C': $this->cb_ActionType->ItemIndex = 3; break;
				}
				$this->cb_ContractName->SelectedKey = $row['ID_ContractName'];
				$this->cb_ContractType->SelectedKey = $row['ID_ContractType'];
				$this->cb_SettlementMethod->Text = $row['SettlementMethod'];
				$this->ed_DeliveryPointOrZone->Text = $row['DeliveryPoint'];
				
				$this->ed_ParticipantID->Text = $row['ParticipantIdentifier'];
				$this->cb_ParticipantIDType->Text = $row['ParticipantIdentifierType'];
				//$this->ed_ParticipantMktID->Text = $row['ParticipantMktID'];
				$this->ed_CounterpartID->Text = $row['CounterpartIdentifier'];
				$this->cb_CounterpartIDType->Text = $row['CounterpartIdentifierType'];				
				//$this->ed_CounterpartMktID->Text = $row['CounterpartMktID'];
				switch ($row['BuySellIndicator'])
				{
					case 'B': $this->cb_BuySell->ItemIndex = 0; break;
					case 'S': $this->cb_BuySell->ItemIndex = 1; break;
					case 'C': $this->cb_BuySell->ItemIndex = 2; break;
				}
				
				$this->ed_TransactionID->Text = $row['TransactionID'];
				$TransactionTimestamp = explode('T', $row['TransactionTimestamp']);
				$this->ed_LinkedTransactionID->Text = $row['LinkedTransactionID'];
				$this->dt_TransactionTimestamp->Text = $TransactionTimestamp[0];
				$this->ed_TransactionTimestamp->Text = $TransactionTimestamp[1];
				
				$this->ed_Price->Text = $row['Price'];
				$this->cb_PriceCurrency->SelectedKey = $row['ID_Currency'];
				$this->ed_PriceFormula->Text = $row['PriceFormula'];
				$this->ed_NotionalAmount->Text = $row['NotionalAmount'];
				$this->cb_NotionalAmount->SelectedKey = $row['ID_NotionalCurrency'];
				$this->ed_Volume->Text = $row['Volume'];
				$this->cb_VolumeMU->Text = $row['VolumeMU'];
				$this->ed_NotionalQuantity->Text = $row['TotalNotionalQuantity'];
				$this->cb_NotionalQuantityMU->Text = $row['TotalNotionalQuantityMU'];
				
				$this->dt_DeliveryStartDate->Text = $row['DeliveryStartDate'];
				$this->dt_DeliveryEndDate->Text = $row['DeliveryEndDate'];
				
				$this->cb_LoadType->SelectedKey = $row['ID_LoadType'];
				//$this->cb_
				
				
				//TQuark::instance()->browserAlert($this->ed_TransactionTimestamp->Text);
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
				
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array(
							//'ContractID' => $this->ed_ContractID->Text,
							'ActionType' => $ActionType,
							'ID_ContractName' => $this->cb_ContractName->SelectedKey == null ? 0 : $this->cb_ContractName->SelectedKey,
							'ID_ContractType' => $this->cb_ContractType->SelectedKey == null ? 0 : $this->cb_ContractType->SelectedKey,
							'SettlementMethod' => $this->cb_SettlementMethod->Text,
							'DeliveryPoint' => $this->ed_DeliveryPointOrZone->Text,
								
							'ParticipantIdentifier' => $this->ed_ParticipantID->Text,
							'ParticipantIdentifierType' => $this->cb_ParticipantIDType->Text,
							//'ParticipantMktID' => $this->ed_ParticipantMktID->Text,
							'CounterpartIdentifier' => $this->ed_CounterpartID->Text,
							'CounterpartIdentifierType' => $this->cb_CounterpartIDType->Text,
							//'CounterpartMktID' => $this->ed_CounterpartMktID->Text,
							'BuySellIndicator' => $this->cb_BuySell->ItemIndex == 0 ? 'B' : ($this->cb_BuySell->ItemIndex == 1 ? 'S' : 'C'),
							
							'TransactionID' => $this->ed_TransactionID->Text,
							'TransactionTimestamp' => $this->dt_TransactionTimestamp->Text .'T'. $this->ed_TransactionTimestamp->Text .':00',
							'LinkedTransactionID' => $this->ed_LinkedTransactionID->Text,
							
							'Price' => (Float) $this->ed_Price->Text, 
							'ID_Currency' => $this->cb_PriceCurrency->SelectedKey == null ? 0 : $this->cb_PriceCurrency->SelectedKey,
							'PriceFormula' => $this->ed_PriceFormula->Text, 
							'NotionalAmount' => (Float) $this->ed_NotionalAmount->Text, 
							'ID_NotionalCurrency' => $this->cb_NotionalAmountCurrency->SelectedKey == null ? 0 : $this->cb_NotionalAmountCurrency->SelectedKey, 
							'Volume' => (Float) $this->ed_Volume->Text, 
							'VolumeMU' => $this->cb_VolumeMU->Text,
							'TotalNotionalQuantity' => (Float) $this->ed_NotionalQuantity->Text, 
							'TotalNotionalQuantityMU' => $this->cb_NotionalQuantityMU->Text, 
							'DeliveryStartDate' => $this->dt_DeliveryStartDate->Text, 
							'DeliveryEndDate' => $this->dt_DeliveryEndDate->Text, 
							'ID_LoadType' => (Int) $this->cb_LoadType->SelectedKey == null ? 0 : $this->cb_LoadType->SelectedKey 
						));
						
						if($db->execute('REMIT', 'addTable1Report', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}
						break;
					default:
						$objects = array('Arguments' => array(
							'ID_Table1Report' => $this->ID,
							//'ContractID' => $this->ed_ContractID->Text,
							'ActionType' => $ActionType,
							'ID_ContractName' => $this->cb_ContractName->SelectedKey == null ? 0 : $this->cb_ContractName->SelectedKey,
							'ID_ContractType' => $this->cb_ContractType->SelectedKey == null ? 0 : $this->cb_ContractType->SelectedKey,
							'SettlementMethod' => $this->cb_SettlementMethod->Text,
							'DeliveryPoint' => $this->ed_DeliveryPointOrZone->Text,
								
							'ParticipantIdentifier' => $this->ed_ParticipantID->Text,
							'ParticipantIdentifierType' => $this->cb_ParticipantIDType->Text,
							//'ParticipantMktID' => $this->ed_ParticipantMktID->Text,
							'CounterpartIdentifier' => $this->ed_CounterpartID->Text,
							'CounterpartIdentifierType' => $this->cb_CounterpartIDType->Text,
							//'CounterpartMktID' => $this->ed_CounterpartMktID->Text,
							'BuySellIndicator' => $this->cb_BuySell->ItemIndex == 0 ? 'B' : ($this->cb_BuySell->ItemIndex == 1 ? 'S' : 'C'),
							
							'TransactionID' => $this->ed_TransactionID->Text,
							'TransactionTimestamp' => $this->dt_TransactionTimestamp->Text .'T'. $this->ed_TransactionTimestamp->Text .':00',
							'LinkedTransactionID' => $this->ed_LinkedTransactionID->Text,
								
							'Price' => (Float) $this->ed_Price->Text, 
							'ID_Currency' => $this->cb_PriceCurrency->SelectedKey == null ? 0 : $this->cb_PriceCurrency->SelectedKey,
							'PriceFormula' => $this->ed_PriceFormula->Text, 
							'NotionalAmount' => (Float) $this->ed_NotionalAmount->Text, 
							'ID_NotionalCurrency' => $this->cb_NotionalAmountCurrency->SelectedKey == null ? 0 : $this->cb_NotionalAmountCurrency->SelectedKey, 
							'Volume' => (Float) $this->ed_Volume->Text, 
							'VolumeMU' => $this->cb_VolumeMU->Text,
							'TotalNotionalQuantity' => (Float) $this->ed_NotionalQuantity->Text, 
							'TotalNotionalQuantityMU' => $this->cb_NotionalQuantityMU->Text, 
							'DeliveryStartDate' => $this->dt_DeliveryStartDate->Text, 
							'DeliveryEndDate' => $this->dt_DeliveryEndDate->Text, 
							'ID_LoadType' => (Int) $this->cb_LoadType->SelectedKey == null ? 0 : $this->cb_LoadType->SelectedKey 
						));
						
						if($db->execute('REMIT', 'editTable1Report', $objects) === false)
						{
							TQuark::instance()->browserAlert(print_r($db->LastResult, true));
							return false;
						}						
						break;
				}
				
				TQuark::instance()->getForm('frm_NonStandardContracts')->refreshData();
				return true;
			}
		}
		
		function ValidateForm()
		{
			//if(!$this->ed_ContractID->Matches) { TQuark::instance()->browserAlert('Invalid Contract ID value'); return false; } 
			if(!$this->ed_ParticipantID->Matches) { TQuark::instance()->browserAlert('Invalid Participant ID value'); return false; }
			if(!$this->ed_CounterpartID->Matches) { TQuark::instance()->browserAlert('Invalid Counterpart ID value'); return false; }
			//if(!$this->ed_ParticipantMktID->Matches) { TQuark::instance()->browserAlert('Invalid Contract ID value'); return false; }
			//if(!$this->ed_CounterpartMktID->Matches) { TQuark::instance()->browserAlert('Invalid Contract ID value'); return false; }
			//if(!$this->ed_TransactionID->Matches) { TQuark::instance()->browserAlert('Invalid Transaction ID value'); return false; }
			if(!$this->ed_Price->Matches) { TQuark::instance()->browserAlert('Invalid Price value'); return false; }
			//if(!$this->ed_PriceFormula->Matches) { TQuark::instance()->browserAlert('Invalid Contract ID value'); return false; }
			if(!$this->ed_NotionalAmount->Matches) { TQuark::instance()->browserAlert('Invalid Notional Amount value'); return false; }
			if(!$this->ed_Volume->Matches) { TQuark::instance()->browserAlert('Invalid Volume value'); return false; }
			if(!$this->ed_NotionalQuantity->Matches) { TQuark::instance()->browserAlert('Invalid Notional Quantity value'); return false; }
						
			return true;
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