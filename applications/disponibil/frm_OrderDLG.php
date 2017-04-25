<?php

	require_once 'applications/disponibil_data/assets.php';

	class Tfrm_OrderDLG extends TForm
	{
		var $ID_Asset = 0;
		var $ID_Order = 0;
		var $ID_Ring = 0;
		var $Direction = false;		
		
		function OnLoad()
		{
			
		}
		
		function RefreshControls()
		{
			$doClose = false;
			$assets = TAssets::instance($this->contextID); if (!isset($assets)) return;
		
			$assets->setPath('.');
			if (!$assets->first()) $this->close();
			if (!$assets->locate($this->ID_Asset)) $this->close();

			$assets->open();
			$this->ID_Ring = $assets->get('ID_Ring');
			$this->Caption = sprintf('%s ordin de %s pe activul %s', ($this->ID_Order == 0 ? 'Adaugare' : 'Editare'), ($this->Direction ? 'vanzare' : 'cumparare'), $assets->get('Code'));
			
			$this->dt_ExpirationDate->Text = date('Y-m-d');
			$this->ed_ExpirationTime->Text = '23:59';
			
			if ($this->ID_Order != 0)
			{
				$assets->setPath('orders');
				/*if ($assets->locate('orders'))
				{
					$assets->open();*/
					if ($assets->locate($this->ID_Order)) 
					{
						$assets->open();
						$this->ed_Quantity->Text = $assets->get('Quantity');
						$this->ed_Price->Text = $assets->get('Price');
						$ExpirationDate = $assets->get('ExpirationDate');
						$assets->close();
					}
					else $doClose = true;
					/*$assets->close();
				}*/
			}
			$assets->close();	
			
			if ($doClose) $this->close();
		}
		
		function buy($ID_Asset, $ID_Order = 0)
		{
			if ($ID_Asset == 0) return;
			$this->ID_Asset = $ID_Asset;
			$this->ID_Order = $ID_Order;
			$this->Direction = false;
			
			$this->RefreshControls();			
			$this->showModal();
		}
		
		function sell($ID_Asset, $ID_Order = 0)
		{
			if ($ID_Asset == 0) return;
			$this->ID_Asset = $ID_Asset;
			$this->ID_Order = $ID_Order;
			$this->Direction = true;

			$this->RefreshControls();			
			$this->showModal();
		}
		
		function btn_LaunchOnClick($sender)
		{
			$webservice = TBRMWebService::instance($this->contextID);
			if (!isset($webservice))
			{
				TQuark::instance()->browserReset();
				return;
			}
				
			$Quantity = (float) $this->ed_Quantity->Text;
			$Price = (float) $this->ed_Price->Text;
			$PartialFlag = false;
			if ($this->rb_Partial->Checked == true) $PartialFlag = true;
			//TQuark::instance()->browserAlert('PartialFlag '.$PartialFlag);
			$ExpirationDate = $this->dt_ExpirationDate->Text .'T'. $this->ed_ExpirationTime->Text .':00';
			
			//  compose objects
			$objects = array('Arguments' => array(
					'ID_Ring' => (int) $this->ID_Ring,
					'ID_Asset' => (int) $this->ID_Asset,
					'Direction' => ($this->Direction ? 'S' : 'B'),
					'Quantity' => $Quantity,
					'Price' => $Price,
					'PartialFlag' => $PartialFlag,
					'ExpirationDate' => $ExpirationDate,
					'isInitial' => false
			));
			
			//  validate order first
			if ($webservice->Writer->execute('Orders', 'validateOrder', $objects) === false)
			{
				$this->lbl_Error->Caption = $webservice->Writer->LastErrorMessage;
				
				switch ($webservice->Writer->LastErrorCode)
				{
					case jsonError::WrongRequestFormat:
					case jsonError::WrongMethodCall:
					case jsonError::InternalError:
					case jsonError::DatabaseError:
					case jsonError::ProcedureNotFound:
					case jsonError::ProcedureArgumentMissing:
						TQuark::instance()->browserAlert('Application Error'."\n".$webservice->Writer->LastErrorMessage);
						break;
					case jsonError::SecurityAuditFailed:
						TQuark::instance()->browserAlert('System Security Override'."\n".$webservice->Writer->LastErrorMessage);
						break;
				}
			
				return;
			}
			
			//  save in the buffer and trigger a refresh on asset's orders
			$assets = TAssets::instance($this->contextID);
			if (isset($assets))
			{
				$object = array();
				$object['ID'] = 0;
				$object['ID_Asset'] = $this->ID_Asset;
				$object['Date'] = date('c');
				$object['ID_Agency'] = $webservice->user['ID_Agency'];
				$object['ID_Broker'] = $webservice->user['ID_Broker'];
				$object['ID_Client'] = $webservice->user['ID_Client'];
				$object['Direction'] = ($this->Direction ? 'S' : 'B');
				$object['Quantity'] = $Quantity;
				$object['Price'] 	= $Price;
				$object['PartialFlag'] = $PartialFlag;
				$object['ExpirationDate'] = $ExpirationDate;
				$object['isTransacted'] = false;
				$object['isSuspended'] = false;
				$object['isActive'] = true;
				$object['isCanceled'] = false;
				$object['isApproved'] = false;
			
				$assets->updateOrder('insert', $object);
			
				$frm = TQuark::instance()->getForm('frm_AssetOrders');
				if (isset($frm)) $frm->refreshOrders($this->ID_Asset);
			}
			
			//  do the actual save
			$this->lbl_Error->Caption = '';
			$result = false;
			
			switch ($this->ID_Order)
			{
				case 0:
					$result = $webservice->Writer->execute('Orders', 'addOrder', $objects);
					break;
				default:
					$objects['Arguments']['ID_Order'] = (int) $this->ID_Order;
					$result = $webservice->Writer->execute('Orders', 'modifyOrder', $objects);
					break;
			}
			
			if ($result === false)
			{
				$this->lbl_Error->Caption = $webservice->Writer->LastErrorMessage;
				
				switch ($webservice->Writer->LastErrorCode)
				{
					case jsonError::WrongRequestFormat:
					case jsonError::WrongMethodCall:
					case jsonError::InternalError:
					case jsonError::DatabaseError:
					case jsonError::ProcedureNotFound:
					case jsonError::ProcedureArgumentMissing:
						TQuark::instance()->browserAlert('Application Error'."\n".$webservice->Writer->LastErrorMessage);
						break;
					case jsonError::SecurityAuditFailed:
						TQuark::instance()->browserAlert('System Security Override'."\n".$webservice->Writer->LastErrorMessage);
						break;
				}
			
				return;
			}
			
			$this->close();
		}
		
		function btn_CancelOnClick($sender)
		{
			$this->close();
		}
	}

?>