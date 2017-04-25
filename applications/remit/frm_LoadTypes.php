<?php

	
	class Tfrm_LoadTypes extends TForm
	{
		var $ID_LoadType;
		
		function OnLoad()
		{
			$this->dg_LoadTypes->Columns = array(
				array('Caption' => 'Code', 'DataType' => 'string', 'DataField' => 'Code'),
				array('Caption' => 'Name', 'DataType' => 'string', 'DataField' => 'Name'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditLoadTypeOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteLoadTypeOnClick'),
						
				//array('Caption' => 'Test 3', 'DataType' => 'string', 'DataField' => 'test3')
			);
			$this->refreshData();
		}
		
		function btn_NewLoadTypeOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_LoadTypeDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_DeleteLoadTypeOnClick($sender, $varname, $varvalue)
		{
			$this->ID_LoadType = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the selected load type?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteLoadTypeOnConfirmation');			
		}

		function btn_DeleteLoadTypeOnConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;
				
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Writer;
			$result = $db->execute('REMIT', 'deleteLoadType', array('Arguments' => array('ID_LoadType' => $this->ID_LoadType)));
			$this->refreshData();				
		}
		
		function btn_EditLoadTypeOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_LoadTypeDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getLoadTypes', array('Arguments' => array('none' => '')));
			
			$this->dg_LoadTypes->DataSet = $ds;	
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/loadTypes.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}