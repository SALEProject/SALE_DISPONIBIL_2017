<?php

if (!defined('Q_TREENODE_Q'))
{
	define ('Q_TREENODE_Q', true);

	class TTreeNode extends TComponent
	{
		var $Data = '';
		private $FhasChildren = false;
		private $FCaption = '';
		var $Items;
		protected $currentChildId = 0;
		var $rendered = false;
		
		function __construct($AParent, $AName)
		{
			parent::__construct($AParent);
			$this->Name = $AName;
			$this->Items = array();
		}
		
		protected function generateChildId()
		{
			return (string)++$this->currentChildId;
		}
		
		function addChild($child)
		{
			$result = null;
			
			if (is_object($child) && $child instanceof TTreeNode) $result = $child;
			else if (is_string($child))
			{
				$result = new TTreeNode($this, $this->generateChildId());
				if ($result != null) $this->Items[] = $result;
				$result->Caption = $child;
			}
			
			return $result;
		}
		
		function findNode($childName)
		{
			foreach ($this->Items as $child)
				if ($child->Name == $childName) return $child;			
		}
		
		function findParent()
		{
			if ($this->Parent instanceof TTreeView) return $this->Parent;
			else if ($this->Parent instanceof TTreeNode) return $this->Parent->findParent();
			else return null;
		}
		
		function get_Caption()
		{
			return $this->FCaption;
		}
		
		function set_Caption($value)
		{
			if ($this->FCaption == $value) return;
			
			$this->FCaption = $value;
			$obj = $this->findParent();
			if ($obj != null) $obj->updateTreeNodeHTML($this);
		}
		
		/*function buildData()
		{
			
		}
		
		function loadData()
		{
			
		}
		
		function get_Data()
		{
			$this->buildData();
			return $this->FData;	
		}
		
		function set_Data($value)
		{
			$this->FData = $value;
			$this->loadData();
		}*/
		
		function get_hasChildren()
		{
			return (count($this->Items) > 0) | $this->FhasChildren;
		}
		
		function set_hasChildren($value)
		{
			$this->FhasChildren = $value;
		}
		
		
	}
	
}

?>