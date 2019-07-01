<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

if (!function_exists('file_get_json')) 
{
	function file_get_json($file)
	{
		$object = new stdclass;

		if(is_file($file))
		{
			$string = file_get_contents($file);
			$object = json_decode($string);
		}
		else
		{
			_LOG(core::getInstance(), $file, "File not found");
		}

		return $object;
	}
}

if (!function_exists('die_dump')) 
{
	function die_dump($var)
	{
 		var_dump($var);
 		die();
	}
}

if (!function_exists('_LOG')) 
{
	function _LOG($App, $file, $message)
	{
		$hora = date('H:i:s', time());
		$App->write_log('<li class="list-group-item"><span class="label label-primary">'.$file.'</span> '.$message.' <span class="badge">'.$hora.'</span></li>');
	}
}

if (!function_exists('_LOG_WRITE')) 
{
	function _LOG_WRITE($message)
	{
 		$filename  = date("Y-m-d", time()).".log";  $dir = "./app/log";

 		if(!is_dir($dir)) { mkdir($dir); }

 		file_put_contents($dir.$filename, $message, FILE_APPEND); 
	}
}

if (!function_exists('_LOG_TRACE')) 
{
	function _LOG_TRACE($message, $route)
	{
		$linea    = replace("{date}[TRACE]: {error} >> {route}\n", array
		(
			"date"  => date("d-m-Y [H:i:s]", time()),
			"route" => $route   ,
			"error" => $message 
		));
	 	  
	 	_LOG_WRITE($linea);
	}
}


if (!function_exists('base_url')) 
{
	function base_url() 
	{
		if (isset($_SERVER['HTTP_HOST'])) 
		{
			$base_url  = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'?'https':'http';
			$base_url .= '://'.$_SERVER['HTTP_HOST'];
			$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
		} 
		else 
		{
			$base_url = 'http://localhost/';
		}

		return $base_url;
	}
}

if (!function_exists('redirect')) 
{
	function redirect($uri = '', $method = 'location', $http_response_code = 302) 
	{
		if (!preg_match('#^https?://#i', $uri)) 
		{
			$uri = base_url().$uri;
		}

		switch ($method) 
		{
			case 'refresh': header("Refresh:0;url=".$uri 						); break;
			default       : header("Location: ".$uri, TRUE, $http_response_code ); break;
		}

		exit;
	}
}

if (!function_exists('replace')) 
{
	function replace($str, $arr) 
	{ 
		//admite objects
		foreach ($arr as $k => $v) 
		{
			$str = str_replace('{'.$k.'}', $v, $str);
		} 
		return $str;
	}
}

if (!function_exists('deflate')) 
{
	function deflate($resource) {
		$resource = string_deflate($resource);

		$resource = quit_spaces($resource);

		return $resource;
	}
}

if (!function_exists('string_deflate')) 
{
	function string_deflate($string) 
	{
		$is_deflate = array("\n", "\t", "\r");

		foreach ($is_deflate as $quit) 
		{
			$string = str_replace($quit, " ", $string);
		}

		return $string;
	}
}

if (!function_exists('quit_spaces')) 
{
	function quit_spaces($string) 
	{
		$ISOK = FALSE;

		while ($ISOK == FALSE) 
		{
			$aesp = explode("  ", $string);

			if (count($aesp) > 1) {
				$string = str_replace("  ", " ", $string);

				$ISOK = FALSE;
			} else {
				$ISOK = TRUE;
			}

		}

		return $string;
	}
}


if (!function_exists('to_link')) 
{
	function to_link($str)
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
}

if (!function_exists('to_post')) 
{
	function to_post($str,$max=400)
	{ 
		$str = strip_tags($str);
		$sz  = strlen($str);
		
		if( $sz > $max  )
		{
			$str  = substr($str, 0, $max);
			$str2 = substr($str, 0, strripos($str, " ") );	
			$sz2  = strlen($str2); 
			
			if( ($sz-10)>$sz2 ) $str = $str2."..."; 
		}
		 
		return $str  ;  
	}
}


function price($precio){
	$num_decimas 	= 2;
	$entero	 		= substr ( $precio, 0, - $num_decimas );
	$decimales 		= substr ( $precio,    - $num_decimas );
	return 			"$ ".($entero ? $entero : '0').".".$decimales;
}

function rprice($precio){
	$num_decimas 	= 2;
	$entero	 		= substr ( $precio, 0, - $num_decimas );
	$decimales 		= substr ( $precio,    - $num_decimas );
	return 			($entero ? $entero : '0').".".$decimales;
}

function clean($nombre){
 	$nombre = trim($nombre);
 	$nombre = str_replace("'" ,"" ,$nombre);
 	$nombre = str_replace('"' ,"" ,$nombre);
 	$nombre = str_replace("\n","" ,$nombre);
 	$nombre = str_replace("\r","" ,$nombre);
 	$nombre = str_replace("/" ,".",$nombre);

	return $nombre;
}