<?php

function INSERT( $table , $object )
{

	$db = database::getInstance();

	$SQL = " INSERT {$table} SET ";

	$item = array();

	foreach ($object as $key => $value)
	{
		switch ($value)
		{
			case 'CURRENT_DATE' : $value = "CURRENT_DATE"	; break;
			case 'NOW' 			: $value = "now()"			; break;
			default     		: $value = "'".addslashes($value)."'"  	; break;
		}

		if($key =='pass')  $value = 'MD5('.$value.')' ;

		if( $db->has_column($table, $key)==TRUE )
		{
			$E     = "{$key} = {$value}";
			$item[]=$E;
		}

	}

	$SQL .= implode(", ", $item);

	return $SQL;
}

function UPDATE( $table , $object , $id)
{
	$db = database::getInstance();

	$SQL = " UPDATE {$table} SET ";

	$item = array();

	foreach ($object as $key => $value)
	{
		switch ($value)
		{
			case 'CURRENT_DATE' : $value = "CURRENT_DATE"	; break;
			case 'NOW' 			: $value = "now()"			; break;
			default     		: $value = "'".addslashes($value)."'"  	; break;
		}

		if($key =='pass')
		{
			$value = str_replace("''", "", $value);

			$value = $value = 'MD5('.$value.')' ;
		}

		if( $db->has_column($table, $key)==TRUE )
		{
			$E     = "{$key} = {$value}";
			$item[]=$E;
		}
	}

	$SQL .= implode(", ", $item);
	$SQL .= " WHERE id = '{$id}'";

	return $SQL;
}

function DELETE( $table, $id )
{
	$SQL = "DELETE FROM {$table} WHERE id = '{$id}'";

	return $SQL;
}

function SELECT( $table )
{
	$SQL = "SELECT * FROM {$table}";

	return $SQL;
}



function QUERIFY($that, $key, $sql, $autoset=FALSE, $optJson = NULL )
{
	if($autoset==FALSE) $that->data->set($key);

	$options = FALSE;

	if($optJson!=NULL)
	{
		$options = json_decode($optJson);
	}

	$rs = $that->db->query ($sql);

	foreach ($rs->result() as $row)
	{
		//add link custom behavior
		if(isset($options->tolink))
			foreach ($options->tolink as $item)
			{
				$row->{$item->name} = TOLINK($row->{$item->field});
			}

		//add active custom behavior
		if(isset($options->active))
		{
			$options->active->equal         = (int)$options->active->equal;
			$row->{$options->active->field} = (int)$row->{$options->active->field};
			$row->active                    = ( $row->{$options->active->field} == $options->active->equal ? "active" : "" );
		}


		$that->data->map($key , $row );
	}
}


function PROCIFY($that, $key, $sql, $autoset=FALSE, $optJson = NULL )
{
	if($autoset==FALSE) $that->data->set($key);

	$options = FALSE;

	if($optJson!=NULL)
	{
		$options = json_decode($optJson);
	}

	$rs = $that->db->procedure ($sql);

	foreach ($rs->result() as $row)
	{
		//add link custom behavior
		if(isset($options->tolink))
			foreach ($options->tolink as $item)
			{
				$row->{$item->name} = TOLINK($row->{$item->field});
			}

		//add active custom behavior
		if(isset($options->active))
		{
			$options->active->equal         = (int)$options->active->equal;
			$row->{$options->active->field} = (int)$row->{$options->active->field};
			$row->active                    = ( $row->{$options->active->field} == $options->active->equal ? "active" : "" );
		}


		$that->data->map($key , $row );
	}
}

function QUERYMAP($that, $sql, $key="", $optJson = NULL)
{
	$options = FALSE;

	if($optJson!=NULL)
	{
		$options = json_decode($optJson);
	}

	$rs = $that->db->query ($sql);



	foreach ($rs->result() as $row)
	{
		//add link custom behavior
		if(isset($options->tolink))
			foreach ($options->tolink as $item)
			{
				$row->{$item->name} = TOLINK($row->{$item->field});
			}

		//add active custom behavior
		if(isset($options->active))
		{
			$options->active->equal         = (int)$options->active->equal;
			$row->{$options->active->field} = (int)$row->{$options->active->field};
			$row->active                    = ( $row->{$options->active->field} == $options->active->equal ? "active" : "" );
		}


		$that->data->automap( $row, $key );

		//var_dump($that->data->get());
	}
}

function PROCYMAP($that, $sql, $key="", $optJson = NULL)
{
	$options = FALSE;

	if($optJson!=NULL)
	{
		$options = json_decode($optJson);
	}

	$rs = $that->db->procedure ($sql);



	foreach ($rs->result() as $row)
	{
		//add link custom behavior
		if(isset($options->tolink))
			foreach ($options->tolink as $item)
			{
				$row->{$item->name} = TOLINK($row->{$item->field});
			}

		//add active custom behavior
		if(isset($options->active))
		{
			$options->active->equal         = (int)$options->active->equal;
			$row->{$options->active->field} = (int)$row->{$options->active->field};
			$row->active                    = ( $row->{$options->active->field} == $options->active->equal ? "active" : "" );
		}


		$that->data->automap( $row, $key );

		//var_dump($that->data->get());
	}
}



function QUERYJS($that, $sql )
{
	$data = array();

	$rs = $that->db->query($sql);

	foreach ($rs->result() as $row)
	{
		foreach ($row as $key => $value) {
			$row->{$key}=$value;
		}

		$data[]= json_encode($row);
	}

	$str = "[".implode(",", $data)."]";

	return $str;
}

function QUERYCOMBO($that, $sql, $message)
{
	$rs = $that->db->query($sql);

	$result = $rs->result();

	if(count($result))
	{
		foreach ($result as $row)
		{
			echo '<option value="'.$row->id.'">'.$row->nombre.'</option>';
		}
	}
	else
	{
		echo '<option>'.$message.'</option>';
	}
}

function QUERYSON($that, $sql, $print =TRUE)
{
	$rs   = $that->db->query( $sql );

	$data = $rs->result();  $output = "{}";

	if(count($data)) foreach ($data as $row) { $output = json_encode($row); }

	if($print ==TRUE)
		die($output);
	else
		return $output;
}


function PROCYSON($that, $sql, $print =TRUE)
{
	$rs   = $that->db->procedure( $sql );

	$data = $rs->result();  $output = "{}";

	if(count($data)) foreach ($data as $row) { $output = json_encode($row); }

	if($print ==TRUE)
		die($output);
	else
		return $output;
}


function TOLINK($str)
{
    $str = str_replace(" ","-",$str);
    $str = str_replace("Á","a",$str);
    $str = str_replace("É","e",$str);
    $str = str_replace("Í","i",$str);
    $str = str_replace("Ó","o",$str);
    $str = str_replace("Ú","u",$str);
    $str = str_replace(" ","-",$str);
    $str = str_replace("á","a",$str);
    $str = str_replace("é","e",$str);
    $str = str_replace("í","i",$str);
    $str = str_replace("ó","o",$str);
    $str = str_replace("ú","u",$str);
    $str = preg_replace("/\W+/",'-',$str);
    $str = strtolower($str);
    return $str;
}


function SPLITMAP($that, $field, $key_parent, $key_child,  $char=",")
{
	$that->data->set($key_parent);

	if(!is_array($field))return;

	$rs = explode($char, $field);



	foreach ($rs as $item)
	{
		$row = new stdclass;

		$row->{$key_child}=$item;

		$that->data->map($key_parent , $row );
	}
}
