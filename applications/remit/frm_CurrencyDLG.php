<?php 


	class Tfrm_CurrencyDLG extends TForm
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
				//TQuark::instance()->browserAlert($this->ID);
				$db = $context->application->Reader;				
				
				$ds = $db->select('Nomenclators', 'getCurrencies', array('Arguments' => array('ID_Currency' => (int) $this->ID )));
				//TQuark::instance()->browserAlert($ds->RowsCount);
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_Code->Text = $row['Code'];
				$this->ed_Currency_EN->Text = $row['Name_EN'];
				$this->ed_Currency_RO->Text = $row['Name_RO'];
				
				//TQuark::instance()->browserAlert($this->ed_Currency_RO->Text);
				
			}							
		}
		
		function SaveData()
		{
			$Code = $this->ed_Code->Text;
			$CurrencyEN = $this->ed_Currency_EN->Text;
			$CurrencyRO = $this->ed_Currency_RO->Text;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('Code' => $Code, 'Name_EN' => $CurrencyEN, 'Name_RO' => $CurrencyRO));
						$result = $db->execute('Nomenclators', 'addCurrency', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_Currency' => (int) $this->ID, 'Code' => $Code, 'Name_EN' => $CurrencyEN, 'Name_RO' => $CurrencyRO));
						$result = $db->execute('Nomenclators', 'EditCurrency', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_Currencies')->refreshData();
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