<?php

if (!defined('Q_MEMO_Q'))
{
	define('Q_MEMO_Q', true);
	
	class TMemo extends TWidget
	{
		static $DefaultStyle = "
textarea.default_TMemo
{
	border-color: #a0a0ff;
	border-style: solid;
	border-width: 1px;
	border-radius: 5px;
	
}
				";
		private $FText = '';
		var $Hint = '';
		var $Alignment = '';
		
		function setProperty($name, $value)
		{
			switch ($name)
			{
				case 'text':
					$this->Text = $value;
					break;
				case 'hint':
					$this->Hint = $value;
					break;	
				case 'alignment':
					$this->Alignment = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function get_Text()
		{
			return $this->FText;
		}
		
		function set_Text($value)
		{
			if ($value != $this->FText)
			{
				$this->FText = $value;
				if ($this->st_rendered) TQuark::instance()->browserUpdate($this->id, $value);
			}
		}

		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			if ($this->Hint != '') $class .= ' hint';
						
			$style = $this->generateStyle();
			
			switch (strtolower($this->Alignment))
			{
				case 'tacenter':
					$style .= 'text-align: center;';
					break;
				case 'tarightjustify':
					$style .= 'text-align: right;';
					break;
				default:
					$style .= 'text-align: left;';
					break;
			}
			
			if ($this->Style != '') $style .= $this->Style;
			
			$hint = '';
			if ($this->Hint != '') 
			{
				switch ($this->Password)
				{
					case false:
						$hint =	' onfocus="if (this.value == \''.$this->Hint.'\') { this.value = \'\'; $removeClass(this.id, \'hint\'); }"'.
								' onblur="if (this.value == \'\') { this.value = \''.$this->Hint.'\'; $addClass(this.id, \'hint\'); } " ';
						break;
					case true:
						$hint =	' onfocus="if (this.value == \''.$this->Hint.'\') { this.value = \'\'; $removeClass(this.id, \'hint\'); this.type=\'password\'; }"'.
								' onblur="if (this.value == \'\') { this.value = \''.$this->Hint.'\'; $addClass(this.id, \'hint\'); this.type=\'text\'; } " ';
						break;
				}
			}
				
			$text = '';
			if ($this->Hint != '') $text = $this->Hint;
			else $text = $this->Text;
			
			$id = $this->id;
			
			$this->st_rendered = true;
			return '<textarea id="'.$id.'" class="'.$class.'" style="'.$style.'" name="'.$this->Name.'" '.$hint.'>'.$text.'</textarea>';
		}
		
		function setValue($value)
		{
			//TQuark::instance()->browserAlert($value);
			$this->Text = $value;
		}
		
	} 
	
	//registerWidget('TMemo', 'TMemo');
	
}

?>