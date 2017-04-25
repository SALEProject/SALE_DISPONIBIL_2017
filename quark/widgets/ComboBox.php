<?php

if (!defined('Q_COMBOBOX_Q'))
{
	define('Q_COMBOBOX_Q', true);
	
	class TComboBox extends TWidget
	{
		var $OnChange = '';
		var $Items = Array();
		private $FItemIndex = -1;
		private $FSelectedKey = null;
		private $FDataSet = null;
		var $DataField = '';
		var $KeyField = '';
				
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'items':
					$this->Items = split('\,', $value);	
					for ($i = 0; $i < count($this->Items); $i++) $this->Items[$i] = trim($this->Items[$i]);
					break;
				case 'datafield':
					$this->DataField = $value;
					break;
				case 'keyfield':
					$this->KeyField = $value;
					break;
				case 'onchange':
					$this->OnChange = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function get_DataSet()
		{
			return $this->FDataSet;
		}
		
		function set_DataSet($value)
		{
			$this->FDataSet = $value;
			if ($this->FDataSet == null) return;
			if (!is_object($this->FDataSet)) return;
			if (!($this->FDataSet instanceof TDataSet)) return;

			$this->Items = array();

			//  set rows
			$idx = 0;
			foreach ($this->FDataSet->Rows as $row)
			{
				$value = ''; if (isset($row[$this->DataField])) $value = $row[$this->DataField];
				$key = ''; if (isset($row[$this->KeyField])) $key = $row[$this->KeyField];
				
				$idx++;
				$this->Items[] = $value;
			}
		}
		
		function formatValue($keyfield, $key, $value, $dataType, $onclick)
		{
			$id = $this->id;
				
			$onclick_event = '';
			if ($onclick != '')
				$onclick_event = 'onclick="'.'getJSform(\''.$this->Parent->id.'\').callBack(\''.$onclick.'\', undefined, \''.$id.'\', \''.$keyfield.'\', \''.$key.'\'); return false;"';
				/*if ($this->OnButtonClick != '')
				 $onclick_event = 'onclick="'.'getJSform(\''.$this->Parent->id.'\').callBack(\''.$this->OnButtonClick.'\', undefined, \''.$id.'\', 123, \''.$value.'\'); return false;"';*/
					
				switch ($dataType)
				{
					case 'boolean':
						if ($value == true) return '<input type="checkbox" value="1" checked disabled></input>';
						else return '<input type="checkbox" value="0" disabled></input>';
					case 'hyperlink':
						return '<a href="" '.$onclick_event.'>'.$value.'</a>';
					case 'integer':
						return $value;
					case 'float':
					case 'double':
						return $value;
					case 'date':
						return $value;
					case 'time':
						return $value;
					case 'datetime':
						return $value;
					case 'string':
						return $value;
					default:
						return $value;
				}
		}
		
		function get_ItemIndex()
		{
			return $this->FItemIndex;
		}
		
		function set_ItemIndex($value)
		{
			if ($value < 0 || $value >= count($this->Items)) return;
			
			if ($value != $this->FItemIndex)
			{
				$this->FItemIndex = $value;
				$this->FSelectedKey = null;
				
				//  search the key
				if ($this->FDataSet != null && is_object($this->FDataSet) && $this->FDataSet instanceof TDataSet && $this->KeyField != '')
				{
					$idx = -1;
					foreach ($this->FDataSet->Rows as $row)
					{
						$idx++;
						if ($idx == $this->FItemIndex && isset($row[$this->KeyField])) $this->FSelectedKey = $row[$this->KeyField];
					}
				}

				
				//  trigger the OnChange event if set
				if ($this->OnChange != '')
				{
					$onchange = $this->OnChange;
					$frm = $this->getParentForm();
					if ($frm != null) $frm->$onchange($this);
				}
			} 
		}
		
		function get_SelectedKey()
		{
			return $this->FSelectedKey;
		}
		
		function set_SelectedKey($value)
		{
			//  search the key
			if ($this->FDataSet != null && is_object($this->FDataSet) && $this->FDataSet instanceof TDataSet && $this->KeyField != '')
			{
				$idx = -1;
				foreach ($this->FDataSet->Rows as $row)
				{
					$idx++;
					if (isset($row[$this->KeyField]) && $row[$this->KeyField] == $value) 
					{
						$this->FSelectedKey = $value;
						$this->FItemIndex = $idx;
					}
				}
			}
		}
		
		function get_Text()
		{
			if ($this->FItemIndex < 0 || $this->FItemIndex >= count($this->Items)) return '';
			else return $this->Items[$this->FItemIndex];
		}
		
		function set_Text($value)
		{
			$this->FItemIndex = -1;
			
			$i = -1;
			$b = false;
			while (!$b && $i < count($this->Items) - 1)
			{
				$i++;
				if ($this->Items[$i] == $value) $b = true;
			}
			
			if ($b) $this->set_ItemIndex($i);
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
						
			$style = $this->generateStyle();
			
			$id = '%parent%.'.$this->Name;
			
			$html = '<select id="'.$id.'" class="'.$class.'" style="'.$style.'" name="'.$this->Name.'" >';
			$idx = 0;
			foreach ($this->Items as $item)
			{
				if ($idx == $this->FItemIndex)
					$html.= '	<option value="'.$idx.'" selected="selected">'.$item.'</option>';
				else
					$html.= '	<option value="'.$idx.'">'.$item.'</option>';
				$idx++;
			}
			$html.=	'</select>';
			
			return $html;
		}
		
		function generateJS()
		{
			
		}
		
		function setValue($value)
		{
			//TQuark::instance()->browserAlert($value);
			$this->ItemIndex = $value;
		}
	}
	
	//registerWidget('TComboBox', 'TComboBox');

}

?>