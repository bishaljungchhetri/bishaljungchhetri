<?php

namespace System\Builders;

trait WhereBuilder{

    private $conditions;

    
    public function where(string $column,string $operator,string $value = null){
        if(is_null($value)){
            $condition = " {$column} = '{$operator}'";
        }
        else{
          $condition = " {$column} {$operator} '{$value}'";
        }
        if(is_null($this->conditions)){
            $this->conditions = $condition;
        }else{
            $this->conditions = "{$this->conditions} AND {$condition}";
        }
        return $this;
  }


    /**
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * 
     * @return Model
     */
    public function orWhere(string $column,string $operator,string $value = null){
          if(is_null($value)){
              $this->conditions .= "OR {$column} = '{$operator}'";
          }
          else{
            $this->conditions .= "OR {$column} {$operator} '{$value}'";
          }
          return $this;
    }

    /**
     * Sets condition to check if given column is null.
     * 
     * @param mixed $column
     * 
     * @return Model
     */
    public function whereNull($column){
        if(is_null($this->conditions)){
            $this->conditions = "{$column} IS NOT NULL";
        }
        else{
            $this->conditions .= "AND {$column} IS NOT NULL";

        }
        return $this;
    }

    
}

?>