<?php

namespace App\Controllers;

use \System\Core\Controller;
use \App\Models\User;
use PDO;

class UserController extends Controller{
    public function index(){
        $user = new User;
        $users = $user->get();

        $title = 'Users';
       
        view('user/index.php',compact("users","title"));
    }

    public function create(){
        view('user/create.php');
    }
}