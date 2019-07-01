<?php
$App = core::getInstance();


$App->get("halcon-folder-list", function(){

    $list       = array(); 
    $c=1;
    foreach( scandir("../") as $dir )
    { 
        if( !in_array( $dir , array(".","..", "desktop.ini", "hidden", "index.php") ) )
        {
            $item       = new stdclass;
            $item->id   = $c;
            $item->name = $dir;
            $item->type = "Carpeta de archivos";
            $item->size = "";
            $item->date = date("d/m/Y",filemtime("../{$dir}"));
            $item->web = base_url()."../".$dir;
            
            $list[]= $item;
            $c++;
        }
    }
      
    die( json_encode($list) ); 

});


$App->get("halcon-file-list", function($config){


    //var_dump($config);
 
    $path = pathinfo( $config, PATHINFO_DIRNAME )."/views/";
    $list = array(); 
    $c    = 1;

    foreach( scandir($path) as $dir )
    { 
        if( !in_array( $dir , array(".","..") ) )
        {
            $item       = new stdclass;
            $item->id   = $c; 
            $item->name = $dir;
            $item->path = $path.$dir;
            $item->value= pathinfo($dir, PATHINFO_FILENAME) ;
            
            if( is_dir($path.$dir) )
            {
                $item->type = "Carpeta de archivos";
                $item->tipo = "folder";
                $item->size = "";
            }
            else
            {
                $item->type = "Archivo ". pathinfo( $dir, PATHINFO_EXTENSION  );
                $item->tipo = "file";
                $item->size = filesize($path.$dir)." bytes";
            }
 
            $item->date = date("d/m/Y",filemtime("{$path}{$dir}"));
            $item->web  = base_url().$path.$dir;
            
            $list[]= $item;
            $c++;
        }
    }
      
    die( json_encode($list) ); 

});




$App->get("halcon-pages-blocks", function($project){

    //check if folder
    //check if ./app/config/theme.json
    //check if view.json

    $path_project = "../{$project}";
    $theme_path   = "{$path_project}/app/config/theme.json"; 

    if( !is_dir($path_project) ) die("[path_project not found]");
    if( !is_file($theme_path)  ) die("[theme_path not found]");

    $theme = file_get_json($theme_path);

    $view_json = "{$path_project}/{$theme->path}{$theme->view}";

    if( !is_file($view_json)  ) die("[view_json not found]");

    $view = file_get_json($view_json);

    $c = 1;

    $tree_data = array();

    foreach ($view as $page => $blocks) 
    {
        $page_block = new stdclass;

        $page_block->id    = $c;
        $page_block->open  = true;
        $page_block->value = $page; 
        $page_block->icon  = "sitemap";
        $page_block->type  = "view";

        //var_dump($page);
        //var_dump($blocks);

        $child_array   = array();
        $sub =1;

        if(isset($blocks->layout))
        if($blocks->layout)
        {
            $layout        = new stdclass;
            $layout->id    = "{$c}_{$sub}";
            $layout->value = pathinfo($blocks->layout, PATHINFO_FILENAME) ;
            $layout->icon  = "microchip";
            $layout->type  = "layout";
            $layout->path  = "{$path_project}/{$theme->path}views/{$blocks->layout}";
            $layout->view  = $page;
            $layout->config= $view_json;
            $layout->file  = $blocks->layout;
            $child_array []= $layout;
        }

        if(isset($blocks->header))
        if($blocks->header)
        {
            $sub++;
            $header        = new stdclass;
            $header->id    = "{$c}_{$sub}";
            $header->value = pathinfo($blocks->header, PATHINFO_FILENAME) ;
            $header->icon  = "arrow-circle-up";
            $header->type  = "header";
            $header->path  = "{$path_project}/{$theme->path}views/{$blocks->header}";
            $header->view  = $page;
            $header->config  = $view_json;
            $header->file  = $blocks->header;
            $child_array []= $header;
        }

        if(isset($blocks->footer))
        if($blocks->footer)
        {
            $sub++;
            $footer        = new stdclass;
            $footer->id    = "{$c}_{$sub}";
            $footer->value = pathinfo($blocks->footer, PATHINFO_FILENAME) ;
            $footer->icon  = "arrow-circle-down";
            $footer->type  = "footer";
            $footer->path  = "{$path_project}/{$theme->path}views/{$blocks->footer}";
            $footer->view  = $page;
            $footer->config  = $view_json;
            $footer->file  = $blocks->footer;
            $child_array []= $footer;
        }


        foreach ($blocks->content as $blk) 
        {
            $sub++;
            $content          = new stdclass;
            $content->id      = "{$c}_{$sub}";
            $content->pos     = $sub;
            $content->value   = pathinfo($blk->file, PATHINFO_FILENAME) ;
            $content->icon    = "puzzle-piece";
            $content->type    = "content";
            $content->path    = "{$path_project}/{$theme->path}views/{$blk->file}";
            $content->view    = $page;
            $content->config  = $view_json;
            $content->file     = $blk->file;
            $child_array    []= $content;
        }

        $page_block->data = $child_array;

        $tree_data[]= $page_block;
        $c++;
    }

   die( json_encode($tree_data) ); 
});


