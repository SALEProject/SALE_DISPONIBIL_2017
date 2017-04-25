<?php

if (!defined('Q_DOJOGAUGE_Q'))
{
	define('Q_DOJOGAUGE_Q', true);
	
	class TdojoGauge extends TWidget
	{
		var $Caption;
		var $GaugeType;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'caption':
					$this->Caption = $value;
					break;
				case 'gaugetype':
					$this->GaugeType = $value;
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
			
			$style = $this->generateStyle();
			
			$id = '%parent%.'.$this->Name;
			
			return '<div id="'.$id.'" class="'.$class.'" style="'.$style.'"></div>';
		}
		
		function generateJS()
		{
			$id =	'%parent%.'.$this->Name;
			
			switch ($this->GaugeType)
			{
				case 'gtGlossyCircular':
					return	'dojo.require("dojox.gauges.GlossyCircularGauge"); '."\n".
							'getJSform("%parent%").gauge_'.$this->Name.' = new dojox.gauges.GlossyCircularGauge({ '."\n".
							'	background: [255, 255, 255, 0], '."\n".
							'	title: "Value", '."\n".
							//'	id: "glossyGauge", '."\n".
							'	width: '.$this->Width.', '."\n".
							'	height: '.$this->Height.' '."\n".
							'}, dojo.byId("'.$id.'")); '."\n".
							'getJSform("%parent%").gauge_'.$this->Name.'.startup(); ';
					break;
				case 'gtGlossySemiCircular':
					return 	'dojo.require("dojox.gauges.GlossySemiCircularGauge"); '."\n".
							'getJSform("%parent%").gauge_'.$this->Name.' = new dojox.gauges.GlossySemiCircularGauge({ '."\n".
							'	title: "Value", '."\n".
							'	background: [255, 255, 255, 0], '."\n".
							//'	id: "glossyGauge", '."\n".
							'	width: '.$this->Width.', '."\n".
							'	height: '.$this->Height.' '."\n".
							'}, dojo.byId("'.$id.'")); '."\n".
							'getJSform("%parent%").gauge_'.$this->Name.'.startup(); ';
					break;
			}
		}
		
		function release()
		{
			$js = 'getJSform("'.$this->Parent->Name.'").gauge_'.$this->Name.'.destroyRecursive();';
			TQuark::instance()->addAjaxStack('', 'script', $js);
		}
	}
	
	//registerWidget('TdojoGauge', 'TdojoGauge');

}
	
?>