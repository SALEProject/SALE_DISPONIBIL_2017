<?php

	require_once 'applications/disponibil_data/messages.php';

	class Tfrm_ChatView extends TForm
	{
		var $last_id = 0;
				
		function OnLoad()
		{
		}
		
		function OnAppTimer($event)
		{
			$messages = TMessages::instance($this->contextID); if (!isset($messages)) return;
			
			$scroll = false;
			
			$messages->setPath('.');
			if ($messages->first())
			{
				if ($messages->locate($event['ID_Resource']))
				{
					$id = $messages->getName();
					if ($id > $this->last_id)
					{
						$messages->open();
						$msg = $messages->get('Message');
						$this->ed_Chat->Text .= $msg."\n";
						$messages->close();
						$this->last_id = $id;
						$scroll = true;
					}
				}
			}
			
			if ($scroll) 
			{
				$id = $this->ed_Chat->id;
				$s = 'if ($("'.$id.'") != null) $("'.$id.'").scrollTop = $("'.$id.'").scrollHeight;';
				TQuark::instance()->browserScript($s);
			}
		}
		
		function btn_SendMessageOnClick()
		{
			$message = $this->ed_Message->Text;
			if (trim($message) == '') return;
			
			$webservice = TBRMWebService::instance($this->contextID); 
			if (!isset($webservice)) return;
			
			$objects = array('Arguments' => array('Message' => (string) $message));			
			$result = $webservice->Writer->execute('Messages', 'addChatMessage', $objects);
			
			$this->ed_Message->Text = '';
			//TQuark::instance()->browserAlert($result);
		}
		
	}
?>
