<?php

	class TControlPanelApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('Tfrm_ControlPanel');
			if ($frm != null) $frm->show();
		}
	}

	class Tfrm_ControlPanel extends TForm
	{
		static $Definition = '
				<Tfrm_ControlPanel>
					<Name>frm_ControlPanel</Name>
					<theme>bitnova</theme>
					
					<Caption>Control Panel</Caption>
					<Left>64</Left>
					<Top>64</Top>
					<Width>304</Width>
					<Height>320</Height>
					<BorderStyle>bsDialog</BorderStyle>
					<Position>poDesigned</Position>	
					
					<!-- <TPopupMenu>
						<Name>pm</Name>
						<TMenuItem>
							<Name>pmItem1</Name>
							<Caption>Item 1</Caption>
							<OnClick>pmItem1OnClick</OnClick>
						</TMenuItem>
						<TMenuItem>
							<Name>pmItem2</Name>
							<Caption>Item 2</Caption>
							<OnClick>pmItem2OnClick</OnClick>
						</TMenuItem>
						<TMenuItem>
							<Name>pmItem3</Name>
							<Caption>Item 3</Caption>
							<OnClick>pmItem3OnClick</OnClick>
						</TMenuItem>
					</TPopupMenu>
					
					<TButton>
						<Name>btn_pm</Name>
						<Caption>Popup</Caption>
						<Left>64</Left>
						<Top>64</Top>
						<Width>120</Width>
						<Height>24</Height>
						<PopupMenu>pm</PopupMenu>
					</TButton>-->
					
					<TButton>
						<Name>btn_RegistryEditor</Name>
						<Caption>Registry Editor</Caption>
						<Left>32</Left>
						<Top>32</Top>
						<OnClick>btn_RegistryEditorOnClick</OnClick>
					</TButton>
				
					<TButton>
						<Name>btn_ViewStatistics</Name>
						<Caption>View Statistics</Caption>
						<Left>32</Left>
						<Top>64</Top>
						<OnClick>btn_ViewStatisticsOnClick</OnClick>
					</TButton>
				
				</Tfrm_ControlPanel>
				';
		
		function OnLoad()
		{
		}
	
		function pmItem1OnClick()
		{
			TQuark::instance()->addAjaxStack('', 'alert', 'item 1');
		}
	
		function pmItem2OnClick()
		{
			TQuark::instance()->addAjaxStack('', 'alert', 'item 2');
		}
	
		function pmItem3OnClick()
		{
			TQuark::instance()->addAjaxStack('', 'alert', 'item 3');
		}
	
		function btn_RegistryEditorOnClick()
		{
			TQuarkOS::instance()->launchApplication('regedit');
		}
	
		function btn_ViewStatisticsOnClick()
		{
			TQuarkOS::instance()->launchApplication('viewstats');
		}
	}
	


?>