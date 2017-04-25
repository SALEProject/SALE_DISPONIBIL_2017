<?php 


	class Tfrm_ContractNameDLG extends TForm
	{
		var $ID = 0;
		
		function newEntry()
		{
			$this->showModal();
		}
		
		function editEntry($ID)
		{
			$this->ID = $ID;
			$this->RefreshControls();
			$this->showModal();
		}
		
		function RefreshControls()
		{							
			$context = $this->getContext();
			if($context != null){
				
				$db = $context->application->Reader;				
				
				$ds = $db->select('REMIT', 'getContractNames', array('Arguments' => array('ID_ContractName' => $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_ContractName->Text = $row['Name'];
				//TQuark::instance()->browserAlert($this->ed_ContractName->Text);
			}							
		}
		
		function SaveData()
		{
			$ContractName = $this->ed_ContractName->Text;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('Name' => $ContractName));
						$result = $db->execute('REMIT', 'addContractName', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_ContractName' => $this->ID, 'Name' => $ContractName));
						$result = $db->execute('REMIT', 'editContractName', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_ContractNames')->refreshData();
			}
		}
		
		function btn_SaveOnClick()
		{
			$this->SaveData();
			$this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>