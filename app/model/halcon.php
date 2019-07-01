<?php
$App = core::getInstance();

$App->get('index', function ()
{
    $this->data->set("rand", rand(100,999));
    $this->parser->parse(BASEPATH."cms/index.html", $this->data->get());
});



#request-login#
$App->get('request-login', function () {

    if( !$this->input->has_post() ) die('{"status":false, "message":"URL invalida"}');

    $post = $this->input->post();

    if(!$post->user || !$post->pass) die('{"status":false, "message":"Debe completar los campos"}');

    $rs = $this->db->query (" SELECT id, tipo FROM usuarios WHERE user = '{$post->user}' AND pass = MD5('{$post->pass}')  LIMIT 1 ");

    foreach($rs->result() as $row)
    {
         $this->session->send( $row->id);

         die('{"status":true}');
    }

    die('{"status":false, "message":"Usuario y/o password invalido"}');
});
#/request-login#

#request-logout#
$App->get('request-logout', function ($rid="")
{
    $this->session->close();

    die('{"status":false}');
});
#/request-logout#

#request-online#
$App->get("request-online", function ($rid="")
{
    $how = $this->session->recv() == FALSE ? 'false' : 'true' ;

    die('{"status":'.$how.($how==false?',"message":"Termino el tiempo de session"':'').'}');
});
#/request-online#

#halcon#
$App->get("halcon", function()
{
    $this->parser->parse("./ui/halcon/layout.html", array());
});
#/halcon#




$App->get('upload-image', function ( $move, $rid="")
{
    $result = new stdclass;

    $key = "upload";

    if(isset($_FILES[$key]))
    {
        $name = $_FILES[$key]["name"    ];
        $type = $_FILES[$key]["type"    ];
        $size = $_FILES[$key]["size"    ];
        $temp = $_FILES[$key]["tmp_name"];
        $error= $_FILES[$key]["error"   ];

        $result->file = $name;

        if ($error > 0)
        {
            $result->status= "error";
            $result->code  = $error ;

            switch($error)
            {
                case 1  : $result->message ="El archivo supera el maximo permitido por el servidor"          ; break;
                case 2  : $result->message ="El archivo supera el maximo permitido por el formulario"        ; break;
                case 3  : $result->message ="El archivo subido fue sólo parcialmente cargado"                ; break;
                case 4  : $result->message ="Ningun archivo fue subido"                                      ; break;
                case 6  : $result->message ="Falta la carpeta temporal."                                     ; break;
                case 7  : $result->message ="No se pudo escribir el archivo en el disco"                     ; break;
                case 8  : $result->message ="Una extension del servidor no permitio la escritura del archivo"; break;
                default : $result->message ="Ocurrio un error no identificado"                               ; break;
            }
        }
        else
        {
            $ext 	= pathinfo( $name , PATHINFO_EXTENSION );
            $id  	= md5_file( $temp );

            if( in_array( strtoupper($ext) , array( 'JPG', 'PNG', 'GIF' ) ) )
            {
                if( move_uploaded_file( $temp , "./{$move}/{$id}.{$ext}" ) == TRUE )
                {
	                $result->status  = "server";
	                $result->code    = 10 ;
					$result->name    = "{$id}.{$ext}" ;
	                $result->message = "Archivo subido correctmente";
                }
                else
                {
	                $result->status  = "error";
	                $result->code    = 9 ;
	                $result->message = "el archivo no puede moverse a ./".$move."/".$name ;
                }
            }
            else
            {
                $result->message = "El archivo tiene una extension no permitida";
                $result->code    = 12 ;
                $result->message = "el archivo no puede moverse a ./".$move."/".$name ;
            }
        }
    }
    else
    {
        $result->status  = "error";
        $result->code    = 11 ;
        $result->message = "POST INVALIDO";
        $result->file    = "";
    }

    die(json_encode($result));
});


function function_list($table)
{
	return function($rid="") use ($table)
	{ 
	    $json = QUERYJS($this,  $this->sqlist->get($table) );

	    die($json);
	};
}

function function_delete($table)
{
	return function($id, $rid="") use ($table)
	{
		$id = (int)$id;

		if($id > 0)
		{
			$this->db->query( DELETE($table, $id) );

			die('{"status":"OK"}');
		}
		else
		{
			die('{"status":"FAIL"}');
		}
	};
}

function function_update($table)
{
	return function($id, $rid="") use ($table)
	{
        $id = (int)$id;

		if( $this->input->has_post() )
		{
			$post = $this->input->post();

			foreach ($post as $key => $value) {
				$post->{$key}= $value;
			}

			if( isset($post->pass ) )
                if($post->pass =="")
                    unset($post->pass);



			if($id>0)
			{
				//update
				unset($post->id);

				$this->db->query( UPDATE( $table, $post, $id ) );

				die('{"result": "ok", "type":"update", "id":"'.$id.'"}');
			}
			else
			{
				//insert

				$this->db->query( INSERT( $table, $post ) );

				$id = $this->db->last_id();

				die('{"result": "ok", "type":"insert", "id":"'.$id.'"}');
			}
		}
		else
		{
			die('{"result": "error", "message":"el post no contiene datos"}');
		}
	};
}

function function_one($table)
{
	return function($rid="") use ($table)
	{
		$rs = $this->db->show_column($table);

		$output = new stdclass;

		foreach ($rs->result() as $row)
		{
			switch ($row->DATA_TYPE) {
				case 'int'    :
				case 'tinyint': $output->{$row->COLUMN_NAME}=0;  break;
				default       : $output->{$row->COLUMN_NAME}=""; break;
			}

		}

		die(json_encode($output));
	};
}

function function_combo($table)
{
	return function( $rid="" ) use ($table)
	{
		$data = QUERYJS($this, $this->sqlist->get_combo($table)  );

		die( $data );
	};
}

function function_drops($table)
{
    return function( $rid="" ) use ($table)
    {
        if($this->input->post())
        {
            $post = $this->input->post();

            if( isset($post->data) )
                if($post->data)
                {
                    $drops = explode(",",$post->data);

                    foreach ($drops as $id)
                    {
                        $id = (int)$id;

                        if($id>0)
                            $this->db->query( DELETE($table, $id));
                    }
                }
        }

        die('{"result":true}');
    };
}




foreach ($this->db->show_tables()->result() as $row)
{
    $tabla = $row->TABLE_NAME;

    $o = function_one   ($tabla);
    $l = function_list  ($tabla);
    $d = function_delete($tabla);
    $u = function_update($tabla);
    $c = function_combo ($tabla);
    $dp = function_drops ($tabla);

    $App->get("{$tabla}-one"   , $o );
    $App->get("{$tabla}-list"  , $l );
    $App->get("{$tabla}-delete", $d );
    $App->get("{$tabla}-update", $u );
    $App->get("{$tabla}-combo" , $c );
    $App->get("{$tabla}-drops" , $dp );
}


$App->get('sidebar-user', function ()
{
    if($this->session->recv() != false)
    {

        $id_user = $this->session->recv();

        $rs = $this->db->query (" SELECT tipo FROM usuarios WHERE id = {$id_user}  LIMIT 1 ");

        foreach($rs->result() as $row)
        {
             $tipo = $row->tipo;
        }

    }

    $rs = $this->db->query ("SELECT vista AS 'id', value, icon FROM menu WHERE id_tipo = {$tipo} ");

    foreach($rs->result() as $row)
    {
         $raw[] = $row;
    }

    $raw = json_encode($raw);

    echo $raw;

});
