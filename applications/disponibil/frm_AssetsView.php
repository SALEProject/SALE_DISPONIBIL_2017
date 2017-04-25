<?php

	require_once 'applications/disponibil_data/assets.php';

	class Tfrm_AssetsView extends TForm
	{
		function OnLoad()
		{
			TQuark::instance()->registerTimer($this, 'OnTimer', 1000);
			$this->RefreshAssets();
		}

		function RefreshAssets()
		{
			$webservice = TBRMWebService::instance($this->contextID); if (!isset($webservice)) return;
			
			$ds = $webservice->Reader->select('Rings', 'getAssets', array('Arguments' => array('ID_Market' => 3)));
			if ($ds == null) return;
			foreach ($ds->Rows as $row)
			{
				$ID_Asset = $row['ID'];
				$ID_Ring = $row['ID_Ring'];
				$Code = $row['Code'];
				$Name = $row['Name'];
				
				$this->RefreshAsset($ID_Ring, $ID_Asset, $Code, $Name);
			}
		}
				
		function OnTimer()
		{
			$context = $this->getContext();
			if (!isset($context)) return;
			if (!isset($context->application)) return;
			
			$context->application->OnTimer();
		}
		
		function OnAppTimer($event)
		{
			$assets = TAssets::instance($this->contextID); if (!isset($assets)) return;
				
			$ID_Asset = 0;
			switch ($event['Resource'])
			{
				case 'Assets':
					$ID_Asset = $event['ID_Resource'];
					break;
				case 'AssetSessions':
					$ID_Asset = $event['ID_LinkedResource'];
					break;
			}
			if ($ID_Asset == 0) return;
			
			//$assets->checkRevisionNumber();
			//$assets->buildLatest();
			$assets->setPath('.');
			if ($assets->first())
			{
				if ($assets->locate($ID_Asset))
				{
					$assets->open();
					$ID_Ring = $assets->get('ID_Ring');
					$Code = $assets->get('Code');
					$Name = $assets->get('Name');

					$this->RefreshAsset($ID_Ring, $ID_Asset, $Code, $Name);
										
					$assets->close();
				}
			}				
		}
		
		function RefreshAsset($ID_Ring, $ID_Asset, $Code, $Name)
		{
			$translations = TTranslations::instance($this->contextID); if (!isset($translations)) return;
			
			//  look for ring in tree view and create it if necessary
			$node_ring = null;
			foreach ($this->tv_Assets->Items->Items as $node)
			{
				if ($node->Data == $ID_Ring)
				{
					$node_ring = $node;
					break;
				}
			}
				
			if ($node_ring == null)
			{
				//  obtain info about the ring
				$webservice = TBRMWebService::instance($this->contextID);
				if (isset($webservice))
				{
					$ds_ring = $webservice->Reader->select('Rings', 'getRings', array('Arguments' => array('ID_Market' => 3, 'ID_Ring' => $ID_Ring)));
						
					if (isset($ds_ring) && $ds_ring instanceof TDataSet)
					{
						$row = $ds_ring->Rows[0];
						$ring_name = $translations->translate($row['Name']);
						$node_ring = $this->tv_Assets->Items->addChild($ring_name);
						$node_ring->Data = $ID_Ring;
					}
				}
			}
				
			if ($node_ring == null) return;
			//  it means the Ring could not be found and was not returned by the webservice
				
			//  look for the asset in the ring and create it if necessary
			$node_asset = null;
			foreach ($node_ring->Items as $node)
			{
				if ($node->Data = $ID_Asset)
				{
					$node_asset = $node;
					break;
				}
			}
				
			if ($node_asset == null)
			{
				$asset_name = $translations->translate($Name);
				$node_asset = $node_ring->addChild('['.$Code.'] '.$asset_name);
				$node_asset->Data = $ID_Asset;
			}
		}
		
		function tv_AssetsOnChange($sender, $selectedNode)
		{
			if ($selectedNode == null) return;
			TQuark::instance()->browserAlert($selectedNode->Data);		
			$ID_Asset = $selectedNode->Data;
			
			$frm = TQuark::instance()->getForm('frm_AssetDetails');
			if ($frm != null) $frm->refreshAsset($ID_Asset);
			
			$frm = TQuark::instance()->getForm('frm_AssetOrders');
			if ($frm != null) $frm->refreshOrders($ID_Asset, true);
		}
		
	}
?>