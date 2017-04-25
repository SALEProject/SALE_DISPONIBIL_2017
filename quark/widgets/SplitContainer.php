<?php

if (!defined('Q_SPLITCONTAINER_Q'))
{
	define('Q_SPLITCONTAINER_Q', true);

	class TSplitContainer extends TWidget
	{
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			$this->Left = 0;
			$this->Top = 0;
			$this->Width = 320;
			$this->Height = 240;
			
			$pnl_minus = new TPanel($this);
			//$pnl_minus = 
			$pnl_plus = new TPanel($this);
			$splitter = new TSplitter($this);
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function generateHTML()
		{
			$html = $this->innerHTML();
			
			$style = $this->generateStyle();
			$id = $this->id;

			return	'<div id="'.$id.'" style="'.$style.'">'."\n".
					$html.
					'</div>'."\n";
		}
		
		function generateJS()
		{
			return $this->innerJS();
		}
		
	}
	
	//registerWidget('TSplitContainer', 'TSplitContainer');
	
}

?>