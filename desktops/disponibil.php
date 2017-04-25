<?php
class TdisponibilDesktop extends TDesktop
{
	var $clock = null;
	var $lbl_FormCaption = null;
	var $lbl_LoginName = null;
	var $lbl_UserName = null;
	var $lbl_CompanyName = null;
	var $btn_Logout = null;

	function OnLoad()
	{
		TQuark::instance()->viewports = Array();
		TQuark::instance()->viewports[0] = 'viewport0';
		TQuark::instance()->viewports[1] = 'viewport1';
		TQuark::instance()->viewports[2] = 'viewport2';
		TQuark::instance()->viewports[3] = 'viewport3';
		TQuark::instance()->currentViewport = 'viewport0';
		
		//  server time component
		$this->clock = new TClock($this);	
		$this->clock->Name = 'servertime';
		$this->addControl($this->clock);

		$this->lbl_FormCaption = new TLabel($this);
		$this->lbl_FormCaption->Name = 'lbl_FormCaption';
		$this->lbl_FormCaption->Caption = 'REMIT';
		$this->lbl_FormCaption->Left = 264;
		$this->lbl_FormCaption->Top = 104;
		$this->lbl_FormCaption->CSSClass = 'formcaption';

		$this->lbl_LoginName = new TLabel($this);
		$this->lbl_LoginName->Name = 'lbl_LoginName';
		$this->lbl_LoginName->Caption = 'loginname';
		$this->lbl_LoginName->Style = '
		display: block;
		position: relative;
		max-width 170px;
		max-width: 170px;
    margin: 0;
		font-size: 13px;
		padding: 0px !important;
		margin: 0px !important;
		line-height: 1.5;
		font-style: normal;
		font-weight: normal;
		color: black;
		';

		$this->lbl_UserName = new TLabel($this);
		$this->lbl_UserName->Name = 'lbl_UserName';
		$this->lbl_UserName->Caption = 'firstname lastname';
		$this->lbl_UserName->Style = '
		display: block;
		position: relative;
		max-width 170px;
		max-width: 170px;
		padding: 0px !important;
		margin: 0px !important;
		line-height: 1.5;
		font-style: normal;
		color: #999;
		font-weight: 600;
		font-size: 11px;
		font-style: italic;
		';


		$this->lbl_CompanyName = new TLabel($this);
		$this->lbl_CompanyName->Name = 'lbl_CompanyName';
		$this->lbl_CompanyName->Caption = 'company';
		$this->lbl_CompanyName->Style = '
		display: block;
		position: relative;
		max-width 170px;
		max-width: 170px;
		padding: 0px !important;
		margin: 0px !important;
		line-height: 1.5;
		font-style: normal;
		color: #999;
		font-weight: 600;
		font-size: 11px;
		font-style: italic;
		';



		$this->btn_Logout = new TButton($this);
		$this->btn_Logout->Name = 'btn_Logout';
		$this->btn_Logout->OnClick = 'btn_LogoutOnClick';
		$this->btn_Logout->CSSClass = 'transparent';



	}

	function generateHTML()
	{
		$s = $this->innerHTML();
		$s = str_replace('%parent%', $this->Name, $s);

		$class = '';
		if ($this->Theme != '') $class = $this->Theme.'_TdisponibilDesktop';

		$style = 	'';//'display: block; '.
		//'position: absolute; '.
		//'left: '.$this->Left.'px; '.
		//'top: '.$this->Top.'px; '.
		//'width: '.$this->Width.'px; '.
		//'height: '.$this->Height.'px;';

		$html =	'<link rel="icon" type="image/png" href="themes/disponibil/img/logo-large.png"/>'.
				'<div id="syshealth" style="background-color: green; height: 4px;"></div>'.
				'<div style="background-color: white">'.
				'<div id="'.$this->Name.'_header" class="'.$class.'_header" style="border: 1px solid red">'.
				'	<div id="logo_box" style="border: 1px solid blue">'.
				'		<a href=""><img src="themes/disponibil/img/logo.png" alt="disponibil logo" style="width: 60px; height: 60px;"/></a>'.
				$this->clock->generateHTML().
				'	</div>'.
				
				'	<div id="session_box" style="border: 1px solid blue">'.
				'		<div id="session_path">'.
				'			<span>Ring</span>'.
				'			<span>> Asset</span>'.
				'		</div>'.
				'		<div id="alerts">'.
				'			<div id="main_alert">'.
				'				<span>Nici o alerta recenta</span>'.
				'			</div>'.
				'		</div>'.
				'	</div>'.
				
				'	<div id="viewports_box" style="border: 1px solid blue">'.
				'		<ul>'.
				'			<li><a href="">Ecran principal</a></li>'.
				'			<li><a href="">Rapoarte si statistici</a></li>'.
				'			<li><a href="">Administrare cont</a></li>'.
				'			<li><a href="">Active</a></li>'.
				'	</div>'.
				
				'	<div id="user_box" style="border: 1px solid blue">'.
				'		<img src="themes/disponibil/img/user-alt-1.png" />'.
				'		<span>User Name</span>'.
				'	</div>'.
				
				'	<div id="logout_box" style="border: 1px solid blue">'.
				'		<a href="" style="display: block; margin-top: 16px; text-decoration: none;" onclick="callBack(\'btn_LogoutOnClick\', \''.$this->Name.'\', \'\'); return false;">'.
				'			<img src="themes/disponibil/img/power-standby.png" /><br/>'.
				'			<span>Logout</span>'.
				'		</a>'.
				'	</div>'.
				'</div></div>'.
				'<div id="'.$this->Name.'_viewports" class="'.$class.'_viewports" style="border: 1px solid red;">'.
				'	<div id="viewport0" class="" style="border: 1px solid green">'.
				'		<div id="column_left" style="border: 1px solid blue">'.
				'		</div>'.
				'		<div id="column_main" style="border: 1px solid blue">'.
				'		</div>'.
				'	</div>'.
				'	<div id="viewport1" class="" style="border: 1px solid green">'.
				'	</div>'.
				'	<div id="viewport2" class="" style="border: 1px solid green">'.
				'	</div>'.
				'	<div id="viewport3" class="" style="border: 1px solid green">'.
				'	</div>'.
				'</div>'.
				'<div id="'.$this->Name.'_iconbar" class="'.$class.'_iconbar" style="border: 1px solid red">'.
				'</div>';

		return $html;
	}

	function generateJS()
	{
		return parent::generateJS();
		
		/*
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

		return $js;*/
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
		/*$frm = TQuark::instance()->firstForm();
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
		}*/
	}
	
	function SynchClock($value)
	{
		$this->clock->synchronize($value);
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
