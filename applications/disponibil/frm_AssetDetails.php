<?php

	class Tfrm_AssetDetails extends TForm
	{
		var $ID_Asset = 0;
		var $Status = 'NONE';
		var $PreOpeningTime = 0;
		var $OpeningTime = 0;
		var $PreClosingTime = 0;
		var $ClosingTime = 0;
		
		function OnLoad()
		{
			
		}
		
		
		function OnAppTimer($event)
		{
			$ID_Asset = 0;
			switch ($event['Resource'])
			{
				case 'AssetSessions':
				case 'Orders':
					$ID_Asset = $event['ID_LinkedResource'];
					break;
				case 'DeltaT1':
					$ID_Asset = $event['ID_Resource'];
					break;
			}
			
			if ($ID_Asset == $this->ID_Asset) $this->refreshAsset();			
			if ($event['Resource'] == 'DeltaT1') $this->clk_DeltaT1Value->start();
		}
		
		function SynchClock($value)
		{
			$this->clk_servertime->synchronize($value);
		}
		
		function resetAsset()
		{
			$this->lbl_AssetName->Caption = '';
			$this->lbl_AssetDescription->Caption = '';
		}
		
		function refreshAsset($ID_Asset = null)
		{
			$assets = TAssets::instance($this->contextID); if (!isset($assets)) return;
			$translations = TTranslations::instance($this->contextID); if (!isset($translations)) return;
						
			if (isset($ID_Asset)) 
			{
				if ($ID_Asset != $this->ID_Asset) 
				{
					$this->clk_DeltaTValue->Started = false;
					$this->clk_DeltaT1Value->Started = false;
				}
				$this->ID_Asset = $ID_Asset;
			}
			
			if ($this->ID_Asset == 0) $this->resetAsset();
			else
			{
				//$assets->buildLatest();
				$assets->setPath('.');
				if ($assets->first())
				{
					if ($assets->locate($this->ID_Asset))
					{
						$assets->open();
						
						$this->lbl_AssetName->Caption = $translations->translate($assets->get('Name'));
						$this->lbl_AssetDescription->Caption = $translations->translate($assets->get('Description'));
						
						$assets->close();
						
						$this->refreshTradingSessionStats();						
					}
				}
			}
		}
		
		function refreshSessionBar()
		{
			$this->lbl_T0->Caption = 'T0('.$this->PreOpeningTime.')';
			$this->lbl_T1->Caption = 'T1('.$this->OpeningTime.')';
			$this->lbl_T2->Caption = 'T2('.$this->PreClosingTime.')';
			$this->lbl_T3->Caption = 'T3('.$this->ClosingTime.')';
			
			//  adjust progress bar
			switch ($this->Status)
			{
				case 'PreOpened':
					$this->shp_SessionOpening->BrushColor = 'dddddd';
					$this->shp_SessionTransactions->BrushColor = 'dddddd';
					$this->shp_SessionClosing->BrushColor = 'dddddd';
					
					$this->shp_ActiveOpening->Width = 0;
					$this->shp_ActiveTransactions->Width = 0;
					$this->shp_ActiveClosing->Width = 0;
					break;
				case 'Opened':
					$this->shp_SessionOpening->BrushColor = 'dddddd';
					$this->shp_SessionTransactions->BrushColor = 'dddddd';
					$this->shp_SessionClosing->BrushColor = 'dddddd';
			
					$len = (strtotime($this->PreClosingTime) - strtotime($this->OpeningTime));
					if ($len > 0) $len = (time() - strtotime($this->OpeningTime)) / $len * 184;
					else $len = 184;
			
					$this->shp_ActiveOpening->Width = 184;
					$this->shp_ActiveTransactions->Width = $len;
					$this->shp_ActiveClosing->Width = 0;
					break;
				case 'PreClosed':
					$this->shp_SessionOpening->BrushColor = 'dddddd';
					$this->shp_SessionTransactions->BrushColor = 'dddddd';
					$this->shp_SessionClosing->BrushColor = 'dddddd';
					
					$this->shp_ActiveOpening->Width = 184;
					$this->shp_ActiveTransactions->Width = 184;
					$this->shp_ActiveClosing->Width = 0;
					break;
				case 'Closed':
					$this->shp_SessionOpening->BrushColor = 'dddddd';
					$this->shp_SessionTransactions->BrushColor = 'dddddd';
					$this->shp_SessionClosing->BrushColor = 'dddddd';
					
					$this->shp_ActiveOpening->Width = 184;
					$this->shp_ActiveTransactions->Width = 184;
					$this->shp_ActiveClosing->Width = 184;
					break;
				default:
					$this->shp_SessionOpening->BrushColor = 'a0a0a0';
					$this->shp_SessionTransactions->BrushColor = 'a0a0a0';
					$this->shp_SessionClosing->BrushColor = 'a0a0a0';
					
					$this->shp_ActiveOpening->Width = 0;
					$this->shp_ActiveTransactions->Width = 0;
					$this->shp_ActiveClosing->Width = 0;
					break;
			}				
		}
		
		function refreshTradingSessionStats()
		{
			$webservice = TBRMWebService::instance($this->contextID);
			if (isset($webservice))
			{
				$ID_Broker = $webservice->user['ID_Broker'];
				
				$ds = $webservice->Reader->select('RingSessions', 'getTradingSessionStats', array('Arguments' => array('ID_Asset' => $this->ID_Asset, 'ID_Broker' => $ID_Broker)));
				
				if (isset($ds) && ($ds instanceof TDataSet))
				{
					//TQuark::instance()->browserAlert(var_export($ds, true));
					if ($ds->RowsCount > 0)
					{
						$row = $ds->Rows[0];
						$this->Status = $row['Status'];
						$this->PreOpeningTime = $row['PreOpeningTime'];
						$this->OpeningTime = $row['OpeningTime'];
						$this->PreClosingTime = $row['PreClosingTime'];
						$this->ClosingTime = $row['ClosingTime'];
						
						$this->refreshSessionBar();						
						
						$deltaT = $row['DeltaTRemaining'];
						$deltaTStarted = $row['DeltaTStarted'];
						$this->clk_DeltaTValue->Duration = $deltaT * 1000;
						if ($deltaTStarted == 'true') $this->clk_DeltaTValue->start();
						else $this->clk_DeltaTValue->Started = false;
						//$this->lbl_DeltaTValue->Caption = date('i:s', $deltaT);//intdiv($deltaT, 60).':'.()
					
						$deltaT1 = $row['DeltaT1Remaining'];
						$deltaT1Started = $row['DeltaT1Started'];
						$this->clk_DeltaT1Value->Duration = $deltaT1 * 1000;
						if ($deltaT1Started == 'true') $this->clk_DeltaT1Value->start();
						else $this->clk_DeltaT1Value->Started = false;
						//$this->lbl_DeltaT1Value->Caption = date('i:s', $deltaT1);//intdiv($deltaT, 60).':'.()
						
						$minQuantity = $row['MinQuantity'];
						if ($minQuantity > 0)
						{
							$this->lbl_MinQuantityLabel->Visible = true;
							$this->lbl_MinQuantityValue->Caption = $minQuantity;
						}
						else 
						{
							$this->lbl_MinQuantityLabel->Visible = false;
							$this->lbl_MinQuantityValue->Visible = false;
						}
						
						
						//  statistics
						$this->lbl_TransactionsCountValue->Caption = $row['TransactionsCount'];
						$this->lbl_QuotationValue->Caption = $row['Quotation'];
					}
				}
			}
		}
		
	}
	
?>