<?php

use Phalcon\Mvc\Model;

class Permission extends Model
{
    public $id;
    public $role;
    public $controller;
    public $action;
}