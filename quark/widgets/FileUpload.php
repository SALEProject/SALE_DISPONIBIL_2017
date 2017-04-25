<?php

if (!defined('Q_FILEUPLOAD_Q'))
{
	define('Q_FILEUPLOAD_Q', true);
	
	class TFileUpload extends TWidget
	{
		static $DefaultStyle = "input[type='button'].default_TFileUpload
{
	background: url('bluegray_button.png') repeat-x 0px 0px;
	height: 25px;
	width: 180px;
	border-color: #a0a0ff;
	border-style: solid;
	border-width: 1px;
	border-radius: 5px;
}

input[type='button'].default_TFileUpload:hover
{
	background: url('bluegray_button.png') repeat-x 0px -23px;
}
	";
		private $FCaption = '';
		
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
						
			$style = $this->generateStyle().'min-width; 180px; ';
				
			//$id = '%parent%.'.$this->Name;
			$id = $this->id;
												
			$html = '<div id="'.$this->id.'_wrapper" class="'.$class.'" style="'.$style.'"> '."\n".
					'	<input id="'.$id.'" class="'.$class.'" type="button" value="'.$this->Caption.'" '.$onclick_event.' '.$oncontextmenu.'></input> '."\n".
					'	<span id="'.$this->id.'_message" class="'.$class.'_message"></span> '."\n".
					'	<div id="'.$this->id.'_animation" class="'.$class.'_animation"></div> '."\n".
					'	<span id="'.$this->id.'_lastupload" class="'.$class.'_lastupload"></div> '."\n".
					'</div> ';
						
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$js =	'upclick '."\n".
					'( '."\n".
					'	{ '."\n".
					'		element: $("'.$this->id.'"), '."\n".
					'		parent: $("'.$this->Parent->Name.'"), '."\n".
					'		action: "index.php?callBack=false", '."\n".
					'		onstart:	function(filename) '."\n".
					'					{ '."\n".
					'						$("'.$this->id.'_message").innerHTML = filename; '."\n".
					'						$("'.$this->id.'_animation").style.visibility = "visible"; '."\n".
					'						$("'.$this->id.'").style.visibility = "hidden"; '."\n".
					'					}, '."\n".
					'		oncomplete:	function(response_data) '."\n".
					'					{ '."\n".
					'						'."\n".
					'						$("'.$this->id.'_animation").style.visibility = "hidden"; '."\n".
					'                       $("'.$this->id.'_message").style.visibility = "hidden";'."\n".
					'						$("'.$this->id.'").style.visibility = "visible"; '."\n".
					'						$("'.$this->id.'_lastupload").style.visibility = "visible"; '."\n".
					'                       $("'.$this->id.'_lastupload").innerHTML = "uploaded " + response_data;'."\n". 
					'					} '."\n".
					'	} '."\n".
					'); '."\n";
		
			return $js;
		}
	}
	
	//registerWidget('TFileUpload', 'TFileUpload');

}

?>