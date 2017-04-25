<?php

if (!defined('Q_DOJOCHART_Q'))
{
	define('Q_DOJOCHART_Q', true);
	
	class TdojoChart extends TWidget
	{
		var $Caption;
		var $ChartType;
		var $DataSet;
		var $XLabelColumn;
		var $YValueColumn;
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'caption':
					$this->Caption = $value;
					break;
				case 'charttype':
					$this->ChartType = $value;
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
		
			return '<div id="'.$this->Name.'" class="'.$class.'" style="'.$style.'"></div>';
		}
		
		function generateJS()
		{
			$s = /*'%parent%_'.*/$this->Name;
			
			$seriesA = Array();
			if (isset($this->DataSet))
			{
				foreach($this->DataSet->Rows as $row)
				{
					$point = Array();
					$point['y'] = $row[$this->YValueColumn];
					$point['text'] = $row[$this->XLabelColumn];
					
					$seriesA[] = $point;
				}
				
				//addAjaxStack('', 'alert', json_encode($seriesA));
			}
			
			switch ($this->ChartType)
			{
				case "ctVerticalStackedBars":
					return	'dojo.require("dojox.charting.Chart");'."\n".
							'dojo.require("dojox.charting.axis2d.Default");'."\n".
							'dojo.require("dojox.charting.plot2d.StackedColumns");'."\n".

							'var chart_'.$this->Name.' = new dojox.charting.Chart("'.$this->Name.'");'."\n".
							'chart_'.$this->Name.'.addAxis("x", {fixLower: "major", fixUpper: "major", includeZero: true});'."\n".
							'chart_'.$this->Name.'.addAxis("y", {vertical: true, fixLower: "major", fixUpper: "major", includeZero: true});'."\n".
							'chart_'.$this->Name.'.addPlot("default", {type: "Columns", gap: 10});'."\n".
							//'chart_'.$this->Name.'.addSeries("Series A", [ 8,  5,  9, 13, 16, 20 ], { stroke: {color: "#00aff0"}, fill: "#00aff0" });'."\n". 
							//'chart_'.$this->Name.'.addSeries("Series B", [ 9, 12, 16, 17, 20, 20 ], { stroke: {color: "#45fffd"}, fill: "#45fffd" });'."\n".
							'chart_'.$this->Name.'.addSeries("Series A", '.json_encode($seriesA).', { stroke: {color: "#00aff0"}, fill: "#00aff0" });'."\n".
							'chart_'.$this->Name.'.render();'.
							'try'."\n".
							'{'."\n".
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[1].setAttribute("fill-opacity", 0); '.
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[2].setAttribute("fill-opacity", 0); '.
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[3].setAttribute("fill-opacity", 0);	'.
							'}'."\n".
							'catch (e) {}'."\n";						
				case "ctPie":
					return	'dojo.require("dojox.charting.Chart"); '.
							'dojo.require("dojox.charting.plot2d.Pie"); '.
							'dojo.require("dojox.charting.themes.Adobebricks"); '.
					
							'var chart_'.$this->Name.' = new dojox.charting.Chart("'.$this->Name.'"); '.
							'chart_'.$this->Name.'.setTheme(dojox.charting.themes.Adobebricks); '.
							'chart_'.$this->Name.'.addPlot("default", '. 
							'	{ '.
							'		type: "Pie", '.
							'		font: "normal normal bold 12pt Tahoma", '.
							'		fontColor: "white", '.
							'		labelOffset: 40 '.
							'	}); '.
							'chart_'.$this->Name.'.addSeries("Series A", '.json_encode($seriesA).'); '.
							//'chart_'.$this->Name.'.addSeries("Series A", [{y: 4, text: "Red"}, {y: 2, text: "Green"}, {y: 1, text: "Blue"}, {y: 1, text: "Other"}]); '.
							'chart_'.$this->Name.'.render();'.
							'try'."\n".
							'{'."\n".
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[1].setAttribute("fill-opacity", 0); '.
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[2].setAttribute("fill-opacity", 0); '.
							'	chart_'.$this->Name.'.surface.rawNode.childNodes[3].setAttribute("fill-opacity", 0);	'.						
							'}'."\n".
							'catch (e) {}'."\n";						
					break;
				case "ctCandleSticks":
					return	'dojo.require("dojox.charting.Chart"); '.
							'dojo.require("dojox.charting.axis2d.Default"); '.
							'dojo.require("dojox.charting.plot2d.Candlesticks"); '.
							'dojo.require("dojox.charting.themes.Adobebricks"); '.
					
							'var chart_'.$this->Name.' = new dojox.charting.Chart("'.$this->Name.'"); '.
							'chart_'.$this->Name.'.setTheme(dojox.charting.themes.Adobebricks); '.
							'chart_'.$this->Name.'.addPlot("default", {type: "Candlesticks", gap: 1}); '.
							'chart_'.$this->Name.'.addAxis("x", {fixLower: "major", fixUpper: "major", includeZero: true}); '.
							'chart_'.$this->Name.'.addAxis("y", {vertical: true, fixLower: "major", fixUpper: "major", natural: true}); '.
							'chart_'.$this->Name.'.addSeries("Series A", [ '.
							'		{ open: 20, close: 16, high: 22, low: 8 }, '.
							'		{ open: 16, close: 22, high: 26, low: 6, mid: 18 }, '.
							'		{ open: 22, close: 18, high: 22, low: 11, mid: 21 }, '.
							'		{ open: 18, close: 29, high: 32, low: 14, mid: 27 }, '.
							'		{ open: 29, close: 24, high: 29, low: 13, mid: 27 }, '.
							'		{ open: 24, close: 8, high: 24, low: 5 }, '.
							'		{ open: 8, close: 16, high: 22, low: 2 }, '.
							'		{ open: 16, close: 12, high: 19, low: 7 }, '.
							'		{ open: 12, close: 20, high: 22, low: 8 }, '.
							'		{ open: 20, close: 16, high: 22, low: 8 }, '.
							'		{ open: 16, close: 22, high: 26, low: 6, mid: 18 }, '.
							'		{ open: 22, close: 18, high: 22, low: 11, mid: 21 }, '.
							'		{ open: 18, close: 29, high: 32, low: 14, mid: 27 }, '.
							'		{ open: 29, close: 24, high: 29, low: 13, mid: 27 }, '.
							'		{ open: 24, close: 8, high: 24, low: 5 }, '.
							'		{ open: 8, close: 16, high: 22, low: 2 }, '.
							'		{ open: 16, close: 12, high: 19, low: 7 }, '.
							'		{ open: 12, close: 20, high: 22, low: 8 }, '.
							'		{ open: 20, close: 16, high: 22, low: 8 }, '.
							'		{ open: 16, close: 22, high: 26, low: 6 }, '.
							'		{ open: 22, close: 18, high: 22, low: 11 }, '.
							'		{ open: 18, close: 29, high: 32, low: 14 }, '.
							'		{ open: 29, close: 24, high: 29, low: 13 }, '.
							'		{ open: 24, close: 8, high: 24, low: 5 }, '.
							'		{ open: 8, close: 16, high: 22, low: 2 }, '.
							'		{ open: 16, close: 12, high: 19, low: 7 }, '.
							'		{ open: 12, close: 20, high: 22, low: 8 }, '.
							'		{ open: 20, close: 16, high: 22, low: 8 } '.
							'		], '.
							'{ stroke: { color: "green" }, fill: "lightgreen" } '.
							'); '.
							'chart_'.$this->Name.'.render();';
						
					break;
			}
				
		}
		
	}
	
	//registerWidget('TdojoChart', 'TdojoChart');
	
}

?>