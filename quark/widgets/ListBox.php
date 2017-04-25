<?php

if (!defined('Q_LISTBOX_Q'))
{
	define('Q_LISTBOX_Q', true);
	
	class TListBox extends TWidget
	{		
		static $DefaultStyle = "
div.default_TListBox_wrapper
{
	
}

ul.default_TListBox
{
	padding-left: 0px;	
}

ul.default_TListBox li
{
	list-style-type: none;
	padding: 4px;
	margin: 4px;	
	cursor: pointer;
}

ul.default_TListBox li.selected
{
	background-color: white;
	border: 1px solid silver;
	border-radius: 3px;
}
				";
		var $OnChange = '';
		
		var $Items = Array();
		private $FItemIndex = -1;
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			$this->Left = 0;
			$this->Top = 0;
			$this->Width = 128;
			$this->Height = 128;
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'onchange':
					$this->OnChange = $value;
					break;
				default:	
					parent::setProperty($name, $value);
					break;
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
				if ($this->OnChange != '')
				{
					$onchange = $this->OnChange;
					$frm = $this->getParentForm();
					if ($frm != null) $frm->$onchange($this);
				}
				//TQuark::instance()->addAjaxStack('', 'alert', 'ItemIndex');
			} 
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$id = '%parent%.'.$this->Name;
			
			$html =	'<div id="'.$id.'_wrapper" class="'.$class.'">'."\n".
					'	<ul id="'.$id.'" class="'.$class.'"> '."\n";
			
			$i = 0;
			foreach ($this->Items as $item) 
			{
				$id_item = $id.'.'.$i;
				$onclick = 	'getJSform(\'%parent%\').'.$this->Name.'_onclick(\''.$id_item.'\');';
				$html .= '		<li id="'.$id_item.'" onclick="'.$onclick.'" class="'.$class.'">'.$item.'</li>'."\n";
				$i++;
			}

			$html .='	</ul> '."\n".
					'</div>'."\n";
			
			return $html;
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
					'	callBack("setControlProperty", this.htmlID, parent.id, "ItemIndex", idx); '."\n".
					//'	alert("click"); '."\n".
					'} '."\n";
		}
	}
	
	//registerWidget('TListBox', 'TListBox');

}

?>