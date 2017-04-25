<?php
	
	class TIMAPClient extends TPropertyClass
	{
		var $Host = '';
		var $Port = 143;
		var $Protocol = 'imap';
		var $User = '';
		var $Password = '';
		
		private $mail = null;
		
		protected function getConnectionString()
		{
			$srv = '{'.$this->Host.':'.$this->Port.'/'.$this->Protocol.'/novalidate-cert}';
			if ($folder != null) $srv .= $folder;
				
			return $srv;
		}
		
		protected function open($folder = null)
		{
			$srv = $this->getConnectionString();
			if ($folder != null) $srv .= $folder;
			if ($mail = imap_open($srv, $this->User, $this->Password)) 
			{
				$this->mail = $mail;
				return true;
			}
			else 
			{
				$errors = imap_errors();
				$this->mail = null;
				return false;
			}
		}
		
		protected function close()
		{
			if ($this->mail != null) 
			{
				imap_close($this->mail);
				$this->mail = null;
			}
		}
		
		
		function getFolders()
		{
			$srv = $this->getConnectionString();
			if (!$this->open()) return null;

			$ds = null;
			try 
			{
				$folders = imap_list($this->mail, $srv, '*');
				
				//remove any } characters from the folder
				if (preg_match("/}/i", $folders[0])) $arr = explode('}', $folders[0]);
			
				//also remove the ] if it exists, normally Gmail have them
				if (preg_match("/]/i", $folders[0])) $arr = explode(']/', $folders[0]);
			
				//remove INBOX. from the folder name
				$folder = str_replace('INBOX.', '', stripslashes($arr[1]));
			
				//check if inbox is first folder if not reorder array
				if($folder !== 'INBOX') krsort($folders);
			
				//make sure the list is an array
				if (is_array($folders))
				{
					//prepare the dataset
					$ds = new TDataSet();
					$ds->FieldDefs[] = 'Folder';
					$ds->FieldDefs[] = 'Unread';
					
					//loop through rach array index
					foreach ($folders as $val)
					{
						//remove any } charactors from the folder
						if (preg_match("/}/i", $val)) $arr = explode('}', $val);
			
						//also remove the ] if it exists, normally Gmail have them
						if (preg_match("/]/i", $val)) $arr = explode(']/', $val);
			
						//remove any slashes
						$folder = trim(stripslashes($arr[1]));
			
						//remove inbox. from the folderName its not needed for displaying purposes
						$folderName = str_replace('INBOX.', '', $folder);
			
						$row = Array();
						$row['Folder'] = $folderName;
						$row['Unread'] = 0;
						$ds->addRow($row);
						//echo "<p><a href=\"?folder=".imap_utf7_decode($folder)."\">".ucwords(strtolower(imap_utf7_decode($folderName)))."</a></p>\n";
					}					
				}
			}
			catch (Exception $exc)
			{
				$this->close();
				return null;
			}
			
			$this->close();
			return $ds;
		}
		
		function getHeaders($folder)
		{
			if (!$this->open($folder)) return null;
			
			$numMessages = imap_num_msg($this->mail);
			
			$ds = new TDataSet();
			$ds->FieldDefs[] = 'uid';
			$ds->FieldDefs[] = 'fromAddr';
			$ds->FieldDefs[] = 'fromName';
			$ds->FieldDefs[] = 'replyAddr';
			$ds->FieldDefs[] = 'replyName';
			$ds->FieldDefs[] = 'subject';
			$ds->FieldDefs[] = 'udate';
			
			for ($i = $numMessages; $i > ($numMessages - 20); $i--) 
			{
				$header = imap_header($this->mail, $i);
			
				$fromInfo = $header->from[0];
				$replyInfo = $header->reply_to[0];
			
				$details = array
				(
					"uid" => null,
					"fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host)) ? $fromInfo->mailbox . "@" . $fromInfo->host : "",
					"fromName" => (isset($fromInfo->personal)) ? $fromInfo->personal : "",
					"replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host)) ? $replyInfo->mailbox . "@" . $replyInfo->host : "",
					"replyName" => (isset($replyTo->personal)) ? $replyto->personal : "",
					"subject" => (isset($header->subject)) ? $header->subject : "",
					"udate" => (isset($header->udate)) ? $header->udate : ""
				);
			
				$uid = imap_uid($imap, $i);
				$details['uid'] = $uid;
				
				$ds->addRow($details);
			
/*				echo "<ul>";
				echo "<li><strong>From:</strong>" . $details["fromName"];
				echo " " . $details["fromAddr"] . "</li>";
				echo "<li><strong>Subject:</strong> " . $details["subject"] . "</li>";
				echo '<li><a href="mail.php?folder=' . $folder . '&uid=' . $uid . '&func=read">Read</a>';
				echo " | ";
				echo '<a href="mail.php?folder=' . $folder . '&uid=' . $uid . '&func=delete">Delete</a></li>';
				echo "</ul>";*/
			}			
			
			$this->close();
			
			return $ds;
		}
	}

	/*
	 // connect to gmail
	$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = 'davidwalshblog@gmail.com';
	$password = 'davidwalsh';
	
		// try to connect
	$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
	
		// grab emails
	$emails = imap_search($inbox,'ALL');
	
		// if emails are returned, cycle through each...
	if($emails) {
	
			// begin output var
	$output = '';
	
			// put the newest emails on top
	rsort($emails);
	
			// for every email...
	foreach($emails as $email_number) {
	
				// get information specific to this email
	$overview = imap_fetch_overview($inbox,$email_number,0);
	$message = imap_fetchbody($inbox,$email_number,2);
	
				// output the email header information
	$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
	$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
	$output.= '<span class="from">'.$overview[0]->from.'</span>';
	$output.= '<span class="date">on '.$overview[0]->date.'</span>';
	$output.= '</div>';
	
				// output the email body
	$output.= '<div class="body">'.$message.'</div>';
	}
	
	echo $output;
	}
	
		// close the connection
	imap_close($inbox);
	*/	
?>