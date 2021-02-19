<?php


namespace application\models;


use application\core\Model;

class Account extends Model
{

    public function validateForm($input, $post)
    {
        $rules = [
            'email' => [
                'pattern' => '#^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$#',
                'message' => 'E-mail is uncorrected',
            ],
            'login' => [
                'pattern' => '#^[a-zA-Z0-9]{3,15}$#',
                'message' => 'Login is uncorrected (eng, 3-15)',
            ],
            'password' => [
                'pattern' => '#^[a-zA-Z0-9]{4,15}$#',
                'message' => 'Password is uncorrected (eng, 10-15)',
            ],
        ];
        foreach ($input as $val) {
            if (!isset($post[$val]) or empty($post[$val]) or !preg_match($rules[$val]['pattern'], $post[$val])) {

                $this->error = $rules[$val]['message'];
                return false;
            }
        }
        return true;

    }

    public function checkData($login, $password)
    {
        $params = [
            'login' => $login,
        ];

        $hash = $this->db->column('Select password from users where login = :login', $params);

        if (!$hash or !password_verify($password, $hash)) {
            return false;
        }
        return true;
    }

    public function checkStatus($type, $data)
    {
        $params = [
            $type => $data,
        ];

        $status = $this->db->column('Select status from users where ' . $type . ' = :' . $type . ' ', $params);
        if ($status != 1) {
            $this->error = 'Аккаунт не подтвержден';
            return false;
        }
        return true;

    }

    public function login($login)
    {
        $params = [
            'login' => $login,
        ];

        $data = $this->db->row('Select * from users where login = :login', $params);

        $_SESSION['account'] = $data[0];

    }

    public function checkEmailExists($email)
    {
        $params = [
            'email' => $email,
        ];
        if ($this->db->column('Select id from users where email = :email', $params)) {
            $this->error = 'Такой email уже существет';
            return false;
        }
        return true;
    }

    public function checkLoginExists($login)
    {
        $params = [
            'login' => $login,
        ];
        if ($this->db->column('Select id from users where login = :login', $params)) {
            $this->error = 'Такой логин уже существет';
            return false;
        }
        return true;
    }

    public function checkTokenExists($token)
    {
        $params = [
            'token' => $token,
        ];

        return $this->db->column('Select id from users where token = :token', $params);
    }

    public function createToken()
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmonpqrtsuvwxyz', 30)), 0, 30);
    }

    public function activateAccount($token)
    {
        $params = [
            'id' => $this->checkTokenExists($token),
        ];
        $this->db->query('UPDATE users SET status = 1, token = "" WHERE id = :id', $params);
    }

    public function register($post)
    {
        $token = $this->createToken();
        $params = [
            'login' => $post['login'],
            'email' => $post['email'],
            'password' => password_hash($post['password'], PASSWORD_BCRYPT),
            'role_id' => 4,
            'token' => $token,
            'status' => 0
        ];

        $this->db->query('INSERT INTO `users` (login, password, email, role_id, token, status) VALUES (:login, :password, :email, :role_id, :token, :status)', $params);
        mail($post['email'], 'Register', 'Confirm: link' . $token);
    }
}