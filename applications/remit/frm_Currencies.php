<?php

	
	class Tfrm_Currencies extends TForm
	{
		var $ID_Currency;
		
		function OnLoad()
		{
			$this->dg_Currencies->Columns = array(
				array('Caption' => 'Code', 'DataType' => 'string', 'DataField' => 'Code'),
				array('Caption' => 'Name', 'DataType' => 'string', 'DataField' => 'NameTR'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditCurrencyOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteCurrencyOnClick'),
						
				//array('Caption' => 'Test 3', 'DataType' => 'string', 'DataField' => 'test3')
			);
			$this->refreshData();
		}
		
		function btn_NewCurrencyOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_CurrencyDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_DeleteCurrencyOnClick($sender, $varname, $varvalue)
		{
			$this->ID_Currency = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the selected currency?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteCurrencyOnConfirmation');			
		}
		
		function btn_DeleteCurrencyOnConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Writer;
			$result = $db->execute('Nomenclators', 'deleteCurrency', array('Arguments' => array('ID_Currency' => $this->ID_Currency)));
			$this->refreshData();				
		}
		
		function btn_EditCurrencyOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_CurrencyDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('Nomenclators', 'getCurrencies', array('Arguments' => array('none' => '')));
			
			$this->dg_Currencies->DataSet = $ds;	
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/currencies.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}