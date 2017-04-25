<?php

if (!defined('Q_DESKTOP_Q'))
{
	define('Q_DESKTOP_Q', true);

	//include 'Form.php';
	
	class TDesktop extends TForm
	{
		var $Caption;
	
		function setProperty($name, $value)
		{
			switch ($name)
			{
				case 'caption':
					$this->Caption = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
	
		function generateHTML()
		{
			$s = $this->innerHTML();
			$s = str_replace('%parent%', $this->Name, $s);
				
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_TForm';
				
			$style = $this->generateStyle();
	
			$html = '';
	
			return	//'<form id="'.$this->Name.'" action="" method=""> '.
					'	<div id="'.$this->Name.'_wrapper" class="'.$class.'" style="'.$style.'"> '.
					$html.
					'	</div> '.
					'	<script type="text/javascript">addFormID("'.$this->Name.'");</script> ';//.
					//'</form> ';
		}
	
		function generateJS()
		{
			$s = $this->innerJS();
			$s = str_replace('%parent%', $this->Name, $s);
	
			/*
			 $js =	'function '.$this->ClassName.'()'.
			'{ '.
			$s.
			'} '.
			'var '.$this->Name.' = new '.$this->ClassName.'();';
			*/
			$js =	$s.
			//'getJSform("'.$this->Name.'").captionClick = function() {this.focus();};'.
			'getJSform("'.$this->Name.'").callBack = callBack;';
					//'$("'.$this->Name.'").callBack = callBack;';
	
			return $js;
		}
	
		function show()
		{
			//$js = $this->generateJS();
	
			switch (TQuark::instance()->IsCallBack)
			{
				case false:
					$html = $this->generateHTML();
					echo $html;
					echo '<script type="text/javascript">addFormID("'.$this->Name.'", true);</script> ';
					//echo '<script type="text/javascript"> '.$js.'</script>';
	
					break;
				case true:
					TQuark::instance()->addAjaxStack('', 'script', 'addFormID("'.$this->Name.'");');
					$this->render();
					//TQuark::instance()->addAjaxStack('', 'append', $html);
					break;
			}
		}
		
		function renderHTML()
		{
			
		}
	
		function loadJS()
		{		
			$js = $this->generateJS();
			//addAjaxStack('', 'alert', $js);
			TQuark::instance()->addAjaxStack('', 'script', $js);
		}
	
		function close()
		{
			TQuark::instance()->removeForm($this->Name);
			TQuark::instance()->addAjaxStack($this->Name, 'delete', '');
			if ($this->IsModal)
				TQuark::instance()->addAjaxStack($this->Name.'_modal', 'delete', '');
			TQuark::instance()->addAjaxStack('', 'script', 'removeFormID("'.$this->Name.'")');
		}
		
		function refreshThumbs()
		{
			
		}
	
	}
	
	//registerWidget('TDesktop', 'TDesktop');

}

?>