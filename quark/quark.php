<?php

	header('Content-Type: text/html; charset=utf-8');
	use phpbrowscap\Browscap;
	
	//-------------------------------------------------------------------------------------------------------	
	//  determine working directory
	$qPath = __DIR__;
	$s = dirname($_SERVER['SCRIPT_FILENAME']);
	if ($s != '\\' && $s != '/') $s .= DIRECTORY_SEPARATOR;
	if (strpos($qPath, $s) >= 0) $qPath = substr($qPath, strlen($s));
	define('qPath', $qPath);
	
	//-------------------------------------------------------------------------------------------------------
	//  Set Exception handling
	//  note: check if qOS already loaded its own exception handling routine	
	//error_reporting(0);
	//register_shutdown_function('quark_shutdown');
	function quark_shutdown()
	{
		if (!is_null($e = error_get_last()))
		{
			switch ($e['type'])
			{
				case E_ERROR:
				case E_COMPILE_ERROR:
					switch(TQuark::instance()->IsCallBack)
					{
						case false:
							print_r($e);
							break;
						true:
							TQuark::instance()->addAjaxStack('', 'alert', print_r($e, true));
							TQuark::instance()->sendAjaxResponse();
							break;
					}
					break;
			}
		}
	}
	
	//-------------------------------------------------------------------------------------------------------
	//  load base classes, qcl and widgets	
	require_once 'qbase/PropertyClass.php';
	require_once 'qbase/Component.php';
	require_once 'qbase/Widget.php';
	require_once 'qbase/ThemeBroker.php';
	
	//  check if OPcache is running and enabled
	global $quark_usecache;
	$quark_usecache = function_exists('opcache_get_status');
	
	//  Quark Component Library Folder
	global $quark_qcl_dir;
	$quark_qcl_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'qcl';
	
	//  Quark Widgets Folder
	global $quark_widgets_dir;
	$quark_widgets_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgets';
	
	//  Quark Component Library Loading
	if (file_exists($quark_qcl_dir))
	{
		$dir_qcl = new DirectoryIterator($quark_qcl_dir);
		foreach ($dir_qcl as $file_qcl)
			if (!$file_qcl->isDot())
			{
				require_once $quark_qcl_dir.DIRECTORY_SEPARATOR.$file_qcl->getFilename();
			}
	}
		
	//  autoloader function
	function quark_autoloader($class)
	{
		global $quark_widgets_dir;
		global $WidgetCollection;
		
		$filename = $class.'.php';
		if ($filename[0] = 'T') $filename = substr($filename, 1);
		include $quark_widgets_dir.DIRECTORY_SEPARATOR.$filename;
		
		if (isWidget($class)) 
		{
			$WidgetCollection[] = $class;
			TQuark::instance()->updateWidgetThemes($class);
		}
	}
	
	spl_autoload_register('quark_autoloader');
	
	//  Quark Widgets Loading
