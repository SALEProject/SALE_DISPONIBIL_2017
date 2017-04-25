<?php

if (!defined('Q_PANEL_Q'))
{
	define('Q_PANEL_Q', true);
	
	class TPanel extends TWidget
	{
		static $DefaultStyle = "
div.default_TPanel
{
	background-color: #dddddd;
	border: 1px solid white;
	/*border-radius: 10px;*/
	/*box-shadow: 0px 0px 10px #888888;*/
	overflow: hidden;	
}

				";
		
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
						
			$style = $this->generateStyle();
			
			$this->st_rendered = true;
			
			return '<div id="'.$this->id.'" class="'.$class.'" style="'.$style.'">'.$s.'</div>';
		}
		
		function generateJS()
		{
			$s = $this->innerJS();
			
			return $s;
		}
		
	} 
	
	//registerWidget('TPanel', 'TPanel');
	
}

?>