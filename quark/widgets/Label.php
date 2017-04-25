<?php

if (!defined('Q_LABEL_Q'))
{
	define('Q_LABEL_Q', true);
	
	class TLabel extends TWidget
	{
		private $FCaption = '';
		var $Alignment = 'taLeftJustify';
		
		function setProperty($name, $value)
		{
			switch ($name)
			{
				case 'caption':
					$this->FCaption = $value;
					break;	
				case 'alignment':
					$this->Alignment = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}

		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
							
			$style = $this->generateStyle();
			if ($this->Alignment != 'taLeftJustify' || $this->Width != 0)
			{
				switch ($this->Alignment)
				{
					case 'taLeftJustify':
						$style .= 'text-align: left; ';
						break;
					case 'taRightJustify':
						$style .= 'text-align: right; ';						
						break;
					case 'taCenter':
						$style .= 'text-align: center; ';						
						break;
				}
			}
			
			if ($this->Style != '') $style .= ' '.$this->Style;
				
			$id = $this->id;
			/*if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
			else $id = '%parent%.'.$id;*/

			$html = '<span id="'.$id.'" class="'.$class.'" style="'.$style.'">'.$this->FCaption.'</span>';
			$this->st_rendered = true;
			return $html;
		}
		
		protected function get_Caption()
		{
			return $this->FCaption;
		}
		
		protected function set_Caption($value)
		{
			if ($this->FCaption != $value)
			{
				$this->FCaption = $value;
				if ($this->st_rendered) TQuark::instance()->browserUpdate($this->id, $value);
			}
		}
		
	} 
	
	//registerWidget('TLabel', 'TLabel');
	
}

?>