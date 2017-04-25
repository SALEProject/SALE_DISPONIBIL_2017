<?php

if (!defined('Q_CUSTOMGRID_Q'))
{
	define('Q_CUSTOMGRID_Q', true);
	
	class TCustomGrid extends TWidget
	{
		static $DefaultStyle = "
div.default_TCustomGrid
{
}

div.default_TCustomGrid table
{
	background-color: #c0c0c0;
}

div.default_TCustomGrid table tr
{
}
				
div.default_TCustomGrid table tr td
{
}
				
div.default_TCustomGrid table tr td.fixed
{
	background-color: #e0e0e0;
}
				";
		
		private $FRowsCount = 5;
		private $FColsCount = 5;
		private $FFixedCols = 1;
		private $FFixedRows = 1;
		private $FDefaultColWidth = 64;
		private $FDefaultRowHeight = 22;
		
		//  to be unset in constructor - for IDE autocompletion
		/*var $RowsCount;
		var $ColsCount;
		var $FixedCols;
		var $FixedRows;
		var $DefaultColWidth;
		var $DefaultRowHeight;*/
		
		protected $FOptions = Array();
		protected $FCells = Array();
		private $FOnChange = '';
		private $FOnValidate = '';
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			/*unset($this->$RowsCount);
			unset($this->$ColsCount);
			unset($this->$FixedCols);
			unset($this->$FixedRows);
			unset($this->$DefaultColWidth);
			unset($this->$DefaultRowHeight);*/				
			
			$this->FCells = Array();
			$this->allocateCells();
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'rowscount':
					$this->RowsCount = $value;
					break;
				case 'colscount':
					$this->ColsCount = $value;
					break;
				case 'fixedrows':
					$this->FixedRows = $value;
					break;
				case 'fixedcols':
					$this->FixedCols = $value;
					break;
				case 'defaultcolwidth':
					$this->DefaultColWidth = $value;
					break;
				case 'defaultrowheight':
					$this->DefaultRowHeight = $value;
					break;
				case 'options':
					$this->Options = $value;
					break;
				case 'onchange':
					$this->OnChange = $value;
					break;
				case'onvalidate':
					$this->OnValidate = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		protected function get_RowsCount()
		{
			return $this->FRowsCount;
		}
		
		protected function set_RowsCount($value)
		{
			$this->FRowsCount = $value;
			$this->allocateCells();
		}
		
		protected function get_ColsCount()
		{
			return $this->FColsCount;
		}
		
		protected function set_ColsCount($value)
		{
			$this->FColsCount = $value;
			$this->allocateCells();
		}
		
		protected function get_FixedRows()
		{
			return $this->FFixedRows;
		}
		
		protected function set_FixedRows($value)
		{
			$this->FFixedRows = $value;
			if ($this->FFixedRows > $this->FRowsCount) $this->RowsCount = $this->FFixedRows;
		}
		
		protected function get_FixedCols()
		{
			return $this->FFixedCols;
		}
		
		protected function set_FixedCols($value)
		{
			$this->FFixedCols = $value;
			if ($this->FFixedCols > $this->FColsCount) $this->ColsCount = $this->FFixedCols;
		}
		
		protected function get_DefaultColWidth()
		{
			return $this->FDefaultColWidth;			
		}
		
		protected function set_DefaultColWidth($value)
		{
			$this->FDefaultColWidth = $value;
		}
		
		protected function get_DefaultRowHeight()
		{
			return $this->FDefaultRowHeight;
		}
		
		protected function set_DefaultRowHeight($value)
		{
			$this->FDefaultRowHeight = $value;
		}
		
		function get_Options()
		{
			return $this->FOptions;
		}
		
		function set_Options($value)
		{
			$obj = json_decode($value);
			if (is_array($obj)) $this->FOptions = $obj;
		}
		
		function get_OnValidate()
		{
			return $this->FOnValidate;
		}
		
		function set_OnValidate($value)
		{
			$this->FOnValidate = $value;
		}
		
		protected function addInternalRow()
		{
			$row = Array();
			for ($i = 0; $i < $this->FColsCount; $i++) $row[] = '';
			$this->FCells[] = $row;
			
			if (count($this->FCells) > $this->FRowsCount) $this->FRowsCount++;
			
			if ($this->st_rendered)
			{		
				$id = $this->id.'_tbody';
				/*$frmname = $this->getParentForm()->Name;
				$id = $this->getParentPath();
				$id.= $this->Name.'_table';*/		
				//$id = frmname$this->Parent->Name.'.'.$this->Name.'_table'; //  id of the table
				$html = $this->generateRowHTML(count($this->FCells) - 1);
				TQuark::instance()->addAjaxStack($id, 'append', $html);
			}
		}
		
		protected function deleteInternalRow()
		{
			if (count($this->FCells) <= $this->FFixedRows) return;
			
			$nrows = count($this->FCells);
			//unset($this->FCells[$nrows - 1]);
			array_splice($this->FCells, -1);
			
			if (count($this->FCells) < $this->FRowsCount) $this->FRowsCount--;
			
			if ($this->st_rendered)
			{
				//$id = $this->Parent->Name.'.'.$this->Name.'.'.($nrows - 1);
				$id = $this->id.'.'.($nrows - 1);
				TQuark::instance()->addAjaxStack($id, 'delete', '');
			}
		}
		
		protected function allocateCells()
		{
			while (count($this->FCells) < $this->FRowsCount) $this->addInternalRow();			
			while (count($this->FCells) > $this->FRowsCount) $this->deleteInternalRow();
			
			for ($i = 0; $i < count($this->FCells); $i++)
			{
				$ncols = count($this->FCells[$i]);
				if ($ncols != $this->FColsCount)
				{
					while ($ncols < $this->FColsCount) 
					{
						$this->FCells[$i][] = '';
						$ncols++;
					}
					
					while ($ncols > $this->FColsCount) 
					{
						unset($this->FCells[$i][$ncols - 1]);
						array_slice($this->FCells[$i], -1);
						$ncols--;
					}
					
					if ($this->st_rendered)
					{
						//$id = $this->Parent->Name.'.'.$this->Name.'.'.$i;
						$id = $this->id.'.'.$i;
						$html = $this->generateRowHTML($i);
						TQuark::instance()->addAjaxStack($id, 'replace', $html);
					}
				}
			}
		}
		
		function getCell($ACol, $ARow)
		{
			if ($ACol < 0 || $ACol >= $this->FColsCount) return null;
			if ($ARow < 0 || $ARow >= $this->FRowsCount) return null;
			
			return $this->FCells[$ARow][$ACol];
		}
		
		function setCell($ACol, $ARow, $value)
		{
			if ($ACol < 0 || $ACol >= $this->FColsCount) return;
			if ($ARow < 0 || $ARow >= $this->FRowsCount) return;
			
			if ($this->FCells[$ARow][$ACol] != $value)
			{
				$this->FCells[$ARow][$ACol] = $value;
				if ($this->st_rendered)
				{
					$id = $this->id.'.'.$ARow.'.'.$ACol;
					TQuark::instance()->browserUpdate($id, $value);
				}
			}
		}
		
		protected function generateCellHTML($ACol, $ARow)
		{
			if ($ACol < 0 || $ACol >= $this->FColsCount) return '';
			if ($ARow < 0 || $ARow >= $this->FRowsCount) return '';
			
			//$id = '%parent%.'.$this->Name.'.'.$ARow.'.'.$ACol;
			$id = $this->id.'.'.$ARow.'.'.$ACol;
			
			$class = '';
			if ($ARow < $this->FFixedRows || $ACol < $this->FFixedCols) $class.= ' fixed';
			else $class.= ($ARow - $this->FFixedRows) % 2 ? ' alternate' : '';
			
			$style = 'width: '.$this->FDefaultColWidth.'px; height: '.$this->FDefaultRowHeight.'px';
			
			$parent = $this->getParentForm()->Name;
			$onclick = 'getJSform(\''.$parent.'\').'.$this->Name.'.enterCell(\''.$id.'\');';
			
			$html =	'<td id="'.$id.'" class="'.$class.'" style="'.$style.'" onclick="'.$onclick.'">'.
					/*$ACol.', '.$ARow*/$this->FCells[$ARow][$ACol].'
					</td>';
			return $html;
		}
		
		protected function generateRowHTML($ARow, $style = '')
		{
			if ($ARow < 0 || $ARow >= $this->FRowsCount) return '';
				
			//$id = '%parent%.'.$this->Name.'.'.$ARow;
			$id = $this->id.'.'.$ARow;
			$html = '<tr id="'.$id.'" '.($style != '' ? 'style="'.$style.'"' : '').'>';
			for ($i = 0; $i < count($this->FCells[$ARow]); $i++)
			{
				$html.= $this->generateCellHTML($i, $ARow);
			}				
			$html.= '</tr>';
			
			return $html;
		}
	
		function generateHTML()
		{
			//$id = '%parent%.'.$this->Name;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
						
			$style = $this->generateStyle();
			
			$html = '<div id="'.$this->id.'" class="'.$class.'" style="'.$style.'">'."\n";
			$html.= '	<table id="'.$this->id.'_table">'."\n".
					'		<tbody id="'.$this->id.'_tbody">'."\n";
			
			for ($i = 0; $i < count($this->FCells); $i++)
			{
				$html.= $this->generateRowHTML($i)."\n";
			}
			
			$html.=	'	</tbody></table> '."\n";
			$html.=	'</div>';
			
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$js = $this->innerJS();
			
			$handleid = TQuark::instance()->registerHandler($this, 'internalOnValidate');
			$js = str_replace('%internalOnValidate%', $handleid, $js);
				
			return $js;
		}
		
		function internalOnValidate($sender, $varName, $varValue)
		{
			//  extract the cell coordinates
			$id = $this->id;
			$s = str_replace($id, '', $varName);
			$a = split('\.', $s);
			
			$k = count($a);
			if ($k < 2) return;
			
			$ARow = (int) $a[$k - 2];
			$ACol = (int) $a[$k - 1];
			
			$method = $this->OnValidate;
			$value = $varValue;
			if (is_array($method))
			{
				call_user_func($method, $sender, $ACol, $ARow, $value);
			}
			else if ($method != '' && method_exists($this->Parent, $method))
			{
				$this->Parent->$method($sender, $ACol, $ARow, $value);
			}
				
			TQuark::instance()->browserUpdate($varName, $value);
		}
	}
	
	//registerWidget('TCustomGrid', 'TCustomGrid');

}

?>