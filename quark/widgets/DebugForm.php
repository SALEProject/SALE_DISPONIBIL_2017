<?php

if (!defined('Q_DEBUGFORM_Q'))
{
	define('Q_DEBUGFORM_Q', true);
	
	//include 'Form.php';
	
	class TDebugForm extends TForm
	{
		static $DefaultStyle = "
div.qDebug_TForm
{
	display: flex;
	flex-direction: column;
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
	width: 100%;
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
		var $pgcontrol;
		var $pg_forms;
		var $pg_callstack;
		var $scroll;
		
		function __construct($Left, $Top, $Width, $Height, $Theme)
		{
			parent::__construct(null);
			
			$this->Name = 'frm_DebugForm';
			$this->Theme = $Theme;
			$this->Left = $Left;
			$this->Top = $Top;
			$this->Width = $Width;
			$this->Height = $Height;
			$this->BorderStyle = 'bsDialog';
			$this->Caption = '.quark Debug Form';
			$this->Position = 'poDesigned';
			
			$this->pgcontrol = new TPageControl($this);
			$this->addControl($this->pgcontrol);
			$this->pgcontrol->Name = 'pgcontrol';
			$this->pgcontrol->Theme = $this->Theme;
			$this->pgcontrol->Left = 8;
			$this->pgcontrol->Top = 8;
			$this->pgcontrol->Width = $Width - 16;
			$this->pgcontrol->Height = $Height - 40;			
						
			$this->pg_forms = new TTabSheet($this);
			$this->pgcontrol->addControl($this->pg_forms);
			$this->pg_forms->Name = 'pg_forms';
			$this->pg_forms->Caption = 'Forms';
			$this->pg_forms->Theme = $this->Theme;
			
			$this->pg_callstack = new TTabSheet($this);
			$this->pgcontrol->addControl($this->pg_callstack);
			$this->pg_callstack->Name = 'pg_callstack';
			$this->pg_callstack->Caption = 'Call Stack';
			$this->pg_callstack->Theme = $this->Theme;
			
			$this->scroll = new TScrollBox($this);
			//$this->Controls[] = $this->scroll;
			$this->pg_forms->addControl($this->scroll);
			$this->scroll->Name = 'pre_div';
			$this->scroll->Theme = $this->Theme;
			$this->scroll->Left = 8;
			$this->scroll->Top = 8;
			$this->scroll->Width = $Width - 16;
			$this->scroll->Height = $Height - 40 - 40;			
		}
		
		function show()
		{
			$html = $this->generateHTML();
			$js = $this->generateJS();
	
			//global $IsCallBack;
			switch (TQuark::instance()->IsCallBack)
			{
				case false:
					echo $html;
					//echo '<script type="text/javascript"> '.$js.'</script>';
					echo '<script type="text/javascript">addFormID("'.$this->Name.'");</script>';
								
					break;
				case true:
					//addAjaxStack('', 'alert', strlen($js));
					TQuark::instance()->addAjaxStack('', 'append', $html);
					TQuark::instance()->addAjaxStack('', 'script', 'addFormID("'.$this->Name.'");');
					//addAjaxStack('', 'script', $js);
					break;
			}
		}
		
		function renderHTML()
		{
			
		}
		
	}

}

?>