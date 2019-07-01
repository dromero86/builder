<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Dependencias:
		- Database ok
		- Exista: $config_file
		- Que la seccion [Telepatia] en json este completa
		- Que la tabla exista
*/
class Telepatia
{
    private $session_time 	= 1    	;
	public  $appname 		= ""	;
	private $table			= ""	;
	private $db 			= NULL 	;
	private $isok 			= FALSE ;

	private $config_file = "app/config/session.json";

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

		$this->db = database::getInstance();

		$this->init();

		$this->after_connect();

		if(	!$this->db->is_ready() )
		{
			_LOG(core::getInstance(), __CLASS__, "[Database] is off");
		}
		else
		{
			// check table exist
			if(!$this->db->exist($this->table))
			{
				_LOG(core::getInstance(), __CLASS__, "Table [{$this->table}] don't exist");
			}
			else
			{
				$this->isok = TRUE;
			}
		}
	}

	private function after_connect() {
		$config   = file_get_json(BASEPATH.$this->config_file);

		if(isset($config->Telepatia))
		{
			$this->table		= $config->Telepatia->table;
			$this->appname		= $config->Telepatia->app;
			$this->session_time	= $config->Telepatia->timeout;
		}
		else
		{
			_LOG(core::getInstance(), __CLASS__, "No se hallo la sección [Telepatia]");
		}

	}

	public function is_ready()
	{
		return $this->isok;
	}

	public function open($db,$table,$appname,$session_time)
	{
		$this->db = $db;
		$this->table = $table;
		$this->appname = $appname;
		$this->session_time = $session_time;
	}

	public function set_database($db)
	{
		$this->db = $db;
	}

	public function set_table($table)
	{
		$this->table = $table;
	}

	public function set_appname($appname)
	{
		$this->appname = $appname;
	}

	public function set_session_time($session_time)
	{
		$this->session_time = $session_time;
	}

	public function init()
	{
		if( session_id() == '' )
		{
			@session_start();
		}
	}

	private function ip_address()
	{
		$ip = '';

		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

    public function  get()
    {
        $cookie = isset( $_COOKIE[ $this->appname ] ) ? $_COOKIE[ $this->appname ] : "";

        $rs     = $this->db->query(" SELECT id_user FROM {$this->table} WHERE cookie = '{$cookie}' LIMIT 1 ");

        $has    = FALSE;

        foreach( $rs->result() as $row )
        {
            $has = (int) $row->id_user > 0 ? $row->id_user : FALSE ;
        }

        return $has;
    }


    public function  has_session()
    {

        $cookie = isset( $_COOKIE[ $this->appname ] ) ? $_COOKIE[ $this->appname ] : "";

        $rs     = $this->db->query(" SELECT TIMESTAMPDIFF( HOUR , last_time, NOW( ) ) AS 'curr_time', expire  FROM  {$this->table} WHERE cookie = '{$cookie}'  LIMIT 1 ");

        $has    = FALSE;

		foreach( $rs->result() as $row )
        {
			$row->expire = (int)$row->expire;

			if( $row->expire > 0 ) $this->session_time = $row->expire;

            $has = TRUE;

            if( (int) $row->curr_time > $this->session_time ) $has = FALSE;

			if( $has == FALSE )
			{
				$this->rem();
			}
			else
			{
				$this->db->query(" UPDATE {$this->table} SET  last_time=now()  WHERE  cookie = '{$cookie}' LIMIT 1");
			}

        }

        return $has;
    }

    private function add($id)
    {
		$cookie =  uniqid();

		setcookie( $this->appname , $cookie , time() + ( 3600 * $this->session_time ) , '/');

        $sIp    = $this->ip_address();

        $this->db->query(" INSERT {$this->table} SET ip = '{$sIp}', id_user = '{$id}', type = '{$this->appname}', cookie = '{$cookie}', expire ='{$this->session_time}' ");
    }

    private function set($id)
    {
        $sIp    = $this->ip_address();

		$cookie =  uniqid();

		setcookie( $this->appname , $cookie , time() + ( 3600 * $this->session_time ) , '/');

        $this->db->query(" UPDATE {$this->table} SET id_user = '{$id}', type = '{$this->appname}', expire ='{$this->session_time}'  WHERE ip = '{$sIp}', cookie = '{$cookie}' LIMIT 1");
    }

    private function rem($id = FALSE)
    {
		$cookie = isset( $_COOKIE[ $this->appname ] ) ? $_COOKIE[ $this->appname ] : FALSE;

		if( $id == FALSE )
		{
			if($cookie == FALSE )
			{
				$sIp    = $this->ip_address();

				$this->db->query(" DELETE FROM {$this->table} WHERE ip = '{$sIp}' ");
			}
			else
			{
				$this->db->query(" DELETE FROM {$this->table} WHERE cookie = '{$cookie}' ");

				unset($_COOKIE[$this->appname]);

				setcookie($this->appname, NULL, -1, '/');
			}
		}
		else
		{
			$this->db->query(" DELETE FROM {$this->table} WHERE id_user = '{$id}'");
		}
    }

    public function  close($id = FALSE)
    {
        $this->rem($id); 
    }


    public function  send($id,$val=NULL)
    {
		$this->rem( $id );
        $this->add( $id );
    }

    public function  recv()
    {
        $ret = FALSE;

        if( $this->has_session() )
        {
            $ret = $this->get();
        }

        return $ret;
    }
}
