<?php

	class Struct
	{
	    /**
		* Define a new struct object, a blueprint object with only empty properties.
		*/
	    public static function factory()
	    {
	        $struct = new self;
	        foreach (func_get_args() as $value) {
	            $struct->$value = null;
	        }
	        return $struct;
	    }
 
	    /**
		* Create a new variable of the struct type $this.
		*/
	    public function create()
	    {
	        // Clone the empty blueprint-struct ($this) into the new data $struct.
	        $struct = clone $this;
	 
	        // Populate the new struct.
	        $properties = array_keys((array) $struct);
	        foreach (func_get_args() as $key => $value) 
	        {
	            if (!is_null($value)) 
	            {
	                $struct->$properties[$key] = $value;
	            }
	        }
	 
	        // Return the populated struct.
	        return $struct;
	    }
	}
	
	/*
	// define a 'coordinates' struct with 3 properties
	$coords = Struct::factory('degree', 'minute', 'second', 'pole');
	
	// create 2 latitude/longitude numbers
	$lat = $coords->create(35, 41, 5.4816, 'N');
	$lng = $coords->create(139, 45, 56.6958, 'E');
	
	// use the different values by name
	echo $lat->degree . '° ' . $lat->minute . "' " . $lat->second . '" ' . $lat->pole;	
	*/
?>