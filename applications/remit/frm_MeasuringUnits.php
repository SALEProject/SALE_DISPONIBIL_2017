<?php

	
	class Tfrm_MeasuringUnits extends TForm
	{
		var $ID_MeasuringUnit;
		
		function OnLoad()
		{
			$this->dg_MeasuringUnits->Columns = array(
				array('Caption' => 'Code', 'DataType' => 'string', 'DataField' => 'Code'),
				array('Caption' => 'Name', 'DataType' => 'string', 'DataField' => 'NameTR'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditMeasuringUnitOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteMeasuringUnitOnClick'),
						
				//array('Caption' => 'Test 3', 'DataType' => 'string', 'DataField' => 'test3')
			);
			$this->refreshData();
		}
		
		function btn_NewMeasuringUnitOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_MeasuringUnitDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_DeleteMeasuringUnitOnClick($sender, $varname, $varvalue)
		{
			$this->ID_MeasuringUnit = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the selected measuring unit?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteMeasuringUnitOnConfirmation');
		}
		
		function btn_DeleteMeasuringUnitOnConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Writer;
			$result = $db->execute('Nomenclators', 'deleteMeasuringUnit', array('Arguments' => array('ID_MeasuringUnit' => $this->ID_MeasuringUnit)));
			$this->refreshData();				
		}
		
		function btn_EditMeasuringUnitOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_MeasuringUnitDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('Nomenclators', 'getMeasuringUnits', array('Arguments' => array('none' => '')));
			
			$this->dg_MeasuringUnits->DataSet = $ds;	
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/measuringUnits.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}