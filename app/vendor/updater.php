<?php 

class updater
{
    private $path = ".";
    private $tmp = "./tmp";
    
    private $db;
    
    private $url = "http://hornero.estudioarbol.com/?action=update";
    
    function __construct()
    {
        $this->db = database::getInstance();    
    }
    
    private function download_secure($from, $to)
    {     
        $fp = fopen( $to, 'w+');
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL           , $from );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true  );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10    );
        curl_setopt( $ch, CURLOPT_FILE          , $fp   );
        curl_exec  ( $ch );
        curl_close ( $ch );
        fclose     ( $fp );
    }
    
    private function download($from, $to)
    {
		echo date('H:i:s', time())." Descargando actualizaciones \n\r";
		
        file_put_contents($to, fopen($from, 'r'));
    }
    
    private function extract($from, &$to)
    {  
		define('FILE_READ_MODE', 0644);
	
		echo date('H:i:s', time())." Analizando el repositorio \n\r";
	
        $unzip = new unzip();
        
        $unzip->extract($from, $to);
		
		$client = FALSE; 
		
		if (is_dir($to)) 
		{
			if ($dh = opendir($to)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
					if( !in_array($file, array(".", "..") ) )
						$client = $file;
				}
				closedir($dh);
			}
		}		
		
		if($client!=FALSE)
		{
			$to = $to."/".$client;
		}
    }
    
    private function install($f)
    {
        $core = file_get_json("./config/core.json");
        $jdb  = file_get_json("./config/db.json"  );
	 
		$jdb->Telepatia          = new stdclass;
		$jdb->Telepatia->table   = "session";
		$jdb->Telepatia->app     = "halcon";
		$jdb->Telepatia->timeout = 1;
		
        $config = $f."/hornero.json";
        
        $hornero = file_get_json($config);
        
        if(!$this->db->is_ready()) die("Fatal: no se configuro la base de datos");
        
        if(isset($hornero->sql_create))
        {
            $file_sql_create = $f."/".$hornero->sql_create;
            
			$sql_create = file_get_contents($file_sql_create);
			
			echo date('H:i:s', time())." Aplicando {$hornero->sql_create} \n\r";
			
			foreach( explode(";", $sql_create) as $query)
			{
				$this->db->query($query);
			}
        }
        
        if(isset($hornero->sql_update))
        {
            $file_sql_update = $f."/".$hornero->sql_update;
            
			$sql_update = file_get_contents($file_sql_update);
			
			echo date('H:i:s', time())." Aplicando {$hornero->sql_update} \n\r";
			
            $this->db->query($sql_update);
        }        
        
        if(isset($hornero->folder))
        { 
            foreach( $hornero->folder as $item )
            {
				echo date('H:i:s', time())." Verficando {$item} \n\r";
				
				if( !is_dir($this->path."/".$item) ) mkdir($this->path."/".$item);
            }
        }
        
        if(isset($hornero->dependences))
        {
            foreach( $hornero->dependences as $item )
            { 
				echo date('H:i:s', time())." Instalando {$item->file} \n\r";
				
                copy($f."/".$item->file, $this->path."/".$item->file );  
            }
              
            foreach($hornero->dependences as $key=>$deps)
            { 
                $found = FALSE ;
                
                foreach($core->loader as $loader)
                {  
					if( isset($loader->library) )
						if( $key == $loader->module )
							$found = TRUE;
						
					if( isset($loader->helper) )
						if( $deps->code == $loader->module )
							$found = TRUE;
						
                }
                
                if($found == FALSE)
                {
                    $newconf = new stdclass;
                    
                    if($deps->type == "library") 
                    {
                        $newconf->library = TRUE;
                        $newconf->module  = $key;
                        $newconf->path    = $deps->code;
                        
						echo date('H:i:s', time())." Add to lib/config: {$deps->file}   \n\r";
						
                        if($deps->rename) $newconf->name = $deps->rename;
                    }
                    
                    if($deps->type == "helper") 
                    {
                        $newconf->helper = TRUE;
                        $newconf->module = $deps->code;
						
						echo date('H:i:s', time())." Add to helper/config: {$deps->file}   \n\r";
				
                    }
                    
                    $core->loader[] = $newconf;
                }
            }
        }
        
        if(isset($hornero->halcon))
        {
            $dephalcon         = new stdclass;
            $dephalcon->helper = TRUE;
            $dephalcon->module = str_replace(".php","", $hornero->halcon);
            
			echo date('H:i:s', time())." Add to helper: {$hornero->halcon}   \n\r";
			
			$foundHalcon = FALSE;
		
			foreach($core->loader as $loader)
			{ 
				if( $dephalcon->module == $loader->module ) $foundHalcon = TRUE;
			}
			
			if( $foundHalcon == FALSE )
			{
				$core->loader[] = $dephalcon;
			}
            
            copy($f."/".$hornero->halcon, $this->path."/".$hornero->halcon );
        }
        
        if(isset($hornero->views))
        {
            foreach( $hornero->views as $item )
            {
				echo date('H:i:s', time())." Add to view: {$item}   \n\r";
				
                copy($f."/".$item, $this->path."/".$item );    
            }
        } 
         
		echo date('H:i:s', time())." write: core.json   \n\r";
		
        file_put_contents( "./config/core.json", json_encode($core) );
        file_put_contents( "./config/db.json"  , json_encode($jdb ) );
		
		echo date('H:i:s', time())." Eliminando temporales  \n\r";
		
		$this->rrmdir($this->tmp); 
		
		echo date('H:i:s', time())." Good bye!   \n\r";
    }
 
	private function rrmdir($dir) 
	{ 
	   if (is_dir($dir)) 
	   { 
			$objects = scandir($dir); 
			
			foreach ($objects as $object) 
			{ 
				if ($object != "." && $object != "..") { 
					if (is_dir($dir."/".$object))
						$this->rrmdir($dir."/".$object);
					else
						unlink($dir."/".$object); 
				} 
			}
			
			rmdir($dir); 
	   } 
	}
 
    public function build()
    {
        //D.D.I
        
        $ZIP   = $this->tmp."/tmp.zip";
        $FOLDER= $this->tmp."/tmp";
		
		if( !is_dir($FOLDER) )
		{
			mkdir($FOLDER, 0777 ,TRUE);
		}
        
		echo "<pre>";
		
        //1- Descargar
        
        $this->download($this->url, $ZIP);
        
        //2- Desempaquetar
        
        $this->extract( $ZIP , $FOLDER);
        
        //3- Instalar
        
        $this->install($FOLDER);
        
		echo "</pre>";
		
        //leer hornero.json
        //check base de datos
        //migrar base de datos
        //copy de archivos
        //check de errores
        //rollback? 
    }
}