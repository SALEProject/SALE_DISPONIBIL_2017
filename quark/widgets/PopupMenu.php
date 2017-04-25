<?php

if (!defined('Q_POPUPMENU_Q'))
{
	define('Q_POPUPMENU_Q', true);

	//include "MenuItem.php";

	class TPopupMenu extends TWidget
	{		
		var $Items = Array();
		
		function addControl($ctrl)
		{
			if ($ctrl instanceof TMenuItem) $this->addMenuItem($ctrl);
			
			parent::addControl($ctrl);
		}
		
		function addMenuItem($item)
		{
			$this->Items[] = $item;
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			
			$style = $this->generateStyle(array('display' => 'none', 'width' => -1, 'height' => -1));
				
			$id = '%parent%.'.$this->Name;
				
			$html = '<div id="'.$id.'" class="'.$class.'" style="'.$style.'"> ' .
					'	<ul> ';
			foreach ($this->Items as $item)
			{
				$onclick = '';
				if ($item->OnClick != '') 
					$onclick = 'onclick="getJSform(\'%parent%\').callBack(\''.$item->OnClick.'\', undefined, \''.$item->Name.'\');"';
				$html.= '		<li '.$onclick.'>'.$item->Caption.'</li>';
			}
			$html.=	'	</ul> '.
					'</div>';
			
			return $html;
		}
		
		function generateJS()
		{			
			$id = '%parent%.'.$this->Name;
			
			$js =	'getJSform(\'%parent%\').'.$this->Name.' = '.
					'{'.
					'	popUp: function(e) '.
					'	{ '.
					'		var obj = $("'.$id.'"); '.
					'		document.body.appendChild(obj); '.
					'		obj.style.display = "block"; '.
					'		obj.style.left = e.clientX; '.
					'		obj.style.top = e.clientY; '.
					'		e.preventDefault(); '.
					'		window.addEventListener("keyup", this.onKeyUp); '.
					'		document.addEventListener("click", this.mouseClick); '.
					'	}, '.
					'	'.
					'	onKeyUp: function(e) '.
					'	{ '.
					'		if (e.keyCode == 27) '.
					'			getJSform(\'%parent%\').'.$this->Name.'.popDown(); '.
					'	}, '.
					'	'.
					'	mouseClick: function(e) '.
					'	{ '.
					'		var button = e.which || e.button; '.
					'		if (button === 1) getJSform(\'%parent%\').'.$this->Name.'.popDown(); '.
					'	}, '. 
					'	'.
					'	popDown: function() '.
					'	{ '.
					'		var obj = $("'.$id.'"); '.
					'		var parent = $(getJSform(\'%parent%\').htmlID); '.
					'		parent.appendChild(obj); '.
					'		obj.style.display = "none"; '.
					'		window.removeEventListener("keyup", this.onKeyUp); '.
					'		document.removeEventListener("click", this.mouseClick); '.
					'	} '.
					'};';
			
			return $js;
		}
		
	}

	//registerWidget('TPopupMenu', 'TPopupMenu');

}

?>