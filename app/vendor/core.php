<?php 

error_reporting(E_ALL); 
ini_set('display_errors', '1');

$system_path = "./"; if (realpath($system_path) !== FALSE)  $system_path = realpath($system_path).'/'; 

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/').'/';

// Is the system path correct?
if (!is_dir($system_path)) exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));


define('EXT' 		, '.php');
define('SELF'		, pathinfo(__FILE__, PATHINFO_BASENAME)); 
define('BASEPATH' 	, str_replace("\\", "/", $system_path)); 
define('FCPATH'  	, str_replace(SELF, '' , __FILE__    ));
define('SYSDIR'  	, trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

require FCPATH."core_helper".EXT;

class core {

	private $config_file 	  = "app/config/core.json";
	
	public  $page 			  = ""      ;
	private $stacklog 		  = array() ;
	private $routes 		  = array() ; 
	private $encoding 		  = 'UTF-8' ;
	private $default_method   = 'index' ;
	private $default_key      = 'action';
	private $timezone         = 'America/Argentina/Buenos_Aires';
	private $leak	 	 	  = '10M'   ;
	private $error    		  = 'On'    ; 
	private $debug 			  = true   ;
	private $config           = NULL    ;


	private $mime = array
	(
		"json" 		 => "application/json"		,
		"javascript" => "application/javascript",
		"html"		 => "text/html" 			,
		"css"		 => "text/css"
	); 
 
	private static $instancia = NULL    ;

	public static function getInstance()
	{
		$that = NULL;

		if (!self::$instancia instanceof self)
		{
			if(self::$instancia == NULL)
			{
				$that = new self;
				self::$instancia = $that;
			} 
		}
		else
		{
			$that = self::$instancia;
		}

		if($that == NULL)
		{
			die("[core] >> Raise Error >> Not get instance.");
		}

		return $that;
	}

	function __construct() {

		self::$instancia = $this;

		$this->before_load();
		//$this->after_load();
	}

	public function write_log($string)
	{
		$this->stacklog[]=$string;
	}


	public function load_helper($module)
	{
		$HelperFile = BASEPATH."{$module}".EXT; 

		if( file_exists($HelperFile) )
			include $HelperFile;
		else 
			_LOG($this, __CLASS__, "Don't load helper {$HelperFile}");
	}

	public function load_library( $path, $module, $name, $runAfterBoot="")
	{
		include BASEPATH."{$path}".EXT; 

		$this->load($module, $name ? $name : ""); 


		
		if($runAfterBoot) call_user_func( array($this->{$module}, $runAfterBoot) );
	}

	public function cloneIn($object, $array)
	{
		foreach ($array as $item) 
		{
			$object->{$item} = $this->{$item};
		}
	}



	public function after_load() 
	{ 
		$this->config = file_get_json( BASEPATH.$this->config_file );
 
		if( isset($this->config->debug    	) ){ $this->debug 		= $this->config->debug 	; }
		if( isset($this->config->timezone 	) ){ $this->timezone	= $this->config->timezone ; }
		if( isset($this->config->leak 		) ){ $this->leak		= $this->config->leak  	; }
		if( isset($this->config->error 		) ){ $this->error		= $this->config->error 	; }		

		date_default_timezone_set( $this->timezone 				 ); 
		ini_set 				 ( "display_errors", $this->error);
		ini_set 				 ( "memory_limit"  , $this->leak );

		foreach ($this->config->loader as $item)
		{
			if(isset($item->helper))
			{
				$this->load_helper($item->module);
			}
			else
			{
				$this->load_library( $item->path, $item->module,  isset($item->name) ? $item->name : "" );
			}
		} 
	}

	public function load($module, $as = '') {


		if(!class_exists($module))
			include BASEPATH."{$module}".EXT;
		else
			_LOG( $this, __CLASS__ , "{$module} ready load" );

		if ($as)
			if( !isset($this->{$as}) )
				$this->{$as} = new $module();
			else
				_LOG( $this, __CLASS__ , "{$module} [{$as}] ready defined" );
		else
			if( !isset($this->{$module}) )
				$this->{$module} = new $module();
			else
				_LOG( $this, __CLASS__ , "{$module} ready defined" );
	}

	public function json_write($text) 
	{
		header("Content-type: ".$this->mime["json"]."; charset={$this->encoding}");

		$options = JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE; 
		$output  = json_encode($text, $options );

		die($output);
	}

	public function before_run()
	{

	}

	public function after_run()
	{	 
		
		if( $this->debug == TRUE )
		{
			$lines = implode("", $this->stacklog);

			echo
			"
			<div style='position:fixed; bottom:0; left:0; right:0; height:400px; overflow-y:auto; background:#F3F2F2; box-shadow:0px 1px 37px #000; z-index: 9999999999999999; '>
				<div class='panel panel-default'>
					<div class='panel-heading'>Debug</div>

					<ul class='list-group'>
					{$lines}
					</ul>
				</div>
			</div>
			";
		}
	}

	private function get_client_route()
	{

		$uri  = $_SERVER["REQUEST_URI"];

		$file = $_SERVER["SCRIPT_NAME"   ]; 

		$dir  = pathinfo($file,PATHINFO_DIRNAME); 

		$dir  = str_replace("/index.php","", $dir);

		

		$uri  = str_replace($dir."/", "", $uri);

		$uri  = trim($uri, "/");

		


		return $uri;
	}

	private function match_simple()
	{
		$request = $this->get_client_route();
		$found   = FALSE;
		
		foreach ($this->routes as $value) 
		{
			if($request == $value)
			{
				$found = $request;
			}
		}

		return $found;
	}

	private function match_params()
	{
		$request_uri = $this->get_client_route();
		$found 		 = FALSE;
		$return 	 = FALSE;

		foreach ($this->routes as $key=>$pattern_uri)
		{ 
			preg_match_all('/:([0-9a-zA-Z_]+)/', $pattern_uri, $names, PREG_PATTERN_ORDER);
			$names = $names[0];

			$pattern_uri_regex  = preg_replace_callback('/:[[0-9a-zA-Z_]+/', array($this, 'pattern_uri_regex'), $pattern_uri);
			$pattern_uri_regex .= '/?';

 


			if(count($names))
			{
				$params = array();
 
				//var_dump( array( preg_match('@^'.$pattern_uri_regex.'$@', $request_uri, $values) , $request_uri, $pattern_uri_regex ) );	

				if (preg_match('@^'.$pattern_uri_regex.'$@', $request_uri, $values))
				{
					array_shift($values);
 
					foreach($names as $index => $value) 
					{
						$params[substr($value, 1)] = urldecode($values[$index]); 
					}
 	
					$return = new stdclass;
					$return->method = $pattern_uri;
					$return->param  = $params;
					return $return;
				}
			} 
		}

		return $return;
	}
	public function pattern_uri_regex($matches) 
	{
		return '([a-zA-Z0-9_\+\-%]+)';
	}

	public function math_query_string()
	{
		$request_uri = $this->get_client_route();
		$found 		 = FALSE;
		$return 	 = FALSE;

		if (strpos($request_uri,'?') !== false) 
		{
			$return = new stdclass;
			$return->method = "";
			$return->param  = array();

		    $spl = explode("?", $request_uri);

		    if(isset($spl[0]))
		    	$return->method = $spl[0];
		    else
		    	return FALSE;

		    if(isset($spl[1]))
		    {
		    	$fullquery = $spl[1];

		    	$spl = explode("&", $fullquery);

		    	foreach ($spl as $item) 
		    	{ 

		    		$splitems = explode("=", $item);
		    		$key = isset($splitems[0]) ? $splitems[0] : "";
		    		$val = isset($splitems[1]) ? $splitems[1] : "";

		    		if($key)
		    			$return->param[ $key ]=$val; 
		    	} 
		    }

		    return $return; 
		}
 
		return FALSE;
	}

	public function run() 
	{ 
		$this->before_run();

		$method = $this->match_simple();

		$PARAM  = array();

		if($method == FALSE)
		{ 
			$method = $this->match_params();

			if($method == FALSE)
			{
				$method = $this->default_method; 
			}
			else
			{
				$PARAM  = $method->param;
				$method = $method->method; 
			}
		}

		if($method == $this->default_method  )
		{
			$method = isset($_GET[ $this->default_key ]) ? $_GET[ $this->default_key ] : FALSE ; if ($method == FALSE) { $method = $this->default_method; }
			$PARAM  = $_GET;
		}
 
		$this->page = $method ; 


		if ($method)
		{
			if($method=="upgrade")
			{
				$this->upgrade();
				die();
			}
				 
			if(isset($this->$method))
			{
				if( $this->$method instanceof Closure )
				{
					if (is_callable($this->$method))
					{
						$param = $PARAM;

						unset($param[ $this->default_key ]);

						try
						{
							$this->parameters = $param;
							$fn               = $this->$method;

							call_user_func_array($fn, $param);
						}
						catch (Exception $e)
						{
							_LOG($this, __CLASS__, "{$method} envio un error {$e->getMessage()}");
						}
					}
					else
					{
						_LOG($this, __CLASS__, "El metodo {$method} no es callable");
					}
				}
				else
				{
					_LOG($this, __CLASS__, "El metodo {$method} no existe");
				}
			}
			else
			{
				_LOG($this, __CLASS__, "El metodo {$method} no existe");
			}
		}
		else
		{
			_LOG($this, __CLASS__, "Sin accion");
		}

		$this->after_run();
	}

	private function before_load() 
	{
		mb_internal_encoding( $this->encoding );
		mb_http_output      ( $this->encoding );
	}

	public function get($name, $function) 
	{
		$this->routes[$name]= $name;
		$this->{$name}      = Closure::bind($function, $this, 'core');
	}
	
	public function upgrade()
	{
		require BASEPATH."app/vendor/updater.php";
		require BASEPATH."app/vendor/unzip.php";
		
		$server = new updater(); 
		$server->build();
	}
}

$App = new core();
$App->after_load(); 