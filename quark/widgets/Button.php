<?php

if (!defined('Q_BUTTON_Q'))
{
	define('Q_BUTTON_Q', true);
	
	class TButton extends TWidget
	{
		static $DefaultStyle = "input[type='button'].default_TButton
{
	background: url('bluegray_button.png') repeat-x 0px 0px;
	background-color: #fff;
	height: 25px;
	border-color: #a0a0ff;
	border-style: solid;
	border-width: 1px;
	border-radius: 5px;
}

input[type='button'].default_TButton:hover
{
	background: url('bluegray_button.png') repeat-x 0px -23px;
	background-color: #fc0;
}
	";
		private $FCaption = '';
		var $OnClick = '';
		var $Default = false;
		
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
				case 'default':
					if (strtolower(trim($value)) == 'true') $this->Default = true;
					else $this->Default = false;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		protected function get_Caption()
		{
			return $this->FCaption;
		}
		
		protected function set_Caption($value)
		{
			if ($value != $this->FCaption)
			{
				$this->FCaption = $value;
				if ($this->st_rendered) TQuark::instance()->browserScript('$("'.$this->id.'").value = "'.$value.'"');
			}
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
						
			$style = $this->generateStyle();
			
			//$id = '%parent%.'.$this->Name;
			$id = $this->id;
				
			$onclick_event = '';
			if ($this->OnClick != '') $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->OnClick.'\', undefined, \''.$id.'\'); return false;"';
			else $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->Name.'_onclick\', undefined, \''.$id.'\'); return false;"';
			
			$oncontextmenu = '';
			if ($this->PopupMenu != '')
			{
				$pm = $this->PopupMenu;
				if (isset($this->Parent->$pm))
					$oncontextmenu = 'oncontextmenu="getJSform(\'%parent%\').'.$pm.'.popUp(event); "';
			}
						
			switch ($this->Default)
			{
				case false:
					$html = '<input id="'.$id.'" class="'.$class.'" style="'.$style.'" type="button" value="'.$this->Caption.'" '.$onclick_event.' '.$oncontextmenu.'></input>';//.$id.'_onclick();"></input>';
					break;
				case true:
					$html = '<input id="'.$id.'" class="'.$class.'" style="'.$style.'" type="submit" value="'.$this->Caption.'" '.$onclick_event.' '.$oncontextmenu.'></input>';//.$id.'_onclick();"></input>';
					break;
			}
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$s = /*'%parent%_'.*/$this->Name;
			
			return	'';/*'getJSform("%parent%").'.$s.'_onclick = '.
					//'$("%parent%").'.$s.'_onclick = './/$s.'_onclick; '.
					'function() './/$s.'_onclick() '.
					'{ '.
					'	this.callBack("'.$s.'_onclick"); '.
					'};';*/
		}
		
	} 
	
	//registerWidget('TButton', 'TButton');
	
}

?>