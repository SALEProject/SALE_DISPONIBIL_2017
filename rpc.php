<?php 

	require_once 'applications/disponibil_data/jsonError.php';
	
	class jsonResult
	{
		var $Success = false;
		var $ErrorCode = 0;
		var $ResultType = '';
		var $Result = '';
		
		function __construct($ErrorCode)
		{
			if ($ErrorCode == jsonError::Success) $this->Success = true;
			else $this->Success = false;
			$this->ErrorCode = $ErrorCode;
			$this->ResultType = 'String';
			$this->Result = jsonError::getErrorString($ErrorCode);
		}
		
		static function setCode($ErrorCode)
		{
			return new self($ErrorCode);
		}

		static function setMessage($ErrorCode, $Message)
		{
			$result = new self($ErrorCode);
			$result->ResultType = 'String';
			$result->Result = $Message;
			return $result;			
		}

		static function setValue($Value)
		{
			$result = new self(jsonError::Success);
			if (is_bool($Value)) $result->ResultType = 'Bool';
			if (is_int($Value)) $result->ResultType = 'Int32';
			if (is_float($Value)) $result->ResultType = 'Float';
			if (is_string($Value)) $result->ResultType = 'String';
			$result->Result = $Value;
			return $result;
		}
	
		function getString()
		{
			return json_encode($this);
		}
	}	
	
	//  here we retrieve our request and attempt to decode it
	$post_body = file_get_contents('php://input');	
	//file_put_contents('cache/rpc_input', $post_body);
	$post_obj = json_decode($post_body, true);
	//file_put_contents('cache/rpc_input', var_export($post_obj, true));
	
	$return = false;
	if ($post_obj == null) { echo jsonResult::setCode(jsonError::WrongRequestFormat)->getString(); $return = true; } 	
	if (!is_array($post_obj)) { echo jsonResult::setCode(jsonError::WrongRequestFormat)->getString(); $return = true; }
	if (count($post_obj) < 1) { echo jsonResult::setCode(jsonError::WrongRequestFormat)->getString(); $return = true; }
	if (!isset($post_obj['Event'])) { echo jsonResult::setCode(jsonError::WrongRequestFormat)->getString(); $return = true; }
	
	if (!$return)
	{
		//  log event for in application debug
		require_once 'applications/disponibil_data/syslog.php';
		TSysLog::instance(0)->update('insert', $post_obj['Event']);
		
		$resource = $post_obj['Event']['Resource'];
		$operation = $post_obj['Event']['EventType'];
		$object = $post_obj['Object'];
	
		switch ($resource)
		{
			case 'Alerts':
				require_once 'applications/disponibil_data/alerts.php';
				TAlerts::instance(0)->update($operation, $object);
				break;
			case 'Messages':
				require_once 'applications/disponibil_data/messages.php';
				TMessages::instance(0)->update($operation, $object);
				break;
			case 'Assets':
				require_once 'applications/disponibil_data/assets.php';
				TAssets::instance(0)->update($operation, $object);
				break;
			case 'AssetSessions':
				require_once 'applications/disponibil_data/assets.php';
				TAssets::instance(0)->updateSession($operation, $object);
				break;
			case 'Orders':
				require_once 'applications/disponibil_data/assets.php';
				TAssets::instance(0)->updateOrder($operation, $object);
				break;
			case 'Transactions':
				require_once 'applications/disponibil_data/transactions.php';
				TTransactions::instance(0)->update($operation, $object);
				break;
			case 'Translations':
				require_once 'applications/disponibil_data/translations.php';
				TTranslations::instance(0)->update($operation, $object);
				break;
		}
		
		echo jsonResult::setCode(jsonError::Success)->getString();
	}
?>


