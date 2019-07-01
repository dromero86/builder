<?php 
/*
	$this->upload->setKey("upload");
	$this->upload->setFolder("./cv");
	$this->upload->setAllowExtension(upload::TYPE_IMAGES);
	$result = $this->upload->process();
 */

class upload
{
	private $status         = ""	;
	private $code           = 0		;
	private $message        = ""	;
	private $file           = ""	; 
	private $key            = ""	; 
	private $folder         = "./"	; 
	private $renameFile     = 0 	; 
	private $extensions 	= array();

	const FILE_SIZE_ERROR  =  1; 
	const FILE_FORM_ERROR  =  2; 
	const FILE_BREAK_ERROR =  3; 
	const FILE_NOTF_ERROR  =  4;  
	const FILE_TEMP_ERROR  =  6; 
	const FILE_PERM_ERROR  =  7; 
	const FILE_EXT_ERROR   =  8;   
	const FILE_POST_ERROR  = 11;
	const FILE_MOVE_ERROR  =  9;
	const FILE_UPLOAD_OK   = 10;
	const FILE_BAD_EXT     = 12;

	const UPLOAD_NO_RENAME 	 = 0;
	const UPLOAD_RENAME_MD5  = 1;
	const UPLOAD_RENAME_LINK = 2;
	const UPLOAD_RENAME_COMP = 3;

	public $TYPE_IMAGES = array('JPG' , 'PNG' , 'GIF', 'JPEG', 'BMP', 'TIFF', 'SVG');
	public $TYPE_OFFICE = array('DOC' , 'DOCX', 'RTF', 'XLS' ,'XLSX','CSV','PDF', 'PPT', 'PPTX' , 'ODT', 'ODF');
	public $TYPE_VIDEO  = array('WEBM', 'SWF' , 'AVI', 'MP4' , 'MPG', 'MPEG' );
	public $TYPE_TEXT   = array('TXT' , 'LOG');

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

	public function setKey($name)
	{
		$this->key = $name;
	}

	public function setFolder($value)
	{
		$this->folder = $value;
	}

	public function setAllowExtension($value)
	{
		$this->extensions = $value;
	}

	public function setRenameType($value)
	{
		$this->renameFile = $value;
	}


	private function decode()
	{
		$FILE = new stdclass;

		$FILE->name = isset($_FILES[$this->key]["name"    ]) ? $_FILES[$this->key]["name"    ] : "";
		$FILE->type = isset($_FILES[$this->key]["type"    ]) ? $_FILES[$this->key]["type"    ] : "";
		$FILE->size = isset($_FILES[$this->key]["size"    ]) ? $_FILES[$this->key]["size"    ] : "";
		$FILE->temp = isset($_FILES[$this->key]["tmp_name"]) ? $_FILES[$this->key]["tmp_name"] : "";
		$FILE->error= isset($_FILES[$this->key]["error"   ]) ? $_FILES[$this->key]["error"   ] : 11;

		return $FILE;
	}

	private function setMessage($status, $code, $message, $file)
	{
		$this->status  = $status ;
		$this->code    = $code   ; 
		$this->message = $message;
		$this->file    = $file   ;

		return $this->returnMessage();
	}

	private function caseError($error)
	{
        switch($error)
        {
            case SELF::FILE_SIZE_ERROR  : $this->message = "El archivo supera el maximo permitido por el servidor"          ; break;
            case SELF::FILE_FORM_ERROR  : $this->message = "El archivo supera el maximo permitido por el formulario"        ; break;
            case SELF::FILE_BREAK_ERROR : $this->message = "El archivo subido fue sólo parcialmente cargado"                ; break;
            case SELF::FILE_NOTF_ERROR  : $this->message = "Ningun archivo fue subido"                                      ; break; 
            case SELF::FILE_TEMP_ERROR  : $this->message = "Falta la carpeta temporal."                                     ; break;
            case SELF::FILE_PERM_ERROR  : $this->message = "No se pudo escribir el archivo en el disco"                     ; break;
            case SELF::FILE_EXT_ERROR   : $this->message = "Una extension del servidor no permitio la escritura del archivo"; break; 
            default 					: $this->message = "Ocurrio un error no identificado"                               ; break; 
        } 		
	}

	private function returnMessage()
	{
		$o          = new stdclass;
		$o->status  = $this->status         ;
		$o->code    = $this->code           ;
		$o->message = $this->message        ;
		$o->file    = $this->file           ;  
		return $o;
	}

	private function getExtension($filename)
	{
		return pathinfo($filename, PATHINFO_EXTENSION);
	}


	private function getNamefile($filename)
	{
		return pathinfo($filename, PATHINFO_FILENAME);
	}



	private function to_link($str)
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


	private function getFilename($originpath , $filename)
	{
		$ext  = $this->getExtension($filename);
		$name = $this->getNamefile ($filename);

		switch($this->renameFile)
		{
			case SELF::UPLOAD_NO_RENAME  :  break;
			
			case SELF::UPLOAD_RENAME_MD5 : 
				$filename = md5_file($originpath).".".$ext;
			break;
			
			case SELF::UPLOAD_RENAME_LINK: 
				$filename = to_link($name).".".$ext;
			break;
			
			case SELF::UPLOAD_RENAME_COMP: 
				$cmp_md5 = substr(md5_file($originpath),0,10);  //substr("abcdef", -1)
				$cmp_lnk = substr($this->to_link($name),0,30);

				$filename = "{$cmp_md5}_{$cmp_lnk}.{$ext}";
			break;
		}
 
		$item = $this->folder.$filename;

		return $item;
	}


	public function process()
	{  
	    if(! isset($_FILES[$this->key])) return $this->setMessage("error", SELF::FILE_POST_ERROR, "POST INVALIDO", ""); 
 
        $FILE       = $this->decode(); 
        $this->file = $FILE->name;

        if ($FILE->error > 0)
        { 
			$this->status= "error";
			$this->code  = $FILE->error ;
			$this->caseError($FILE->error); 

			return $this->returnMessage();
        }
        else
        { 
        	if ( !in_array( strtoupper($this->getExtension($FILE->name)) , $this->extensions  ) )  return $this->setMessage("server", SELF::FILE_BAD_EXT, "Extension no permitida", $FILE->name); 
 			
 			$this->file  = $this->getFilename($FILE->temp, $FILE->name) ;
  
        	$moveResult  = move_uploaded_file($FILE->temp, $this->file );
 
            if($moveResult != TRUE)  
            	return $this->setMessage("error" , SELF::FILE_MOVE_ERROR, "el archivo no puede moverse a ".$this->folder.$FILE->name, ""); 
			
				return $this->setMessage("server", SELF::FILE_UPLOAD_OK , "Archivo subido correctmente", $this->file); 
        }
	     
	} 

}