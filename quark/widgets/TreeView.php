<?php

if (!defined('Q_TREEVIEW_Q'))
{
	define('Q_TREEVIEW_Q', true);
	
	//include 'TreeNode.php';

	class TTreeView extends TWidget
	{
		static $DefaultStyle = "				
div.default_TTreeView 
{
	user-select: none;
	/*margin: 50px auto 0;*/
 	background: #f5f5f5;
 	padding: 8px;
 	border-radius: 3px;
 	border: solid 1px #777;
 	box-shadow: 0 0 -3px #333;
	overflow: scroll;
}
 
ul.default_TTreeView, ul.default_TTreeView li
{
    padding: 0;
    margin: 0;
    list-style: none;
	display: inline-block;
	position: relative;
	wdth: 100%;
	float: left;
}

ul.default_TTreeView_items
{
	padding-left: 16px;
}				
 
ul.default_TTreeView li input
{
    position: absolute;
    opacity: 0;
}
 
ul.default_TTreeView li label
{
    display: inline-block;
    height: 16px;
    line-height: 16px;
    vertical-align: middle;
	background: url('http://experiments.wemakesites.net//pages/css3-treeview/example/icons.png') no-repeat;
	cursor: pointer;
    background-position: 18px 0;
}

ul.default_TTreeView li label::before
{
    background: url('http://experiments.wemakesites.net//pages/css3-treeview/example/icons.png') no-repeat;
	cursor: pointer;
    display: inline-block;
    height: 16px;
    line-height: 16px;
    vertical-align: middle;
				
    content: '';
    width: 16px;
    margin: 0 22px 0 0;
    vertical-align: middle;
    background-position: 0 -32px;
}

ul.default_TTreeView li a
{
    display: inline-block;
    height: 16px;
    line-height: 16px;
    vertical-align: middle;
	color: #35d;
    text-decoration: none;
	padding-left: 24px;
}
 
ul.default_TTreeView li a:hover
{
    text-decoration: underline;
}
 
/*ul.default_TTreeView li input + label + ul
{
    margin: 0 0 0 22px;
}*/
 
ul.default_TTreeView li input ~ ul
{
    display: none;
	padding-left: 16px;
}
 
ul.default_TTreeView li input:disabled + label
{
    cursor: default;
    opacity: .6;
}
 
ul.default_TTreeView li input:checked:not(:disabled) ~ ul
{
    display: block;
}

ul.default_TTreeView li input:checked + label::before
{
    background-position: 0 -16px;
}

/*@media screen and (-webkit-min-device-pixel-ratio:0)
{
    .css-treeview 
    {
        -webkit-animation: webkit-adjacent-element-selector-bugfix infinite 1s;
    }
 
    @-webkit-keyframes webkit-adjacent-element-selector-bugfix 
    {
        from 
        { 
            padding: 0;
        } 
        to 
        { 
            padding: 0;
        }
    }
}*/
				";
		
		var $Items = null;
		protected $handle_OnChange = null;
		var $OnChange = '';
		
		function __construct($AParent)
		{
			parent::__construct($AParent);
			
			$this->Items = new TTreeNode($this, 'root');
		}
		
		function setProperty($name, $value)
		{
			switch (strtolower($name))
			{
				case 'items.data':
					$this->Items->Data = $value;
					break;
				case 'onchange':
					$this->OnChange = $value;
					break;
				default:
					parent::setProperty($name, $value);
					break;
			}
		}
		
		function internalOnChange($sender, $varName, $varValue)
		{
			//  extract the cell coordinates
			$id = $this->id;
			$s = str_replace($id, '', $varName);
			$a = split('\.', $s);			
			
			if (count($a) == 0) return; //  check if there is anything
			
			//  eliminate empty slot
			if (empty($a[0])) { unset($a[0]); $a = array_values($a); } 
			if (count($a) == 0) return;
			
			//  eliminate the root element and search for the others
			if ($a[0] == 'root') unset($a[0]); 		
			$node = $this->Items;
			foreach ($a as $nodeName)
			{
				$node = $node->findNode($nodeName);
				if ($node == null) break; 
			}

			if ($node != null && isset($this->OnChange) && !empty($this->OnChange)) 
			{
				$method = $this->OnChange;
				if (is_array($method))
				{
					call_user_func($method, $sender, $node);
				}
				else if ($method != '' && method_exists($this->getParentForm(), $method))
				{
					$this->getParentForm()->$method($sender, $node);
				}
			}
		}		
		
		function generateTreeNodeHTML($node)
		{
			if ($node == null) return '';
			if (!is_object($node)) return '';
			if (!($node instanceof TTreeNode)) return '';
			
			$id = $node->id;
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$parent = $this->getParentForm()->Name;
			$onclick = 'getJSform(\''.$parent.'\').callBack(\''.$this->handle_OnChange.'\', undefined, \''.$this->id.'\', \''.$id.'\', \'select\');';
			
			$html = '<li id="'.$id.'">';
			if ($node->hasChildren)
			{
				$html.= '<input type="checkbox" id="item-0-0" />';
				$html.= '<label onclick="'.$onclick.'">'.$node->Caption.'</label>'."\n";
				$html.=	'	<ul id="'.$id.'_items" class="'.$class.'_items">'."\n";
				if (isset($node->Items))
				{
					foreach ($node->Items as $item) $html.= $this->generateTreeNodeHTML($item);
				}
				$html.=	'	</ul>'."\n";
			}
			else
			{
				$html.= '<a href="./" onclick="'.$onclick.' return false;">'.$node->Caption.'</a>'."\n";
			}
			$html.= '</li>'."\n";
			
			$node->rendered = true;
			return $html;
		}
		
		function updateTreeNodeHTML($node)
		{
			//TQuark::instance()->browserAlert('updating node: '.$node->id);
			
			if (!$this->st_rendered) return; //  no need to update, the control was not rendered yet
			if ($node == null || !is_object($node) || !($node instanceof TTreeNode)) return;
			
			//  the parent might be rendered as a link, check that
			$parent = $node->Parent;
			if ($parent == null || !is_object($parent) /*|| !($parent instanceof TTreeNode)*/) return;
			
			//TQuark::instance()->browserAlert('parent items '.count($parent->Items).' node rendered '.((bool)$node->rendered));
			
			if (count($parent->Items) == 1 && !$node->rendered) $this->updateTreeNodeHTML($parent);
			else 
			{
				$id = $node->id;
				$parentId = $node->Parent->id.'_items';
				$html = $this->generateTreeNodeHTML($node);
				
				if (!$node->rendered)
				{
					//TQuark::instance()->browserAlert('append '.$id.' into '.$parentId);
				
					TQuark::instance()->browserAppend($parentId, $html);
					$node->rendered = true;
				}
				else 
				{
					//TQuark::instance()->browserAlert('replace '.$id);
					
					TQuark::instance()->browserReplace($id, $html);
				}
			}
		}
		
		function generateHTML()
		{
			$this->handle_OnChange = TQuark::instance()->registerHandler($this, 'internalOnChange');
				
			$class = '';
			if ($this->Theme != '') $class = $this->Theme.'_'.$this->ClassName;
			
			$id = '%parent%.'.$this->Name;
			
			$style = $this->generateStyle();
			
			$html =	'<div id="'.$id.'_wrapper" style="'.$style.'" class="'.$class.'"> '."\n".
					'	<ul id="'.$id.'_items" class="'.$class.'"> '."\n";
			$html.= $this->generateTreeNodeHTML($this->Items);
/*					'		<li><input type="checkbox" id="item-0" /><label for="item-0">This Folder is Closed By Default</label> '."\n".
					'			<ul> '."\n".
					'				<li><input type="checkbox" id="item-0-0" /><label for="item-0-0">Ooops! A Nested Folder</label> '."\n".
					'				<ul> '."\n".
					'					<li><input type="checkbox" id="item-0-0-0" /><label for="item-0-0-0">Look Ma - No Hands!</label> '."\n".
					'						<ul> '."\n".
					'							<li><a href="./">First Nested Item</a></li> '."\n".
					'							<li><a href="./">Second Nested Item</a></li> '."\n".
					'							<li><a href="./">Third Nested Item</a></li> '."\n".
					'							<li><a href="./">Fourth Nested Item</a></li> '."\n".
					'						</ul> '."\n".
					'					</li> '."\n".
					'					<li><a href="./">Item 1</a></li> '."\n".
					'					<li><a href="./">Item 2</a></li> '."\n".
					'					<li><a href="./">Item 3</a></li> '."\n".
					'				</ul> '."\n".
					'				</li> '."\n".
					'				<li><input type="checkbox" id="item-0-1" /><label for="item-0-1">Yet Another One</label> '."\n".
					'					<ul> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'						<li><a href="./">item</a></li> '."\n".
					'					</ul> '."\n".
					'				</li> '."\n".
					'			</ul> '."\n".
					'		</li> '."\n";*/
			$html.= '	</ul> '."\n".
					'</div>'."\n";
			
			$this->st_rendered = true;
			return $html;
		}
	}
	
	//registerWidget('TTreeView', 'TTreeView');
}
?>