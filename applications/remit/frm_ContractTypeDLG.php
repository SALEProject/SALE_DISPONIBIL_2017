<?php 


	class Tfrm_ContractTypeDLG extends TForm
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
				
				$ds = $db->select('REMIT', 'getContractTypes', array('Arguments' => array('ID_ContractType' => $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_Code->Text = $row['Code'];
				$this->ed_ContractType->Text = $row['Name'];
				$this->ckb_enableTable1->Checked = (bool) $row['enableTable1'];
				$this->ckb_enableTable2->Checked = (bool) $row['enableTable2'];
				
				//TQuark::instance()->browserAlert($this->ed_ContractType->Text);
				
			}				
				
		}
		
		function SaveData()
		{
			$Code = $this->ed_Code->Text;
			$ContractType = $this->ed_ContractType->Text;
			$enableTable1 = (bool) $this->ckb_enableTable1->Checked;
			$enableTable2 = (bool) $this->ckb_enableTable2->Checked;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('Code' => $Code, 'Name' => $ContractType, 'enableTable1' => $enableTable1, 'enableTable2' => $enableTable2));
						$result = $db->execute('REMIT', 'addContractType', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_ContractType' => $this->ID, 'Code' => $Code, 'Name' => $ContractType, 'enableTable1' => $enableTable1, 'enableTable2' => $enableTable2));
						$result = $db->execute('REMIT', 'editContractType', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_ContractTypes')->refreshData();
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