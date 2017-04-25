<?php

if (!defined('Q_GRID_Q'))
{
	define('Q_GRID_Q', true);
	
	class TGrid extends TWidget
	{
		static $DefaultStyle = "
div.bluegray_TGrid
{
	background-color: #dde;
	overflow: scroll;
}

div.bluegray_TGrid table.bluegray_TGrid
{
	
}

div.bluegray_TGrid table.bluegray_TGrid tr.even
{
	background-color: #e8e8e8;	
}

div.bluegray_TGrid table.bluegray_TGrid tr.odd
{
	background-color: #eef;	
}

div.bluegray_TGrid table.bluegray_TGrid tr:hover
{
	background-color: ccd !important;	
}

div.bluegray_TGrid table.bluegray_TGrid tr.selected
{
	background-color: white !important;
	color: black !important;
}

div.bluegray_TGrid table.bluegray_TGrid tr.selected:hover
{
	background-color: white !important;
	color: black !important;
}

div.bluegray_TGrid table.bluegray_TGrid tr th.bluegray_TGrid
{
	background: -webkit-linear-gradient(#FFFFFF, #EEEEEE) repeat scroll 0 0 transparent;
	background: -o-linear-gradient(#FFFFFF, #EEEEEE) repeat scroll 0 0 transparent;
	background: -moz-linear-gradient(#FFFFFF, #EEEEEE) repeat scroll 0 0 transparent;
	background: linear-gradient(#FFFFFF, #EEEEEE) repeat scroll 0 0 transparent;
/*background-color: #9999ff;
	background: url('bluegray_bkgrnd_th.png') repeat-x;*/
	color: gray;
	font-size: 12px;
	border-bottom: 1px solid #99a;
	border-right: 1px solid #99a;
	padding-left: 4px;
	padding-right: 4px;
	padding-top: 4px;
	padding-bottom: 4px;
}

div.bluegray_TGrid table.bluegray_TGrid tr td.bluegray_TGrid
{
	/*background-color: #ffffff;*/
	color: black;
	font-size: 12px;
	border-bottom: 1px solid #99a;
	padding-left: 4px;
	padding-right: 4px;
}
";
		private $FDataSet = null;		
		private $FItemIndex = -1;
		
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
			switch ($name)
			{
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
			
			if ($this->st_rendered)
			{
				$frmName = $this->getParentForm()->Name;
				$id = $frmName.'.'.$this->Name;
				$html = $this->generateHTML();
				$html = str_replace('%parent%', $frmName, $html);
				TQuark::instance()->addAjaxStack($id, 'replace', $html);
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

		function generateHTML()
		{
			$id = '%parent%.'.$this->Name;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
						
			$style = $this->generateStyle();
				
			$html =				'<div id="'.$id.'" class="'.$class.'" style="'.$style.'">';
			if ($this->FDataSet != null)
			{
				$html.=			'	<table class="'.$class.' '.$this->CSSClass.'" border="0" cellspacing="0"> '.
								'		<tr> ';
				foreach($this->FDataSet->FieldDefs as $fielddef)
				{
					$html.=		'			<th class="'.$class.'">'.$fielddef.'</th>';
				}
				$html.= 		'		</tr> ';
				
				$i = 0;
				foreach ($this->FDataSet->Rows as $row)
				{
					$id_item = $id.'.'.$i;
					$onclick = 	'getJSform(\'%parent%\').'.$this->Name.'_onclick(\''.$id_item.'\');';
					
					if ($i % 2 == 1) $html.=	'		<tr id="'.$id_item.'" class="'.$class.' odd" onclick="'.$onclick.'">';
					else $html.=				'		<tr id="'.$id_item.'" class="'.$class.' even" onclick="'.$onclick.'">';
					
					foreach ($row as $field)
					{
						$html.=	'			<td class="'.$class.'">'.$field.'</td>';
					}
					$html.=		'		</tr>';

					$i++;
				}
				
				$html.=			'	</table>';
			}
			$html .=			'</div>';
			
			$this->st_rendered = true;				
			return	$html;
		}
		
		function generateJS()
		{
			return	'getJSform(\'%parent%\').'.$this->Name.'_onclick = function(id_item) '."\n".
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
		}
		
	} 
	
	//registerWidget('TGrid', 'TGrid');
	
}

?>