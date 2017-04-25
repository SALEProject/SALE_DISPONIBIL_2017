<?php

	require_once "stats.php";
	
	class TStructFile
	{
		var $logOperations = false;
		
		protected $filename = '';
		protected $data = null;
		protected $diffs = array();
		protected $latest	= null;
		protected $latestRevision = 0;
		protected $revisions = array();
		protected $currentRevision = 0;
		protected $saved = true;
		protected $data_mtime = 0;
		protected $diff_mtime = 0;
		
		protected $currentPath = '';
		protected $pathNodes = array();
		
		protected $traversed = false;
		protected $currentKey = null;
		protected $currentKeyName = '';
		protected $parentKeys = array();
		protected $parentKeyNames = array();
		
		protected $oldPath = '';
		protected $oldPathNodes = null;
		protected $oldKey = null;
		protected $oldKeyName = '';
		protected $oldParentKeys = null;
		protected $oldParentKeyNames = null;
		
		protected $childIndexes = null;
		protected $childNodes = null;
		protected $childIndex = 0;
		
		protected $stack = array();
		
		protected $workdir = '';
		
		function __construct($filename)
		{
			$this->workdir = getcwd();
			$this->filename = $filename;
			if (!is_dir($filename)) mkdir($filename, 0755, true);
			
			$this->checkRevisionNumber();
			$this->saved = true;
		}
		
		function __destruct()
		{
			if (!$this->saved) $this->save();
		}
		
		function __sleep()
		{
			if (!$this->saved) $this->save();
			
			return array('logOperations', 'filename', 'data', 'diffs', 'latest', 'latestRevision', 'revisions', 'currentRevision', 'saved', 'data_mtime', 'diff_mtime',
					'currentPath', 'pathNodes', 'traversed', 'currentKey', 'currentKeyName', 'parentKeys', 'parentKeyNames',
					'oldPath', 'oldPathNodes', 'oldKey', 'oldKeyName', 'oldParentKeys', 'oldParentKeyNames',
					'childIndexes', 'childNodes', 'childIndex', 
					'stack', 'workdir'
			);			
		}
		
		//  check revisionNumber only establishes the current diff file index to start with when saving diffs
		function checkRevisionNumber()
		{
			$this->currentRevision = 0;
			
			if (!isset($this->filename) || $this->filename == '') return false;
			if (!is_dir($this->filename)) mkdir($this->filename, 0755, true);
			
			$dir_data = new DirectoryIterator($this->filename);
			foreach ($dir_data as $file)
			if (!$file->isDot())
			{
				try 
				{
					$name = $file->getFilename();
					$time = $file->getMTime();
					
					if ($name != 'data.json' && $name != 'data_lock.json' && $name != 'log' && $name != 'mirror') 
					{
						$idx_diff = strpos($name, 'diff');
						$idx_php = strpos($name, '.php');
						if ($idx_diff >= 0 && $idx_php >= 0)
						{
							$s = substr($name, 4, $idx_php - 4);
							$k = strpos($s, '_');
							if ($k >= 0) $s = substr($s, 0, $k);
						
							if ((int) $s > $this->currentRevision) $this->currentRevision = (int) $s;
						}
					}
				}
				catch (Exception $e)
				{
					//  something should be done here, right?
				}
			}				
		}
		
		//  buildLatest is similar to checkRevisionNumber but it makes the actual build of data
		//  and includes all diff files which are new
 		function buildLatest()
		{
 			if (!$this->saved) $this->save(); //  first save the diffs before loading new data, so objects 
			$this->latestRevision = 0;        //  from different sessions can share data between them
			
			if (!isset($this->filename) || $this->filename == '') return false;
			if (!is_dir($this->filename)) mkdir($this->filename);

			//  first check the data file, this might have changed following a consolidate
			$data_reloaded = false;
			$t0_data = microtime(true);
			$data_file = $this->filename.DIRECTORY_SEPARATOR.'data.json';
			if (file_exists($data_file))
			{
				$mtime = filemtime($data_file);
				if ($mtime != $this->data_mtime)
				{
					$this->data = json_decode(file_get_contents($data_file));
					if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': read '.'data.json'."\n", FILE_APPEND);
					$this->data_mtime = $mtime;
					$this->diff_mtime = 0;
					$this->latestRevision = 0;
					unset($this->latest);
					$this->latest = null;
					$this->diffs = array();
					$data_reloaded = true;
				}
			}
			else 
			{
				$data_reloaded = true;
				$this->data_mtime = 0;
			}
			$t1_data = microtime(true);
			$t_data = $t1_data - $t0_data;
			
			//  now check diff files and load them to memory if their mtime is greater than ours
			$t0_diff = microtime(true);			
			$newmtime = $this->diff_mtime;
			$dir_data = new DirectoryIterator($this->filename);
			foreach ($dir_data as $file)
			if (!$file->isDot())
			{
				try 
				{
					$name = $file->getFilename();
					$mtime = $file->getMTime();
					
					if (strpos($name, 'data') === false && $mtime >= $this->diff_mtime && $name != 'log' && $name != 'mirror') 
					{
						$idx_diff = strpos($name, 'diff');
						$idx_php = strpos($name, '.php');
						if ($idx_diff >= 0 && $idx_php >= 0)
						{
							$s = substr($name, 4, $idx_php - 4);
							$k = strpos($s, '_');
							if ($k >= 0) $s = substr($s, 0, $k);
						
							$this->diffs[(int) $s] = file_get_contents($this->filename.DIRECTORY_SEPARATOR.$name);
							if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': read '.$name.' mtime = '.$mtime.' diff_mtime = '.$this->diff_mtime."\n", FILE_APPEND);
							if ((int) $s > $this->latestRevision) $this->latestRevision = (int) $s;
							if ($mtime > $newmtime) $newmtime = $mtime;
						}
					}
				}
				catch (Exception $e)
				{
					//  something should be done here also
					return;
				}
			}
			$this->diff_mtime = $newmtime;
			$t1_diff = microtime(true);
			$t_diff = $t1_diff - $t0_diff;

			ksort($this->diffs);
				
			if (!isset($this->data)) $this->data = new stdClass();

			//  build the actual latest data block
			$rebuilt = false;
			if (!isset($this->latest)) { $this->latest = clone $this->data; $rebuilt = true; }
			foreach ($this->diffs as $diff)
			{
				try 
				{
					if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': apply diff '.key($this->diffs).' '.$diff."\n", FILE_APPEND);
					if (eval($diff) === false) TQuark::instance()->browserAlert($diff);
				}
				catch (Exception $e)
				{
					
				}
				$rebuilt = true;
			}
			
			if ($rebuilt && $this->logOperations) if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'mirror', 'session id = '.session_id()."\n".var_export($this->latest, true)."\n");
			
			$this->currentKey = $this->latest;
			$this->currentKeyName = '';
			$this->currentPath = '';
			$this->pathNodes = array();
			$this->parentKeys = array();
			$this->parentKeyNames = array();
			$this->traversed = true;
			$this->oldKey = null;
			$this->oldKeyName = '';
			$this->oldPath = '';
			$this->oldPathNodes = null;
			$this->oldParentKeys = null;
			$this->oldParentKeyNames = null;
			//$this->diffs = array();
			
			if ($data_reloaded && count($this->diffs) > 0 && $t_diff > $t_data) 
			{
				if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': consolidate t_diff = '.$t_diff.' t_data = '.$t_data."\n", FILE_APPEND);				
				$this->consolidate();
			}
		}
		
		function setPath($path, $forceKeys = false)
		{
			if (!isset($this->latest)) $this->buildLatest(); //  check if we have the latest built
			if ($path == '') return; //  no change in the currentPath
			
			//  make a backup for path & prepare new vars
			$this->oldPath = $this->currentPath;
			$this->oldPathNodes = $this->pathNodes;
			$this->currentPath = $path;
			$this->pathNodes = array();
			$this->traversed = false;
			
			//  split path and process it
			$trace = explode('.', $path);
			if (count($trace) > 0 && $trace[count($trace) - 1] == '') unset($trace[count($trace) - 1]);
			$first = true;
			$prev = '';
			foreach ($trace as $node)
			{
				if ($node == '' && $prev == '' && !$first) 
				{
					//  it's an up level thingie, add a node only when a prev up thingie is already there
					$idx = count($this->pathNodes) - 1;
					if ($this->pathNodes[$idx] == '') $this->pathNodes[$idx] = ' ';
					else $this->pathNodes[] = ' ';
				}
				else $this->pathNodes[] = $node;
					
				if ($first) $first = false;
				$prev = $node;
			}
			
			if ($forceKeys) $this->traversePath(true, false);
		}
		
		protected function traversePath($forceKeys = false, $setChildNodes = false)
		{
			//  backup currentKey etc
			$this->oldKey = $this->currentKey;
			$this->oldKeyName = $this->currentKeyName;
			$this->oldParentKeys = $this->parentKeys;
			$this->oldParentKeyNames = $this->parentKeyNames;
			
			//  traverse according to path nodes
			foreach ($this->pathNodes as $node)
			{
				switch ($node)
				{
					case '':
						$this->currentKey = $this->latest;
						$this->currentKeyName = '';
						$this->parentKeys = array();
						$this->parentKeyNames = array();
						break;
					case ' ':
						$idx = count($this->parentKeys) - 1;
						if ($idx >= 0)
						{
							$this->currentKey = $this->parentKeys[$idx];
							$this->currentKeyName = $this->parentKeyNames[$idx];
							unset($this->parentKeys[$idx]);
							unset($this->parentKeyNames[$idx]);
							$this->parentKeys = array_filter($this->parentKeys);
							$this->parentKeyNames = array_filter($this->parentKeyNames);
						}
						break;
					default:
						if (isset($this->currentKey->$node))
						{
							if (!is_object($this->currentKey->$node))
							{
								//  save the value and place it as an attribute 
								$value = $this->currentKey->$node;
								$this->currentKey->$node = new stdClass();
								$this->currentKey->$node->value = $value;
							}
								
							$this->parentKeys[] = $this->currentKey;
							$this->parentKeyNames[] = $this->currentKeyName;
							$this->currentKey = $this->currentKey->$node;
							$this->currentKeyName = $node;
						}
						else if ($forceKeys)
						{
							//  create nodes if they do not exist already
							$this->currentKey->$node = new stdClass();							
							$this->parentKeys[] = $this->currentKey;
							$this->parentKeyNames[] = $this->currentKeyName;
							$this->currentKey = $this->currentKey->$node;
							$this->currentKeyName = $node;
						}
						break;
				}
			}
			
			$this->traversed = true;
			if ($setChildNodes) $this->first(); //  this will set the child nodes array
			
			//  return absolute path
			return $this->getAbsPath();
		}
		
		protected function getAbsPath()
		{
			$path = '';
			foreach ($this->parentKeyNames as $name) $path .= $name == '' ? $name : '.'.$name;
			return $path . ($this->currentKeyName == '' ? '' : '.'.$this->currentKeyName);
		}
		
		protected function revertPath()
		{
			//  simply copy back old key and parent nodes
			$this->currentKey = $this->oldKey;
			$this->currentKeyName = $this->oldKeyName;
			$this->parentKeys = $this->oldParentKeys;
			$this->parentKeyNames = $this->oldParentKeyNames;
			$this->currentPath = $this->oldPath;
			$this->pathNodes = $this->oldPathNodes;
		}
		
		//  this will return the currentKey name 
		function keyName()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			return $this->currentKeyName;
		}
		
		//  this will return the currentKey value
		function keyValue()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			if (!isset($this->currentKey)) return null;
			return $this->currentKey->value;
		}
		
		//  this will return the currentKey path
		function keyPath()
		{
			if (!$this->traversed) $this->traversePath(false, true);
			return $this->getAbsPath();
		}
		
		//  child nodes iteration
		function first()
		{
			if (!$this->traversed) $this->traversePath(true, false); 
			
			$this->childIndexes = null;
			$this->childNodes = null;
			$this->childIndex = 0;
				
			if (is_object($this->currentKey))
			{
				$this->childNodes = get_object_vars($this->currentKey);
				$this->childIndexes = array_keys($this->childNodes);
				
				if (count($this->childNodes) > 0) return true;
			}
			
			return false;
		}
		
		function next()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			
			if (is_array($this->childNodes) && $this->childIndex < count($this->childNodes) - 1)
			{
				$this->childIndex++;
				return true;
			}
				
			return false;
		}
		
		function prev()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			
			if (is_array($this->childNodes) && $this->childIndex > 0)
			{
				$this->childIndex--;
				return true;
			}
				
			return false;
		}
		
		function last()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			
			if (is_array($this->childNodes))
			{
				$this->childIndex = count($this->childNodes) - 1;
				return true;
			}
				
			return false;
		}
		
		function open()
		{
			if (count($this->childNodes) == 0) return false;
			
			$idx = count($this->stack);
			$this->stack[] = array();
			
			$this->stack[$idx]['currentPath']		= $this->currentPath;
			$this->stack[$idx]['pathNodes']			= $this->pathNodes;
			
			$this->stack[$idx]['traversed']			= $this->traversed;
			$this->stack[$idx]['currentKey']		= $this->currentKey;
			$this->stack[$idx]['currentKeyName']	= $this->currentKeyName;
			$this->stack[$idx]['parentKeys']		= $this->parentKeys;
			$this->stack[$idx]['parentKeyNames']	= $this->parentKeyNames;
			
			$this->stack[$idx]['oldPath']			= $this->oldPath;
			$this->stack[$idx]['oldPathNodes']		= $this->oldPathNodes;
			$this->stack[$idx]['oldKey']			= $this->oldKey;
			$this->stack[$idx]['oldKeyName']		= $this->oldKeyName;
			$this->stack[$idx]['oldParentKeys']		= $this->oldParentKeys;
			$this->stack[$idx]['oldParentKeyNames'] = $this->oldParentKeyNames;
			
			$this->stack[$idx]['childIndexes']		= $this->childIndexes;
			$this->stack[$idx]['childNodes']		= $this->childNodes;
			$this->stack[$idx]['childIndex']		= $this->childIndex;
			
			$path = $this->getAbsPath().'.'.$this->childIndexes[$this->childIndex];
			$this->setPath($path);
			
			return true;
		}
		
		function close()
		{
			$idx = count($this->stack) - 1;
			if ($idx < 0) return false;
				
			$this->currentPath			= $this->stack[$idx]['currentPath'];
			$this->pathNodes			= $this->stack[$idx]['pathNodes'];
				
			$this->traversed			= $this->stack[$idx]['traversed'];
			$this->currentKey			= $this->stack[$idx]['currentKey'];
			$this->currentKeyName		= $this->stack[$idx]['currentKeyName'];
			$this->parentKeys			= $this->stack[$idx]['parentKeys'];
			$this->parentKeyNames		= $this->stack[$idx]['parentKeyNames'];
				
			$this->oldPath				= $this->stack[$idx]['oldPath'];
			$this->oldPathNodes			= $this->stack[$idx]['oldPathNodes'];
			$this->oldKey				= $this->stack[$idx]['oldKey'];
			$this->oldKeyName			= $this->stack[$idx]['oldKeyName'];
			$this->oldParentKeys		= $this->stack[$idx]['oldParentKeys'];
			$this->oldParentKeyNames	= $this->stack[$idx]['oldParentKeyNames'];
				
			$this->childIndexes			= $this->stack[$idx]['childIndexes'];
			$this->childNodes			= $this->stack[$idx]['childNodes'];
			$this->childIndex			= $this->stack[$idx]['childIndex'];
			
			unset($this->stack[$idx]);
			$this->stack = array_filter($this->stack);
			return true;
		}
		
		//  get and set operations for child nodes
		function getName()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			if (!is_array($this->childNodes) || $this->childIndex < 0 || $this->childIndex >= count($this->childNodes)) return '';
			return $this->childIndexes[$this->childIndex];
		}
		
		function hasChildren()
		{
			if (!$this->traversed) $this->traversePath(false, true); 
			if (!is_array($this->childNodes) || $this->childIndex < 0 || $this->childIndex >= count($this->childNodes)) return false;
			if (is_object($this->childNodes[$this->childIndexes[$this->childIndex]])) return true; 
			else return false;
		}
		
		function keyExists($key)
		{
			if (!$this->traversed) $this->traversePath(false, true);
			if (!is_array($this->childNodes) || $this->childIndex < 0 || $this->childIndex >= count($this->childNodes)) return false;
			return array_key_exists($key, $this->childNodes);
		}
		
		function locate($key)
		{
			if (!$this->traversed) $this->traversePath(false, true);
			if (!is_array($this->childNodes) || $this->childIndex < 0 || $this->childIndex >= count($this->childNodes)) return false;
			if (!array_key_exists($key, $this->childNodes)) return false;
			
			$this->childIndex = array_search($key, $this->childIndexes);
			return true;
		}
		
		function get($key = '')
		{			
			if (!isset($this->latest)) $this->buildLatest();
				
			$doRevertPath = false;
			if (!$this->traversed) $this->traversePath(false, true);
			
			$value = null;
			if ($key == '') //  return the current child value
			{
				if (is_array($this->childNodes) && $this->childIndex >= 0 && $this->childIndex < count($this->childNodes))
				{
					if (is_object($this->childNodes[$this->childIndexes[$this->childIndex]])) 
						$value = $this->childNodes[$this->childIndexes[$this->childIndex]]->value;
					else
						$value = $this->childNodes[$this->childIndexes[$this->childIndex]];
				}
			}
			else 
			{			
				$pos = strrpos($key, '.');
				if ($pos !== false)
				{
					$path = substr($key, 0, $pos);
					$key = substr($key, $pos + 1);
					
					//  set path only if traversed and path is changed
					if ($path != '' && $path != $this->currentPath) 
					{
						$this->setPath($path);
						$this->traversePath(false);
						$doRevertPath = true;
					}
				}
				
				if (isset($this->currentKey->$key))
				{
					if (!is_object($this->currentKey->$key)) $value = $this->currentKey->$key;
					else $value = $this->currentKey->$key->value;
				}
			}
			
			if ($doRevertPath) $this->revertPath();
			return $value;
		}
		
		function set($key, $value, $incrementRevision = true)
		{			
			if (!isset($this->latest)) $this->buildLatest();
			
			$absPath = '';
			$doRevertPath = false; //  will revert only if necessary
			if (!$this->traversed) $absPath = $this->traversePath(true, true); //  found a setPath by user, so mark this one as base
			
			$changed = false;
			if ($key == '')
			{
				if (is_array($this->childNodes) && $this->childIndex >= 0 && $this->childIndex < count($this->childNodes))
				{
					$isobj = is_object($this->childNodes[$this->childIndexes[$this->childIndex]]); 

					if ($isobj) $oldval = $this->childNodes[$this->childIndexes[$this->childIndex]]->value;
					else $oldval = $this->childNodes[$this->childIndexes[$this->childIndex]];
					
					if ($oldval != $value)
					{
						$changed = true;
						
						if ($isobj) $this->childNodes[$this->childIndexes[$this->childIndex]]->value = $value;
						else $this->childNodes[$this->childIndexes[$this->childIndex]] = $value;
					}
				}
			}
			else 
			{
				$pos = strrpos($key, '.');
				if ($pos !== false)
				{
					$path = substr($key, 0, $pos);
					$key = substr($key, $pos + 1);
					
					//  set path only if traversed and path is changed
					if ($path != '' && $path != $this->currentPath) 
					{
						$this->setPath($path);
						$absPath = $this->traversePath(true);
						$doRevertPath = true;
					}
				}
			
				//if (!isset($this->currentKey->$key)) $this->currentKey->$key = new stdClass();
				if (!isset($this->currentKey->$key))
				{
					$changed = true;
					$this->currentKey->$key/*->value*/ = $value;
				}
				else
				{
					$isobj = is_object($this->currentKey->$key);
					
					if ($isobj) $oldval = $this->currentKey->$key->value;
					else $oldval = $this->currentKey->$key;
					
					if ($oldval != $value)
					{
						$changed = true;
						
						if ($isobj) $this->currentKey->$key->value = $value;
						else $this->currentKey->$key = $value;
					}
				}
			}
			
			//if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': changed'."\n", FILE_APPEND);
			
			//  build the diff command
			if ($changed && $incrementRevision)
			{
				if ($absPath == '') $absPath = $this->getAbsPath();
				
				if ($this->currentPath != '') $key = $absPath.'.'.$key;
				$key = '"'.addslashes($key).'"';
								
				if (is_string($value)) $value = '"'.addslashes($value).'"';
						
				//  special treatment for boolean values
				if (is_bool($value)) $str_value = $value == true ? 'true' : 'false';
				else $str_value = (string) $value;
				
				//  special case for null values
				if (is_null($value)) $str_value = 'null';
				
				$cmd = '$this->set('.$key.', '.$str_value.', false);';
				$this->revisions[] = $cmd;
				$this->currentRevision++;
				$this->saved = false;
			}
			
			if ($doRevertPath) $this->revertPath();
		}

		function reset($key, $incrementRevision = true)
		{
			if (!isset($this->latest)) $this->buildLatest();
				
			$absPath = '';
			$doRevertPath = false; //  will revert only if necessary
			if (!$this->traversed) $absPath = $this->traversePath(true, true); //  found a setPath by user, so mark this one as base
				
			if ($key == '')
			{
				if (is_array($this->childNodes) && $this->childIndex >= 0 && $this->childIndex < count($this->childNodes))
				{
					$isobj = is_object($this->childNodes[$this->childIndexes[$this->childIndex]]);
		
					if ($isobj) 
					{
						unset($this->childNodes[$this->childIndexes[$this->childIndex]]->value);
						$this->childNodes[$this->childIndexes[$this->childIndex]]->value = null;
					}
					else 
					{
						unset($this->childNodes[$this->childIndexes[$this->childIndex]]);
						$this->childNodes[$this->childIndexes[$this->childIndex]] = null;
					}
				}
			}
			else
			{
				$pos = strrpos($key, '.');
				if ($pos !== false)
				{
					$path = substr($key, 0, $pos);
					$key = substr($key, $pos + 1);
						
					//  set path only if traversed and path is changed
					if ($path != '' && $path != $this->currentPath)
					{
						$this->setPath($path);
						$absPath = $this->traversePath(true);
						$doRevertPath = true;
					}
				}
					
				//if (!isset($this->currentKey->$key)) $this->currentKey->$key = new stdClass();
				if (!isset($this->currentKey->$key))
				{
					unset($this->currentKey->$key);
					$this->currentKey->$key = null;
				}
				else
				{
					$isobj = is_object($this->currentKey->$key);
					
					if ($isobj) 
					{
						unset($this->currentKey->$key->value);
						$this->currentKey->$key->value = null;
					}
					else 
					{
						unset($this->currentKey->$key);
						$this->currentKey->$key = null;
					}
				}
			}
				
			//if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': changed'."\n", FILE_APPEND);
				
			//  build the diff command
			if ($incrementRevision)
			{
				if ($absPath == '') $absPath = $this->getAbsPath();
		
				if ($this->currentPath != '') $key = $absPath.'.'.$key;
				$key = '"'.addslashes($key).'"';
				
				$cmd = '$this->reset('.$key.', false);';
				$this->revisions[] = $cmd; 
				$this->currentRevision++;
				$this->saved = false;
			}
				
			if ($doRevertPath) $this->revertPath();
		}
		
		function save()
		{
			if (getcwd() != $this->workdir) chdir($this->workdir);
			
			$sid = session_id();
			if ($sid == '') 
			{
				session_start();
				$sid = session_id();
			}
			
			if ($this->filename == '') return;
			$filename = $this->filename;
			
			if (!is_dir($filename)) mkdir($filename);
						
			$i = $this->latestRevision + 1;
			foreach ($this->revisions as $revision) 
			{
				//echo $revision;
				file_put_contents($filename.DIRECTORY_SEPARATOR.'diff'.$i.'_'.$sid.'.php', $revision);
				if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': write '.'diff'.$i.'_'.$sid.'.php'."\n", FILE_APPEND);
				$i++;
				
				unset($revision);
			}
				
			$this->revisions = array();
			$this->latestRevision = $this->currentRevision;
			$this->saved = true;
		}
		
		function consolidate()
		{
			if (!isset($this->latest)) $this->buildLatest();
			
			$sid = session_id();
			if ($sid == '') 
			{
				session_start();
				$sid = session_id();
			}
			
			$tmp_name = $this->filename.DIRECTORY_SEPARATOR.'data_'.$sid.'_lock.json';
			file_put_contents($tmp_name, json_encode($this->latest, JSON_PRETTY_PRINT));
			if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': write '.'data_'.$sid.'_lock.json'."\n", FILE_APPEND);
			if (!file_exists($tmp_name)) return;
			
			$dir_data = new DirectoryIterator($this->filename);
			foreach ($dir_data as $file)
			{
				$name = $file->getFilename();

				if (!$file->isDot() && strpos($name, 'data_') === false && $name != 'log' && $name != 'mirror')
				{
					unlink($this->filename.DIRECTORY_SEPARATOR.$name);
					if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': delete '.$name."\n", FILE_APPEND);
				}		
			}

			copy($tmp_name, $this->filename.DIRECTORY_SEPARATOR.'data.json');
			if ($this->logOperations) file_put_contents($this->filename.DIRECTORY_SEPARATOR.'log', session_id().': overwrite '.'data.json'."\n", FILE_APPEND);
			//rename($tmp_name, $this->filename.DIRECTORY_SEPARATOR.'data.json');
			$this->revisions = array();
			$this->latestRevision = 0;
			$this->currentRevision = 0;
			$this->data_mtime = filemtime($this->filename.DIRECTORY_SEPARATOR.'data.json');
			$this->data = clone $this->latest;
			$this->diff_mtime = 0;
			$this->saved = true;
		}
		
		
	}
	
?>
