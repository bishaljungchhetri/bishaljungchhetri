<?php 

  namespace App\Models;

  use System\Core\Model;

  class User extends Model{

    /**
     * @var string
     */
    protected $table = 'users';

    public function comments(){
      return $this->related(Comment::class,'comments','user_id');
    }

    

  }

?>