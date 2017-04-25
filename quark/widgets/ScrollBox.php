<?php

if (!defined('Q_SCROLLBOX_Q'))
{
	define('Q_SCROLLBOX_Q', true);

	class TScrollBox extends TWidget
	{

		function setProperty($name, $value)
		{
			switch ($name)
			{
				default:
					parent::setProperty($name, $value);
					break;
			}
		}

		function generateHTML()
		{
			$s = $this->innerHTML();
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
						
			$style = $this->generateStyle().'overflow: scroll;';
			
			return '<div id="%parent%_'.$this->Name.'" class="'.$class.'" style="'.$style.'">'.$s.'</div>';
		}
		
	} 
	
	//registerWidget('TScrollBox', 'TScrollBox');
	
}

?>