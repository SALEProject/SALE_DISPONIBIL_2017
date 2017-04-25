<?php

if (!defined('Q_CHECKBOX_Q'))
{
	define('Q_CHECKBOX_Q', true);
	
	class TCheckBox extends TWidget
	{
		static $DefaultStyle = "input[type='checkbox'].default_TCheckBox
{
}		

span.default_TCheckBox_caption
{
}
	";
		
		private $FCaption = '';
		private $FChecked = false;
		var $OnClick = '';
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'caption':
					$this->Caption = $value;
					break;	
				case 'checked':
					if ($value == 'true') $this->FChecked = true;
					else $this->FChecked = false;
					break;
				case 'onclick':
					$this->OnClick = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		protected function get_Caption()
		{
			return $this->FCaption;
		}
		
		protected function set_Caption($value)
		{
			if ($value != $this->FCaption)
			{
				$this->FCaption = $value;
				if ($this->st_rendered) TQuark::instance()->browserScript('$("'.$this->id.'").value = "'.$value.'"');
			}
		}
		
		protected function get_Checked()
		{
			return $this->FChecked;
		}
		
		protected function set_Checked($value)
		{
			if ($value == $this->FChecked) return;
			$this->FChecked = $value;
			if ($this->st_rendered) ;
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;

			$style = $this->generateStyle();
			
			$id = $this->id;
				
			$onchange_event = '';
			/*if ($this->OnClick != '') $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->OnClick.'\', undefined, \''.$id.'\');"';
			else $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->Name.'_onclick\', undefined, \''.$id.'\');"';*/			
								
			$html = '<div style="'.$style.'">'."\n".
					'	<input id="'.$id.'" class="'.$class.'" type="checkbox" name="'.$this->Name.'" value="'.$this->Caption.'" '.($this->FChecked == true ? 'checked="checked"' : '').' '.$onchange_event.' ></input>'.
					'	<span id="'.$id.'_caption" class="'.$class.'_caption">'.$this->FCaption.'</span>'."\n".
					'</div>';
			
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$s = /*'%parent%_'.*/$this->Name;
			
			return	'';/*'getJSform("%parent%").'.$s.'_onclick = '.
					//'$("%parent%").'.$s.'_onclick = './/$s.'_onclick; '.
					'function() './/$s.'_onclick() '.
					'{ '.
					'	this.callBack("'.$s.'_onclick"); '.
					'};';*/
		}
		
		function setValue($value)
		{
			$this->FChecked = $value;
		}
	}
	
	//registerWidget('TCheckBox', 'TCheckBox');

}

?>