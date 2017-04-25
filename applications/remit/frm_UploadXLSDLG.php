<?php 


	class Tfrm_UploadXLSDLG extends TForm
	{
		
		function SaveData()
		{
			//  find out ID_DataSource
			$context = $this->getContext();
			if ($context == null) return;
			if (!is_object($context)) return;
			$db = $context->application->Reader;
			$ds_DataSource = $db->select('REMIT', 'getDataSources', array('Arguments' => array('DataSourceType' => 'XLS')));
			
			$ID_DataSource = 0;
			if ($ds_DataSource instanceof TDataSet)
			{
				if ($ds_DataSource->RowsCount > 0)
					$ID_DataSource = $ds_DataSource->Rows[0]["ID"];
			}

			//  encode file contents			
			$file = TQuark::instance()->retrieveUploadedFile();
			$FileName = $file['name'];
			$tmp_name = $file['tmp_name'];
			
			$handle = fopen($tmp_name, "rb");
			$fsize = filesize($tmp_name);
			$binary = fread($handle, $fsize);
			$FileContent = base64_encode($binary);
				
			//  send the file & refresh
			$db = $context->application->Writer;
			$objects = array('Arguments' => array('ID_DataSource' => $ID_DataSource, 'FileName' => $FileName, 'FileContent' => $FileContent));
			$result = $db->execute('REMIT', 'uploadFile', $objects);
			
			if (!$result)
			{
				$s =	'HTTP Status: '.$db->LastHTTPStatus."\n".
						'Error Code: '.$db->LastErrorCode."\n".
						'Error Message: '.$db->LastErrorMsg;
				TQuark::instance()->browserAlert($s);
			}
		
			TQuark::instance()->getForm('frm_XLSHistory')->refreshData();
		}
		
		function btn_SaveOnClick()
		{
			$this->SaveData();
			$this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>
