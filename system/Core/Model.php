<?php

namespace System\Core;

use \System\Builders\WhereBuilder;
use \System\Database\DB;
use \System\Exceptions\QueryBuilderException;
use \System\Exceptions\DataNotFoundException;
abstract class Model extends DB {

    use WhereBuilder;
  
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $pk = 'id';

    private $query;

    private $select = '*';

    private $order;

    private $limit;

    /**
     * @var bool
     */
    protected $loaded = false;

    protected $related = [];

   

    /**
     * Sets columns to be retrieved from database.
     * 
     * @param mixed ...$columns
     * 
     * @return  Model
     */
    public function select(...$columns){
        if(count($columns) > 0){
            $this->select = implode(',',$columns);
        }

        return $this;

    }

    /**
     *Sets sorting order for data from database .
     * 
     * @param string $column
     * @param string $direction
     * 
     * @return Model
     */
    public function orderBy(string $column, $direction = 'ASC'){
        if(is_null($this->order)){
            $this->order = "{$column} {$direction}";

        }else{
           $this->order .= ", {$column} {$direction}"; 

        }
        return $this;
    }

    
    /**
     * @param string $offset
     * @param string|null $limit
     * 
     * @return Model
     */
    public function limit(string $offset ,string $limit = null){
        if(is_null($this->limit)){
            $this->limit = "{$offset}, {$limit}" ;
        }else{
            $this->limit .= ", {$offset} ,{$limit}";
        }
        return $this;
        

    }

    /**
     * Runs select query and returns data.
     * 
     * @return array
     */
    public function get(){
        $this->build('select');
        
        $this->run($this->query);

        $this->resetVars();
        
       if($this->num_rows()>0){
        $data = $this->fetch();
        $models = [];

        if(empty($this->related)){
        $class = get_class($this);
        }
        else{           
            $class = $this->related['class'];
            $this->related = [];

        }
         foreach($data as $row){
            $obj = new $class;

            foreach($row as $key => $value){
                $obj->{$key} = $value;

            }
            $obj->setLoaded(true);
            $models[] = $obj;
        }
        return $models;

        

       }else{
           return [];
       }
  
    }

    public function first(){
        $this->limit(1);
        $data = $this->get();

        if(empty($data)){
            return null;
        }else{
            return $data[0];
        }
    }

    /**
     * Loads data to current object based on given value of primary key
     * 
     * @param mixed $id
     * 
     * @throws DataNotFoundException
     */
    public function load($id){
        $this->where($this->pk, $id);
        $this->build('select');
        
        $this->run($this->query);

        $this->resetVars();
        
       if($this->num_rows()>0){
        $data = $this->fetch();
        foreach($data[0] as $key => $value){
            $this->{$key} = $value;

        }
        $this->setLoaded(true);
       }
       else{
       throw new DataNotFoundException("Data with condition '{$this->pk}= {$id}' not found in the table '{$this->table}'");
       }
    }

    public function save(){
        if($this->loaded){
            $this->build('update');
        }else{
            $this->build('insert');
        }

        $this->run($this->query);

        $this->resetVars();

        if($this->loaded){
            $id = $this->{$this->pk};
        }
        else{
            $id = $this->insertId();
        }
        $this->load($id);
    }

    public function delete(){
        $this->build('delete');

        $this->run($this->query);
        $this->resetVars();
        $variables = $this->getDataVariable();
        foreach($variables as $key => $value){
            unset($this->{$key});

        }

        $this->setloaded(false);
    }

    public function related(string $class,string $table,string $fk,string $pk = 'id',$relation = 'child'){
        $this->related = compact('class','table','fk','pk','relation');
        return $this;
    }

    /**
     * Builds sql query.
     * 
     * @param string $type
     */
    private function build(string $type){
        switch($type){
            case 'select':
                $this->buildSelect();
                break;
            
            case 'insert':
                $this->buildInsert();
                break;

            case 'update':
                $this->buildUpdate();
                break; 
                
            case 'delete':
                $this->buildDelete();
                break;   

                default:
                throw new QueryBuilderException("Invalid Type '{$type}' given for builder function.");
        }
    }

    /**
     * Builds select query from set values.
     */
    private function buildSelect(){
        if(empty($this->related)){
        $this->query = "SELECT {$this->select} FROM {$this->table}";
        }
        else{
           $this->query = "SELECT {$this->select} FROM {$this->related['table']}";

           if($this->related['relation'] == 'child'){
           $this->where($this->related['fk'],$this->{$this->pk});
           }
           else{
               $this->where($this->related['pk'],$this->{$this->related['fk']});
           }
        }
        if(!is_null($this->conditions)){
            $this->query .= " WHERE {$this->conditions}";
        }

        if(!is_null($this->order)){
            $this->query .= " ORDERBY {$this->order}";
        }

        if(!is_null($this->limit)){
            $this->query .= " LIMIT {$this->limit}";
        }
        
    }

    private function buildInsert(){
        $variables = $this->getDataVariable();
        $dataSet = [];

        foreach($variables as $key => $value){
            if(is_null($value)){
            $dataSet[] = "{$key} = NULL";
        }else{
            $dataSet[] = "{$key} = '{$value}'";
        }
    }
        $this->query = "INSERT INTO {$this->table} SET ".implode(",",$dataSet);
    }

    private function buildUpdate(){
        $variables = $this->getDataVariable();
        $dataSet = [];

        foreach($variables as $key => $value){
            if(is_null($value)){
            $dataSet[] = "{$key} = NULL";
        }else{
            $dataSet[] = "{$key} = '{$value}'";
        }
    }
        $this->query = "UPDATE {$this->table} SET ".implode(",",$dataSet)." WHERE {$this->pk}='{$this->{$this->pk}}'";
    }

  private function buildDelete(){
        $this->query = "DELETE FROM {$this->table} WHERE {$this->pk}='{$this->{$this->pk}}'";
    }
    
    /**
     * Extracts data from current object and returns it.
     * 
     * @return array
     */
    private function getDataVariable(){
        $all = get_class_vars(get_class($this));
        $objVars = get_object_vars($this);

        $dataVars = array_diff_key($objVars,$all);

        return $dataVars;
    }


    /**
     * Resets query builder variables.
     */
    private function resetVars(){
        $this->query = null;
        $this->select = '*';
        $this->conditions = null;
        $this->order = null;
        $this->limit = null;
    }

    /**
     *Sets value of loaded variable.
     * 
     *  @param bool $data
     */
    protected function setLoaded(bool $data){
        $this->loaded = $data;
    }
}
 

   
