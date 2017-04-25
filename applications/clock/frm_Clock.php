<?php

	class Tfrm_Clock extends TForm
	{
		function OnLoad()
		{
			for ($i = 0; $i < 12; $i++)
			{
				$alpha = $i * 30;
				
				$shp = new TShape();
				$shp->Name = 'shp_'.$i;
				$shp->Left = 152 + 128 * cos(deg2rad($alpha)) - 4;
				$shp->Top = 144 + 128 * sin(deg2rad($alpha)) - 4;
				$shp->Width = 8;
				$shp->Height = 8;
				$shp->Shape = 'stCircle';
				$shp->PenColor = 'silver';
				$shp->Style = 'box-shadow: 0px 0px 5px gray;';
				$this->Controls[$shp->Name] = $shp;
			}
			
			$shp_hour = new TShape();
			$shp_hour->Name = 'shp_hour';
			$shp_hour->Left = 152 - 3;
			$shp_hour->Top = 144 - 64;
			$shp_hour->Width = 4;
			$shp_hour->Height = 64;
			$shp_hour->Shape = 'stRectangle';
			$shp_hour->BrushColor = 'black';
			$shp_hour->Style =	'box-shadow: 0px 0px 5px gray; '.
								'transform-origin: 2px 64px; -ms-transform-origin: 2px 64px; -webkit-transform-origin: 2px 64px;'.
								'transform: rotate(0deg); -ms-transform: rotate(0deg); -webkit-transform: rotate(0deg);';
			$this->Controls[$shp_hour->Name] = $shp_hour;
				
			$shp_minute = new TShape();
			$shp_minute->Name = 'shp_minute';
			$shp_minute->Left = 152 - 3;
			$shp_minute->Top = 144 - 112;
			$shp_minute->Width = 4;
			$shp_minute->Height = 112;
			$shp_minute->Shape = 'stRectangle';
			$shp_minute->BrushColor = 'black';
			$shp_minute->Style =	'box-shadow: 0px 0px 5px gray; '.
									'transform-origin: 2px 112px; -ms-transform-origin: 2px 112px; -webkit-transform-origin: 2px 112px;'.
									'transform: rotate(60deg); -ms-transform: rotate(60deg); -webkit-transform: rotate(60deg);';
			$this->Controls[$shp_minute->Name] = $shp_minute;

			$shp_second = new TShape();
			$shp_second->Name = 'shp_second';
			$shp_second->Left = 152 - 1;
			$shp_second->Top = 144 - 112;
			$shp_second->Width = 0;
			$shp_second->Height = 112;
			$shp_second->Shape = 'stRectangle';
			$shp_second->BrushColor = 'red';	
			$shp_second->PenColor = 'red';			
			$shp_second->Style =	'box-shadow: 0px 0px 5px gray; '.
									'transform-origin: 0px 112px; -ms-transform-origin: 0px 112px; -webkit-transform-origin: 0px 112px; '.
									'transform: rotate(120deg); -ms-transform: rotate(120deg); -webkit-transform: rotate(120deg);';
			$this->Controls[$shp_second->Name] = $shp_second;
			
			TQuark::instance()->registerTimer($this, 'OnTimer', 1000);
		}
		
		function btn_Close_onclick()
		{
			$this->close();
		}
		
		function OnTimer()
		{
			$dt = getdate();
			$hour = $dt['hours'];
			$minute = $dt['minutes'];
			$second = $dt['seconds'];
			
			// set hour
			$id = 'frm_Clock.shp_hour';
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'transform: rotate('.(($hour % 12) * 30 + $minute / 2).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'msTransform: rotate('.(($hour % 12) * 30 + $minute / 2).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'webkitTransform: rotate('.(($hour % 12) * 30 + $minute / 2).'deg)');
				
			// set minute
			$id = 'frm_Clock.shp_minute';
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'transform: rotate('.($minute * 6).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'msTransform: rotate('.($minute * 6).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'webkitTransform: rotate('.($minute * 6).'deg)');
				
			// set second
			$id = 'frm_Clock.shp_second';
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'transform: rotate('.($second * 6).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'msTransform: rotate('.($second * 6).'deg)');
			TQuark::instance()->addAjaxStack($id, 'setStyle', 'webkitTransform: rotate('.($second * 6).'deg)');
		}
	}
	
?>
