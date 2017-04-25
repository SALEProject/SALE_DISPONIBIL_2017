<?php

if (!defined('Q_RADIOBUTTON_Q'))
{
	define('Q_RADIOBUTTON_Q', true);

	class TRadioButton extends TWidget
	{
		private $FCaption = '';
		private $FChecked = false;
		var $OnClick = '';
		var $RadioGroup = 0;
		
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
			if ($this->Theme != '' ) $class = $this->Theme.'_'.$this->ClassName;
			
			$style = $this->generateStyle();
			
			$id = $this->id;
			
			$onclick =	'onclick="var a = $(\'[data-radiogroup]\'); '.
						'for (var i = 0; i < a.length; i++) '.
						'if (a[i].tagName == \'INPUT\') '.
						'{ '.
						'  if (a[i].id == this.id) a[i].checked = true; '.
						'  else a[i].checked = false; '.
						'} "';
			
			$onchange_event = '';
			/*if ($this->OnClick != '') $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->OnClick.'\', undefined, \''.$id.'\');"';
			else $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->Name.'_onclick\', undefined, \''.$id.'\');"';*/			
								
			$html = '<div style="'.$style.'">'."\n".
					'	<input id="'.$id.'" class="'.$class.'" type="radio" name="'.$this->Name.'" data-radiogroup="'.$this->getParentForm()->id.'_'.$this->RadioGroup.'" value="'.$this->Caption.'" '.($this->FChecked == true ? 'checked="checked"' : '').' '.$onclick.' '.$onchange_event.' ></input>'.
					'	<label for="'.$id.'">'.$this->FCaption.'</label>'.
					//'	<span id="'.$id.'_caption" class="'.$class.'_caption" style="cursor: pointer;" onclick="$(\''.$this->id.'\').click();">'.$this->FCaption.'</span>'."\n".
					'</div>';
			
			return $html;
		}

		function setValue($value)
		{
			if (is_bool($value)) $this->FChecked = $value;
			else if (is_string($value))
			{
				if ($value == 'true') $this->FChecked = true;
				else $this->FChecked = false;
			}
			else $this->FChecked = false;
		}
	}
	
	//registerWidget('TRadioButton', 'TRadioButton');

}

?>