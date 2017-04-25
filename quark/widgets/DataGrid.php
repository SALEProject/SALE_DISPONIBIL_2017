<?php

if (!defined('Q_DATAGRID_Q'))
{
	define('Q_DATAGRID_Q', true);
	
	//include 'CustomGrid.php';

	class TDataGrid extends TCustomGrid
	{
		private $FDataSet = null;		
		private $FItemIndex = -1;
		var $RowsPerPage = 0;
		var $Columns = array();
		var $OnButtonClick = '';
		var $OnFilter = '';
		var $ShowFilter = false;
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			$this->Left = 0;
			$this->Top = 0;
			$this->Width = 320;
			$this->Height = 240;
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'onbuttonclick':
					$this->OnButtonClick = $value;
					break;
				case 'rowsperpage':
					$this->RowsPerPage = $value;
					break;
				case 'onfilter':
					$this->OnFilter = $value;
					break;
				case 'showfilter':
					if (strtolower($value) == 'false') $this->ShowFilter = false;
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
			
			//  set columns automatically if they are not already defined
			if (count($this->Columns) == 0)
			{
				foreach ($this->FDataSet->FieldDefs as $col)
				{
					$column = array('Caption' => $col, 'DataType' => 'string', 'DataField' => $col, 'KeyField' => '', 'Text' => '', 'OnClick' => '');
					$this->Columns[] = $column;
				}
			}
			
			$this->ColsCount = count($this->Columns);
			$this->RowsCount = $this->FDataSet->RowsCount + 1;
			$this->FixedCols = 0;
			$this->FixedRows = 1;
							
			//  set header
			for ($i = 0; $i < $this->ColsCount; $i++) $this->setCell($i, 0, $this->Columns[$i]['Caption']);
			
			//  set rows
			$j = 0;
			foreach ($this->FDataSet->Rows as $row)
			{
				$j++;
				$i = 0;
				foreach ($this->Columns as $column)
				{
					$datafield = ''; if (isset($column['DataField'])) $datafield = $column['DataField'];
					$keyfield = ''; if (isset($column['KeyField'])) $keyfield = $column['KeyField'];
					$text = ''; if (isset($column['Text'])) $text = $column['Text'];
					$datatype = ''; if (isset($column['DataType'])) $datatype = $column['DataType'];
					$onclick = ''; if (isset($column['OnClick'])) $onclick = $column['OnClick'];
					
					$value = '';
					if (isset($row[$datafield])) $value = $row[$datafield];
					else if ($text != '') $value = $text;
					
					$key = '';
					if (isset($row[$keyfield])) $key = $row[$keyfield];
					
					if ($value != '') $value = $this->formatValue($keyfield, $key, $value, $datatype, $onclick);
					
					$this->setCell($i, $j, $value);
					$i++;
				}
			}
			
				if($this->RowsPerPage != 0 && $this->getParentForm()->Visible)
					$this->reDrawPagination();
			
			
			/*if ($this->st_rendered)
			{
				$frmName = $this->getParentForm()->Name;
				$id = $frmName.'.'.$this->Name;
				$html = $this->generateHTML();
				$html = str_replace('%parent%', $frmName, $html);
				TQuark::instance()->addAjaxStack($id, 'replace', $html);
			}*/
		}
		
		
		function reDrawPagination()
		{
			$pages = ceil($this->RowsCount / $this->RowsPerPage);
			$end = $this->RowsCount < $this->RowsPerPage ? $this->RowsCount : $this->RowsPerPage;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			
			$script = 	'var obj = getJSform(\''.$this->getParentForm()->id.'\').'.$this->Name.';'."\n".
						'if (obj != null) obj.reDrawPagination(\''.$this->Name.'\', '.$this->RowsCount.', '.$this->RowsPerPage.', '.$this->FixedRows.', '. '\''.$class.'\');';
			TQuark::instance()->browserScript($script);
		}
		
		function formatValue($keyfield, $key, $value, $dataType, $onclick)
		{			
			$id = $this->id;
			
			$onclick_event = '';
			if ($onclick != '') 
				$onclick_event = 'onclick="'.'getJSform(\''.$this->getParentForm()->Name.'\').callBack(\''.$onclick.'\', undefined, \''.$id.'\', \''.$keyfield.'\', \''.$key.'\'); return false;"';
			/*if ($this->OnButtonClick != '')
				$onclick_event = 'onclick="'.'getJSform(\''.$this->Parent->id.'\').callBack(\''.$this->OnButtonClick.'\', undefined, \''.$id.'\', 123, \''.$value.'\'); return false;"';*/
					
			switch (strtolower($dataType))
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
			$this->FItemIndex = $value;
		}
		
		protected function generateFilter()
		{
			if (!$this->ShowFilter) return '';
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			
			$html = '<div id="'.$this->id.'_filterWrap" class="'.$class.'_filter">'.
					'	<input type="text"  oninput="getJSform(\'%parent%\').'.$this->Name.'.filterGrid(event, \''.$this->getParentForm()->id.'\', \''.$this->OnFilter.'\', \''.$class.'\', \''.$this->RowsPerPage.'\');" id="'.$this->id.'_filter"></input>'.
					'	<input type="button" value="X" style="	display: inline;
																margin-left: 6px;
																width: 28px;
																background-color: rgb(21, 38, 57);
																color: rgb(255, 255, 255);
																height: 22px;
																border: 0px none;
																cursor: pointer;
																border-radius: 4px;"
						 onclick="getJSform(\'%parent%\').'.$this->Name.'.resetFilter();" id="'.$this->id.'_reset">'.
					'</div>';

			return $html;
		}
		
		protected function generatePagination($position)
		{
			if ($this->RowsPerPage == 0) return '';
			$nopages = ceil($this->RowsCount / $this->RowsPerPage);
			$nofiltered = 0;
			$start = 1;
			$end = $this->RowsCount < $this->RowsPerPage ? $this->RowsCount : $this->RowsPerPage;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
				
			$html = '<div id="'.$this->id.'_pagination_'.$position.'" class="'.$class.'_pagination">'."\n".
					'	<div class="'.$class.'_pageposition" style="float: left; border-right: 1px solid #CECECE; padding-right: 16px;">'.sprintf('From %d to %d of %d', $start, $end, $this->RowsCount).'</div>'."\n".
					'	<div class="'.$class.'_pages" style="float: right; margin-left: 16px;">';//."\n";
					
			$current = '';
			for ($page = 1; $page <= $nopages; $page++)
			{
				if($page == 1) $current = ' current';
				else $current = '';
				
				$html.=	'<a class="'.$class.'_page'.$current.'" id="'.$this->id.'.page_'.$page.'" onclick="getJSform(\'%parent%\').'.$this->Name.'.navigate('.$this->RowsPerPage.', '.$this->FixedRows.', '.$page.', '.$nopages.', '.$nofiltered.');">'.$page.'</a>';
			}
			
			$html.=	'	</div>'."\n".
					'</div>'."\n".
					'<div id="pages-hidden" style="display:none;">'.$nopages.'</div>';
			
			return $html;
		}

		function generateHTML()
		{
			//$id = '%parent%.'.$this->Name;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
						
			$style = $this->generateStyle();
			
			$id = $this->id;
			
			$html = '<div id="'.$id.'" class="'.$class.'" style="'.$style.'">'."\n";
			//$html.= '	<div id="'.$id.'_filternav"> '.$this->generateFilter().'</div>'."\n";
			$html.= '	<div id="'.$id.'_filternav"> '.$this->generateFilter().$this->generatePagination('filternav').'</div>'."\n";
			$html.= '	<table id="'.$id.'_table"> '."\n";
			$html.= '		<tbody id="'.$this->id.'_tbody">'."\n";
			
			for ($i = 0; $i < count($this->FCells); $i++)
			{
				if ($this->RowsPerPage > 0 && $i >= $this->RowsPerPage + $this->FixedRows) $html.=	$this->generateRowHTML($i, 'display: none');
				else $html.= $this->generateRowHTML($i)."\n";
			}
			
			$html.= '   	</tbody>'."\n";
			$html.=	'	</table> '."\n";
			$html.= '	<div id="'.$id.'_bottomnav"> '.$this->generatePagination('bottomnav').'</div>'."\n";
			$html.=	'</div>';
			
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			 
			$js = $this->innerJS();
				
			$js.=	'jsself.current_className = "'.$class.'";'."\n".
					'getJSform(\'%parent%\').'.$this->Name.'_onclick = function(id_item) '."\n".
					'{ '."\n".
					'	var parent = $(id_item).parentNode; '."\n".
					'	if (parent != null) '."\n".
					'	{ '."\n".
					'		for (var i = 0; i < parent.childNodes.length; i++) '."\n".
					'		{ '."\n".
					'			$removeClass(parent.childNodes[i].id, "selected"); '."\n".
					'		} '."\n".
					'	} '."\n".
					'	'."\n".
					'	$addClass(id_item, "selected"); '."\n".
					'	'."\n".
					'	var a = id_item.split("."); '."\n".
					'	var idx = a[a.length - 1]; '."\n".
					'	callBack("setControlProperty", this.htmlID, "%parent%.'.$this->Name.'", "ItemIndex", idx); '."\n".
					//'	alert("click"); '."\n".
					'} '."\n";		
			
			return $js;
		}
		
	} 
	
	//registerWidget('TDataGrid', 'TDataGrid');
	
}

?>