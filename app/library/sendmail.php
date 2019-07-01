<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class sendmail {
	  
	private $config = NULL;
	private $config_file = "app/config/sendmail.json";
	 
	public function load()
 	{  
 		core::getInstance()->cloneIn($this, array("db", "data", "input", "parser", "email", "upload"));
 	}

 	private function setConfig($key)
 	{
 		$this->config = file_get_json( BASEPATH.$this->config_file );

 		return $this->config->{$key};
 	}

 	private function getPostKeys($config)
 	{
		$post	= new stdClass; 
		 
		foreach($config->fields as $item)
		{  
			$post->{$item} = $this->input->post($item ,TRUE); 
		} 

		return $post;
 	}
 
 	private function getHtmlTemplate($config, $post)
 	{
        $data 	= (array) $post; 

        $data["app"]= $config->app;

 		return $this->parser->parse( $config->template , $data , TRUE ) ;
 	} 

	public function contacto()
	{     
		if(!count($_POST)) die("POST NOT SEND");

		$config = $this->setConfig("contacto");

    	$post  = $this->getPostKeys($config); 
 
        $this->db->query( INSERT($config->table, $post) ); 
 
        $this->email->from	 ( $this->email->contacto, $this->email->nombre);
        $this->email->to	 ( $config->to  	);     
        $this->email->subject( $config->subject );
        $this->email->message( $this->getHtmlTemplate($config, $post) );
 
		$this->upload->setKey($config->upload->key);
		$this->upload->setFolder($config->upload->folder);
		$this->upload->setAllowExtension($this->upload->{$config->upload->allow});

		$upload = $this->upload->process();

        if($upload->code == upload::FILE_UPLOAD_OK)  $this->email->attach($upload->file); 

        $result = $this->email->send();

        if($config->debug==true){ var_dump($result); echo  $this->email->print_debugger(); die(); }	   

        redirect($config->redir);  	
	}

	public function curriculum()
	{    
		if(count($_FILES)<1) die("FILES NOT SEND");

		$config = $this->setConfig("curriculum");
 
		$this->upload->setKey($config->upload->key);
		$this->upload->setFolder($config->upload->folder);
		$this->upload->setAllowExtension($this->upload->{$config->upload->allow});
		$this->upload->setRenameType(upload::UPLOAD_RENAME_COMP);
 
		$upload = $this->upload->process();

		if($upload->code != upload::FILE_UPLOAD_OK) die("UPLOAD CODE".$upload->code);
   
        $this->email->from	 ( $this->email->contacto, $this->email->nombre );
        $this->email->to	 ( $config->to      );         
        $this->email->subject( $config->subject );
        $this->email->message( $config->message );
        $this->email->attach ( $upload->file    ); 
        $result = $this->email->send	 ( );	
        if($config->debug==true){ var_dump($upload); var_dump($result); echo  $this->email->print_debugger(); die(); }	  


        redirect($config->redir); 	
	}

	
	public function pedido()
	{ 
		$config = $this->setConfig("pedido"); 
 
		$data = $this->get_user_data();
		$cant = $this->get_pedido($data);

		if($cant < 1 ) redirect("pedidos#empty-list"); 

		$setup 				= new stdClass;
		$setup->subject		= "Gili y Cia. - Pedido de materiales";

		$setup->message 	= $this->parser->parse("./Views/_mail_pedido.php", $this->data->get(), TRUE);

		$this->email->from   ($this->email->contacto, $this->email->nombre);
        $this->email->to     ($correo->contacto); 
        $this->email->subject($setup->subject);
        $this->email->message($setup->message);

        $this->email->send(); 
	}	
  
    public function registro()
    {
    	$config = $this->setConfig("registro");

        $this->email->from	 ( $this->email->contacto, $this->email->nombre);
        $this->email->to	 ( $config->to  	);     
        $this->email->subject( $config->subject );
        $this->email->message( $this->getHtmlTemplate( $config, $this->data->get() ) );
        $result = $this->email->send();

        if($config->debug==true){ var_dump($result); echo  $this->email->print_debugger(); die(); }	   
    }

    public function recuperar()
    {
    	$config = $this->setConfig("recuperar");

        $this->email->from	 ( $this->email->contacto, $this->email->nombre);
        $this->email->to	 ( $config->to  	);     
        $this->email->subject( $config->subject );
        $this->email->message( $this->getHtmlTemplate( $config, $this->data->get() ) );
        $result = $this->email->send();

        if($config->debug==true){ var_dump($result); echo  $this->email->print_debugger(); die(); }	   
    } 
} 
