<?php

namespace Models;

use \PDO;

/**
* Database connection models
*/
class Database
{
    protected $db;
    public function __construct()
    {
        $this->db = new PDO("sqlite:".__DIR__."/../../zackadtech.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
