<?php 


	class Tfrm_OrdersHistory extends TForm
	{
		function OnLoad()
		{
			$this->dg_Orders->Columns = array(
				array('Caption' => 'Trade', 'DataType' => 'hyperlink', 'DataField' => 'Trade', 'KeyField' => 'ID', 'Text' => '', 'OnClick' => 'OnDetailsClick'),	
				array('Caption' => 'Participant', 'DataType' => 'string', 'DataField' => 'ParticipantCode', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'Order ID', 'DataType' => 'string', 'DataField' => 'OrderID', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'I/A', 'DataType' => 'string', 'DataField' => 'I/A', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'Date/Time', 'DataType' => 'datetime', 'DataField' => 'Date/Time', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'Volume (MW)', 'DataType' => 'string', 'DataField' => 'Volume', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				//array('Caption' => 'MU', 'DataType' => 'string', 'DataField' => 'MU', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'N.Quantity (MWh)', 'DataType' => 'string', 'DataField' => 'N. Quantity', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				//array('Caption' => 'N.Qty.MU', 'DataType' => 'string', 'DataField' => 'N.Qty. MU', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'Price (RON)', 'DataType' => 'string', 'DataField' => 'Price', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				//array('Caption' => 'Currency', 'DataType' => 'string', 'DataField' => 'Currency', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'Start Delivery', 'DataType' => 'string', 'DataField' => 'Start Delivery', 'KeyField' => '', 'Text' => '', 'OnClick' => ''),	
				array('Caption' => 'End Delivery', 'DataType' => 'string', 'DataField' => 'End Delivery', 'KeyField' => '', 'Text' => '', 'OnClick' => '')	
			);
			$this->refreshData();
		}
		
		function btn_RefreshOnClick()
		{
			$this->refreshData();
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ID_Agency = 0;
			if ($context->application->user["isAdministrator"]) $ID_Agency = -1;
			else if (isset($context->application->user["ID_Agency"])) $ID_Agency = $context->application->user["ID_Agency"];
			if ($ID_Agency == null) $ID_Agency = 0;
			
			$ds = $db->select('REMIT', 'getOrders', array('Arguments' => array('ID_Agency' => $ID_Agency)));
			
			$this->dg_Orders->DataSet = $ds; 
		}
		
		function OnDetailsClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_OrderDetailsDLG.xml');
			if ($frm != null) $frm->showOrderDetails($varvalue);
		}
		
		function generateThumbHTML()
		{
			$html = '<img src="applications/remit/images/history.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
		
	}
	
?>
