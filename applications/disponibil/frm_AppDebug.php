<?php

	class Tfrm_AppDebug extends TForm
	{
		function OnAppTimer($event, $msec)
		{
			$s = '%s: rcvd evt for %s ID: %d (%d msec)';
			$s = sprintf($s, date('H:i:s', time()), $event['Resource'], $event['ID_Resource'], $msec);
			$this->ed_Log->Text.= $s."\n";
			
			$id = $this->ed_Log->id;
			$s = 'if ($("'.$id.'") != null) $("'.$id.'").scrollTop = $("'.$id.'").scrollHeight;';
			TQuark::instance()->browserScript($s);
		}
		
		function  btn_ClearCacheOnClick()
		{
			TQuark::instance()->clearCache(false);
		}
		
		function btn_ControlPanelOnClick()
		{
			TQuarkOS::instance()->launchApplication('controlPanel');
			
		}
	}
?>