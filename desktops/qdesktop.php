<?php

	class Tqdesktop extends TDesktop
	{
		function OnLoad()
		{
			TQuark::instance()->viewports = Array();
			TQuark::instance()->viewports[0] = 'qdesktop_viewport';
			TQuark::instance()->currentViewport = 'qdesktop_viewport';
			
			$ds = new TDataSet();
			$ds->loadXML('application/data/data_qdesktopmenu.xml');
			
			$ds->Filter = 'ID_Parent = 0';
			$rows0 = $ds->Rows;
			foreach ($rows0 as $row0)
			{
				$pm = new TMenuItem();
				$pm->Name = 'pm_'.$row0['Name'];
				$pm->Caption = $row0['MenuCaption'];
				
				$ds->Filter = 'ID_Parent = '.$row0['ID'];
				$rows1 = $ds->Rows;
				
				foreach ($rows1 as $row1)
				{
					$mi = new TMenuItem();
					$mi->Name = $row1["Name"];
					$mi->Caption = $row1['MenuCaption'];
					$mi->OnClick = 'MenuItemClick';
					$mi->Data = $row1['Command'];
					$pm->addMenuItem($mi);
					$this->Controls[$mi->Name] = $mi;
				}
				
				$this->mn_Desktop->addMenuItem($pm);
			}
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
					//'<span style="font-size: 56px; color: white">'.$this->Caption.'</span>'.
					//'<div class="'.$class.'_res"><div class="caption">1024x768</div></div>'.
					//'<a style="position: absolute; right: 12px; top: 12px;" onclick="callBack(\'shutdown_onclick\', \''.$this->Name.'\', \'\');"><img src="images/shutdown-48_white.png"></img></a>'.
					//'<div class="'.$class.'_bkgrnd" style="border: 1px solid red;"></div>'.
					//'<div id="'.$this->Name.'_wrapper" class="'.$class.'_wrapper" style="'.$style.'"> '.
					'<div class="'.$class.'_Menu"><table style="width: 100%; font-size: inherit;" cellpadding="0" cellspacing="0" border="0"><tr><td width="80px"><span style="width: 80px; padding-left: 16px; font-size: 14px; text-shadow: 0 0 1px #999999;">.quark</span></td><td>'.$s.'</td><td style="width: 80px; text-align: center;"><span id="qdesktop_time">time</span></td></tr></table></div>'."\n".
					'	<div id="'.$this->Name.'_viewport" class="'.$class.'_viewport" style="border: 1px solid blue;">'."\n".
					$html."\n".
					'	</div>'."\n".
					'	<div id="'.$this->Name.'_iconbar" class="'.$class.'_iconbar" style="">'."\n".
					'	</div>'."\n".
					//'</div> '.
					'<script type="text/javascript">addFormID("'.$this->Name.'");</script> ';//.
					//'</form> ';
		}
		
		function generateJS()
		{
			$s = $this->innerJS();
			$s = str_replace('%parent%', $this->Name, $s);
			
			$js =	$s.
					'getJSform("'.$this->Name.'").callBack = callBack; '."\n".
					'getJSform("'.$this->Name.'").thumbMouseDown = function(id)'."\n".
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
		
		function MenuItemClick($sender)
		{
			//TQuark::instance()->addAjaxStack('', 'alert', $sender);
			if (!isset($sender)) return;
			if ($sender == null) return;
							
			$url = $this->Controls[$sender]->Data;
			if ($url == null) return;

			$frm = TQuark::instance()->loadForm($url);
			if ($frm != null) $frm->show();
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
			TQuark::instance()->addAjaxStack('qdesktop_time', 'update', date('H:i:s'));
			
			foreach(TQuark::instance()->forms as $frm)
			{
				if (!$this->ThumbExists($frm->Name) && $frm->ThumbVisible)
				{				
					$thumb = new TFormThumb();
					$thumb->Name = $frm->Name.'_thumb';
					$thumb->FormName = $frm->Name;
					$this->Controls[$thumb->Name] = $thumb;
					
					$thumbCSSClass = $frm->ThumbCSSClass;
					$thumbHtml = $frm->generateThumbHTML();
					
					// style="display: block; position: relative; width: 120px; height: 120px; margin: 8px 8px;"
					$html =	'<a id="'.$thumb->Name.'" class="desktopMetro_tile '.$thumbCSSClass.'" '.
							'onmousedown="getJSform(\''.$this->Name.'\').thumbMouseDown(\''.$thumb->Name.'\')" '.
							'onmouseup="getJSform(\''.$this->Name.'\').thumbMouseUp(\''.$thumb->Name.'\')" '.
							'onclick="callBack(\'thumb_onclick\', \''.$this->Name.'\', \''.$thumb->Name.'\');">';
					if ($thumbHtml != '')
						$html .= '	<div class="content">'.$thumbHtml.'</div>';
					else
						$html .= '	<div class="caption">'.$frm->Caption.'</div>';							
					$html .= '</a>';
					
					TQuark::instance()->addAjaxStack($this->Name.'_iconbar', 'append', $html);
					
				}
			}
			
			$ctrls = Array();
			foreach ($this->Controls as $key => $ctrl)
			{
				if ($ctrl instanceof TFormThumb)
				{
					if (!$this->FormExists($ctrl->FormName))
					{
						TQuark::instance()->addAjaxStack($ctrl->FormName.'_thumb', 'delete', '');	
						unset($this->Controls[$key]);
						//$this->Controls = array_values($this->Controls);
					}
					else $ctrls[$key] = $ctrl;
				}
				else $ctrls[$key] = $ctrl;
			}
			$this->Controls = $ctrls;
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