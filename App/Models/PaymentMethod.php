<?php

namespace App\Models;

use PDO;
use \Core\View;

class PaymentMethod extends \Core\Model
{
    public $name;
    public $save_success;
    public $save_errors = [];
    public $update_success;
    public $update_errors = [];
    public $delete_success;
    public $delete_errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
          $this->$key = $value;
        }
    }

    public static function copyDefaultMethods($user_id)
    {
        $sql = 'INSERT INTO payment_methods_assigned_to_users (name, user_id)
                SELECT payment_methods_default.name, :user_id
                FROM payment_methods_default';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function fetchMethodsAssignedToUser($user_id)
    {
        $sql = 'SELECT id, name
                FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();

        $rows = $stmt->fetchAll();

        $methods = [];

        foreach ($rows as $row) {
            $methods[$row['id']] = $row['name'];
        }
        
        return $methods;
    }

    public function save()
    {
        $user_id = $_SESSION['user_id'];

        $this->save_errors = $this->validate($user_id);


        if (empty($this->errors)) {
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);

            $this->save_success = true;

            return $stmt->execute();
        }
        return false;
    }

    private function validate($user_id)
    {
        $errors = [];
        
        if ($this->name != '') {
            $methods = $this->fetchMethodsAssignedToUser($user_id);

            foreach ($methods as $method) {
                if ($method == $this->name) {
                    $errors[] = 'Metoda płatności już istnieje';
                }
            }
            
        } else {
            $errors[] = 'Nazwa jest wymagana';
        }
        return $errors;
    }
}