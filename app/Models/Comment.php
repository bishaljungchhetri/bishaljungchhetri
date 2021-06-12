<?php 

namespace App\Models;

use \System\Core\Model;

class Comment extends Model{

    protected $table = 'comments';

    public function user(){
        return $this->related(User::class,'users','user_id','id','parent');
    }


}






?>