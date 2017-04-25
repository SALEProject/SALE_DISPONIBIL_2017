<?php

if (!defined('Q_EDIT_Q'))
{
	define('Q_EDIT_Q', true);
	
	class TEdit extends TWidget
	{
		static $DefaultStyle = "
input[type='text'].default_TEdit
{
	border-color: #a0a0ff;
	border-style: solid;
	border-width: 1px;
	border-radius: 5px;
}
				";
		private $FText = '';
		var $Hint = '';
		var $Password = false;
		var $Alignment = '';
		var $Mask = '';
		var $Matches = false;
		
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
				case 'password':
					if (strtolower(trim($value)) == 'true') $this->Password = true;
					else $this->Password = false;
					break;
				case 'alignment':
					$this->Alignment = $value;
					break;
				case 'mask':
					$this->Mask = $value;
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
				if ($this->st_rendered) TQuark::instance()->browserScript('$("'.$this->id.'").value = "'.$value.'"');
			}
		}
		
		function resetClassName()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			if ($this->Hint != '') $class .= ' hint';
						
			if($this->st_rendered) TQuark::instance()->browserScript('$("'.$this->id.'").className = "'.$class.'"');
			
		}
		
		function addClassName($value)
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;

			$class .= $value;
			
			if($this->st_rendered) TQuark::instance()->browserScript('$("'.$this->id.'").className = "'.$class.'"');
		}


		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			if ($this->Hint != '' && $this->FText == '') $class .= ' hint';
						
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
			
			$type = 'text';
			if ($this->Password && $this->Hint == '') $type = 'password';
			
			$hint = '';
			if ($this->Hint != '') 
			{
				switch ($this->Password)
				{
					case false:
						$hint =	' onfocus="if (this.value == \''.$this->Hint.'\') this.value = \'\'; $removeClass(this.id, \'hint\');  "'.
								' onblur="if (this.value == \'\') { this.value = \''.$this->Hint.'\'; $addClass(this.id, \'hint\'); } " ';
						break;
					case true:
						$hint =	' onfocus="if (this.value == \''.$this->Hint.'\') { this.value = \'\'; $removeClass(this.id, \'hint\');  this.type=\'password\'; }"'.
								' onblur="if (this.value == \'\') { this.value = \''.$this->Hint.'\'; $addClass(this.id, \'hint\'); this.type=\'text\'; } " ';
						break;
				}
			}
			
			/*if($this->Mask != ''){
				$mask = ' onchange="var re = '.$this->Hint.'; if (this.value.match(re)) alert("suckcess") else alert("Fail")" ';
			}*/
				
			$text = '';
			if ($this->FText != '') $text = $this->FText;
			else $text = $this->Hint;
			
			$id = $this->id; //'%parent%.'.$this->Name;
			
			$this->st_rendered = true;
			//return '<input id="'.$id.'" class="'.$class.'" style="'.$style.'" type="'.$type.'" name="'.$this->Name.'" value="'.$text.'" '.$hint.'></input>';
			
			$onkeyup = '';
			$mask = '';
			if($this->Mask != '')
			{
				$mask = $this->Mask;
				$onkeyup = ' onkeyup=" var valid = (this.value && this.pattern && new RegExp(this.pattern).test(this.value) ); '.
						    '            if(!valid){ $addClass(this.id, \'invalid\');  }'. 
							'            else { $removeClass(this.id, \'invalid\'); $addClass(this.id, \'verified\'); } "';
			}
			
			return '<input '.$onkeyup.' pattern="'.$mask.'" id="'.$id.'" class="'.$class.'" style="'.$style.'" type="'.$type.'" name="'.$this->Name.'" value="'.$text.'" '.$hint.'></input>';
				
		}
		
		function setValue($value)
		{
			if ($value != $this->Hint) 
			{				
				$this->FText = $value;
			}
			else $this->FText = '';
			
			if($this->Mask != '')
			{
				$matches = preg_match_all('/'.$this->Mask.'/', $this->Text);
					
				if ($matches) $this->Matches = true;
				else $this->Matches = false;
			}
			else $this->Matches = true;
		}
		
	} 
	
	//registerWidget('TEdit', 'TEdit');
	
}

?>