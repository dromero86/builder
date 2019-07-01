<?php 

class sqlist
{
	private $assoc = array();
	private $combo = array(); 

	function __construct()
	{
		$this->db = database::getInstance();
	}

	public function get($table)
	{
		$rs = $this->db->query("SELECT get_all FROM wx_query WHERE nombre = '{$table}' ");

		$query = NULL; 

		foreach ($rs->result() as $row) 
		{
			$query = $row->get_all;
		}

		if($query == NULL)
		{
			$query = "SELECT * FROM {$table}";
		}

		return $query;
	}

	public function get_combo($table)
	{
		$rs = $this->db->query("SELECT get_combo FROM wx_query WHERE nombre = '{$table}' ");

		$query = NULL; 

		foreach ($rs->result() as $row) 
		{
			$query = $row->get_combo;
		}

		if($query == NULL)
		{
			$query = "SELECT id, nombre AS 'value' FROM {$table}";
		}

		return $query; 
	}
}