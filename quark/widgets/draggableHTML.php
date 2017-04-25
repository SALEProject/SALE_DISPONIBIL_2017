<?php

if (!defined('Q_DRAGGABLEHTML_Q'))
{
	define('Q_DRAGGABLEHTML_Q', true);

	class TdraggableHTML extends TWidget{
		var $className = 'TdraggableHTML';
		var $id, $class;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
			   
			}
		}
		
		function generateJS(){
				$js = 	'dragStart = function(e){'."\n".
						'	e.dataTransfer.setData("text/html", e.target.id);'."\n".
						'}'."\n".
						
						'document.onmousemove = mouseMove;'."\n".
						'document.onmouseup   = mouseUp;'."\n".
						'var dragObject  = null;'."\n".
						'var mouseOffset = null;'."\n".
						
						'makeDraggable = function(item){'."\n".
						'	if(!item) return;'."\n".
						'	item.onmousedown = function(ev){'."\n".
						'		dragObject  = this;'."\n".
						'		mouseOffset = getMouseOffset(this, ev);'."\n".
						'		return false;'."\n".
						'	};'."\n".
						'}	'."\n".
					
						'getMouseOffset = function(target, ev){'."\n".
						'		ev = ev || window.event;'."\n".
						'		var docPos    = getPosition(target);'."\n".
						'		var mousePos  = mouseCoords(ev);'."\n".
						'		return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};'."\n".
						'} '."\n".
						
						'getPosition = function(e){'."\n".
						'	var left = 0;'."\n".
						'	var top  = 0;'."\n".
						'	/*while (e.offsetParent){'."\n".
						'		left += e.offsetLeft;'."\n".
						'		top  += e.offsetTop;'."\n".
						'		e     = e.offsetParent;'."\n".
						'	}*/ '."\n".
						'	left += e.offsetLeft;'."\n".
						'	top  += e.offsetTop;'."\n".
						'	return {x:left, y:top};'."\n".
						'}'."\n".
						
						'function mouseMove(ev){'."\n".
						'	ev           = ev || window.event;'."\n".
						'	var mousePos = mouseCoords(ev);'."\n".
						'	if(dragObject){'."\n".
						'		dragObject.style.position = "absolute";'."\n".
						'		dragObject.style.top      = mousePos.y - mouseOffset.y + "px";'."\n".
						'		dragObject.style.left     = mousePos.x - mouseOffset.x + "px";'."\n".
						'		return false;'."\n".
						'	}'."\n".
						'}'."\n".
					
						'function mouseUp(){'."\n".
						'	dragObject = null;'."\n".
						'}'."\n".
					
						'mouseCoords = function(ev){'."\n".
						'	if(ev.pageX || ev.pageY){'."\n".
						'		return {x:ev.pageX, y:ev.pageY};'."\n".
						'          alert(x);'."\n".
						'	}'."\n".
						'	return {'."\n".
						'		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,'."\n".
						'		y:ev.clientY + document.body.scrollTop  - document.body.clientTop'."\n".
						'	};'."\n".
						'}'."\n";
	
					/*				'	allowDrop = function(e){'."\n".
					'		e.preventDefault && e.preventDefault();'."\n".
					'	}'."\n".
	
					'	function dragStart(e){'."\n".
					'		e.dataTransfer.setData("text/html", e.target.id);'."\n".
					'	}'."\n".
	
					'	function drop(e){'."\n".
					'		e.preventDefault && e.preventDefault();'."\n".
					'		var data = e.dataTransfer.getData("text/html");'."\n".
					'		var nodecopy = document.getElementById(data).cloneNode(true);'."\n".
					'		nodecopy.id = data + "_copy";'."\n".
					'		e.target.appendChild(nodecopy);'."\n".	
					'	}';*/
			
			return $js;		
		}
		
	}
	
}

?>