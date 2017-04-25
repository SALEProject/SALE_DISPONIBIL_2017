<?php

	class TRegistryEditorApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('Tfrm_RegistryEditorView');
			if ($frm != null) $frm->show();
		}
	}
	
	class Tfrm_RegistryEditorView extends TForm
	{
		static $Definition = '
				<Tfrm_RegistryEditorView>
					<Name>frm_RegistryEditorView</Name>
					<Left>120</Left>
					<Top>128</Top>
					<Width>640</Width>
					<Height>480</Height>
					<Caption>Registry Editor</Caption>
					<BorderStyle>bsSizeable</BorderStyle>
				
					<TSplitContainer>
						<Name>pnl_SplitContainer</Name>
						<Left>0</Left>
						<Top>0</Top>
						<Width>640</Width>
						<Height>480</Height>
						<Align>alClient</Align>
				
						<TPanel>
							<Name>pnl_Left</Name>
							<Align>alLeft</Align>
							<Width>160</Width>
							
							<TTreeView>
								<Name>tv_Keys</Name>
								<Left>0</Left>
								<Top>0</Top>
								<Width>160</Width>
								<Height>480</Height>
								<Align>alLeft</Align>
								
								<OnChange>tv_KeysOnChange</OnChange>
							</TTreeView>
						</TPanel>
						
						<TSplitter>
							<Name>split</Name>
							
							<Left>168</Left>
							
							<MinusPanel>pnl_Left</MinusPanel>
							<PlusPanel>pnl_Right</PlusPanel>
						</TSplitter>
						
						<TPanel>
							<Name>pnl_Right</Name>
							
							<Align>alRight</Align>
							<Width>460</Width>
					
							<TCustomGrid>
								<Name>dg_Values</Name>
								<Width>320</Width>
								<Height>320</Height>
								<ColsCount>2</ColsCount>
								<RowsCount>10</RowsCount>
								<FixedCols>1</FixedCols>
								<FixedRows>1</FixedRows>
								<DefaultColWidth>120</DefaultColWidth>
								<Align>alClient</Align>
								<Options>[goEditing, goTabs]</Options>
							</TCustomGrid>
						</TPanel>
						
					</TSplitContainer>
				</Tfrm_RegistryEditorView>
				';
		
		function OnLoad()
		{
			$this->dg_Values->setCell(0, 0, 'Property');
			$this->dg_Values->setCell(1, 0, 'Value');
			$this->refreshKeys();
		}
		
		function refreshSubKeys($parentNode)
		{
			$reg = TRegistry::instance();
			if ($reg->openCurrent())
			{
				if ($reg->first())
				{
					do 
					{
						if ($reg->hasChildren())
						{
							$name = $reg->name();
							$node = $parentNode->addChild($name);
							$node->hasChildren = true;
							$node->Data = $reg->keyPath().'.'.$name;
						
							$this->refreshSubKeys($node);
						}
					} while ($reg->next());
				}
				
				$reg->closeCurrent();
			}
		}
		
		function refreshKeys()
		{				
			$reg = TRegistry::instance();
			$reg->openKey('.');
			if ($reg->first())			
			{				
				do 
				{
					if ($reg->hasChildren())
					{
						$name = $reg->name();
						$node = $this->tv_Keys->Items->addChild($name);
						$node->hasChildren = true;
						$node->Data = $reg->keyPath().'.'.$name;
						
						$this->refreshSubKeys($node);
					}
				} while ($reg->next());
				
			}
		}
		
		function tv_KeysOnChange($sender, $selectedNode)
		{
			//TQuark::instance()->browserAlert($selectedNode->Data);	
			$key = $selectedNode->Data;
			if (!empty($key))
			{
				$reg = TRegistry::instance();
				$reg->openKey($key);
				if ($reg->first())
				{
					$i = 0;
					do 
					{
						$name = $reg->name();
						$value = $reg->read();
						$i++;
						$this->dg_Values->setCell(0, $i, $name);
						$this->dg_Values->setCell(1, $i, $value);
					} while ($reg->next());
				}
			}
		}
		
		function dg_ValuesOnValidate($sender, $ACol, $ARow, $value)
		{
			TQuark::instance()->browserAlert('on validate');
		}
	}

?>