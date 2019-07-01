<?php 


class Dataset
{
	private $store = array(); 

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

	public function set($key, $value = array() ) 
	{
		$this->store[ $key ] = $value ;
	} 
 
	public function add($key, $value)
	{
		$this->store[ $key ][] = $value ;
	}

	public function get()
	{
		return $this->store;
	}  

	/* MULTIDEFS  - TRICKS */

	public function init( $list , $value="")
	{
		foreach ($list as $key) 
		{
			$this->set($key, $value);
		}
	}

	public function map($key, $object)
	{
		$item  = array();

		foreach ($object as $name => $value) 
		{
			$item [ "{$key}_{$name}" ]= $value;
		}

		$this->store[$key][]=$item;		
	}


	public function automap($object, $prefix="")
	{
		foreach ($object as $name => $value) 
		{
			$this->store [ $prefix.$name ]= $value;
		}
	}
}