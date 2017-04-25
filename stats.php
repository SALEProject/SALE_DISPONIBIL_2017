<?php

	class TStats
	{
		static private $Finstance = null;
		private $data = null;
		private $workdir = '';
		private $filename = '';
		protected $idx = 0;
		
		static function instance()
		{
			if (self::$Finstance != null) return self::$Finstance;
				
			self::$Finstance = new TStats();
			return self::$Finstance;
		}
		
		function __construct()
		{
			$this->workdir = getcwd();
			$this->filename = 'cache'.DIRECTORY_SEPARATOR.'statistics.json';
			
			if (file_exists($this->filename))
			{
				$s = file_get_contents($this->filename);
				try
				{
					$this->data = json_decode($s);
				}
				catch (Exception $e)
				{
					$this->data = null;
				}
			}
			
			if (!isset($this->data)) $this->data = new stdClass();
			if (!isset($this->data->records)) $this->data->records = new stdClass();
			if (!isset($this->data->stats)) $this->data->stats = new stdClass();
		}
		
		function __destruct()
		{
			if (getcwd() != $this->workdir) chdir($this->workdir);
			file_put_contents($this->filename, json_encode($this->data, JSON_PRETTY_PRINT));
		}
		
		function record($key)
		{
			$t = microtime(true);
			$this->data->records->$key = $t;
			return $t;
		}
		
		function store($key, $time)
		{
			$ths->data->records->$key = $time;			
		}
		
		function evaluate($index, $key_start, $key_end, $avg_count = 0)
		{
			if (!isset($this->data->records->$key_start)) return 0;
			if (!isset($this->data->records->$key_end)) return 0;
			
			$t_start = $this->data->records->$key_start;
			$t_end = $this->data->records->$key_end;
			
			$count = 1;
			$last = $t_end - $t_start;
			$avg = $last;
			$min = $last;
			$max = $last;
			
			if (!isset($this->data->stats->$index))
			{
				$this->data->stats->$index = new stdClass();
				$this->data->stats->$index->count = $count;
				$this->data->stats->$index->last = $last;
				$this->data->stats->$index->avg = $avg;
				$this->data->stats->$index->min = $min;
				$this->data->stats->$index->max = $max;
			}
			else 
			{
				$count = $this->data->stats->$index->count;
				$avg = $this->data->stats->$index->avg;
				$min = $this->data->stats->$index->min;
				$max = $this->data->stats->$index->max;
			
				if ($last < $min) $min = $last;
				if ($last > $max) $max = $last;
				
				if ($avg_count == 0 || $count < $avg_count) 
				{
					$avg = ($avg * $count + $last);
					$count++;
					$avg /= $count;
				}
				else 
				{
					$avg = ($avg * ($count - 1) + $last) / $count;
				}
				
				$this->data->stats->$index->count = $count;
				$this->data->stats->$index->last = $last;
				$this->data->stats->$index->avg = $avg;
				$this->data->stats->$index->min = $min;
				$this->data->stats->$index->max = $max;				
			}
			
			return $last;
		}
		

		function getStatsArray()
		{
			$result = array();
			foreach($this->data->stats as $index => $stat)
			{
				$result[$index] = clone $stat;
			}
			
			return $result;
		}
		
		function getStat($index)
		{
			if (!isset($this->data->stats->$index)) return null;
			else return $this->data->stats->$index;
		}
		
		function getCount($index)
		{
			if (!isset($this->data->stats->$index)) return 0;
			else return $this->data->stats->$index->count;
		}
		
		function getLast($index)
		{
			if (!isset($this->data->stats->$index)) return 0;
			else return $this->data->stats->$index->last;
		}
		
		function getAverage($index)
		{
			if (!isset($this->data->stats->$index)) return 0;
			else return $this->data->stats->$index->avg;
		}

		function getMin($index)
		{
			if (!isset($this->data->stats->$index)) return 0;
			else return $this->data->stats->$index->min;
		}
	
		function getMax($index)
		{
			if (!isset($this->data->stats->$index)) return 0;
			else return $this->data->stats->$index->max;
		}
	}

?>