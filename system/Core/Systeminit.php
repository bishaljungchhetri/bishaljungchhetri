<?php
   
   namespace System\Core;

   use \System\Exceptions\NotControllerException;
   use \Whoops\Run;
   use \Whoops\Handler\PrettyPageHandler;
   class SystemInit{

    public function __construct(){

        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
        
        date_default_timezone_set(config('timezone'));
        
    }

    public function start(){
        $parts = $this->getUrlParts();
        $this->loadController($parts);
    }

    /**
     * Loads controller based on the url parts 
     * 
     * Returns parts of current URL removing base URL.
     * 
     * @return [type]
     */
    private function getUrlParts(){
        $baseUrl = config('base_url');
        $fullUrl = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
        
        $uri = str_replace($baseUrl,'',$fullUrl);
        $parts = explode('/',$uri);
        return $parts;
    }

    /**
     * @param array $parts
     * 
     * @throws NotControllerException
     * @throws \Error
     */
    private function loadController(array $parts){
        if(empty($parts[0])){
            $controller  = config('default_controller');
        }
        else{
            $controller = $parts[0];
        }
        $class = "\App\Controllers\\".ucfirst($controller)."Controller";
       $obj = new $class;

       if($obj instanceof Controller){
           if(!empty($parts[1])){
               $method = $parts[1];
           }else{
               $method = 'index';
           }

           if(!empty($parts[2])){
               $obj->{$method}($parts[2]);
           }
           else{
               $obj->{$method}();
           }

       }else{
           throw new NotControllerException("Class '{$class}' is  not an instance of '\System\Core\Controller'");
       }
       
    }
    

   }





?>