$App->get("halcon-view-source", function(){

    $post = $this->input->post();

    $post->source = json_decode($post->source);

    if(!isset($post->source->path)) die("[]");
    if( !is_file($post->source->path) ) die("[]");
    
    $o  = new stdclass;

    $o->html = file_get_contents($post->source->path);

    die( json_encode($o) ); 
});



$App->get("halcon-save-source", function(){

    $post = $this->input->post();

    $post->source = json_decode($post->source);
    $post->code   = base64_decode($post->code);

    if( !isset  ($post->source->path) ) die('{"result":false, "message":"path isnt set"}');
    if( !is_file($post->source->path) ) die('{"result":false, "message":"path isnt file"}');
    

    file_put_contents($post->source->path, $post->code);

    die('{"result":true, "message":"udpate succesfully"}'); 
});


/*
{
    "index" :
    { 
        "layout" : "_layout.php",
        "content": 
        [ 
            { "name":"1", "file": "_topbar.php"          }
        ] 
    }
} 
 */


$App->get("halcon-update-tree", function(){

    $post         = $this->input->post();
    $post->source = json_decode($post->source);
    $post->order  = json_decode($post->order );

    if( !isset   ( $post->source->project ) ) die('{"result":false, "message": "project isnt set"}');
    if( !is_array( $post->order           ) ) die('{"result":false, "message": "order isnt set"}');


    $path_project = "../{$post->source->project}";
    $theme_path   = "{$path_project}/app/config/theme.json"; 

    if( !is_dir($path_project) ) die("[path_project not found]");
    if( !is_file($theme_path)  ) die("[theme_path not found]");

    $theme = file_get_json($theme_path);

    $view_json_file = "{$path_project}/{$theme->path}{$theme->view}";

    if( !is_file($view_json_file)  ) die("[view_json not found]");


    $array_hash = array();

    $view_json_data = new stdclass;

    foreach ($post->order as $view_object) 
    { 
        $view = $view_object->value;

        $view_blocks = new stdclass;
        $view_blocks->content =  array();

        if(isset($view_object->data))
            foreach ($view_object->data as $block) 
            {    
                if( !isset( $array_hash[$block->id] ) )
                {
                    switch ($block->type) 
                    {
                        case 'layout' : $view_blocks->layout =  $block->file; break; 
                        case 'header' : $view_blocks->header =  $block->file; break; 
                        case 'footer' : $view_blocks->footer =  $block->file; break; 
                        case 'content':  
                            $block_part = new stdclass;
                            $block_part->name = $block->value;
                            $block_part->file = $block->file;

                            $view_blocks->content[]= $block_part;
                        break; 
                    } 
                    $array_hash[$block->id]=$block->id;
                } 
            } 
        $view_json_data->{$view} = $view_blocks;

    } 

    $new_json_view = json_encode($view_json_data, JSON_PRETTY_PRINT);

    file_put_contents($view_json_file, $new_json_view);

    die('{"result":true, "message": "succesfully updated"}');

});