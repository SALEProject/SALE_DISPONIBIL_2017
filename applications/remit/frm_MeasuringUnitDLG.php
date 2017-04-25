<?php 


	class Tfrm_MeasuringUnitDLG extends TForm
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
				
				$ds = $db->select('Nomenclators', 'getMeasuringUnits', array('Arguments' => array('ID_MeasuringUnit' => (int) $this->ID )));
				//TQuark::instance()->browserAlert($ds->RowsCount);
				
				if ($ds == null) return;
				if ($ds->RowsCount == 0) return;
				
				$row = $ds->Rows[0];
				
				$this->ed_Code->Text = $row['Code'];
				$this->ed_MeasuringUnit_EN->Text = $row['Name_EN'];
				$this->ed_MeasuringUnit_RO->Text = $row['Name_RO'];
				
				//TQuark::instance()->browserAlert($this->ed_MeasuringUnit_RO->Text);
				
			}							
		}
		
		function SaveData()
		{
			$Code = $this->ed_Code->Text;
			$MeasuringUnitEN = $this->ed_MeasuringUnit_EN->Text;
			$MeasuringUnitRO = $this->ed_MeasuringUnit_RO->Text;
			
			$context = $this->getContext();
			if ($context != null)
			{
				$db = $context->application->Writer;
				$result = null;
				switch ($this->ID)
				{
					case 0:
						$objects = array('Arguments' => array('Code' => $Code, 'Name_EN' => $MeasuringUnitEN, 'Name_RO' => $MeasuringUnitRO));
						$result = $db->execute('Nomenclators', 'addMeasuringUnit', $objects);
						break;
					default:
						$objects = array('Arguments' => array('ID_MeasuringUnit' => (int) $this->ID, 'Code' => $Code, 'Name_EN' => $MeasuringUnitEN, 'Name_RO' => $MeasuringUnitRO));
						$result = $db->execute('Nomenclators', 'EditMeasuringUnit', $objects);
						break;
				}
				
				TQuark::instance()->getForm('frm_MeasuringUnits')->refreshData();
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