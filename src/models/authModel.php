<?php

namespace Models;

/**
* Authentication models
*/
class Auth extends Database
{
    private $login;
    private $username;
    private $role;

    public function __construct()
    {
        parent::__construct();
        $this->login = $this->isLogin();
    }

    private function createTable()
    {
        $create = "CREATE TABLE IF NOT EXISTS user (id integer primary key, username text unique not null, password text, role text default 'user')";
        return $this->db->query($create);
    }

    public function isLogin()
    {
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            return true;
        } else {
            $_SESSION['login'] = false;
            return false;
        }
    }

    public function login($username, $password)
    {
        $userCheck = "SELECT * FROM user WHERE username = :username and password = :password";
        $prepare = $this->db->prepare($userCheck);
        $prepare->execute([
            ':username' => $username,
            ':password' => $password,
        ]);
        $res = $prepare->fetch();
        if (!empty($res)) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $res['username'];
            $_SESSION['role'] = $res['role'];
        } else {
            $_SESSION['login'] = false;
        }
        return $_SESSION['login'];
    }

    public function logout()
    {
        session_destroy();
    }
}