/*	if (file_exists($quark_widgets_dir))
	{
		$dir_widgets = new DirectoryIterator($quark_widgets_dir);
		foreach ($dir_widgets as $file_widget)
			if (!$file_widget->isDot() && $file_widget->getExtension() == 'php')
			{
				require_once $quark_widgets_dir.DIRECTORY_SEPARATOR.$file_widget->getFilename();	
			}
	}
*/

	//-------------------------------------------------------------------------------------------------------
	//  declare the TQuark class
	class TQuark extends TPropertyClass
	{
		static private $Finstance = null;
		
		static function instance()
		{
			if (self::$Finstance != null) return self::$Finstance;
			
			self::$Finstance = new TQuark();
			return self::$Finstance;
		}
		
		var $debugJS = false;
		var $debugForm = null;
		var $IsCallBack = false;		
		var $browser_info = null;
		var $clientScreen = null;
		var $themes = Array();
		var $desktop = null;
		var $viewports = Array();
		var $currentViewport = '';
		protected $forms = Array();		
		protected $idxForm = 0;
		protected $AjaxStack = Array();
		protected $handlers = Array();		
		protected $timers = Array();
		protected $files = Array();
		protected $skipCache = false;
		protected $markedThemes = array();
		
		function __construct()
		{
			$this->session_id = session_id();
			if ($this->session_id == '')
			{
				session_start();
				session_write_close();
				$this->session_id = session_id();
			}

			$debugJS = false;
			if (isset($_REQUEST['callBack']))
				if ($_REQUEST['callBack'] == 'true') $this->IsCallBack = true;
				
			//$this->addTheme('qDebug', 'themes/qDebug/qDebug.css');			
		}
		
		//---------------------------------------------------------------------------------------------
		//  Context Manager vars & methods
		
		private $FcontextManager = null;
		private $FcontextMain = null;
		private $FcontextCallBack = null;
		
		protected function get_contextManager()
		{
			return $this->FcontextManager;
		}
		
		protected function get_contextMain()
		{
			return $this->FcontextMain;
		}
		
		protected function get_contextCallBack()
		{
			return $this->FcontextCallBack;
		}
		
		function getContext($contextID)
		{
			$obj = $this->contextManager;
			$proc = $this->contextCallBack;
			
			if (isset($proc))
			{
				if (isset($obj) && is_object($obj) && method_exists($obj, $proc)) return $obj->$proc($contextID);
				else if (function_exists($proc)) return $proc($contextID);
				
				return null;
			}
			
			return null;
		}
		
		//---------------------------------------------------------------------------------------------
		
		protected function sendDebugFlag()
		{
			if ($this->debugJS) 
			{
				if (!$this->IsCallBack) echo '<script type="text/javascript">debugJS = true;</script>';
				if (!$this->IsCallBack) echo '<link rel="stylesheet" type="text/css" href="themes/qDebug/qDebug.css">';
				
				$clientW = 640;
				$clientH = 480;
				if (isset($this->clientScreen)) 
				{
					$clientW = $this->clientScreen->Width;
					$clientH = $this->clientScreen->Height;
				}
				
				$w = 320; $h = 400;
				$this->debugForm = new TDebugForm($clientW - $w - 8, 32, $w, $h, 'qDebug');
				if (!$this->IsCallBack) $this->debugForm->show();
			}
		}
		
		protected function checkBrowser()
		{
			$browser = new Browscap('browscap_cache');
			$browser->localFile = 'browscap_ini/php_browscap.ini';
			$browser->doAutoUpdate = false;
			$this->browser_info = $browser->getBrowser();			
		}
		
		function addTheme($ThemeName, $ThemeURL)
		{
			if (count($this->themes) == 0) $this->themes['default'] = new TThemeBroker('default', '');
			$this->themes[$ThemeName] = new TThemeBroker($ThemeName, $ThemeURL);
		}
		
		protected function sendThemes()
		{
			foreach ($this->themes as $name => $theme)
			{			
				echo '<style id="theme_'.$name.'" type="text/css">'."\n";
				echo $theme->Style;
				echo '</style>'."\n";
			}
		}
		
		protected function updateThemes()
		{
			foreach ($this->markedThemes as $theme)
			{
				$style = $this->themes[$theme]->Style;
				$this->addAjaxStack('theme_'.$theme, 'setStyleSheet', $style);
			}
		}
		
		protected function markTheme4Update($ThemeName)
		{
			if (!in_array($ThemeName, $this->markedThemes)) $this->markedThemes[] = $ThemeName;
		}
		
		function updateWidgetThemes($widgetClass)
		{
			if (!$this->IsCallBack) return; //  do not make theme updates on initial load, css files will be sent through Ajax
			if (!class_exists($widgetClass)) return;
					
			foreach ($this->themes as $name => $theme) 
			{
				if ($theme->needUpdateStyle($widgetClass)) $this->markTheme4Update($name);
			}
		}

		protected function initDesktop($SettingsFile = null)
		{
			echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />'."\n";
			echo '<meta name="viewport" content="width-device-width, initial-scale=1.0" />'."\n";
			
			//  add default theme if nothing else there is
			if (count($this->themes) == 0) $this->themes['default'] = new TThemeBroker('default', '');
			$this->sendThemes();
			
			$s = qPath.DIRECTORY_SEPARATOR.'quark.js';
			$v = md5_file($s);
			echo '<script type="text/javascript" src="'.qPath.'/js/q.js"></script>';
			echo '<script type="text/javascript" src="'.qPath.'/js/base64.js"></script>';
			echo '<script type="text/javascript" src="'.qPath.'/js/json2.js"></script>';
			echo '<script type="text/javascript" src="'.qPath.'/js/upclick.js"></script>';
			echo '<script type="text/javascript" src="'.qPath.'/quark.js?v='.$v.'"></script>';
			echo '<script type="text/javascript" src="'.qPath.'/dojo/dojo.js"></script>';
			//echo '<link rel="stylesheet" type="text/css" href="dijit/themes/claro/claro.css">';
						
			if (!isset($SettingsFile)) return;
		
			$codefile = dirname($SettingsFile).DIRECTORY_SEPARATOR.basename($SettingsFile, '.xml');
			$codefile .= '.php';
		
			if (!file_exists($codefile)) return null;
		
			require_once $codefile;
		
			$s = file_get_contents($SettingsFile);
			$xml = simplexml_load_string($s);
		
			$type = $xml->getName();
			$dsk = new $type(null);
			if (!($dsk instanceof TDesktop)) return;
			$dsk->loadProperties($s);
			$dsk->ClassName = $type;
			$dsk->CodeFile = $codefile;
			$this->setFormVars($dsk);
			$dsk->OnLoad();
			
			$this->desktop = $dsk;
			//$this->forms[$dsk->Name] = $dsk;

			$this->desktop->show();			
		}
		
		//---------------------------------------------------------------------------------------------
		//  Forms managing routines
		
		protected function setFormVars(TForm $frm, TWidget $parent = null)
		{
			if (!($frm instanceof TForm)) return;
			if ($parent == null) $parent = $frm;
			if (!($parent instanceof TWidget)) return;
			
			foreach($parent->Controls as $ctrl)
			{
				eval('$frm->'.$ctrl->Name.' = $ctrl;');
				$this->setFormVars($frm, $ctrl);
			}
		}
	
		function loadForm($ref, $contextID = 0)
		{
			$className = '';
			$definition = '';
			$resfile = '';
			$xml = null;
			
			if (class_exists($ref) && is_subclass_of($ref, 'TForm'))
			{
				$className = $ref;	
				if (property_exists($ref, 'Definition')) $definition = $ref::$Definition;
				else 
				{
					$reflector = new ReflectionClass($className);
					$resfile = $reflector->getFileName();
					$resfile = dirname($resfile).DIRECTORY_SEPARATOR.basename($resfile, '.php').'.xml';
					if (file_exists($resfile)) $definition = file_get_contents($resfile);
					else $resfile = '';
				}
				
				if (!empty($definition)) $xml = simplexml_load_string($definition);
			}
			else 
			{
				if (!file_exists($ref)) return null;
			
				//  $ref must be a url for an xml definition file		
				$codefile = dirname($ref).DIRECTORY_SEPARATOR.basename($ref, '.xml');
				$codefile .= '.php';
			
				if (!file_exists($codefile)) return null;
			
				require_once $codefile;
			
				$definition = file_get_contents($ref);
				$xml = simplexml_load_string($definition);
				$className = $xml->getName();
			}
		
			//  actual form loading takes place here
			$form = null;
			try
			{
				$form = new $className(null);
				$form->contextID = $contextID;
				$form->loadProperties($definition);
				$this->setFormVars($form);
				$form->OnLoad();
			}
			catch (Exception $e)
			{
				$this->addAjaxStack('', 'alert', 'An error occured while loading form.');
			}
		
			//global $forms;
			$this->forms[$form->Name] = $form;
		
			return $form;
		}

		function removeForm($form_name)
		{
			if ($this->forms[$form_name] != null) 
			{
				unset($this->forms[$form_name]);
				$this->forms = array_filter($this->forms);
			}
		}	
		
		function countForms()
		{
			return count($this->forms);
		}

		protected function getFormPointer($frm_name)
		{
			if (!key_exists($frm_name, $this->forms)) return null;			
			$frm = $this->forms[$frm_name];
			if (is_string($frm) && $frm == 'placeholder') 
			{
				$this->uncacheForm($frm_name);
				$frm = $this->forms[$frm_name];
			}
			
			if (is_object($frm) && $frm instanceof TForm) return $frm;
			
			return null;
		}
		
		function getForm($index)
		{
			if (count($this->forms) == 0) return null;
			
			$frm_name = $index;
			if (is_numeric($index))
			{
				if ($index < 0 || $index >= count($this->forms)) return null;		
				$keys = array_keys($this->forms);
				$frm_name = $keys[$index];
			}
			
			return $this->getFormPointer($frm_name);
		}
		
		function firstForm()
		{
			if (count($this->forms) == 0) return null;
			$this->idxForm = 0;
			$keys = array_keys($this->forms);
			$frm_name = $keys[0];
			
			return $this->getFormPointer($frm_name);
		}
		
		function nextForm()
		{
			if (count($this->forms) == 0) return null;
			if ($this->idxForm >= count($this->forms) - 1) return null;
			
			$this->idxForm++;
			$keys = array_keys($this->forms);
			$frm_name = $keys[$this->idxForm];
			
			return $this->getFormPointer($frm_name);
		}
		
		function prevForm()
		{
			if (count($this->forms) == 0) return null;
			if ($this->idxForm <= 0) return null;
			
			$this->idxForm--;
			$keys = array_keys($this->forms);
			$frm_name = $keys[$this->idxForm];
			
			return $this->getFormPointer($frm_name);
		}
		
		function lastForm()
		{
			$count = count($this->forms);
			if ($count == 0) return null;
			$this->idxForm = $count - 1;
			$keys = array_keys($this->forms);
			$frm_name = $keys[$this->idxForm];
			
			return $this->getFormPointer($frm_name);
		}
		
		//---------------------------------------------------------------------------------------------
		//  caching functions
		
		protected $session_id = '';
		protected $cacheManager = null;
		protected $cacheRead = null;
		protected $cacheWrite = null;
		protected $cacheRemove = null;
		
		protected function checkLocalCache($key = '', $writemode = false)
		{		
			if ($this->session_id == '') return '';
			$dir = 'cache'.DIRECTORY_SEPARATOR.$this->session_id;
			$path = $dir.DIRECTORY_SEPARATOR.($key == '' ? '_' : $key);
			
			if (file_exists($path)) return $path;
						
			if (!$writemode) return '';

			if (!is_dir('cache')) mkdir('cache');
				
			if (is_dir('cache'))
			{
				if (!is_writeable('cache')) return '';
				
				if (!is_dir($dir)) mkdir($dir);
				if (is_dir($dir))
				{
					if (!is_writeable($dir)) return ''; 

					return $path;
				}
			}

			return '';
		}
		
		protected function readCache($key = '')
		{
			if ($this->session_id == '') return '';
			
			//  attempt to call external cache reader
			$obj = $this->cacheManager;
			$proc = $this->cacheRead;
			
			if (isset($proc))
			{
				if (isset($obj) && is_object($obj) && method_exists($obj, $proc)) return $obj->$proc($key);
				else if (function_exists($proc)) return $proc($key);
				
				return '';
			}
			
			//  fallback to default cache reading routine
			$path = $this->checkLocalCache($key);
			if ($path == '') return '';
			try 
			{
				return file_get_contents($path);
			}
			catch (Exception $e)
			{
				return '';
			}
		}
		
		protected function writeCache($key = '', $content)
		{
			if ($this->session_id == '') return false;
			
			//  attempt to call external cache writer
			$obj = $this->cacheManager;
			$proc = $this->cacheWrite;
			
			if (isset($proc))
			{
				if (isset($obj) && is_object($obj) && method_exists($obj, $proc)) return $obj->$proc($key, $content);
				else if (function_exists($proc)) return $proc($key, $content);
				
				return false;
			}
			
			//  fallback to default cache writing routine
			$path = $this->checkLocalCache($key, true);
			if ($path == '') return false;
			file_put_contents($path, $content);
			return true;
		}
		
		protected function removeCache($key = '', $all = false)
		{
			if ($this->session_id == '') return false;
			
			//  attempt to call external cache remover
			$obj = $this->cacheManager;
			$proc = $this->cacheRemove;
			if (isset($proc))
			{
				if (isset($obj) && is_object($obj) && method_exists($obj, $proc)) return $obj->$proc($key, $all);
				else if (function_exists($proc)) return $proc($key, $all);
				
				return false;
			}
			
			//  fallback to default cache writing routine
			$dir_cache = new DirectoryIterator('cache');
			foreach ($dir_cache as $file_cache)
			{
				if (!$file_cache->isDot())
				{
					if ($all) unlink('cache'.DIRECTORY_SEPARATOR.$file_cache->getFilename());
					else 
					{
						if ($file_cache->getFilename() == $this->session_id.($key == '' ? '' : '_'.$key)) 
						{
							unlink('cache'.DIRECTORY_SEPARATOR.$file_cache->getFilename());
						}
					}
				}
			}
			
			return true;
		}
		
		//---------------------------------------------------------------------------------------------
		//  semaphores
		
		function tryEnterCriticalSection($id)
		{
			$key = 'lock'.($id == '' ? '' : '_'.$id);
			$value = $this->readCache($key);
			if ($value == '') 
			{
				$this->writeCache($key, getmypid());
				return true;
			}
			else return false;
		}
		
		function enterCriticalSection($id)
		{
			while (!$this->tryEnterCriticalSection($id)) usleep(10000);
		}
		
		function leaveCriticalSection($id)
		{
			$key = 'lock'.($id == '' ? '' : '_'.$id);
			$this->removeCache($key);
		}
		
		//---------------------------------------------------------------------------------------------
		//  form vars state caching
		
		protected function serializeForms()
		{
			//  serialize clientScreen
			if (isset($this->clientScreen) && is_object($this->clientScreen))
			{
				$s = serialize($this->clientScreen);
				$this->writeCache('clientScreen', $s);
			}
			
			//  serialize themes
			$s = serialize($this->themes);
			$this->writeCache('themes', $s);
			
			//  serialize desktop
			if (isset($this->desktop) && $this->desktop instanceof TDesktop)
			{
				$dsk_codefile = $this->desktop->CodeFile;				
				$dsk_properties = base64_encode(serialize($this->desktop));
				$dsk_cache = array('codefile' => $dsk_codefile, 'properties' => $dsk_properties, 'currentViewport' => $this->currentViewport);
				
				$this->writeCache('desktop', json_encode($dsk_cache));
			}
			
			//  serialize forms
			$frm_array = array();
			foreach ($this->forms as $key => $frm)
			{
				if (is_object($frm) && $frm instanceof TForm)
				{
					$frm_codefile = $frm->CodeFile;
					$frm_properties = base64_encode(serialize($frm));
					$frm_cache = array('codefile' => $frm_codefile, 'properties' => $frm_properties);
					$this->writeCache($frm->Name, json_encode($frm_cache));
				}
				
				$frm_array[] = $key;
			}
			
			//  save forms array
			$this->writeCache('forms', serialize($frm_array));
			
			//  serialize form handlers
			$s = serialize($this->handlers);
			$this->writeCache('handlers', $s);
			
			//  serialize timers
			$s = serialize($this->timers);
			$this->writeCache('timers', $s);
			
			//  serialize files
			$s = serialize($this->files);
			$this->writeCache('files', $s);
			
			$s =	'<cache>'."\n";
			
			/*
			if ($this->clientScreen != null)
			{				
				$s .=	'	<clientScreen>'."\n".
						'		<Orientation>'.$this->clientScreen->Orientation.'</Orientation>'."\n".
						'		<Width>'.$this->clientScreen->Width.'</Width>'."\n".
						'		<Height>'.$this->clientScreen->Height.'</Height>'."\n".
						'		<AvailWidth>'.$this->clientScreen->AvailWidth.'</AvailWidth>'."\n".
						'		<AvailHeight>'.$this->clientScreen->AvailHeight.'</AvailHeight>'."\n";
				if ($this->clientScreen->TouchCapable)
					$s .= 	'		<TouchCapable>true</TouchCapable>'."\n";
				else
					$s .= 	'		<TouchCapable>false</TouchCapable>'."\n";
				$s .=	'	</clientScreen>'."\n";				
			}	
			*/
			
			//  themes serialization
			/*
			$s .=	'	<themes>'."\n";
			foreach ($this->themes as $name => $theme)
			{
				$s .=	'		<'.$name.'>'.base64_encode(serialize($theme)).'</'.$name.'>'."\n";
			}
			$s .=	'	</themes>'."\n";
			*/
					
			/*
			$dsk_codefile = '';
			$dsk_properties = '';
			if ($this->desktop != null)
				if ($this->desktop instanceof TDesktop)
				{
					$dsk_codefile = $this->desktop->CodeFile;
					$dsk_properties = base64_encode(serialize($this->desktop));
				}
				
			$s .=	'	<desktop>'."\n";
			$s .=	'		<codefile>'.$dsk_codefile.'</codefile>'."\n";
			$s .=	'		<properties>'.$dsk_properties.'</properties>'."\n";
			$s .=	'		<currentViewport>'.$this->currentViewport.'</currentViewport>'."\n";
			$s .=	'	</desktop>'."\n";
			*/
		
			/*
			$s .=	'	<forms>'."\n";
			foreach($this->forms as $frm)
			{
				$s .=	'		<'.$frm->Name.'>'."\n".
						'			<codefile>'.$frm->CodeFile.'</codefile>'."\n".
						'			<properties>'.base64_encode(serialize($frm)).'</properties>'."\n".
						'		</'.$frm->Name.'>'."\n";
			}
			$s .=	'	</forms>'."\n";
			
			$s .=	'	<handlers>'.base64_encode(serialize($this->handlers)).'</handlers>'."\n";
			
			$s .=	'	<files>'.serialize($this->files).'</files>'."\n";
			
			$s .=	'	<timers>'."\n";
			foreach ($this->timers as $timer)
			{
				$s .=	'	<timer>'."\n".
						'		<form>'.$timer['form'].'</form>'."\n".
						'		<method>'.$timer['method'].'</method>'."\n".
						'		<interval>'.$timer['interval'].'</interval>'."\n".
						'	</timer>'."\n";
			}
			$s .=	'	</timers>'."\n";*/
			$s .=	'</cache>'."\n";

			//  to do
			//  error handling when there can't be written the information
			//$res_count = file_put_contents('cache'.DIRECTORY_SEPARATOR.$this->session_id, $s);
			return $this->writeCache('', $s);
		}
	
		protected function createFromCache()
		{
			//  read clientScreen from cache
			$cache = $this->readCache('clientScreen');
			if ($cache != '') $this->clientScreen = unserialize($cache);	
			
			//  unserialize themes
			$cache = $this->readCache('themes');
			if ($cache != '') $this->themes = unserialize($cache);
			
			//  unserialize desktop
			$cache = $this->readCache('desktop');
			if ($cache != '')
			{
				$dsk_cache = json_decode($cache, true);
				if (is_array($dsk_cache) && isset($dsk_cache['codefile']) && isset($dsk_cache['properties']))
				{
					$dsk_codefile = $dsk_cache['codefile'];
					$dsk_properties = $dsk_cache['properties'];
					
					if ($dsk_codefile != '')
					{
						require_once $dsk_codefile;
						$dsk = unserialize(base64_decode($dsk_properties));
						if ($dsk != null)
						{
							$this->desktop = $dsk;
						}
					}
					
					if (isset($dsk_cache['currentViewport'])) $this->currentViewport = $dsk_cache['currentViewport'];
				}
			}
			
			//  unserialize forms
			$cache = $this->readCache('forms');
			if ($cache != '')
			{
				$frm_array = unserialize($cache);
				
				foreach ($frm_array as $frm_name)
				{
					//  fill internal forms array just with placeholders, nothing more
					//  forms will be loaded on first access
					$this->forms[$frm_name] = 'placeholder';					
				}
			}
			
			//  unserialize handlers
			$cache = $this->readCache('handlers');
			if ($cache != '') $this->handlers = unserialize($cache);
			
			//  unserialize timers
			$cache = $this->readCache('timers');
			if ($cache != '') $this->timers = unserialize($cache);
			
			//  unserialize files
			$cache = $this->readCache('files');
			if ($cache != '') $this->files = unserialize($cache);
			
			
			$cache = $this->readCache('');
			if ($cache == '') return false;
			
			$xml = simplexml_load_string($cache);		
			
			foreach ($xml->children() as $xml_node)
			{
				switch ($xml_node->getName())
				{
					/*case 'clientScreen':
						if (!isset($this->clientScreen)) $this->clientScreen = new stdClass();
						$this->clientScreen->Orientation = (string)$xml_node->Orientation;
						$this->clientScreen->Width = (int)$xml_node->Width;
						$this->clientScreen->Height = (int)$xml_node->Height;
						$this->clientScreen->AvailWidth = (int)$xml_node->AvailWidth;
						$this->clientScreen->AvailHeight = (int)$xml_node->AvailHeight;
						$this->clientScreen->TouchCapable = false;
						if ((string)$xml_node->TouchCapable == 'true') $this->clientScreen->TouchCapable = true;
						break;*/
					/*case 'themes':
						foreach($xml_node->children() as $xml_theme)
						{							
							$name = $xml_theme->getName();
							$theme = unserialize(base64_decode((string)$xml_theme));
							$this->themes[$name] = $theme;
						}
						break;*/
					/*case 'desktop':
						$this->currentViewport = (string)$xml_node->currentViewport;
						$dsk_codefile = (string)$xml_node->codefile;
						$dsk_properties = (string)$xml_node->properties;
						
						if ($dsk_codefile != '')
						{
							require_once $dsk_codefile;
							$dsk = unserialize(base64_decode($dsk_properties));
							if ($dsk != null)
							{
								$this->desktop = $dsk;
							}
						}
						
						break;*/
					/*case 'forms':
						foreach($xml_node->children() as $xml_frm)
						{							
							$codefile = $xml_frm->codefile;
							$properties = $xml_frm->properties;
						
							require_once $codefile;
							$frm = unserialize(base64_decode($properties));
							if ($frm != null)
							{
								$this->forms[$frm->Name] = $frm;
							}
						}
						break;*/
					/*case 'handlers':
						$b64_handlers = (string)$xml_node;
						$this->handlers = unserialize(base64_decode($b64_handlers));
						break;
					case 'files':
						$this->files = unserialize((string)$xml_node);
						break;
					case 'timers':
						foreach ($xml_node->children() as $xml_timer)
						{
							$formname = (string)$xml_timer->form;
							$method = (string)$xml_timer->method;
							$interval = (int)$xml_timer->interval;
							
							if (isset($this->forms[$formname]))
							{
								$frm = $this->forms[$formname];
								if ($frm != null) $this->registerTimer($frm, $method, $interval);
							}
						}
						break;*/
				}
			}		
		
			return true;
		}
		
		function clearCache($all = false)
		{
			$this->removeCache('', $all);
			$this->browserReset();
			$this->skipCache = true;
		}
	
		protected function uncacheForm($frm_name)
		{
			$cache = $this->readCache($frm_name);
			if ($cache != '')
			{
				$frm_cache = json_decode($cache, true);
				if (is_array($frm_cache) && isset($frm_cache['codefile']) && isset($frm_cache['properties']))
				{
					$frm_codefile = $frm_cache['codefile'];
					$frm_properties = $frm_cache['properties'];
						
					require_once($frm_codefile);
					$frm = unserialize(base64_decode($frm_properties));
					if ($frm != null) $this->forms[$frm_name] = $frm;
				}
			}
		}
		
		//---------------------------------------------------------------------------------------------
		
		
		var $callStack = '';
		function traceCallStack($object = null, $method = null)
		{
			$this->callStack = '';
			
			$dbg = debug_backtrace();
			
			for ($i = count($dbg) - 1; $i > 0; $i--)
			{
				$item = $dbg[$i];
				
				$file = $item['file'];
				$class = isset($item['class']) ? $item['class'] : '';
				$line = $item['line'];
				$function = $item['function'];
				$type = isset($item['type']) ? $item['type'] : '';
				
				$this->callStack .= '<span class="debugBlock">'.$class.$type.$function.'</span><br/>';
			}
			
			if (isset($object) && isset($method))
			{
				if ($object == null) $this->callStack .= $method;
				else if (is_object($object)) $this->callStack .= '<span class="debugBlock">'.get_class($object).'->'.$method.'</span><br/>'; 
			}
		}
		
		//-------------------------------------------------------------------------------------------------------
		//  Ajax Stack functions
		function addAjaxStack($target, $action, $content)
		{
			//global $AjaxStack;
		
			$a = Array();
			$a['target'] = $target;
			$a['action'] = $action;
			$a['content'] = base64_encode($content);
		
			$this->AjaxStack[] = $a;
		}
		
		//  specific ajax stack functions
		function browserReportCallStack($message)
		{
			$callstack = '';
			
			$dbg = debug_backtrace();
				
			for ($i = count($dbg) - 1; $i > 0; $i--)
			{
				$item = $dbg[$i];
			
				$file = $item['file'];
				$class = isset($item['class']) ? $item['class'] : '';
				$line = $item['line'];
				$function = $item['function'];
				$type = isset($item['type']) ? $item['type'] : '';
			
				$callstack .= $class.$type.$function."\n";
			}
				
			$this->browserAlert($message."\n".$callstack);
		}
		
		function browserAlert($message)
		{
			$this->addAjaxStack('', 'alert', $message);
		}
		
		function browserAppend($htmlID, $content)
		{
			$this->addAjaxStack($htmlID, 'append', $content);
		}
		
		function browserDelete($htmlID)
		{
			$this->addAjaxStack($htmlID, 'delete', '');
		}
		
		function browserUpdate($htmlID, $content)
		{
			$this->addAjaxStack($htmlID, 'update', $content);
		}
		
		function browserReplace($htmlID, $content)
		{
			$this->addAjaxStack($htmlID, 'replace', $content);
		}
		
		function browserAddClass($htmlID, $className)
		{
			$this->addAjaxStack($htmlID, 'addClass', $className);
		}
		
		function browserRemoveClass($htmlID, $className)
		{
			$this->addAjaxStack($htmlID, 'removeClass', $className);
		}
		
		function browserSetStyle($htmlID, $content)
		{
			$this->addAjaxStack($htmlID, 'setStyle', $content);
		}
		
		function browserScript($script)
		{
			$this->addAjaxStack('', 'script', $script);
		}
		
		function browserDownload($filename, $content)
		{
			file_put_contents('cache/'.$filename, $content);
			$this->addAjaxStack('', 'download', qPath.'/download.php?filename='.$filename);
		}
		
		function browserReset()
		{
			$this->addAjaxStack('', 'reset', '');
		}
		
		protected function sendAjaxResponse()
		{
			echo json_encode($this->AjaxStack);
		}
		
		function MessageDlg($Text, $Caption, $Buttons, $CallbackObject, $CallbackMethod)
		{
			try
			{
				$codefile = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'MessageDlg.php';
				if (file_exists($codefile))
				{
					//  create the handle
					$CallbackHandle = $this->registerHandler($CallbackObject, $CallbackMethod);
					
					$msgdlg = new TMessageDlg($Text, $Caption, $Buttons, $CallbackHandle, 'disponibil');
					$msgdlg->contextID = 0;
					$msgdlg->ClassName = 'TMessageDlg';
					$msgdlg->CodeFile = $codefile;
					$msgdlg->OnLoad();
					
					if ($msgdlg != null) 
					{
						$this->forms[$msgdlg->Name] = $msgdlg;
						$msgdlg->showModal();
					}
				}
			}
			catch (Exception $e)
			{
				$this->addAjaxStack('', 'alert', 'An error occured while loading message dialog.');
			}			
		}
				
		//-------------------------------------------------------------------------------------------------------
		//  Handlers management
		function registerHandler(TComponent &$obj, $method_name)
		{
			if (!isset($obj)) return '';
			if (!($obj instanceof TComponent)) return '';			
			if (!method_exists($obj, $method_name)) return '';
			
			//  create handle id
			$handleid = '$handle_'.count($this->handlers);
			
			$this->handlers[$handleid] = Array();
			$this->handlers[$handleid]['form'] = $obj->getParentForm()->Name;
			$this->handlers[$handleid]['object_ref'] = $obj->id;			
			$this->handlers[$handleid]['method_name'] = $method_name;			
			$this->handlers[$handleid]['handle'] = $handleid;			
			
			return $handleid;
		}
		
		function callHandler($handle, $sender, $varName, $varValue)
		{
			if (!isset($this->handlers[$handle])) return false;
			
			$frm_name = $this->handlers[$handle]['form'];
			$frm = $this->getForm($frm_name);
			if ($frm != null)
			{
				$object = $frm->getControlbyID($this->handlers[$handle]['object_ref']);
				$method_name = $this->handlers[$handle]['method_name'];
				
				if (!method_exists($object, $method_name)) return false;
				
				//  call the event
				//if ($this->debugJS) $this->traceCallStack($frm, $event);
				if ($sender !== null && $varName !== null && $varValue !== null) $object->$method_name($sender, $varName, $varValue);
				else if ($varName !== null && $varValue !== null) $object->$method_name($varName, $varValue);
				else if ($sender !== null) $object->$method_name($sender);
				else $object->$method_name();				
			}			
		}
		
		protected function handleFileUpload()
		{
			if (!isset($_FILES['Filedata'])) return false;
			
			$tmp_file_name = $_FILES['Filedata']['tmp_name'];
			$filename = basename($tmp_file_name);
			$ok = move_uploaded_file($tmp_file_name, 'cache/'.$filename);
			
			if ($ok)
			{
				$name = $_FILES['Filedata']['name'];
				$type = $_FILES['Filedata']['type'];
				$tmp_name = 'cache/'.$filename;
				$size = $_FILES['Filedata']['size'];
				$this->files[] = array('name' => $name, 'type' => $type, 'tmp_name' => $tmp_name, 'size' => $size);
			}
			
			// This message will be passed to 'oncomplete' function
			echo $ok ? "OK" : "FAIL";
			
			return true;
		}
		
		function retrieveUploadedFile()
		{
			if (count($this->files) > 0) 
			{
				$file = $this->files[0];
				unset($this->files[0]);
				$this->files = array_values($this->files);
				
				return $file;
			}
		}
		
		protected function handleFormEvent($event, $frm_name, $sender, $varName, $varValue)
		{
			//  retrieve the form object
			$frm = null;
			if ($frm_name == "frm_DebugForm") $frm = $this->debugForm;
			if ($frm_name == $this->desktop->Name) $frm = $this->desktop;
			if (key_exists($frm_name, $this->forms)) $frm = $this->getFormPointer($frm_name);
			//if (isset($this->forms[$frm_name])) $frm = $this->forms[$frm_name];
			if ($frm == null) return;
			
			//  update form fields
			foreach($_REQUEST as $key => $value)
			{
				if ($key != 'callBack' && $key != 'form' && $key != 'event' &&
				$key != 'sender' && $key != 'varName' && $key != 'varValue')
				{
					if (isset($frm->$key) && method_exists($frm->$key, 'setValue')) $frm->$key->setValue($value);
				}
			}
			
			$object = null;
			$method_name = '';
				
			//  check handlers
			if (isset($this->handlers[$event]) && $this->handlers[$event]['form'] == $frm_name)
			{
				$object = $frm->getControlbyID($this->handlers[$event]['object_ref']);
				$method_name = $this->handlers[$event]['method_name'];
			}
			else 
			{
				$object = $frm;
				$method_name = $event;
			}
			
			if (!method_exists($object, $method_name)) return;

			//  call the event
			if ($this->debugJS) $this->traceCallStack($frm, $event);
			if ($sender !== null && $varName !== null && $varValue !== null) $object->$method_name($sender, $varName, $varValue);
			else if ($varName !== null && $varValue !== null) $object->$method_name($varName, $varValue);
			else if ($sender !== null) $object->$method_name($sender);
			else $object->$method_name();
		}
		
		function run($contextManager = null, $contextMain = null, $contextCallBack = null, $desktopFile = null,
					$cacheManager = null, $cacheRead = null, $cacheWrite = null, $cacheRemove = null)
		{
			$this->FcontextManager = $contextManager;
			$this->FcontextMain = $contextMain;
			$this->FcontextCallBack = $contextCallBack;
			$this->cacheManager = $cacheManager;
			$this->cacheRead = $cacheRead;
			$this->cacheWrite = $cacheWrite;
			$this->cacheRemove = $cacheRemove;
			
			if (!defined('QOS_ENV_RUN')) $this->enterCriticalSection('');
			switch ($this->createFromCache())
			{
				case false:
					require_once 'qbase/browscap.php';
					$this->checkBrowser();
					
					$this->initDesktop($desktopFile);
					$this->sendDebugFlag();

					if (isset($contextMain))
					{
						if (isset($contextManager) && is_object($contextManager) && method_exists($contextManager, $contextMain)) $contextManager->$contextMain();
						else if (function_exists($contextMain)) $contextMain();
					}
					break;
				case true:
					switch ($this->IsCallBack)
					{
						case false:
							if ($this->handleFileUpload()) break;

							$this->initDesktop($desktopFile);
							$this->sendDebugFlag();
								
							foreach(array_keys($this->forms) as $frm_name)
							{
								$frm = $this->getFormPointer($frm_name);
								if (!isset($frm)) continue;
								if (!$frm->Visible) continue;
								
								switch ($frm->IsModal)
								{
									case false:
										echo $frm->show();
										break;
									case true:
										echo $frm->showModal();
										break;
								}
							}
								
							break;
						case true:
							$this->sendDebugFlag();
							
							//  it appears that clientScreen is not always sent back to server
							if ($this->clientScreen == null) $this->browserScript('callBack("setClientScreen", "", undefined, undefined, undefined, clientScreen);');
								
							//  retrieve callback arguments
							$frm_name = $_REQUEST['form'];
							$event = $_REQUEST['event'];
							
							$sender = null; 
							if (isset($_REQUEST['sender'])) 
							{
								$sender = $_REQUEST['sender'];
								$a = split('\.', $sender);
								if(count($a) >= 2)
								{
									//$parent = $a[0];
									$sender = $a[1];		
								}									
							}
							
							$varName = null; if (isset($_REQUEST['varName'])) $varName = $_REQUEST['varName'];
							$varValue = null; if (isset($_REQUEST['varValue'])) $varValue = $_REQUEST['varValue'];
								
							switch ($event)
							{
								case 'systemTimer':
									$this->systemTimer();
									break;
								case 'setClientScreen':
									$post_body = file_get_contents('php://input');
									$obj = json_decode($post_body);
									$this->clientScreen = $obj;
									break;
								default:									
									if ($frm_name == '') break;
									$this->handleFormEvent($event, $frm_name, $sender, $varName, $varValue);									
									if ($this->debugJS) $this->addAjaxStack('', 'debugStack', $this->callStack);									
									break;
							}
								
							$this->updateThemes();
							$this->sendAjaxResponse();
								
							break;
					}
						
					break;
			}
			
			if (!$this->skipCache) $this->serializeForms();
			if (!defined('QOS_ENV_RUN')) $this->leaveCriticalSection('');	
		}
		
		//-------------------------------------------------------------------------------------------------------
		//  Timers management  
		protected function systemTimer()
		{
			//  until timer registrations will be possible, this will explicitly call refreshing on the desktop
			
			if ($this->desktop != null)
				if ($this->desktop instanceof TDesktop)
					$this->desktop->refreshThumbs();
				
			foreach ($this->timers as $timer)
			{
				try
				{
					$frm_name = $timer['form'];
					$method = $timer['method'];
					
					$frm = $this->getFormPointer($frm_name);
					//$frm = $this->forms[$form_name];
					if ($frm != null)
						$frm->$method();
				}
				catch (Exception $exc)
				{
					
				}
			}
		}
		
		function registerTimer($form, $method, $interval)
		{
			$timer = Array
			(
				'form' => $form->Name,
				'method' => $method,
				'interval' => $interval
			);
			
			$this->timers[] = $timer;
		}
		
	}
	

?>
