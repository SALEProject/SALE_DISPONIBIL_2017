<?php

	class TBRMDataClient extends TComponent
	{
		var $WebServiceURL = '';
		var $Parameters = array();
		var $LastHTTPStatus = 0;
		
		function __construct($AParent, $WebServiceURL)
		{
			parent::__construct($AParent);
			
			$this->WebServiceURL = $WebServiceURL;
		}
		
		function setParameter($name, $value)
		{
			$this->Parameters[$name] = $value;
		}
		
		function getParameter($name)
		{
			if (isset($this->Parameters[$name])) return $this->Parameters[$name];
			else return null;
		}
		
		function initialize()
		{
			
		}
		
		function makeRequest($method, $collection, $procedure, $objects)
		{
			$data = array();
			foreach ($this->Parameters as $key => $value) $data[$key] = $value;
			$data['objects'] = [$objects];
			
			$json_content = json_encode($data);
			$vurl = array($this->WebServiceURL, $method);
			if ($collection != null) $vurl[] = $collection;
			if ($procedure != null) $vurl[] = $procedure;			
			$url = implode('/', $vurl);

		

			$curl = curl_init();


	
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json_content);
			

			$json_response = curl_exec($curl);
			$this->LastHTTPStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);			
			curl_close($curl);			
			
			if (is_string($json_response))
			{
				$response = json_decode($json_response, true);
				
				switch ((bool) $response['Success'])
				{
					case false:
						$this->LastErrorCode = $response['ErrorCode'];
						if ($response['ResultType'] == 'String')
							$this->LastErrorMsg = $response['Result'];
						
						return null;
					case true:
						return $response;						
				}
			}
			
			return null;
		}
		
		function callMethod($method, $objects)
		{
			$response = $this->makeRequest($method, null, null, $objects);
			if ($response == null) return null;
			if (!is_array($response)) return null;
			
			return $response;
		}
		
		function select($collection, $procedure, $objects)
		{
			$response = $this->makeRequest('select', $collection, $procedure, $objects);
			if ($response == null) return null;
			if (!is_array($response) ) return null;
			
			if ($response['ResultType'] != 'DataSet') return null;
			
			$ds = new TDataSet($this->Parent);
			foreach($response['Result']['Columns'] as $column)
			{
				$ds->FieldDefs[] = $column['Name'];
			}
			
			foreach($response['Result']['Rows'] as $row)
			{
				$ds->addRow($row);
			}
			
			return $ds;
		}
		
		function execute($collection, $procedure, $objects)
		{
			$response = $this->makeRequest('execute', $collection, $procedure, $objects);
			if ($response == null) return false;
			if (!is_array($response)) return false;
			if (!$response['Success']) return false;
			
			return $response['Result'];
		}
		
	}

?>
