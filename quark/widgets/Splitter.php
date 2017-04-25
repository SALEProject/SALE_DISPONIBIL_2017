<?php

if (!defined('Q_SPLITTER_Q'))
{
	define('Q_SPLITTER_Q', true);

	class TSplitter extends TWidget
	{
		static $DefaultStyle = "
div.bluegray_TSplitter
{
	background-color: #eeeeee;
	width: 8px;
	height: 100%;
}
				";
		var $SplitterType = 'stVertical';
		var $MinusPanel = null;
		var $PlusPanel = null;

		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'splittertype':
					$this->SplitterType = $value;
					break;
				case 'minuspanel':
					$this->MinusPanel = $value;
					break;
				case 'pluspanel':
					$this->PlusPanel = $value;
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
						
			$style = 	'display: block; '.
						'position: absolute; ';
			
			switch (strtolower($this->SplitterType))
			{
				case 'stvertical':
					$style .= 'left: '.$this->Left.'px; top: 0px; width: 8px; cursor: ew-resize; ';
					break;
				case 'sthorizontal':
					$style .= 'left: 0px; top: '.$this->Top.'px; height: 8px; cursor: ew-resize; ';
					break;
			}
			
			$str_onmousedown = 'onmousedown="getJSform(\'%parent%\').splitterMouseDown(event);"';
			$str_onmousemove = 'onmousemove="getJSform(\'%parent%\').splitterMouseMove(event);"';
			$str_onmouseup = 'onmouseup="getJSform(\'%parent%\').splitterMouseUp(event);"';
			if (TQuark::instance()->clientScreen->TouchCapable)
			{
				$str_onmousedown = 	'ontouchstart="splitterMouseDown(event);"';
				$str_onmousemove = 	'ontouchmove="splitterMouseMove(event);"';
				$str_onmouseup = 	'ontouchend="splitterMouseUp(event);" '.
									'onmouseup="splitterMouseUp(event);"';	
			}
						
			return '<div id="%parent%.'.$this->Name.'" class="'.$class.'" style="'.$style.'" '.$str_onmousedown.' '.$str_onmousemove.' '.$str_onmouseup.' ></div>';
		}
				
		function generateJS()
		{
			$id_minuspanel = 'none';
			$id_pluspanel = 'none';
			
			if (isset($this->Parent->Controls[$this->MinusPanel])) $id_minuspanel = $this->Parent->Controls[$this->MinusPanel]->id;
			if (isset($this->Parent->Controls[$this->PlusPanel])) $id_pluspanel = $this->Parent->Controls[$this->PlusPanel]->id; 
			
			return	'getJSform("%parent%")._splitterType = "'.strtolower($this->SplitterType).'"; '."\n".
					'getJSform("%parent%")._moveSplitter = false; '."\n".
					'getJSform("%parent%").splitterMouseDown = function(event)'."\n".
					'{ '."\n".
					'	this._moveSplitter = true; '."\n".
					'	var clientX = 0; '."\n".
					'	var clientY = 0; '."\n".
					'	'."\n".
					'	clientX = event.clientX; '."\n".
					'	clientY = event.clientY; '."\n".
					'	'."\n".
					'	this._splitterX = clientX; '."\n".
					'	this._splitterY = clientY; '."\n".
					'	'."\n".
					'	var obj = $("%parent%.'.$this->Name.'"); '."\n".
					'	var obj_minus = $("'.$id_minuspanel.'"); '."\n".
					'	var obj_plus = $("'.$id_pluspanel.'"); '."\n".
					'	if (obj != null) '."\n".
					'	{ '."\n".
					'		switch (this._splitterType) '."\n".
					'		{ '."\n".
					'			case "stvertical": '."\n".
					'				this.splitterLeft = obj.offsetLeft;//obj.style.left; '."\n".
					'				if (obj_minus != null) this.splitminusWidth = obj_minus.offsetWidth; '."\n".
					'				if (obj_plus != null) this.splitplusWidth = obj_plus.offsetWidth; '."\n".
					'				break; '."\n".
					'			case "sthorizontal": '."\n".
					'				this.splitterTop = obj.offsetTop;//obj.style.top; '."\n".
					'				if (obj_minus != null) this.splitminusHeight = obj_minus.offsetHeight; '."\n".
					'				if (obj_plus != null) this.splitplusHeight = obj_plus.offsetHeight; '."\n".
					'				break; '."\n".
					'		} '."\n".
					'	} '."\n".
					'	'."\n".
					'}'."\n".
					''."\n".
					'getJSform("%parent%").splitterMouseMove = function(event)'."\n".
					'{'."\n".
					'	if (this._moveSplitter) '."\n".
					'	{ '."\n".
					'		var clientX = 0; '."\n".
					'		var clientY = 0; '."\n".
					'		'."\n".
					'		clientX = event.clientX; '."\n".
					'		clientY = event.clientY; '."\n".
					'		'."\n".
					'		var obj = $("%parent%.'.$this->Name.'"); '."\n".
					'		var obj_minus = $("'.$id_minuspanel.'"); '."\n".
					'		var obj_plus = $("'.$id_pluspanel.'"); '."\n".
					'		if (obj != null) '."\n".
					'		{ '."\n".
					'			switch (this._splitterType) '."\n".
					'			{ '."\n".
					'				case "stvertical": '."\n".
					'					x = this.splitterLeft + clientX - this._splitterX; '."\n".
					'					obj.style.left = x + "px"; '."\n".
					'					if (obj_minus != null) obj_minus.style.width = (this.splitminusWidth + clientX - this._splitterX) + "px"; '."\n".
					'					if (obj_plus != null) obj_plus.style.width = (this.splitplusWidth - clientX + this._splitterX) + "px"; '."\n".
					'					break; '."\n".
					'				case "sthorizontal": '."\n".
					'					y = this.splitterTop + clientY - this._splitterY; '."\n".
					'					obj.style.top = y + "px"; '."\n".
					'					if (obj_minus != null) obj_minus.style.height = (this.splitminusHeight + clientY - this._splitterY) + "px"; '."\n".
					'					if (obj_plus != null) obj_plus.style.height = (this.splitplusHeight - clientY + this._splitterY) + "px"; '."\n".
					'					break; '."\n".
					'			} '."\n".
					'		} '."\n".
					'		'."\n".
					'	} '."\n".
					'}'."\n".
					''."\n".
					'getJSform("%parent%").splitterMouseUp = function(event)'."\n".
					'{'."\n".
					'	this._moveSplitter = false; '."\n".
					'	'."\n".
					'}'."\n".
					''."\n".
					''."\n";		
		}
		
		
	}
	
	//registerWidget('TSplitter', 'TSplitter');

}

?>