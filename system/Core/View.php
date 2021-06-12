<?php


  namespace System\Core;

  use \System\Exception\ViewNotFoundException;

  class View {

      /**
       * loads view with given data
       * 
       * @param string $file
       * @param array $data
       * 
       * @throws ViewNotFoundException
       */
      public function __construct(string $file, array $data){
          $viewBase = BASEPATH."/views/";

         

          if(is_file($viewBase.$file)){
              require $viewBase.$file;

          }else{
              throw new ViewNotFoundException("View file '{$file}' not found inside '{$viewBase}'");
          }
      }
  }








?>