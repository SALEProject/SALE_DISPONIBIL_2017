<?php

if (!defined('Q_SHAPE_Q'))
{
	define('Q_SHAPE_Q', true);

	class TShape extends TWidget
	{
		private $FShape = 'stRectangle';
		private $FBrushColor = '#ffffff';
		private $FPenColor = '#000000';
		private $FFontColor = '#000000';
		private $FCaption = '';
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'shape':
					$this->FShape = $value;
					break;
				case 'brushcolor':
					$this->FBrushColor = $value;
					break;
				case 'pencolor':
					$this->FPenColor = $value;
					break;
				case 'fontcolor':
					$this->FFontColor = $value;
					break;
				case 'caption':
					$this->FCaption = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}	
		
		function set_BrushColor($value)
		{
			$this->FBrushColor = $value;
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'backgroundColor: '.$this->FBrushColor);
			}
		}
		
		function set_PenColor($value)
		{
			$this->FPenColor = $value;
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'borderColor: '.$this->FPenColor);
			}
		}
		
		function set_FontColor($value)
		{
			$this->FFontColor = $value;
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
				TQuark::instance()->addAjaxStack($id, 'setStyle', 'color: '.$this->FFontColor);
			}
		}
		
		function get_Caption()
		{
			return $this->FCaption;
		}
		
		function set_Caption($value)
		{
			if ($this->FCaption != $value)
			{
				$this->FCaption = $value;
				if ($this->st_rendered) TQuark::instance()->browserUpdate($this->id, $value);
			}
		}
		
		function set_Shape($value)
		{
			$this->FShape = $value;
			if ($this->st_rendered)
			{
				$id = $this->Name;
				if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
			
				$style = '';
				switch (strtolower($this->FShape))
				{
					case 'stcircle':
						$rad = min($this->Width, $this->Height) / 2;
						$style .= 'borderRadius: '.$rad.'px; ';
						break;
					case 'strectangle':
						break;
					case 'stroundrect':
						$style .= 'borderRadius: 3px; ';
						break;
				}
				TQuark::instance()->addAjaxStack($id, 'setStyle', $style);
			}
		}
		
		function generateHTML()
		{
			$style = $this->generateStyle();
			$style.=	'background-color: '.$this->FBrushColor.'; '.
						'border: 1px solid '.$this->FPenColor.'; '.
						'color: '.$this->FFontColor.'; '.
						'text-align: center; '.
						'vertical-align: middle; ';
			
			switch (strtolower($this->FShape))
			{
				case 'stcircle':
					$rad = min($this->Width, $this->Height) / 2;
					$style .= 'border-radius: '.$rad.'px; ';
					break;
				case 'strectangle':
					break;
				case 'stroundrect':
					$style .= 'border-radius: 3px; ';
					break;
			}
			
			if ($this->Style != '') $style .= ' '.$this->Style;
			
			$id = $this->Name;
			if ($this->Parent != null) $id = $this->Parent->Name.'.'.$id;
			else $id = '%parent%.'.$id;
			
			$html = '<div id="'.$id.'" style="'.$style.'">'.$this->FCaption.'</div>';
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			
		}
	}
	
	//registerWidget('TShape', 'TShape');

}

?>