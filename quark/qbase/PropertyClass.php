<?php

abstract class TPropertyClass
{
	public function __get($name)
	{
		if (method_exists($this, ($method = 'get_'.$name))) 
		{
			if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, $method);
			return $this->$method();
		}
		else return;
	}
 
	public function __isset($name)
	{
		if (method_exists($this, ($method = 'isset_'.$name))) 
		{
			if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, $method);				
			return $this->$method();
		}
		else return;
	}
	 
	public function __set($name, $value)
	{
		if (method_exists($this, ($method = 'set_'.$name))) 
		{
			if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, $method);
			$this->$method($value);
		}
		else $this->$name = $value;
	}
	 
	public function __unset($name)
	{
		if (method_exists($this, ($method = 'unset_'.$name))) 
		{
			if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, $method);
			$this->$method();
		}
	}
}

?>