<?php

if (!defined('Q_MESSAGEDLG_Q'))
{
	define('Q_MESSAGEDLG_Q', true);

	//include 'Form.php';
	
	class TMessageDlg extends TForm
	{
		static $DefaultStyle = "
div.qDebug_TForm
{
	background-color: #dddddd;
	border-style: solid;
	border-color: white;
	border-width: 1px;
	border-radius: 10px;
	box-shadow: 0px 0px 10px #888888;	
}

div.qDebug_TForm_modal
{
	background-color: #888888;
	opacity: 0.5;
	filter: alpha(opacity=50); /* For IE8 and earlier */
}

div.qDebug_TForm div.qDebug_TForm_caption
{
	background-color: #ff0000;
	height: 22px;
	border-style: none;
	border-top-color: white;
	border-top-style: solid;
	border-top-width: 1px;
	border-left-color: white;
	border-left-style: solid;
	border-left-width: 1px;
	border-right-color: white;
	border-right-style: solid;
	border-right-width: 1px;
	border-radius: 10px 10px 0px 0px;
	box-shadow: 0px 0px 10px #888888;	
	text-shadow: 0 0 3px #999999;
}

div.qDebug_TForm span.qDebug_TForm_caption
{
	display: block;
	position: absolute;
	left: 8px;
	top: 4px;
	font-weight: bold;
	color: white;
}
				
ul.qDebug_TPageControl
{
	list-style: none;
	min-width: 16px;
}

ul.qDebug_TPageControl li
{
	display: inline-block;
	background-color: #e8e8e8;
	border: 1px solid white;
	border-top-left-radius: 5px;
	border-top-right-radius: 5px;
}

ul.qDebug_TPageControl li:hover
{
	background-color: white;	
}

ul.qDebug_TPageControl li.active
{
	background-color: white;
}

ul.qDebug_TPageControl a
{
	display: block;
	min-width: 16px;
	height: 24px;
	padding-left: 4px;
	padding-right: 4px;
	padding-top: 8px;
	text-decoration: none;
}


div.qDebug_TPageControl
{
	
}

div.qDebug_TTabSheet
{
	display: none;
	border: 1px solid white;
	width: 100%;
	height: 100%;
	overflow: hidden;
}
				";

		var $CallbackHandle;
		var $lbl_Message;
		var $btn_Yes;
		var $btn_No;
		
		function __construct($Message, $Caption, $Buttons, $CallbackHandle, $Theme)
		{
			parent::__construct(null);
			
			$this->Name = 'frm_MessageDlg';
			$this->Theme = $Theme;
			$this->Left = 0;
			$this->Top = 0;
			$this->Width = 320;
			$this->Height = 120;
			$this->BorderStyle = 'bsDialog';
			$this->Caption = $Caption;
			$this->Position = 'poScreenCenter';
			
			$this->lbl_Message = new TLabel($this);
			$this->lbl_Message->Name = 'lbl_Message';
			$this->lbl_Message->Left = 32;
			$this->lbl_Message->Top = 16;
			$this->lbl_Message->Caption = $Message;
			$this->addControl($this->lbl_Message);
			
			if (is_array($Buttons))
				foreach ($Buttons as $button)
				{
					switch ($button)
					{
						case 'mbYes':
							$this->btn_Yes = new TButton($this);
							$this->btn_Yes->Name = 'btn_Yes';
							$this->btn_Yes->Left = 104;
							$this->btn_Yes->Top = 48;
							$this->btn_Yes->Caption = 'Yes';
							$this->btn_Yes->OnClick = 'btn_YesOnClick';
							$this->addControl($this->btn_Yes);
							break;
						case 'mbNo':
							$this->btn_No = new TButton($this);
							$this->btn_No->Name = 'btn_No';
							$this->btn_No->Left = 168;
							$this->btn_No->Top = 48;
							$this->btn_No->Caption = 'No';
							$this->btn_No->OnClick = 'btn_NoOnClick';
							$this->addControl($this->btn_No);
							break;
					}
				}
			
			$this->CallbackHandle = $CallbackHandle;
			$this->ThumbVisible = false;
		}
		
		function btn_YesOnClick($sender)
		{
			TQuark::instance()->callHandler($this->CallbackHandle, $this, 'ModalResult', 'mrYes');
			$this->close();
		}
		
		function btn_NoOnClick($sender)
		{
			TQuark::instance()->callHandler($this->CallbackHandle, $this, 'ModalResult', 'mrNo');
			$this->close();
		}
				
		
	}

}

?>