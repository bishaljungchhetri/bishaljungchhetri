<?php

namespace System\Database;

use \PDO;

/**
 * Class to connect and handle database interactions.
 */
  abstract class DB {

    private $conn;
    private $result;

    /**
     * Default constructor for DB class that connects to the database
     * 
     * @param string $host
     * @param string $dbname
     * @param string $username
     * @param  string
     */

    public function __construct()
    {
        $this->conn = new PDO("mysql:host=".config('db_host').";dbname=".config('db_name'),config('db_user'),config('db_pass'));

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Default destructor to disconnect database.
     */
    public function __destruct()
    {
        $this->conn = null;
    }
/*query run garna ko lagi */
    /**
     * Execute the given database query.
     * 
     * @param string $sql
     * 
     * @return bool
     */
    public function run(string $sql){
       
        $this->result = $this->conn->prepare($sql);
        $this->result->execute();
        return true;

        
    }

    /**
     * Fetch data array from the result and return it
     * 
     * @return array
     */
    public function fetch(){
        $this->result->setFetchMode(PDO::FETCH_ASSOC);
        return $this->result->fetchAll();
    }

    /**
     * Returns primary key from last inserted data.
     * 
     * @return mixed
     */
    public function insertID(){
        return $this->conn->lastInsertId();

    }

    /**
     * Returns count of data from the result.
     * 
     * @return int
     */
    public function num_rows(){
        return $this->result->rowCount();
    }

    /**
     *Returns last run data base query.
     * 
     * @return string
     */
    public function last_query(){
        return $this->result->queryString;
     }


}

?>