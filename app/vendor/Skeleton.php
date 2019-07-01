<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    Dependencias:
        - Parser ok
        - que exista cache/css
        - Exista: $config_file
        - Exista: $vars_file
        - Que exista un layout
        - Que exista al menos un "content"
*/ 

class Skeleton {

	private $config;
	private $route;
	private $parser;
    private $config_file = "app/config/view.json";
    private $vars_file   = "app/config/vars.json";

    private $create_css = FALSE;

    private $css = array();

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
		$this->before_connect($this->config_file);
		$this->parser = Parser::getInstance();
	}

    public function before_connect($config, $extern=FALSE)
    {
		if($extern==FALSE)
			$this->config = file_get_json(BASEPATH.$config);
		else
			$this->config = file_get_json($config);
		
		 
    }

    public function set_route($route)
    {
    	$this->route = $route;
    }

    private function _extract_css($file)
    {
        $cssName = pathinfo($file, PATHINFO_FILENAME);
        $cssDir  = pathinfo($file, PATHINFO_DIRNAME );
        $cssFile = $cssDir."/".$cssName.".css";

        if(is_file($cssFile))
        {
            $this->css[] = file_get_contents($cssFile);
        }
        else
        { 
            _LOG(core::getInstance(), __CLASS__, "No se puede extraer el css de {$cssFile}");
        }
    }

    private function _write_css()
    {
        $cachedir = BASEPATH."ui/css/cache/";

        $data = implode("", $this->css);
        $path = "{$cachedir}{$this->route}.css";

        if(!is_dir($cachedir))
        {
            if(mkdir($cachedir,0777,TRUE)==FALSE)
            {
                _LOG(core::getInstance(), __CLASS__, "No se puede crear {$cachedir}");
            }
        }

        file_put_contents($path, $data);
    }

    public function build()
    {
        $skeleton = "";

        if(isset($this->config->{$this->route}))
        {
            $skeleton = $this->config->{$this->route};
        }
        else
        {
            _LOG(core::getInstance(), __CLASS__, "Error al procesar la ruta [{$this->route}]");
            return "";
        }

    	$content  = array();

        if(isset($skeleton->content))
            if(is_array($skeleton->content))
            {
            	foreach( $skeleton->content as $pieces)
            	{
                    if(is_file($pieces->file))
                    {
                        $content[] = $this->parser->parse( $pieces->file , array() , TRUE);
                        $this->_extract_css($pieces->file);
                    }
                    else
                    {
                        _LOG(core::getInstance(), __CLASS__, "Content - {$pieces->file} no existe");
                    }
            	}
            }
            else
            {
                if(is_file($skeleton->content))
                {
                    $content[] = $this->parser->parse( $skeleton->content , array() , TRUE);
                    $this->_extract_css($skeleton->content);
                }
                else
                {
                    _LOG(core::getInstance(), __CLASS__, "Content - {$skeleton->content} no existe");
                }
            }

        $header = "";

        if(isset($skeleton->header))
            if(is_array($skeleton->header))
            {
                foreach( $skeleton->header as $pieces)
                {
                    if(is_file($pieces->file))
                    {
                        $content[] = $this->parser->parse( $pieces->file , array() , TRUE);
                        $this->_extract_css($pieces->file);
                    }
					else
					{
						_LOG(core::getInstance(), __CLASS__, "header - {$pieces->file} no existe");
					}
                }
            }
            else
            {
                if(is_file($skeleton->header))
                {
                    $header = $this->parser->parse( $skeleton->header , array()  , TRUE);
                    $this->_extract_css($skeleton->header);
                }
				else
				{
					_LOG(core::getInstance(), __CLASS__, "header - {$skeleton->header} no existe");
				}
            }

        $footer = "";

        if(isset($skeleton->footer))
            if(is_array($skeleton->footer))
            {
                foreach( $skeleton->footer as $pieces)
                {
                    if(is_file($pieces->file))
                    {
                        $content[] = $this->parser->parse( $pieces->file , array() , TRUE);
                        $this->_extract_css($pieces->file);
                    }
					else
					{
						_LOG(core::getInstance(), __CLASS__, "footer - {$pieces->file} no existe");
					}
                }
            }
            else
            {
                if(is_file($skeleton->footer))
                {
                    $footer = $this->parser->parse( $skeleton->footer , array()  , TRUE);
                    $this->_extract_css($skeleton->footer);
                }
				else
				{
					_LOG(core::getInstance(), __CLASS__, "footer - {$skeleton->footer} no existe");
				}
            }

		$layout  = array
		(
			'header'  => $header,
			'content' => count($content) ? implode("",$content) : "",
			'footer'  => $footer
		);

        $output ="";

        if(isset($skeleton->layout))
        {
             if(is_file($skeleton->layout))
             {
                $output = $this->parser->parse( $skeleton->layout , $layout, TRUE );
                $this->_extract_css($skeleton->layout);
             }
             else
             {
                _LOG(core::getInstance(), __CLASS__, "Layout - {$skeleton->layout} no existe ");
             }
        }

    	return $output;
    }


    private function attach_vars($route, &$data)
    {
        $vararray = file_get_json(BASEPATH.$this->vars_file);

        if( isset($vararray->{$route}))
        {
            $item = $vararray->{$route};

            foreach ($item as $valueobject)
            {
                if(isset($valueobject->name) && isset($valueobject->value))
                {
                    $key = $valueobject->name;
                    $val = "";

                    if(is_string($valueobject->value))
                    {
                        $val = $valueobject->value;
                    }
                    else
                    {
                        if(is_array($valueobject->value))
                        {
                            $val = array();

                            foreach ($valueobject->value as $itemvar)
                            {
                                $cell = array();
                                foreach($itemvar as $k=>$v)
                                {
                                    $cell [$k]=$v;
                                }
                                $val[]=$cell;
                            }
                        }
                    }

                    $data[$key]=$val;
                }
            }
        }
    }

    public function write($route, $data=array(), $return=FALSE)
    {
	    $this->set_route($route);

        $data["csscache"]=$route; 

        $html = $this->build();

        if( $this->create_css ==TRUE )$this->_write_css();

        $this->attach_vars("_global", $data);
        $this->attach_vars($route   , $data);
        
        for ($i=0; $i<3; $i++) 
        { 
            $html = $this->parser->parse_string(  $html , $data , TRUE);
        }
 
        if($return == TRUE)
        {
            return $html;
        }
        else
        {
            header('Content-type: text/html; charset=utf8');
            echo ($html);
        }
    }

    public function get_config()
    {
        return $this->config->{$this->route};
    }

}
/* End of file */