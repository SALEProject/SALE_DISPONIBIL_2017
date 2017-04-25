<?php

	class TViewStatistics extends TApplication
	{
		
		function main()
		{
			$frm = $this->CreateForm('Tfrm_ViewStatistics');
			if ($frm != null) $frm->show();
		}
	}
	
	class Tfrm_ViewStatistics extends TForm
	{
		static $Definition = '
				<Tfrm_ViewStatistics>
					<Name>frm_ViewStatistics</Name>
					<Left>240</Left>
					<Top>64</Top>
					<Width>320</Width>
					<Height>360</Height>
					<Caption>System Statistics</Caption>
					<BorderStyle>bsDialog</BorderStyle>
				
					<TPageControl>
						<Name>pg_ctrl</Name>
						<Left>0</Left>
						<Top>0</Top>
						<Width>320</Width>
						<Height>360</Height>
				
						<TTabSheet>
							<Name>tab_sysinfo</Name>
							<Caption>Sys. Info</Caption>
				
							<TLabel>
								<Name>Label1</Name>
								<Left>8</Left><Top>8</Top>
								<Caption>Process ID:</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_PID</Name>
								<Left>120</Left><Top>8</Top>
								<Caption>getmypid()</Caption>
							</TLabel>

							<TLabel>
								<Name>Label2</Name>
								<Left>8</Left><Top>32</Top>
								<Caption>UID/GID:</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_UIDGID</Name>
								<Left>120</Left><Top>32</Top>
								<Caption>getmyuid()/getmygid()</Caption>
							</TLabel>

							<TLabel>
								<Name>Label3</Name>
								<Left>8</Left><Top>56</Top>
								<Caption>Username:</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_Username</Name>
								<Left>120</Left><Top>56</Top>
								<Caption>get_current_user()</Caption>
							</TLabel>

							<TLabel>
								<Name>Label4</Name>
								<Left>8</Left><Top>80</Top>
								<Caption>OS:</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_OS</Name>
								<Left>120</Left><Top>80</Top>
								<Caption>php_uname()</Caption>
							</TLabel>

							<TLabel>
								<Name>Label5</Name>
								<Left>8</Left><Top>184</Top>
								<Caption>Avg Load:</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_LoadAvg1min</Name>
								<Left>120</Left><Top>184</Top>
								<Caption>sys_getloadavg()</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_LoadAvg5min</Name>
								<Left>120</Left><Top>208</Top>
								<Caption>sys_getloadavg()</Caption>
							</TLabel>
							<TLabel>
								<Name>lbl_LoadAvg15min</Name>
								<Left>120</Left><Top>232</Top>
								<Caption>sys_getloadavg()</Caption>
							</TLabel>
						</TTabSheet>
				
						<TTabSheet>
							<Name>tab_counters</Name>
							<Caption>Perf. Counters</Caption>
				
							<TDataGrid>
								<Name>dg_counters</Name>
								<Left>0</Left><Top>0</Top>
								<Width>320</Width><Height>360</Height>
							</TDataGrid>
						</TTabSheet>
					</TPageControl>
				</Tfrm_ViewStatistics>
				';
		
		var $tick = 0;
		
		function OnLoad()
		{			
			//$this->refreshStatistics();
			TQuark::instance()->registerTimer($this, 'OnTimer', 1000);
		}
		
		function OnTimer()
		{
			if ($this->tick % 2 == 0) $this->refreshStatistics();
			$this->tick++;
			if ($this->tick >= 10) $this->tick = 0;
		}
		
		function refreshStatistics()
		{
			$this->lbl_PID->Caption = getmypid();
			$this->lbl_UIDGID->Caption = getmyuid().'/'.getmygid();
			$this->lbl_Username->Caption = get_current_user();
			$this->lbl_OS->Caption = php_uname();
			$load = sys_getloadavg();
			$this->lbl_LoadAvg1min->Caption = ($load[0] * 100).'% prev 1 min';
			$this->lbl_LoadAvg5min->Caption = ($load[1] * 100).'% prev 5 min';
			$this->lbl_LoadAvg15min->Caption = ($load[2] * 100).'% prev 15 min';
			
			$ds = new TDataSet(null);
			$ds->FieldDefs = array('Index', 'N', 'Last', 'Avg', 'Min', 'Max');
			$stats = TStats::instance()->getStatsArray();
			foreach ($stats as $index => $stat)
			{
				$row = array();
				$row['Index'] = $index;
				$row['N'] = $stat->count;
				$row['Last'] = round($stat->last, 2);
				$row['Avg'] = round($stat->avg, 2);
				$row['Min'] = round($stat->min, 2);
				$row['Max'] = round($stat->max, 2);
				$ds->addRow($row);
			}
			
			$this->dg_counters->Dataset = $ds;
		}
	}

?>