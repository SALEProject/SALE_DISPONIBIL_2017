<?php

if (!defined('Q_BEVEL_Q'))
{
	define('Q_BEVEL_Q', true);
	
	class TBevel extends TWidget
	{
		static $DefaultStyle = 'div.default_TBevel { border-color: #9999ff; }';
		var $Shape;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'shape':
					$this->Shape = $value;
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
				
			$style = $this->generateStyle();
			
			switch ($this->Shape)
			{
				case 'bsBox':
					$style .=	'border-style: inset; '.
								'border-width: 1px; ';
					break;
				case 'bsFrame':
					$style .=	'border-style: groove; '.
								'border-width: 2px; ';
					break;
				case 'bsLeftLine':
					$style .=	'border-left-style: groove; '.
								'border-left-width: 2px; ';
					break;
				case 'bsRightLine':
					$style .=	'border-right-style: groove; '.
								'border-right-width: 2px; ';
					break;
				case 'bsTopLine':
					$style .=	'border-top-style: groove; '.
								'border-top-width: 2px; ';
					break;
				case 'bsBottomLine':
					$style .=	'border-bottom-style: groove; '.
								'border-bottom-width: 2px; ';
					break;
				default:
					$style .=	'border-style: inset; '.
								'border-width: 1px; ';
					break;
			}
				
			$id = '%parent%.'.$this->Name;
			
			return '<div class="'.$class.'" style="'.$style.'"></div>';
				
		}
		
		
	}

	//registerWidget('TBevel', 'TBevel');
	
}

?>