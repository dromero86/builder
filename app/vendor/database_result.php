<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class database_result {

	private $query 		= "";
	private $source 	= array();
	private $dataobject = array();
	private $dataassoc 	= array();

	//private static $instancia = null;
	/*
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
	*/

    public function __construct() {   }
    public function __destruct() {   }

	public function set_databind($query, $result)
	{
		$this->query = $query;
		$this->source = $result;
	}


	function result()
	{
	    $data = array();
	    
	    if($this->source)
		foreach($this->source as $rs)
		{
		    $o = new stdClass;
		    
		    foreach($rs as $k=>$v)
		    {
			$o->$k = $v;
		    }
		    
		    $data[] = $o;
		}


	    return $data;
	} 

	function result_array()
	{
	    $this->dataassoc= array();

	    if($this->source) 
		    foreach($this->source as $rs)
		    {
			    $this->dataassoc[] = $rs;
		    } 

	    return $this->dataassoc;
	}

	function first() { $rox = FALSE; foreach($this->result() as $row) { return $row; } return $rox; }

}
