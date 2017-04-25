<?php

if (!defined('Q_DOJOMENUBAR_Q'))
{
	define('Q_DOJOMENUBAR_Q', true);
	
	class TdojoMenuBar extends TWidget
	{
		var $Items = Array();
		
		function addMenuItem($item)
		{
			$this->Items[] = $item;
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$style = $this->generateStyle();
			
			$id = '%parent%.'.$this->Name;
			
			return 	//'<link rel="stylesheet" href="dijit/themes/claro/claro.css"> '.
					'<div id="'.$this->Name.'" class="claro"></div>';
					//'<div id="'.$this->Name.'" class="'.$class.'" style="'.$style.'"></div>';
		}

		function generateJS()
		{
			$s = '';
			foreach ($this->Items as $item0)
			{
				$s .=			'			var '.$item0->Name.' = new DropDownMenu({}); '."\n";
				foreach ($item0->Items as $item1)
				{							
					$id = $this->Parent->Name.'.'.$item1->Name;
					
					$onclick_event = '';
					if ($item1->OnClick != '') $onclick_event = $item1->OnClick;
					else $onclick_event = $item1->Name.'_onclick';
					$func = 'onClick: function(){ getJSform("'.$this->Parent->Name.'").callBack("'.$onclick_event.'", undefined, "'.$id.'") }';
					//alert("you clicked status bar")
					
					if ($func != '')	
						$s .=	'			'.$item0->Name.'.addChild(new MenuItem({ label: "'.$item1->Caption.'", '.$func.' })); '."\n";
					else
						$s .=	'			'.$item0->Name.'.addChild(new MenuItem({ label: "'.$item1->Caption.'" })); '."\n";
				}
				$s .=			'			pMenuBar.addChild(new PopupMenuBarItem({ label: "'.$item0->Caption.'", popup: '.$item0->Name.' })); '."\n";
			}
			
			return	'require(["dijit/MenuBar", "dijit/PopupMenuBarItem", "dijit/Menu", "dijit/MenuItem", "dijit/DropDownMenu", "dojo/domReady!"], '."\n".
					'		function(MenuBar, PopupMenuBarItem, Menu, MenuItem, DropDownMenu) '."\n".
					'		{ '."\n".
					'			var pMenuBar = new MenuBar({}); '."\n".
					'			pMenuBar.placeAt("'.$this->Name.'"); '."\n".
					'			pMenuBar.startup(); '."\n".
					' '."\n".$s.
					/*'			var pSubMenu = new DropDownMenu({}); '."\n".
					'			pSubMenu.addChild(new MenuItem({ label: "File item #1" })); '."\n".
					'			pSubMenu.addChild(new MenuItem({ label: "File item #2" })); '."\n".
					'			pMenuBar.addChild(new PopupMenuBarItem({ label: "File", popup: pSubMenu })); '."\n".
					' '."\n".
					'			var pSubMenu2 = new DropDownMenu({}); '."\n".
					'			pSubMenu2.addChild(new MenuItem({ label: "Cut", iconClass: "dijitEditorIcon dijitEditorIconCut" })); '."\n".
					'			pSubMenu2.addChild(new MenuItem({ label: "Copy", iconClass: "dijitEditorIcon dijitEditorIconCopy" })); '."\n".
					'			pSubMenu2.addChild(new MenuItem({ label: "Paste", iconClass: "dijitEditorIcon dijitEditorIconPaste" })); '."\n".
					' '."\n".
					'			pMenuBar.addChild(new PopupMenuBarItem({ label: "Edit", popup: pSubMenu2 })); '."\n".
					' '."\n".*/
					'		} '."\n".
					'); '."\n";
				
		}
	}
	
	
	//registerWidget('TdojoMenuBar', 'TdojoMenuBar');

}

?>