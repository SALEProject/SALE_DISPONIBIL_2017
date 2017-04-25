<?php

if (!defined('Q_DATEPICKER_Q'))
{
	define('Q_DATEPICKER_Q', true);
	
	class TDatePicker extends TWidget
	{
		private $FText = '';
		var $Hint = '';
		var $Format = '';
		
		function setProperty($name, $value)
		{
			switch($name)
			{
				case 'text':
					$this->Text = $value;
					break;
				case 'hint':
					$this->Hint = $value;
					break;
				case 'format':
					$this->Format = $value;
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
		
		
		function setValue($value)
		{
			$this->Text = $value;
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
			
			$style_wrapper = $this->generateStyle();			
			$style =	'width: '.$this->Width.'px; '.
						'height: '.$this->Height.'px;';
			
			$type = 'text';
				
			$hint = '';
			if ($this->Hint != '')
			{
				$hint =	 ' onfocus="var obj = getJSform(\''.$this->getParentForm()->Name.'\').'.$this->Name.';';
				$hint .= ' 		   if(obj != null){';
				$hint .= ' 		   		obj.open();';
				$hint .= ' 				document.addEventListener(\'click\', obj.documentClick(event));';
				$hint .= '			}';
				$hint .= ' if (this.value == \''.$this->Hint.'\') this.value = \'\'; $removeClass(this.id, \'hint\'); $removeClass(this.id, \'verified\'); "';
				//$hint =	' onfocus="this.value = \'\'; $removeClass(this.id, \'hint\'); $removeClass(this.id, \'verified\') "';		
				$hint .= ' onblur=" if (this.value == \'\') { this.value = \''.$this->Hint.'\'; $addClass(this.id, \'hint\'); } if(this.value != \'\' && this.value != \''.$this->Hint.'\') $addClass(this.id, \'verified\'); else $removeClass(this.id, \'verified\');" ';
			}
			
			$text = '';
			if ($this->FText != '') $text = $this->FText;
			else $text = $this->Hint;
			
			$id = $this->id;//'%parent%.'.$this->Name;
			
			$this->st_rendered = true;	
			
			if(!($this->Format))
				$this->Format = 'Y-m-d';
	
	
			$onkeyup = ' onkeyup=" " ';
			
			$onmousedown =  ' onmousedown=" var obj = getJSform(\''.$this->getParentForm()->Name.'\').'.$this->Name.';';
			$onmousedown .= ' 				if(obj != null){';
			$onmousedown .= '					obj.calendarClick(event);';
			$onmousedown .= '				} "';
							
			return '<div  style="'.$style_wrapper.'">'.
					 '<div class="datepicker-wrapper" id="'.$id.'-wrapper">'.
					 		'<input readonly type="'.$type.'" value="'.$text.'" name="'.$this->Name.'" pattern="'.$this->Format.'" id="'.$id.'" class="'.$class.'"'.$hint.' style="'.$style.'." '.$onkeyup.' />'.
					 		'<div class="datepicker-calendar" '. $onmousedown .'>'.
					 		'<div class="datepicker-months">'.
					 		'	<span class="datepicker-prev-month"><<</span>'.
					 		'	<span class="datepicker-next-month">>></span>'.
					 		'	<div class="datepicker-current-month" id="datepicker-current-month-'.$id.'"></div>'.
					 		'</div>'.
					 		'<table id="datepicker-table-'.$id.'"><tbody></tbody></table>'.
					 		'</div>'.
					 '</div>'.		
			'</div>';	
		}
		
		function generateJS()
		{		
			$js = $this->innerJS();
			
			return $js;		
		}
			
	}
	
	//registerWidget('TDatePicker', 'TDatePicker');
	
}

?>