<?php

	class TdesktopMetro extends TDesktop
	{
		function OnLoad()
		{
			TQuark::instance()->viewports = Array();
			TQuark::instance()->viewports[0] = 'desktopMetro_viewport';
			TQuark::instance()->currentViewport = 'desktopMetro_viewport';
		}
		
		function generateHTML()
		{
			$s = $this->innerHTML();
			$s = str_replace('%parent%', $this->Name, $s);
				
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_TdesktopMetro';
				
			$style = 	'';//'display: block; '.
						//'position: absolute; '.
						//'left: '.$this->Left.'px; '.
						//'top: '.$this->Top.'px; '.
						//'width: '.$this->Width.'px; '.
						//'height: '.$this->Height.'px;';
		
			$html = '';
		
			return	//'<form id="'.$this->Name.'" action="" method=""> '.
					'<span style="font-size: 56px; color: white">'.$this->Caption.'</span>'.
					//'<div class="'.$class.'_res"><div class="caption">1024x768</div></div>'.
					'<a style="position: absolute; right: 12px; top: 12px;" onclick="callBack(\'shutdown_onclick\', \''.$this->Name.'\', \'\');"><img src="images/shutdown-48_white.png"></img></a>'.
					'<div class="'.$class.'_bkgrnd"></div>'.
					'<div id="'.$this->Name.'_wrapper" class="'.$class.'_wrapper" style="'.$style.'"> '.
					'	<div id="'.$this->Name.'_iconbar" class="'.$class.'_iconbar">'.
					'	</div>'.
					'	<div id="'.$this->Name.'_separator" class="'.$class.'_separator">'.
					'		<img src="themes/desktopMetro/vertical_separator.png"></img>'.
					'	</div>'.
					'	<div id="'.$this->Name.'_viewport" class="'.$class.'_viewport">'.
					$html.
					'	</div>'.
					'</div> '.
					'<script type="text/javascript">addFormID("'.$this->Name.'");</script> ';//.
						//'</form> ';
		}
		
		function generateJS()
		{
			$js =	'getJSform("'.$this->Name.'").thumbMouseDown = function(id)'."\n".
					'{'."\n".
					'	$addClass(id, "fg-yellow");'."\n".
					'	$addClass(id, "bg-black");'."\n".
					//'	$addClass(id, "tile-transform-right");'."\n".
					'};'."\n".
					'getJSform("'.$this->Name.'").thumbMouseUp = function(id)'."\n".
					'{'."\n".
					'	$removeClass(id, "fg-yellow");'."\n".
					'	$removeClass(id, "bg-black");'."\n".
					//'	$removeClass(id, "tile-transform-right");'."\n".
					'};'."\n";
				
			return $js;
		}
		
		function ThumbExists($FormName)
		{
			$b = false;
			foreach ($this->Controls as $ctrl)
			{
				if ($ctrl instanceof TFormThumb)
				{
					if ($ctrl->FormName == $FormName) $b = true;
				}
			}
			
			return $b;
		}
		
		function FormExists($FormName)
		{
			$b = false;
			foreach (TQuark::instance()->forms as $frm)
			{
				if ($frm->Name == $FormName) $b = true;
			}
			
			return $b;
		}
		
		function refreshThumbs()
		{
			foreach(TQuark::instance()->forms as $frm)
			{
				if (!$this->ThumbExists($frm->Name) && $frm->ThumbVisible)
				{				
					$thumb = new TFormThumb();
					$thumb->Name = $frm->Name.'_thumb';
					$thumb->FormName = $frm->Name;
					$this->Controls[] = $thumb;
					
					$thumbCSSClass = $frm->ThumbCSSClass;
					$html = $frm->generateThumbHTML();
					
					// style="display: block; position: relative; width: 120px; height: 120px; margin: 8px 8px;"
					$html =	'<a id="'.$thumb->Name.'" class="desktopMetro_tile '.$thumbCSSClass.'" '.
							'onmousedown="getJSform(\''.$this->Name.'\').thumbMouseDown(\''.$thumb->Name.'\')" '.
							'onmouseup="getJSform(\''.$this->Name.'\').thumbMouseUp(\''.$thumb->Name.'\')" '.
							'onclick="callBack(\'thumb_onclick\', \''.$this->Name.'\', \''.$thumb->Name.'\');">'.
							'	<div class="content">'.$html.'</div>'.
							'	<div class="caption">'.$frm->Caption.'</div>'.							
							'</a>';
					
					TQuark::instance()->addAjaxStack($this->Name.'_iconbar', 'append', $html);
					
				}
			}
			
			foreach ($this->Controls as $key => $ctrl)
			{
				if ($ctrl instanceof TFormThumb)
				{
					if (!$this->FormExists($ctrl->FormName))
					{
						TQuark::instance()->addAjaxStack($ctrl->FormName.'_thumb', 'delete', '');	
						unset($this->Controls[$key]);
						$this->Controls = array_values($this->Controls);
					}
				}
			}
		}

		function shutdown_onclick()
		{
			foreach (TQuark::instance()->forms as $frm) $frm->close();
			$frm = TQuark::instance()->loadForm('frm_LoginDLG.xml');
			if ($frm != null) $frm->showModal(); 			
		}
		
		function thumb_onclick($sender)
		{
			if ($sender == null) return;
			//TQuark::instance()->addAjaxStack('', 'alert', $sender);
				
			$frm_name = '';
			foreach ($this->Controls as $ctrl)
			{
				if ($ctrl instanceof TFormThumb && $ctrl->Name == $sender) $frm_name = $ctrl->FormName; 									
			}
			
			if ($frm_name != '')
			{
				$frm_selected = TQuark::instance()->forms[$frm_name];
				if ($frm_selected != null && !$frm_selected->Visible)
				{
					//TQuark::instance()->addAjaxStack('', 'alert', $frm_selected->Name);
					foreach (TQuark::instance()->forms as $frm)
					{
						if ($frm != $frm_selected) $frm->hide();
					}
					
					$frm_selected->show();
				}
			}
		}
		
		
	}
?>