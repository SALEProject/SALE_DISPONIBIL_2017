<?php

	class Tfrm_TransactionsView extends TForm
	{
		function OnLoad()
		{
			$this->dg_Transactions->Columns = array(
				array('Caption' => 'Ora', 'DataType' => 'time', 'DataField' => 'Date'),
				array('Caption' => 'Tip', 'DataType' => 'string', 'DataField' => 'Direction'),
				array('Caption' => 'Activ', 'DataType' => 'string', 'DataField' => 'Asset'),
				array('Caption' => 'Cant.', 'DataType' => 'float', 'DataField' => 'Quantity'),
				array('Caption' => 'Pret', 'DataType' => 'float', 'DataField' => 'Price')
			); 

			$this->refreshTransactions(true);
		}

		function OnAppTimer($event)
		{
			$this->refreshTransactions();
		}
		
		function refreshTransactions($updateCache = false)
		{
			$transactions = TTransactions::instance($this->contextID); if (!isset($transactions)) return;
			
			$ds = null;
			$transactions->getTransactions(0, $ds, $updateCache);
			$this->dg_Transactions->DataSet = $ds;
		}
	}

?>
