<?php

if (!defined('Q_CLOCK_Q'))
{
	define('Q_CLOCK_Q', true);
	
	class TClock extends TWidget
	{
		static $DefaultStyle = '';
		
		private $FTime = 0;
		var $ClockType = 'ctClock'; //  ctStopWatch, ctCountDown
		var $FDuration = 60;
		var $FStarted = false;
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			$this->FTime = time();
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'clocktype':
					$this->ClockType = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function get_Duration()
		{
			return $this->FDuration;
		}
		
		function set_Duration($value)
		{
			if ($value != $this->FDuration)
			{
				$this->FDuration = $value;
				
				if ($this->st_rendered)
				{
					//TQuark::instance()->browserAlert($this->Name.'.Duration = '.$value);
					$js = 	'var jsfrm = getJSform("'.$this->getParentForm()->id.'");'."\n".
							'jsfrm.'.$this->Name.'.Duration = '.$value.';'."\n".
							'jsfrm.'.$this->Name.'.dateObject = new Date();'."\n";
					TQuark::instance()->browserScript($js);
				}
			}			
		}
		
		function get_Started()
		{
			return $this->FStarted;
		}
		
		function set_Started($value)
		{
			if ($value != $this->FStarted)
			{
				$this->FStarted = $value;
			
				if ($this->st_rendered)
				{
					/*$js = 	'var jsfrm = getJSform("'.$this->getParentForm()->id.'");'."\n".
							'jsfrm.'.$this->Name.'.Duration = '.$this->FDuration.';'."\n".
							'jsfrm.'.$this->Name.'.dateObject = new Date();'."\n";
							'jsfrm.'.$this->Name.'.Started = '.($this->FStarted ? 'true': 'false').';'."\n";*/
					switch ($value)
					{
						case false:
							$js = 	'var jsfrm = getJSform("'.$this->getParentForm()->id.'");'."\n".
									'jsfrm.'.$this->Name.'.Duration = '.$this->FDuration.';'."\n".
									'jsfrm.'.$this->Name.'.stop();'."\n";
							break;
						case true:
							$js = 	'var jsfrm = getJSform("'.$this->getParentForm()->id.'");'."\n".
									'jsfrm.'.$this->Name.'.Duration = '.$this->FDuration.';'."\n".
									'jsfrm.'.$this->Name.'.start();'."\n";
							break;
					}
					//TQuark::instance()->browserAlert($this->Name.'.Duration = '.$value);
					TQuark::instance()->browserScript($js);
				}
			}
		}
		
		function start()
		{
			$this->Started = false;
			$this->Started = true;
		}
		
		function synchronize($value)
		{
			if (!$this->st_rendered) return;
			
			$millis = 0;
			if (is_string($value)) $millis = strtotime($value) * 1000;
			else $millis = $value;
			//TQuark::instance()->browserAlert($millis);
					
			$js = 	'var jsfrm = getJSform("'.$this->getParentForm()->id.'");'."\n".
					//'jsfrm.'.$this->Name.'.Duration = '.$this->FDuration.';'."\n".
					//'jsfrm.'.$this->Name.'.stop();'."\n";
					'jsfrm.'.$this->Name.'.sync('.$millis.');'."\n";
			
			TQuark::instance()->browserScript($js);
		}
		
		function generateHTML()
		{
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			if ($this->CSSClass !=  '') $class.= ' '.$this->CSSClass;
			
			$style = $this->generateStyle();
			
			$html = '<span id="'.$this->id.'" class="'.$class.'" style="'.$style.'">'.date('h:i:s', $this->FTime).'</span>';
			$this->st_rendered = true;
			return $html;
		}
		
		function generateJS()
		{
			$js = $this->innerJS();
			
			/*$js = str_replace('%ClockType%', $this->ClockType, $js);
			$js = str_replace('%Duration%', $this->FDuration, $js);*/
			
			$js.=	'jsself.ClockType = "'.$this->ClockType.'";'."\n".
					'jsself.Duration = '.$this->FDuration.';'."\n".
					'jsself.Started = '.($this->FStarted ? 'true' : 'false').';'."\n";
			
			return $js;
		}
	}
	
}

?>