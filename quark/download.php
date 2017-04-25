<?php
	//-------------------------------------------------------------------------------------------------------	
	//  determine working directory
	$qPath = __DIR__;
	$s = dirname($_SERVER['SCRIPT_FILENAME']);
	if ($s != '\\' || $s != '/') $s .= DIRECTORY_SEPARATOR;
	if (strpos($qPath, $s) >= 0) $qPath = substr($qPath, strlen($s));
	define('qPath', $qPath);

	$filename = '';
	if (isset($_REQUEST['filename'])) $filename = $_REQUEST['filename'];
	
	$filepath = $filename;
	if ($filepath != '') $filepath = '../cache/'.$filename;
	
	if (file_exists($filepath))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($filepath));
		
		readfile($filepath);
	}

?>