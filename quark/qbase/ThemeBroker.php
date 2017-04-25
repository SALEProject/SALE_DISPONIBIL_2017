<?php

	class TThemeBroker extends TPropertyClass
	{
		var $ThemeName = '';
		var $ThemeURL = '';
		var $BuildTime;
		var $ModifiedTime;
		
		protected $Classes = array();
		
		function __construct($ThemeName, $ThemeURL)
		{
			$this->ThemeName = $ThemeName;
			$this->ThemeURL = $ThemeURL;
			
			//$this->load();
		}
		
		function load()
		{
			//  obtain the build date of the cached version
			unset($this->BuildTime);
			if (is_dir('cache'))
			{
				$cachefile = 'cache'.DIRECTORY_SEPARATOR.$this->ThemeName.'css';
				if (file_exists($cachefile)) $this->BuildTime = filemtime($cachefile);
			}
			
			//  obtain the modify date of the source
			switch (strtolower($this->ThemeName))
			{
				case 'default':
					$this->ModifiedTime = $this->getDefaultModifiedTime();
					break;
				default:
					$this->ModifiedTime = $this->getModifiedTime();
					break;
			}			
		}
		
		function get_Style()
		{
			$style = '';
			$files = array();
			foreach ($this->Classes as $class) $files[] = $class['filename'];
			
			if (is_dir($this->ThemeURL))
			{
				$dir = new DirectoryIterator($this->ThemeURL);
				foreach ($dir as $file)
					if (!$file->isDot() && $file->getExtension() == 'css')
					{						
						$filename = $this->ThemeURL.DIRECTORY_SEPARATOR.$file->getFilename();
						
						if (!in_array($filename, $files)) $style .= file_get_contents($filename)."\n";
					}
			}
			
			foreach ($this->Classes as $name => $class)
			{
				switch ($class['tryDefault'])
				{
					case false:
						if (file_exists($class['filename'])) $style .= file_get_contents($class['filename'])."\n";
						break;
					case true:
						if (property_exists($name, 'DefaultStyle')) $style .= $name::$DefaultStyle."\n";						
						break;
				}
			}			
			
			return $style;			
			/*$cachefile = 'cache'.DIRECTORY_SEPARATOR.$this->ThemeName.'.css';
			if (isset($this->BuildTime) && isset($this->ModifiedTime) && $this->BuildTime >= $this->ModifiedTime)
			{
				if (file_exists($cachefile)) return file_get_contents($cachefile);
				else return '';
			}
			else
			{			
				$style = '';
				switch (strtolower($this->ThemeName))
				{
					case 'default':
						$style = $this->buildDefault();
						break;
					default:
						$style = $this->buildTheme();
						break;
				}
				
				file_put_contents($cachefile, $style);
				return $style;
			}*/
		}
		
		function needUpdateStyle($className)
		{
			if (!class_exists($className)) //  if class doesn't exist anymore why bother
			{
				if (isset($this->Classes[$className]))
				{
					unset($this->Classes[$className]);
					$this->Classes = array_filter($this->Classes);
					return true;
				}
				
				return false;
			}
			
			//  build the css name that would normally be used in a theme
			$cssName = $className.'.css';
			if ($cssName[0] = 'T') $cssName = substr($cssName, 1);
			$cssName = $this->ThemeURL.DIRECTORY_SEPARATOR.$cssName;
			
			$tryDefault = false;
			if (file_exists($cssName)) $filename = $cssName;
			else 
			{
				$reflector = new ReflectionClass($className);
				$filename = $reflector->getFileName();
				$tryDefault = true;
			}
			
			$size = filesize($filename);
			$mtime = filemtime($filename);
			
			if (!isset($this->Classes[$className]))
			{
				$this->Classes[$className] = array('filename' => $filename, 'size' => $size, 'mtime' => $mtime, 'tryDefault' => $tryDefault);
				return true;
			}
			else
			{							
				//  extract existing values to compare against
				$old_filename = $this->Classes[$className]['filename'];
				$old_size = $this->Classes[$className]['size'];
				$old_mtime = $this->Classes[$className]['mtime'];
				$old_tryDefault = $this->Classes[$className]['tryDefault'];
				
				if ($filename != $old_filename || $size != $old_size || $mtime != $old_mtime || $tryDefault != $old_tryDefault)
				{
					$this->Classes[$className] = array('filename' => $filename, 'size' => $size, 'mtime' => $mtime, 'tryDefault' => $tryDefault);
					return true;
				}
				
				return false;
			}
		}
		
	}

?>