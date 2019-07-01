<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Dependencias:
		- Exista: $config_file
		- Que la seccion [Database] en json este completa
		- Que la conexion este ok
*/
class database {

	private $driver  = NULL;
	private $user    = NULL;
	private $pass    = NULL;
	private $db      = NULL; 
	private $charset = NULL;
	private $collate = NULL;
	private $link    = NULL;
	private $isok 	 = FALSE;
	private $debug   = FALSE;

	private $config_file = "app/config/db.json";

	private static $instancia= null;

	public static function getInstance()
	{
		$that = null;

		if (!self::$instancia instanceof self)
		{
			if(self::$instancia == null)
			{
				$that = new self;
				self::$instancia = $that;
			}

		}
		else
		{
			$that = self::$instancia;
		}

		if($that == null)
			die(__CLASS__.": Fallo el singleton");

		return $that;
	}

	function __construct() {

		self::$instancia = $this;

		$this->before_connect();

		$this->connect();

		$this->after_connect();
	}

	public function is_ready()
	{
		return $this->isok;
	}

	public function exist($table)
	{
		$rs = $this->query("SELECT * FROM information_schema.TABLES WHERE table_schema = '{$this->db}'  AND table_name = '{$table}' LIMIT 1");

		$ret = FALSE; foreach ($rs->result() as $row) { $ret = TRUE;  }

		return $ret;
	}

	public function show_tables()
	{
		return $this->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE table_schema = '{$this->db}'");
	}

	public function show_column($table)
	{
		return $this->query("SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.COLUMNS WHERE table_schema = '{$this->db}'  AND table_name = '{$table}'");
	}

	public function has_column($table, $column)
	{
		$rs = $this->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE table_schema = '{$this->db}'  AND table_name = '{$table}' AND COLUMN_NAME='{$column}' LIMIT 1");

		$ret = FALSE; foreach ($rs->result() as $row) { $ret = TRUE;  }

		return $ret;
	}

	public function show_full_column($table)
	{
		return $this->query("SELECT ORDINAL_POSITION, COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY  FROM information_schema.COLUMNS WHERE table_schema = '{$this->db}'  AND table_name = '{$table}'");
	}
 
	private function connect()
	{
		if($this->isok == FALSE)
		{  
		    $this->link = new PDO($this->driver.':host='.$this->host.';dbname='.$this->db, $this->user, $this->pass);
		    $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
		    $this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
		    $this->isok = TRUE; 
		}
	}

	private function before_connect() 
	{

		$config = file_get_json(BASEPATH.$this->config_file);

		if( isset($config->database->debug) )
		{
			$this->debug 	= $config->database->debug;
		}

		if(isset($config->database))
		{
			$this->user 	 = $config->database->user   ;
			$this->pass 	 = $config->database->pass   ;
			$this->host 	 = $config->database->host   ;
			$this->db   	 = $config->database->db     ;
			$this->charset   = $config->database->charset;
			$this->collate   = $config->database->collate;
			$this->driver    = $config->database->driver ;
		}
		else
		{
			_LOG(core::getInstance(), __CLASS__, "No se hallo la sección [database]");
		}

		unset($config);
	}

	private function after_connect() {

		$this->rawExec("SET NAMES {$this->charset}");
		$this->rawExec("SET CHARACTER SET {$this->charset}");
		$this->rawExec("
			SET
				character_set_results 	 = '{$this->charset}',
				character_set_client 	 = '{$this->charset}',
				character_set_connection = '{$this->charset}',
				character_set_database 	 = '{$this->charset}',
				character_set_server 	 = '{$this->charset}',
				collation_connection 	 = '{$this->collate}';
		");

		//@mysql_set_charset($this->charset, $this->link);
	}

	public function rawExec($str)
	{

		$result = $this->link->query($str, PDO::FETCH_ASSOC);
 
		if(!$result)
		{
		    _LOG(core::getInstance(), __CLASS__, "SQL Error: {$str}");
		}
		else
		{
		    if($this->debug == TRUE) _LOG(core::getInstance(), __CLASS__, "SQL: {$str}");
		}

		return $result;
	}

	public function query($str)
	{
		if  (!$this->link)
		{
		    $this->isok = FALSE;
		    $this->connect();
		    $this->after_connect();
		}

		$result = new database_result();

		 
		$result->set_databind($str, $this->rawExec($str, PDO::FETCH_ASSOC)); 


		//$result->set_databind($str, $this->rawExec($str) );
 
		return $result;
	}

	public function procedure($str)
	{
		if  (!$this->link)
		{
		    $this->isok = FALSE;
		    $this->connect();
		    $this->after_connect();
		}
 		
		if($this->debug == TRUE) _LOG(core::getInstance(), __CLASS__, "PROCEDURE: {$str}");

		$sql_query = $this->link->prepare($str);

		$sql_query->execute(); 

		$result = new database_result();
 
		try
		{
		    $result->set_databind($str, $sql_query->fetchAll(PDO::FETCH_ASSOC) );
		}
		catch (Exception $e)
		{ 
		    _LOG(core::getInstance(), __CLASS__, "ERROR: {$e->getMessage()}");
		}

		$sql_query->closeCursor();
 
		return $result;
	}


	public function last_id()
	{
		$rs = $this->query("SELECT LAST_INSERT_ID() AS 'id'");

		$id = 0; 

		foreach ($rs->result() as $row)  
		{ 
			$id = (int)$row->id; 
		}	
		
		return $id;
	}

	public function table($name)
	{
		// aqui va el kit
		// retornar un objeto donde 
		// $this->db->table('test')->all()

		$QB = new datatable();
		$QB->connect($this);
		$QB->set($name);

		return $QB;
	}

	public function close() {
	    if($this->link) $this->link = NULL;
	}
}



class datatable
{
	private $table = "";
	private $db    = NULL;

	public function connect($database)
	{
		$this->db = $database;
	}

	public function run($query)
	{ 
		$result = $this->db->query($query);
		
		echo "{$query};";

		return $result;
	}

	public function set($name)
	{ 
		$this->table = $name; 
	}

	public function all()
	{
		$query = " SELECT * FROM {$this->table} ";
		
		return $this->run($query);
	}

	public function id($id)
	{
		$query = " SELECT * FROM {$this->table} WHERE id ='{$id}' ";
		
		return $this->run($query);
	}

	public function where($where)
	{
		$query = " SELECT * FROM {$this->table} WHERE {$where} ";
		
		return $this->run($query);
	}

	public function compose($select, $where, $order ="", $limit="")
	{
		$order = $order ? "ORDER BY {$order}" : "";
		$limit = $limit ? "LIMIT {$limit}" : "";
		

		$query = " SELECT {$select} FROM {$this->table} WHERE {$where} {$order} {$limit}";

		return $this->run($query);
	}
}
