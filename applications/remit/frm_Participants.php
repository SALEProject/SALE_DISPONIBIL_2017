<?php 


	class Tfrm_Participants extends TForm
	{
		var $filterInput = '';
		
		function OnLoad()
		{
			$this->dg_Participants->Columns = array(
				array('Caption' => 'Agency Code', 'DataType' => 'string', 'DataField' => 'AgencyCode'),
				array('Caption' => 'ACER Code', 'DataType' => 'string', 'DataField' => 'ParticipantCode'),
				array('Caption' => 'Agency Name', 'DataType' => 'string', 'DataField' => 'Name'),
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
			$ds = $db->select('REMIT', 'getParticipants', array('Arguments' => array('QueryKeyword' => $this->filterInput)));
			
			$this->dg_Participants->DataSet = $ds; 
		}
		
		function dg_ParticipantsOnFilter($sender, $varName, $varValue)
		{
			$this->filterInput = $varValue;
			$this->refreshData();
		}
		
		function generateThumbHTML()
		{
			$html = '<img src="applications/remit/images/participanti.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}
	
?>
