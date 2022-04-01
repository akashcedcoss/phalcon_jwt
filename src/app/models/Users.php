<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $jwt;
    // public $quantity;
}