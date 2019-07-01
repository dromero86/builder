<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class input
{
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
	}

	public function has_server()
	{
		return count($_SERVER)>0 ? TRUE : FALSE;
	}	

	public function server($key='')
	{ 

		if($key)
		{
			$ret = isset($_SERVER[$key]) ? $_SERVER[$key] : FALSE;
		}
		else
		{
			$ret = new stdclass;

			foreach ($_SERVER as $key => $value) 
			{
				$ret->{$key} = $value;
			}
		}

		return $ret;
	}

	

	public function has_post()
	{
		return count($_POST)>0 ? TRUE : FALSE;
	}

	public function post($key='')
	{
		//if( !$this->has_post() ) return FALSE;

		if($key)
		{
			$ret = isset($_POST[$key]) ? $_POST[$key] : FALSE;
		}
		else
		{
			$ret = new stdclass;

			foreach ($_POST as $key => $value) {
				$ret->{$key} = $value;
			}
		}

		return $ret;
	}

	public function post2json($array)
	{
		//if( !$this->has_post() ) return FALSE;

		$obj = new stdclass;

		foreach ($array as $value) {
			$obj->{$value} = $this->post($value);
		}

		return json_encode($obj);
	}

}