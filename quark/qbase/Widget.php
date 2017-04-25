<?php

	global $WidgetCollection;
	$WidgetCollection = Array();

	class TWidget extends TComponent
	{
		protected $FJSFile = '';
		protected $FCSSFile = ''; 
		var $ExternalJS = '';
		static $DefaultStyle = '';
		protected $FTheme = 'default';
		protected $FLeft = 0;
		protected $FTop = 0;
		protected $FWidth = -1;
		protected $FHeight = -1;
		protected $FAlign = 'alNone';
		protected $FVisible = true;
		protected $st_rendered = false;
		public $PopupMenu = '';
		var $CSSClass = '';
		var $Style = '';
		var $Data = null;
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			if (isset($AParent) && $AParent instanceof TWidget) $this->FTheme = $AParent->Theme;
				
			$this->FJSFile = dirname($this->FCodeFile).DIRECTORY_SEPARATOR.basename($this->FCodeFile, '.php');
			$this->FCSSFile = $this->FJSFile;
			$this->FJSFile .= '.js';		
			$this->FCSSFile .= '.css';	
			
			if (file_exists($this->FJSFile))
			{
				$this->ExternalJS = file_get_contents($this->FJSFile);
			}			
		}
		
		function get_JSFile()
		{
			return $this->FJSFile;
		}
		
		function get_CSSFile()
		{
			return $this->FCSSFile;
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'left':
					$this->Left = $value;
					break;
				case 'top':
					$this->Top = $value;
					break;
				case 'width':
					$this->Width = $value;
					break;
				case 'height':
					$this->Height = $value;
					break;
				case 'align':
					$this->Align = $value;
					break;
				case 'visible':
					if (strtolower(trim($value)) == 'false') $this->Visible = false;
					else $this->Visible = true;
					break;
				case 'popupmenu':
					$this->PopupMenu = $value;
					break;
				case 'theme':
					$this->Theme = $value;
					break;
				case 'cssclass':
					$this->CSSClass = $value;
					break;
				case 'style':
					$this->Style = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function get_Theme()
		{
			return $this->FTheme;
		}
		
		function set_Theme($value)
		{
			if (isset(TQuark::instance()->themes[$value])) $this->FTheme = $value;
			else $this->FTheme = 'default';
		}
		
		function get_Left()
		{
			return $this->FLeft;
		}
		
		function set_Left($value)
		{
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->id; //  $id = $this->Parent->Name.'.'.$id;
			
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'left: '.$value);
			}
				
			$this->FLeft = $value;
		}
		
		function get_Top()
		{
			return $this->FTop;
		}
		
		function set_Top($value)
		{
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->id; //  $id = $this->Parent->Name.'.'.$id;
			
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'top: '.$value);
			}
				
			$this->FTop = $value;
		}
		
		function get_Width()
		{
			return $this->FWidth;
		}
		
		function set_Width($value)
		{
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->id; //  $id = $this->Parent->Name.'.'.$id;
	
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'width: '.$value);
			}
			
			$this->FWidth = $value;
		}
		
		function get_Height()
		{
			return $this->FHeight;
		}
		
		function set_Height($value)
		{
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->id; //  $id = $this->Parent->Name.'.'.$id;
	
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'height: '.$value);
			}
			
			$this->FHeight = $value;
		}
		
		function get_Align()
		{
			return $this->FAlign;
		}
		
		function set_Align($value)
		{
			if ($this->st_rendered)
			{
				
			}
			
			$this->FAlign = $value;
		}
		
		function get_Visible()
		{
			return $this->FVisible;
		}
		
		function set_Visible($value)
		{
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->id; //  $id = $this->Parent->Name.'.'.$id;
	
				//TQuark::instance()->addAjaxStack('', 'alert', $value);
				switch ($this->FVisible)
				{
					case false:						
						if ((bool)$value == true) TQuark::instance()->browserSetStyle($id, 'visibility: visible');
						break;
					case true:
						if ((bool)$value == false) TQuark::instance()->browserSetStyle($id, 'visibility: hidden');
						break;				
				}
			}
			
			$this->FVisible = $value;		
		}
		
		function BringToFront()
		{
			
		}
		
		function SendToBack()
		{
			
		}
		
		function generateStyle($custargs = array())
		{
			$style = '';
			$style.= 'display: '.(key_exists('display', $custargs) ? $custargs['display'] : 'block').'; ';
			$style.= 'position: '.(key_exists('position', $custargs) ? $custargs['position'] : 'absolute').'; ';
			
			$al = key_exists('align', $custargs) ? $custargs['align'] : $this->FAlign;
				
			$l = key_exists('left', $custargs) ? $custargs['left'] : $this->FLeft.'px';
			$t = key_exists('top', $custargs) ? $custargs['top'] : $this->FTop.'px';
			
			$w = key_exists('width', $custargs) ? $custargs['width'] : $this->FWidth.'px'; 
			$h = key_exists('height', $custargs) ? $custargs['height'] : $this->FHeight.'px'; 
			
			switch (strtolower($al))
			{
				case 'alnone':
					$style .=	'left: '.$l.'; top: '.$t.'; ';
					break;
				case 'altop':
					$style .=	'left: 0px; top: 0px; right: 0px; min-width: 100%; ';
					break;
				case 'alleft':
					$style .=	'left: 0px; top: 0px; bottom: 0px; min-height: 100%; ';
					break;
				case 'alright':
					$style .=	'top: 0px; right: 0px; bottom: 0px; min-height: 100%; ';
					break;
				case 'albottom':
					$style .=	'left: 0px; right: 0px; bottom: 0px; min-width: 100%; ';
					break;
				case 'alclient':
					$style .=	'left: 0px; top: 0px; right: 0px; bottom: 0px; min-width: 100%; min-height: 100%; ';
					break;
			}
			
			if ($w >= 0) $style.= 'width: '.$w.'; ';
			if ($h >= 0) $style.= 'height: '.$h.'; ';
				
			if (!$this->FVisible) $style.= 'visibility: hidden; ';
				
			return $style;
		}

		function innerHTML()
		{
			$s = '';
			foreach ($this->Controls as $ctrl)
			{
				$s .= $ctrl->generateHTML();
			}	
			
			return $s;
		}
		
		function generateHTML()
		{
		}
		
		function innerJS()
		{
			$s = '';
			foreach ($this->Controls as $ctrl)
			{
				$s .= $ctrl->generateJS();
			}
				
			$js = '';
			if ($this->ExternalJS != '')
			{
				$id = $this->id; //'%parent%.'.$this->Name;
				
				$js =	'var jsfrm = getJSform(\'%parent%\');'."\n".
						'jsfrm.'.$this->ClassName.' = '.$this->ExternalJS.';'."\n".
						'jsfrm.'.$this->Name.' = new jsfrm.'.$this->ClassName.'("'.$id.'");'."\n".
						'var jsself = jsfrm.'.$this->Name.';'."\n";
			}
			
			return $s.$js;
		}
		
		function generateJS()
		{
			
		}
		
		function release()
		{
			
		}
		
		function setValue($value)
		{
			
		}
		
		function OnLoad()
		{
			
		}
	}
	
	function isWidget($typename)
	{
		if (!isset($typename)) return false;
		
		if (is_object($typename)) return ($typename instanceof TWidget);
		else 
		{
			if (!class_exists($typename)) return false;
			return is_subclass_of($typename, 'TWidget');
			
			/*$reflection = new ReflectionClass($typename);
			return $reflection->isSubclassOf('TWidget');*/
		}
	}
	
	/*
	function registerWidget($typename, $type)
	{
		global $WidgetCollection;
		
		$WidgetCollection[$typename] = $type;
	}
	
	function isWidget($typename)
	{
		global $WidgetCollection;
		
		if (isset($WidgetCollection[$typename])) return true;
		else return false;
	}
	
	function createWidget($typename)
	{
		global $WidgetCollection;
		
		return new $WidgetCollection[$typename](null);
	}
	*/
		

?>
