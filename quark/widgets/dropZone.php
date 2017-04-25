<?php

if (!defined('Q_DROPZONE_Q'))
{
	define('Q_DROPZONE_Q', true);
	
	class TdropZone extends TWidget{
		var $className = 'TdropZone';
		var $Left = 400;
		var $Top = 20;
		var $Width = 256;
		var $Height = 184;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'top':
					$this->Top = $value;
					break;
				case 'left':
					$this->Left = $value;
					break;
				case 'width':
					$this->Width = $value;
					break;
				case 'height':
					$this->Height = $value;
					break;
				default:
					$this->__set($name, $value);
					break;
			}
		}
		
	/*	function generateHTML($name, $value){
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$id = '%parent%.'.$this->Name;
			
			$style = 'position: absolute;'.
					'top:'.$this->Top.'px;'.
					'left:'.$this->Left.'px;'.
					'width:'.$this->Width.'px;'.
					'height:'.$this->Height.'px;'.
					'margin: 10px;'.
					'padding: 10px;'.
					'border: 1px solid #f0f0f0;';
			
			$ondragover="allowDrop(event)";
			$ondrop = "dropHTML(event)";
			
			$html = '<div id="dropzone" ondrop="'.$ondrop.'" ondragover="'.$ondragover.'" class="'.$class.'" style="'.$style.'">Drop items here.</div><span>'.$name.'</span>.'."\n";
		}*/
		
		
		function generateJS(){
			$js = '		dropHTML = function(e){'."\n".
				  '			e.preventDefault && e.preventDefault();'."\n".
				  '			if(e.dataTransfer.getData("text/html")){'."\n".
				  '				var data = e.dataTransfer.getData("text/html");'."\n".
				  '				var nodecopy = document.getElementById(data).cloneNode(true);'."\n".
				  '				nodecopy.id = data + "_copy"; '."\n".
				  '				e.target.appendChild(nodecopy);'."\n".
				  '				callBack("saveLI", "frm_Products", e.target.id, data, nodecopy.innerHTML, document.getElementById(data));'."\n".
				  '			}'."\n".
				  '		}'."\n".
				  '		allowDrop = function(e){'."\n".
				  '			e.preventDefault && e.preventDefault();'."\n".
				  '		 }'."\n";
			return $js;
		}
	}
	
	//registerWidget('TdropZone', 'TdropZone');

}

?>