<?php
	
	require_once 'AltoRouter.php';

	class TRouter extends AltoRouter
	{
		static private $Finstance = null;
		
		static function instance()
		{
			if (self::$Finstance == null) self::$Finstance = TQuarkOS::instance()->router;
			if (self::$Finstance != null) return self::$Finstance;
				
			self::$Finstance = new TRouter();
			return self::$Finstance;
		}
		
		function __construct($routes = array(), $basePath = '', $matchTypes = array())
		{
			parent::__construct($routes, $basePath, $matchTypes);
			
			$this->buildRoutes();
		}
						
		function isInstalled()
		{
			if (file_exists('.htaccess')) return true;
			return false;
		}
		
		function enableRouter()
		{
			$s =	'RewriteEngine on'."\n".
					'RewriteCond %{REQUEST_FILENAME} !-f'."\n".
					'RewriteRule . index.php [L]'."\n";
			
			file_put_contents('.htaccess', $s);
		}
		
		function disableRouter()
		{
			if (file_exists('.htaccess')) unlink('.htaccess');
		}
		
		protected function loadDefaultRoutes()
		{
			$reg = TRegistry::instance();
			if ($reg == null) return;
			
			//  add default route here
			$reg->openKey('.routes.slash', true);
			$reg->write('method', 'GET|POST');
			$reg->write('route', '/');
			$reg->write('target', 'default');
			
			$reg->openKey('.routes.index', true);
			$reg->write('method', 'GET|POST');
			$reg->write('route', '/index.php');
			$reg->write('target', 'default');
			
			$reg->openKey('.routes.rpc', true);
			$reg->write('method', 'GET|POST');
			$reg->write('route', '/rpc');
			$reg->write('target', 'rpc.php');
			
			/*$reg->write('/', '');
			 $reg->write('/rpc', 'rpc.php');*/
			
			$reg->openKey('.environment.PATH', true);
			$reg->write('applications', 'applications');
			$reg->write('sysutils', 'sysutils');		
		}
	
		protected function buildRoutes()
		{
			$reg = TRegistry::instance();			
			if ($reg == null) return;
			
			$reg->openKey('.routes');
			if (!$reg->first()) 
			{
				$this->loadDefaultRoutes();
				$reg->openKey('.routes');
			}
			
			//  try again  
			if ($reg->first())
			{
				do 
				{
					$name = $reg->name();
					if ($reg->hasChildren())
					{
						$reg->openCurrent();
						
						$method = $reg->read('method');
						$route = $reg->read('route');
						$target = $reg->read('target');
						
						$this->map($method, $route, $target, $name);
						
						$reg->closeCurrent();
					}
				} while ($reg->next());
			}			
		}
		
	}
	
?>