<?php 


	class Tfrm_LoadTypeDLG extends TForm
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
				
				$ds = $db->select('REMIT', 'getLoadTypes', array('Arguments' => array('ID_LoadType' => (int) $this->ID )));
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_LoadType->Text = $row['Name'];
				$this->ed_Code->Text = $row['Code'];				
			}				
				
		}
		
		function SaveData()
		{
			$LoadType = $this->ed_LoadType->Text;
			$Code = $this->ed_Code->Text;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('Code' => $Code, 'Name' => $LoadType));
						$result = $db->execute('REMIT', 'addLoadType', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_LoadType' => (int) $this->ID, 'Code' => $Code, 'Name' => $LoadType));
						$result = $db->execute('REMIT', 'editLoadType', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_LoadTypes')->refreshData();
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