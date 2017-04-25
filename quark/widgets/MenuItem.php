<?php

if (!defined('Q_MENUITEM_Q'))
{
	define('Q_MENUITEM_Q', true);
	
	class TMenuItem extends TWidget
	{
		var $Caption = '';		 
		var $Items = Array();
		var $OnClick = '';
		
		function hasChildren()
		{
			if (count($this->Items) > 0) return true;
			else return false;
		}
		 
		function addMenuItem($item)
		{
			$this->Items[] = $item;
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'caption':
					$this->Caption = $value;
					break;
				case 'onclick':
					$this->OnClick = $value;
					break;
			}
		}
		 
	}
	
	//registerWidget('TMenuItem', 'TMenuItem');

}

?>