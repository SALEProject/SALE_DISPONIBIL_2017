<?php

if (!defined('Q_TABSHEET_Q'))
{
	define('Q_TABSHEET_Q', true);

	class TTabSheet extends TWidget
	{
		var $Caption = '';
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'caption':
					$this->Caption = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;	
			}	
		}
		
		function generateHTML()
		{
			$html = $this->innerHTML();
			
			$id = '%parent%.'.$this->Name;
						
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
						
			return	'<div id="'.$id.'" class="'.$class.'">'."\n".
					$html.
					'</div>';
		}
		
		function generateJS()
		{
			return $this->innerJS();
		}
	}
	
	//registerWidget('TTabSheet', 'TTabSheet');

}

?>