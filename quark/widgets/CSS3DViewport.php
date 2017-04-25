<?php

if (!defined('Q_CSS3DVIEWPORT_Q'))
{
	define('Q_CSS3DVIEWPORT_Q', true);
	
	class TCSS3Dviewport extends TWidget
	{
		var $transform_rotateX = 0;
		var $transform_rotateY = 0;
		var $transform_rotateZ = 0;
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			$this->Left = 0;
			$this->Top = 0;
			$this->Width = 256;
			$this->Height = 256;
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
		
		function generateHTML()
		{
			$html = $this->innerHTML();
			
			$id_viewport = '%parent%.'.$this->Name;
			$style_viewport = $this->generateStyle();
			$style_viewport.=	'transform: perspective(100px); '.
								'-ms-transform: perspective(100px); '.
								'-moz-transform: perspective(100px); '.
								'-webkit-transform: perspective(100px); ';							
			if ($this->Style != '') $style_viewport .= $this->Style;
			
			$id_object = '%parent%.'.$this->Name.'_object';
			$style_object =	'width: 100%; '.
							'height: 100%; '.
							'position: absolute; '.
					
							'transform-style: preserve-3d; '.
							'-ms-transform-style: preserve-3d; '.
							'-moz-transform-style: preserve-3d; '.
							'-webkit-transform-style: preserve-3d; '.
			
							'transition: transform 2s; '.
							'-ms-transition: -ms-transform 2s; '.
							'-moz-transition: -moz-transform 2s; '.
							'-webkit-transition: -webkit-transform 2s; '.
			
							'transition-timing-function: linear; '.
							'-ms-transition-timing-function: linear; '.
							'-moz-transition-timing-function: linear; '.
							'-webkit-transition-timing-function: linear; ';
						
			return 	'<div id="'.$id_viewport.'" style="'.$style_viewport.'">'."\n".
					'	<div id="'.$id_object.'" style="'.$style_object.'">'."\n".
					$html.
					'	</div>'."\n".
					'</div>';
		}
		
		function generateJS()
		{
			
		}
		
		var $controls_enabled = true;
		var $controls_modified = false;
		
		function DisableControls()
		{
			$this->controls_enabled = false;	
		}
		
		function EnableControls()
		{
			$this->controls_enabled = true;
		}
		
		protected function sendObjectTransform()
		{
			$id = $this->Parent->Name.'.'.$this->Name.'_object';
			
			$Xdeg = $this->transform_rotateX;
			$Ydeg = $this->transform_rotateY;
			$Zdeg = $this->transform_rotateZ;
			
			$Xtr = 0;
			$Ytr = 0;
			$Ztr = 0;
			
			$transform = 	'rotateX('.$Xdeg.'deg) '.
							'rotateY('.$Ydeg.'deg) '.
							'rotateZ('.$Zdeg.'deg) '.
							'translateX('.$Xtr.'px) '.
							'translateY('.$Ytr.'px) '.
							'translateZ('.$Ztr.'px)';
			
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'transform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'msTransform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'mozTransform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'webkitTransform: '.$transform);
		}
		
		protected function sendTransform($name)
		{
			$ctrl = $this->Controls[$name];
			if (!($ctrl instanceof TShape)) return;
			
			$id = $this->Parent->Name.'.'.$ctrl->Name;
			$Xdeg = $ctrl->geometry_rotateX;// + $ctrl->transform_rotateX;
			$Ydeg = $ctrl->geometry_rotateY;// + $ctrl->transform_rotateY;
			$Zdeg = $ctrl->geometry_rotateZ;// + $ctrl->transform_rotateZ;
			
			$Xtr = $ctrl->geometry_translateX;
			$Ytr = $ctrl->geometry_translateY;
			$Ztr = $ctrl->geometry_translateZ;
			
			$transform = 	'rotateX('.$Xdeg.'deg) '.
							'rotateY('.$Ydeg.'deg) '.
							'rotateZ('.$Zdeg.'deg) '.
							'translateX('.$Xtr.'px) '.
							'translateY('.$Ytr.'px) '.
							'translateZ('.$Ztr.'px)';
				
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'transform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'msTransform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'mozTransform: '.$transform);
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'webkitTransform: '.$transform);
				
		}
		
		function rotateX($deg)
		{
			$this->transform_rotateX = $deg;
			$this->sendObjectTransform();
			
			
			foreach ($this->Controls as $ctrl)
			{
				if (!($ctrl instanceof TShape)) continue;
				
				$ctrl->transform_rotateX = $deg;
				
				if ($this->controls_enabled) $this->sendTransform($ctrl->Name);
				else $this->controls_modified = true; 								
			}
			
		}
		
		function rotateY($deg)
		{
			$this->transform_rotateY = $deg;
			$this->sendObjectTransform();
			
			
			foreach ($this->Controls as $ctrl)
			{
				if (!($ctrl instanceof TShape)) continue;
			
				$ctrl->transform_rotateY = $deg;
				
				if ($this->controls_enabled) $this->sendTransform($ctrl->Name);
				else $this->controls_modified = true; 								
			}
			
		}
		
		function rotateZ($deg)
		{
			$this->transform_rotateZ = $deg;
			$this->sendObjectTransform();
		
			
			foreach ($this->Controls as $ctrl)
			{
				if (!($ctrl instanceof TShape)) continue;
			
				$ctrl->transform_rotateZ = $deg;
				
				if ($this->controls_enabled) $this->sendTransform($ctrl->Name);
				else $this->controls_modified = true; 				
			}
			
		}
		
	}
	
	//registerWidget('TCSS3DViewport', 'TCSS3DViewport');
	
}

?>