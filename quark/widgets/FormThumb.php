<?php

if (!defined('Q_FORMTHUMB_Q'))
{
	define('Q_FORMTHUMB_Q', true);
	
	class TFormThumb extends TWidget
	{
		var $FormName = '';
		var $OnClick = '';
		
		function __construct($AParent, $FormName = '')
		{
			parent::__construct($AParent);
			
			$this->FormName = $FormName;
		}
		
		function setProperty($name, $value)
		{
			switch ($name)
			{
				case 'onclick':
					$this->OnClick = $value;
					break;
				default:
					break;
			}
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass != '') $class .= ' '.$this->CSSClass;
			
			$frm_html = '';
			$frm = TQuark::instance()->getForm($this->FormName);
			if ($frm != null)
			{
				$class .= ' '.$frm->ThumbCSSClass;
				$frm_html = $frm->generateThumbHTML();
				
				if ($frm->Visible) $class .= ' thumbActive';
			}

			$onclick_event = '';
			if ($this->OnClick != '') $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->OnClick.'\', undefined, \''.$this->id.'\');"';
			else $onclick_event = 'onclick="'.'getJSform(\'%parent%\').callBack(\''.$this->Name.'_onclick\', undefined, \''.$this->id.'\');"';
			$onclick_event = str_replace('%parent%', $this->Parent->Name, $onclick_event);
				
			$html =	'<a id="'.$this->id.'" class="'.$class.'" '.
					'	onmousedown="getJSform(\''.$this->Parent->Name.'\').thumbMouseDown(\''.$this->id.'\')" '.
					'	onmouseup="getJSform(\''.$this->Parent->Name.'\').thumbMouseUp(\''.$this->id.'\')" '.
						$onclick_event.'/>'."\n".
					'	<div class="content">'.$frm_html.'</div>'.
					'	<div class="caption">'.$frm->Caption.'</div>'.
					'</a>';
				
			return $html;
		}
		
		function generateJS()
		{
			$s = /*'%parent%_'.*/$this->Name;
				
			return	'getJSform("%parent%").'.$s.'_onclick = '.
			//'$("%parent%").'.$s.'_onclick = './/$s.'_onclick; '.
			'function() './/$s.'_onclick() '.
			'{ '.
			'	this.callBack("'.$s.'_onclick"); '.
			'};';
		}
		
	}
	
	//registerWidget('TFormThumb', 'TFormThumb');

}

?>