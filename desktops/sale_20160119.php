<?php
class TsaleDesktop extends TDesktop
{
	var $lbl_FormCaption = null;
	var $lbl_LoginName = null;
	var $lbl_UserName = null;
	var $lbl_CompanyName = null;
	var $btn_Logout = null;
	
	function OnLoad()
	{
		TQuark::instance()->viewports = Array();
		TQuark::instance()->viewports[0] = 'saleDesktop_viewport';
		TQuark::instance()->currentViewport = 'saleDesktop_viewport';
		
		$this->lbl_FormCaption = new TLabel($this);
		$this->lbl_FormCaption->Name = 'lbl_FormCaption';
		$this->lbl_FormCaption->Caption = 'Form Caption';
		$this->lbl_FormCaption->Left = 264;
		$this->lbl_FormCaption->Top = 104;
		$this->lbl_FormCaption->CSSClass = 'formcaption';

		$this->lbl_LoginName = new TLabel($this);
		$this->lbl_LoginName->Name = 'lbl_LoginName';
		$this->lbl_LoginName->Caption = 'loginname';
		$this->lbl_LoginName->Left = 16;
		$this->lbl_LoginName->Top = 16;
		$this->lbl_LoginName->CSSClass = '';
	
		$this->lbl_UserName = new TLabel($this);
		$this->lbl_UserName->Name = 'lbl_UserName';
		$this->lbl_UserName->Caption = 'firstname lastname';
		$this->lbl_UserName->Left = 16;
		$this->lbl_UserName->Top = 40;
		$this->lbl_UserName->CSSClass = '';
	
		$this->lbl_CompanyName = new TLabel($this);
		$this->lbl_CompanyName->Name = 'lbl_CompanyName';
		$this->lbl_CompanyName->Caption = 'company';
		$this->lbl_CompanyName->Left = 16;
		$this->lbl_CompanyName->Top = 64;
		$this->lbl_CompanyName->CSSClass = '';
		
		$this->btn_Logout = new TButton($this);
		$this->btn_Logout->Name = 'btn_Logout';
		$this->btn_Logout->Caption = 'logout';
		$this->btn_Logout->Left = 120;
		$this->btn_Logout->Top = 0;
		$this->btn_Logout->CSSClass = '';
		$this->btn_Logout->OnClick = 'btn_LogoutOnClick';
	}

	function generateHTML()
	{
		$s = $this->innerHTML();
		$s = str_replace('%parent%', $this->Name, $s);

		$class = '';
		if ($this->Theme != '') $class = $this->Theme.'_TsaleDesktop';

		$style = 	'';//'display: block; '.
		//'position: absolute; '.
		//'left: '.$this->Left.'px; '.
		//'top: '.$this->Top.'px; '.
		//'width: '.$this->Width.'px; '.
		//'height: '.$this->Height.'px;';

		$html = '';

		return	//'<span style="font-size: 56px; color: white">'.$this->Caption.'</span>'.
				//'<a style="position: absolute; right: 12px; top: 12px;" onclick="callBack(\'shutdown_onclick\', \''.$this->Name.'\', \'\');"><img src="images/shutdown-48_white.png"></img></a>'.
				'<div id="'.$this->Name.'_wrapper" class="'.$class.'_wrapper" style="'.$style.'"> '.
				'	<div id="'.$this->Name.'_toprow" > '.
				'		<div id="'.$this->Name.'_logo" class="'.$class.'_logo" style=""> '.
				'			<a href="" style="display: block; width: 100%; margin: 18px auto; text-align: center;" > '.
				'				<img src="themes/sale/logo-disponibil.png" width="100px" /> '.
				'			</a> '.
				'		</div> '.
				'		<div id="'.$this->Name.'_top" class="'.$class.'_top" style=""> '."\n".
				$this->lbl_FormCaption->generateHTML().
				'			<div id="'.$this->Name.'_login" class="'.$class.'_login" style="">'."\n".
				$this->lbl_LoginName->generateHTML().
				str_replace('%parent%', $this->Name, $this->btn_Logout->generateHTML()).
				$this->lbl_UserName->generateHTML().
				$this->lbl_CompanyName->generateHTML().
				'			</div>'."\n".
				'		</div> '.
				'	</div> '.
				'	<div id="'.$this->Name.'_bottomrow" > '.
				'		<div id="'.$this->Name.'_iconbar" class="'.$class.'_iconbar"></div> '.
				'		<div id="'.$this->Name.'_viewport" class="'.$class.'_viewport">'.
				$html.
				'		</div>'.
				'	</div> '.
				'</div> '.
				'<script type="text/javascript">'."\n".
				'	addFormID("'.$this->Name.'");'."\n".
				'	getJSform("'.$this->Name.'").callBack = callBack;'."\n".
				'</script> ';

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
		$frm = TQuark::instance()->firstForm();
		while ($frm != null && !$b)
		{
			if ($frm->Name == $FormName) $b = true;
			
			$frm = TQuark::instance()->nextForm();
		}
			
		return $b;
	}

	function refreshThumbs()
	{
		$frm = TQuark::instance()->firstForm();
		while ($frm != null)
		{
			if (!$this->ThumbExists($frm->Name) && $frm->ThumbVisible)
			{
				$thumb = new TFormThumb($this, $frm->Name);
				$thumb->Name = $frm->Name.'_thumb';
				$thumb->OnClick = 'thumb_onclick';
				$this->Controls[] = $thumb;

				$html = $thumb->generateHTML();
				TQuark::instance()->browserAppend($this->Name.'_iconbar', $html);					
			}
			
			$frm = TQuark::instance()->nextForm();
		}
			
		foreach ($this->Controls as $key => $ctrl)
		{
			if ($ctrl instanceof TFormThumb)
			{
				if (!$this->FormExists($ctrl->FormName))
				{
					TQuark::instance()->browserDelete($ctrl->id);
					unset($this->Controls[$key]);
					$this->Controls = array_values($this->Controls);
				}
			}
		}
	}

	function setLoginInfo($LoginName, $UserName, $CompanyName)
	{
		$this->lbl_LoginName->Caption = $LoginName;
		$this->lbl_UserName->Caption = $UserName;
		$this->lbl_CompanyName->Caption = $CompanyName;
	}
	
	function btn_LogoutOnClick()
	{
		TQuarkOS::instance()->sendMessage(0, 'logout');
	}

	function thumb_onclick($sender)
	{
		if ($sender == null) return;
		//TQuark::instance()->addAjaxStack('', 'alert', $sender);

		$frm_name = '';
		$thumb = null;
		foreach ($this->Controls as $ctrl)
		{
			if ($ctrl instanceof TFormThumb)
			{
				TQuark::instance()->browserRemoveClass($ctrl->id, 'thumbActive');
				
				if ($ctrl->Name == $sender) 			
				{
					$frm_name = $ctrl->FormName;
					$thumb = $ctrl;
				}
			}
		}
			
		if ($frm_name != '')
		{
			$frm_selected = TQuark::instance()->getForm($frm_name);
			if ($frm_selected != null && !$frm_selected->Visible)
			{
				$frm = TQuark::instance()->firstForm();				
				while ($frm != null)
				{
					if ($frm != $frm_selected) $frm->hide();
					$frm = TQuark::instance()->nextForm();
				}
					
				$frm_selected->show();
				$this->lbl_FormCaption->Caption = $frm_selected->Caption;
				if ($thumb != null) TQuark::instance()->browserAddClass($thumb->id, 'thumbActive');
			}
		}
	}


}
?>