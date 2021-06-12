<?php


   if(!function_exists('config')){

     /**
      * Returns the configuration value for the given key
      * @param string $key
      * 
      * @return string|bool
      */
     function config(string $key){
         require BASEPATH."/config/settings.php";
         
         if(array_key_exists($key, $config)){
             return $config[$key];
         }
         else{
             return false;
         }

     }
   }

   if(!function_exists('view')){

    /**
     * loads view with given data.
     * 
     * @param string $file
     * @param array $data
     */
    function view(string $file, array $data = []){
        new \System\Core\View($file,$data);
    }
   }

   if(!function_exists('url')){
       function url(string $uri = ""){
          return config('base_url').$uri;
       }
   }




?>