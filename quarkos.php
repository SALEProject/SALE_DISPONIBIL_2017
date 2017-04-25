<?php

	require_once 'stats.php';
	require_once 'registry.php';
	require_once 'router.php';
	require_once 'application.php';
	require_once 'context.php';

	class TQuarkOS
	{
		static private $Finstance = null;
		
		static function instance()
		{
			if (self::$Finstance != null) return self::$Finstance;
				
			self::$Finstance = new TQuarkOS();
			return self::$Finstance;
		}
		
		protected $FbasePath = '';
		private $loginName = '';
		private $FRegistry = null;
		private $FRouter = null;
		private $contexts = Array();
		
		function __construct()
		{
		}
		
		function __get($name)
		{
			if (method_exists($this, ($method = 'get_'.$name)))
			{
				//if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, $method);
				return $this->$method();
			}
			else return;				
		}
		
		function get_basePath()
		{
			if (!empty($this->FbasePath)) return $this->FbasePath;
			
			//  basePath appears to not be set, it will be determined
			$uri = $_SERVER['REQUEST_URI'];
			$script = $_SERVER['SCRIPT_NAME'];
			$filename = basename($_SERVER['SCRIPT_FILENAME']);
				
			$len_uri = strlen($uri);
			$len_script = strlen($script);
				
			$i = 0;
			while ($i < $len_uri && $i < $len_script)
			{
				if ($uri[$i] != $script[$i]) break;
				$i++;
			}
				
			$basePath = substr($uri, 0, $i);
			$i = strpos($basePath, $filename);
			if ($i !== false) $basePath = substr($basePath, 0, $i);
			if (strlen($basePath) > 0 && ($basePath[strlen($basePath) - 1] == '/' || $basePath[strlen($basePath) - 1] == '\\'))
				$basePath = substr($basePath, 0, -1);
			
			$this->FbasePath = $basePath;			
			return $basePath;
		}
		
		function get_Registry()
		{
			return $this->FRegistry;
		}
		
		function get_Router()
		{
			return $this->FRouter;
		}
		
		function getValidContextID()
		{
			$contextID = 0;
			$found = false;

			do 
			{
				$found = true;
				$contextID++;
				foreach ($this->contexts as $context)
				{
					if ($context->contextID == $contextID) $found = false;
				}	
			} while (!$found);
			
			return $contextID;
		}
		
		var $PATH = array('sysutils', 'applications');
		
		protected function searchApplicationFile($url)
		{			
			$info = pathinfo($url);
			
			$workdir = $info['dirname'];
			$filename = $info['filename'];
			$contextfile = $filename.'.xml';
			$codefile = $filename.'.php';
			$contextpath = '';
			$codepath = '';
			
			$paths = array($workdir);
			foreach ($this->PATH as $p) $paths[] = $p;
	
			if (isset($info['extension']))
			{
				if ($info['extension'] != 'php' && $info['extension'] != 'xml') return '';
			}
			//else $paths[] = $workdir.DIRECTORY_SEPARATOR.$info['filename'];
			
			foreach ($paths as $path)
			{
				$workdir = $path; 			
				$contextpath = $workdir.DIRECTORY_SEPARATOR.$contextfile;
				$codepath = $workdir.DIRECTORY_SEPARATOR.$codefile;
				
				if (isset($info['extension']) && $info['extension'] == 'xml')
				{
					//  in this case context file must exist
					if (file_exists($contextpath) && file_exists($codepath)) return $codepath;				
				}
				else if (file_exists($codepath)) return $codepath;
				else if (!isset($info['extension']) && file_exists($workdir.DIRECTORY_SEPARATOR.$filename))
				{
					//maybe filename is a folder containing the application
					$workdir .= DIRECTORY_SEPARATOR.$filename;
					if (file_exists($workdir.DIRECTORY_SEPARATOR.'application.php')) return $workdir.DIRECTORY_SEPARATOR.'application.php';
				}
			}
	
			return '';
		}
		
		function launchApplication($url)
		{
			$codefile = $this->searchApplicationFile($url);
			if ($codefile == '') return;

			$contextfile = dirname($codefile).DIRECTORY_SEPARATOR.basename($codefile, '.php');
			$contextfile .= '.xml';
						
			if (file_exists($codefile))
			{
				$classes = get_declared_classes();
				require_once $codefile;
				$diff = array_diff(get_declared_classes(), $classes);
				
				//  find class name
				$found = false;
				$className = reset($diff);
				while (!empty($className) && !$found)
				{
					if (is_subclass_of($className, 'TApplication')) $found = true;
					else $className = next($diff);
				}

				if (!$found) return; // no point to continue loading

				$app = null;
				$context = null;
				try
				{
					$app = new $className();
					$app->contextID = $this->getValidContextID();
					$app->WorkingDirectory = dirname($codefile);
					
					if (file_exists($contextfile))
					{
						$s = file_get_contents($contextfile);
						$xml = simplexml_load_string($s);
						
						//  nothing else to do at the moment
						//  future idea welcomed
					}

					//  the application is initialized, add it to the contexts
					$context = new TContext();
					$context->contextID = $app->contextID;
					$context->application = $app;
					$context->loginName = $this->loginName;
					$context->WorkingDirectory = $app->WorkingDirectory;
					$context->codeFile = $codefile;
					$this->contexts[] = $context; 
					
					$app->main();
				}
				catch (Exception $e)
				{
					$this->addAjaxStack('', 'alert', 'An error occured while loading form.');
				}
			}
		}
		
		function removeContext($contextID)
		{
			if (class_exists('TQuark') && TQuark::instance()->debugJS) TQuark::instance()->traceCallStack();
				
			$found = false;
			$context = reset($this->contexts);
			while(!empty($context) && !$found)
			{
				if ($context->contextID == $contextID) $found = true;
				else $context = next($this->contexts);
			}
			
			if ($found)
			{
				unset($this->contexts[key($this->contexts)]);
				$this->contexts = array_filter($this->contexts);
			}
		}

		//---------------------------------------------------------------------------------------------
		//  caching functions
		
		protected $session_id = '';
		
		protected function checkLocalCache($key = '', $writemode = false)
		{		
			if ($this->session_id == '') return '';
			$path = 'cache'.DIRECTORY_SEPARATOR.$this->session_id.($key == '' ? '' : '_'.$key);
			
			if (file_exists($path)) return $path;
			
			if ($writemode)
			{
				if (is_dir('cache'))
				{
					if (is_writeable('cache')) return $path;
					return '';
				}
				
				mkdir('cache');
				if (is_dir('cache')) return $path;
			}
			
			return '';
		}
		
		protected function readCache($key = '')
		{
			if ($this->session_id == '') return '';
			
			//  TODO - call a cache plugin here
			//  ----
						
			//  fallback to default cache reading routine
			$path = $this->checkLocalCache($key);
			if ($path == '') return '';
			try 
			{
				if (file_exists($path)) return file_get_contents($path);
				else return '';
			}
			catch (Exception $e)
			{
				return '';
			}
		}
		
		protected function writeCache($key = '', $content)
		{
			if ($this->session_id == '') return false;
			
			//  TODO - call a cache plugin here
			//  ----
			
			//  fallback to default cache writing routine
			$path = $this->checkLocalCache($key, true);
			if ($path == '') return false;
			file_put_contents($path, $content, LOCK_EX);
			return true;
		}
		
		protected function removeCache($key = '', $all = false)
		{
			if ($this->session_id == '') return false;
			
			//  TODO - call a cache plugin here
			//  ----
			
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
				
				//  check value to be my pid
				if ($this->readCache($key) == getmypid()) 
				{ 
					//file_put_contents('cache/log', getmypid().' tryEnterCriticalSection ret true '.var_export($_REQUEST, true)."\n", FILE_APPEND); 
					return true; 
				}
				else 
				{ 
					//file_put_contents('cache/log', getmypid().' tryEnterCriticalSection ret false'.var_export($_REQUEST, true)."\n", FILE_APPEND); 
					return false; 
				}
			}
			else 
			{ 
				//file_put_contents('cache/log', getmypid().' tryEnterCriticalSection ret false'.var_export($_REQUEST, true)."\n", FILE_APPEND); 
				return false; 
			}
		}
		
		function enterCriticalSection($id)
		{
			//file_put_contents('cache/log', getmypid().' enterCriticalSection'.var_export($_REQUEST, true)."\n", FILE_APPEND);
			while (!$this->tryEnterCriticalSection($id)) usleep(10000);
		}
		
		function leaveCriticalSection($id)
		{
			//file_put_contents('cache/log', getmypid().' leaveCriticalSection'.var_export($_REQUEST, true)."\n", FILE_APPEND);
			$key = 'lock'.($id == '' ? '' : '_'.$id);
			$this->removeCache($key);
		}
		
		//---------------------------------------------------------------------------------------------
		//  contexts caching
		
		function serializeContexts()
		{		
			$s =	'<cache>'."\n";
		
			$s .=	'	<registry>'.base64_encode(serialize($this->registry)).'</registry>'."\n";
			$s .=	'	<router>'.base64_encode(serialize($this->router)).'</router>'."\n";

			$s .=	'	<contexts>'."\n";
			foreach($this->contexts as $context)
			{
				$s .=	'		<context>'."\n".
						'			<contextID>'.$context->contextID.'</contextID>'."\n".
						'			<WorkingDirectory>'.$context->WorkingDirectory.'</WorkingDirectory>'."\n".
						'			<codeFile>'.$context->codeFile.'</codeFile>'."\n".
						'			<loginName>'.$context->loginName.'</loginName>'."\n".
						'			<application>'.base64_encode(serialize($context->application)).'</application>'."\n".
						'		</context>'."\n";
			}
			$s .=	'	</contexts>'."\n";
				
			$s .=	'</cache>'."\n";
		
			return $this->writeCache('qos', $s);
		}
		
		function createFromCache()
		{
			$cache = $this->readCache('qos');
			if ($cache == '') return false;
		
			$xml = simplexml_load_string($cache);
				
			foreach ($xml->children() as $xml_node)
			{
				switch ($xml_node->getName())
				{
					case 'registry':
						$this->FRegistry = unserialize(base64_decode((string)$xml_node));
						break;
					case 'router':
						$this->FRouter = unserialize(base64_decode((string)$xml_node));
						break;
					case 'contexts':
						foreach($xml_node->children() as $xml_context)
						{
							$contextID = (int)$xml_context->contextID;
							$WorkingDirectory = (string)$xml_context->WorkingDirectory;
							$codefile = (string)$xml_context->codeFile;
							$loginName = (string)$xml_context->loginName;
							$app = (string)$xml_context->application;
		
							//$codefile = $WorkingDirectory.DIRECTORY_SEPARATOR.'application.php';
							require_once $codefile;
							$application = unserialize(base64_decode($app));
							if ($application != null)
							{
								$context = new TContext();
								$context->contextID = $contextID;
								$context->loginName = $loginName;
								$context->WorkingDirectory = $WorkingDirectory;
								$context->codeFile = $codefile;
								$context->application = $application;
								
								$this->contexts[] = $context;
							}
						}
						break;
				}
			}
		
			return true;
		}
		
		function run()
		{			
			define('QOS_ENV_RUN', true);
			TStats::instance()->record('qos_run_start');
			
			session_start();		
			//session_write_close(); //  unlock session file for other requests to happen asynchronously
			$this->session_id = session_id();
			
			TStats::instance()->record('qos_session_start');			
			ob_start();
			
			//  find out if quark GUI exists
			$load_quarkGUI = false;
			if (file_exists('quark'.DIRECTORY_SEPARATOR.'quark.php')) 
			{
				$load_quarkGUI = true;
				require_once 'quark'.DIRECTORY_SEPARATOR.'quark.php'; //  load quark class references
				TStats::instance()->record('qos_GUI_loaded');
			}						
			
			$this->enterCriticalSection(''); //   working with cache 
					
			//  initialize registry, router and contexts
			$this->createFromCache();
			if (!isset($this->FRegistry)) $this->FRegistry = TRegistry::instance();
			if (!isset($this->FRouter)) { $this->FRouter = TRouter::instance(); $this->FRouter->setBasePath($this->basePath); }	
			
			//  match route
			$route = TRouter::instance()->match();
			$target = 'default';
			if (is_array($route) && isset($route['target'])) $target = $route['target'];
			//file_put_contents('cache/match', var_export($route, true)."\n", FILE_APPEND);
			
			//  here should be the part where the actual called service would initialize then run
			//  for now we take a shortcut
			if ($target == 'rpc.php') require_once 'rpc.php';
			else 
			{
				//  load GUI environment and run main GUI or else
				TStats::instance()->record('qos_main_start');
				
				if ($load_quarkGUI)
				{
					TQuark::instance()->debugJS = true;
					TQuark::instance()->addTheme('disponibil', 'themes/disponibil');
				
					TQuark::instance()->run($this, 'main_GUI', 'getContext', 'desktops/disponibil.xml');			
				}		
				else
				{
					//  run the main loop of the OS directly
					$this->main_CLI();
				}
				TStats::instance()->record('qos_main_stop');
			}
				
			$this->serializeContexts();
			$this->leaveCriticalSection(''); //  end working with cache
			
			ob_end_flush();
			TStats::instance()->record('qos_session_flush');
				
			TStats::instance()->record('qos_run_stop');
			TStats::instance()->evaluate('qos_run', 'qos_run_start', 'qos_run_stop', 5);
			TStats::instance()->evaluate('qos_session_start', 'qos_run_start', 'qos_session_start', 5);
			TStats::instance()->evaluate('qos_session_run', 'qos_session_start', 'qos_session_flush', 5);
			TStats::instance()->evaluate('qos_env_init', 'qos_session_start', 'qos_main_start', 5);
			TStats::instance()->evaluate('qos_GUI_load', 'qos_session_start', 'qos_GUI_loaded', 5);
			TStats::instance()->evaluate('qos_env_execute', 'qos_main_start', 'qos_main_stop', 5);
				
			
			return QOS_ERR_OK;
		}
		
		function main_GUI()
		{
			$this->launchApplication('applications/disponibil');
			//if ($this->loginName == '') $this->launchApplication('applications/login');
		}
		
		function main_CLI()
		{
			
		}
		
		function getContext($contextID)
		{
			$b = false;
			$i = -1;
			$context = null;
			while (!$b && $i < count($this->contexts) - 1)
			{
				$i++;
				$context = $this->contexts[$i];
				if ($context->contextID == $contextID) $b = true;	
			}
			
			if ($b) return $context; 
			return null;
		}
		
		function sendMessage($contextID, $msg)
		{
			if ($contextID == 0) 
			{
				//  broadcast
				foreach ($this->contexts as $context)
				{
					if ($context != null) $context->application->processMessage($msg);
				}
			}
			else 
			{			
				$context = $this->getContext($contextID);
				if ($context != null) $context->application->processMessage($msg);
			}
		}
		
	}

?>
