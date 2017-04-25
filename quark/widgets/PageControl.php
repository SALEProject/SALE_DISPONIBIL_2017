<?php

if (!defined('Q_PAGECONTROL_Q'))
{
	define('Q_PAGECONTROL_Q', true);
	
	class TPageControl extends TWidget
	{
		static $DefaultStyle = '
		ul.default_TPageControl
		{
			list-style: none;
			min-width: 16px;
		}
		
		ul.default_TPageControl li
		{
			display: inline-block;
			background-color: #e8e8e8;
			border: 1px solid white;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
		}
		
		ul.default_TPageControl li:hover
		{
			background-color: white;
		}
		
		ul.default_TPageControl li.active
		{
			background-color: white;
		}
		
		ul.default_TPageControl a
		{
			display: block;
			min-width: 16px;
			height: 24px;
			padding-left: 4px;
			padding-right: 4px;
			padding-top: 8px;
			text-decoration: none;
		}
		
		
		div.default_TPageControl
		{
		
		}
		
		div.default_TTabSheet
		{
			display: none;
			border: 1px solid white;
			width: 100%;
			height: 100%;
			overflow: hidden;
		}
		';
		
		var $Pages = Array();
		
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
		
		function addControl($ctrl)
		{
			parent::addControl($ctrl);

			if ($ctrl instanceof TTabSheet) $this->Pages[] = $ctrl;
		}
		
		function generateHTML()
		{
			$id = '%parent%.'.$this->Name;
			
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			
			$style = $this->generateStyle(array('top' => $this->FTop + 32, 'height' => $this->FHeight - 32));
						
			$html_tabs = 	'<ul class="'.$class.'" id="'.$id.'_buttons">';
			foreach ($this->Pages as $i => $page)
			{
				$button_id = "'".$id.'_page.'.$i."'";
				$html_tabs .=	'	<li id="'.$id.'_page.'.$i.'">'."\n".
								'		<a href="javascript: getJSform(\'%parent%\').activateTab('.$button_id.', \'%parent%.'.$page->Name.'\');">'.$page->Caption.'</a>'."\n".
								'	</li>'."\n";
			}
			$html_tabs .=	'</ul>'."\n";
			
			$html_ctrl = 	'<div id="'.$id.'" class="'.$class.'" style="'.$style.'">'."\n";
			foreach ($this->Pages as $page)
			{
				$html = $page->generateHTML();
				$html_ctrl .=	'	'.$html."\n";
			}
			$html_ctrl .=	'</div>'."\n";
			
			return $html_tabs.$html_ctrl;
		}
		
		function generateJS()
		{
			$js = '';
			foreach ($this->Pages as $page) $js .= $page->generateJS()."\n";
			$js.=	"\n".
					'getJSform("%parent%").activateTab = function(buttonId, pageId) '."\n".
					'{ '."\n".
					'	'."\n".
					'	var buttons = $("%parent%.'.$this->Name.'_buttons");'."\n".
					'	var buttonToActivate = $(buttonId);'."\n".
					'	for(var i = 0; i < buttons.children.length; i++)'."\n".
					' 	{ '."\n".
					'		var button = buttons.children[i];'."\n".
					'		button.className = (button == buttonToActivate) ? "active" : ""; '."\n".
					'	}'."\n".
					'	var tabCtrl = $("%parent%.'.$this->Name.'"); '."\n".
					'	var pageToActivate = $(pageId); '."\n".
					'	for (var i = 0; i < tabCtrl.childNodes.length; i++) '."\n". 
					'	{ '."\n".
					'		var node = tabCtrl.childNodes[i]; '."\n".
					'		if (node.nodeType == 1) '."\n". 
					'		{ /* Element */ '."\n".
					'			node.style.display = (node == pageToActivate) ? "block" : "none"; '."\n".
					'		} '."\n".
					'	} '."\n".
					'}; '."\n";
			
			if (count($this->Pages) > 0)
				$js.= 'getJSform(\'%parent%\').activateTab(\'%parent%.'.$this->Name.'_page.0\', \'%parent%.'.$this->Pages[0]->Name.'\');';
			
			return $js;
		}
		
	}
	
	//registerWidget('TPageControl', 'TPageControl');
	
}

?>