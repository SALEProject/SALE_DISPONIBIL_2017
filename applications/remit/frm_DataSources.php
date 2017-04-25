<?php 


	class Tfrm_DataSources extends TForm
	{
		var $ID_DataSource;
		function OnLoad()
		{
			$this->dg_DataSources->Columns = array(
					array('Caption' => 'Datasource Name', 'DataType' => 'string', 'DataField' => 'DataSourceName'),
					array('Caption' => 'Datasource Type', 'DataType' => 'string', 'DataField' => 'DataSourceType'),
					array('Caption' => 'Active', 'DataType' => 'boolean', 'DataField' => 'isActive'),
					array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditDataSourceOnClick'),
					array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteDataSourceOnClick'),
			);				
			$this->refreshData();
		}
			
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSources', array('Arguments' => array('cucu' => 'bau')));
			
			$this->dg_DataSources->DataSet = $ds; 
		}
		
		function btn_NewDataSourceOnClick()
		{
			$context = $this->getContext();
			$frm = $context->application->CreateForm('frm_DataSourceDLG.xml');
			if ($frm != null) $frm->newEntry();
		}

		function btn_DeleteDataSourceOnClick($sender, $varname, $varvalue)
		{
			$this->ID_DataSource = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the selected item?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteDataSourceOnConfirmation');
		}
		
		function btn_DeleteDataSourceOnConfirmation($sender, $varname, $varvalue)
		{
			if ($varvalue != 'mrYes') return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Writer;
			$result = $db->execute('REMIT', 'deleteDataSource', array('Arguments' => array('ID_DataSource' => $this->ID_DataSource)));
			$this->refreshData();				
		}
		
		function generateThumbHTML()
		{
			$html = '<img src="applications/remit/images/483-file-excel.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}
	
?>