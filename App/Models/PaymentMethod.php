<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\Expense;

class PaymentMethod extends \Core\Model
{
    public $id;
    public $name;
    public $new_name;
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


        if (empty($this->save_errors)) {
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

    public function update()
    {
        $user_id = $_SESSION['user_id'];

        $this->update_errors = $this->validateNewName($user_id);

        if (empty($this->update_errors)) {

            $sql = 'UPDATE payment_methods_assigned_to_users
                    SET name = :new_name
                    WHERE id = :id AND user_id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':new_name', $this->new_name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            $this->update_success = true;

            return $stmt->execute();
        }
        return false;
    }

    public function delete()
    {
        $user_id = $_SESSION['user_id'];

        $this->delete_errors = $this->validateDelete($user_id);

        if (empty($this->delete_errors)) {

            $sql = 'DELETE FROM payment_methods_assigned_to_users
                    WHERE id = :id AND user_id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            $this->delete_success = true;

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
                if (strtolower($method) == strtolower($this->name)) {
                    $errors[] = 'Metoda płatności ' . $method . ' już istnieje';
                }
            }
            
        } else {
            $errors[] = 'Nazwa jest wymagana';
        }
        return $errors;
    }

    private function validateNewName($user_id)
    {
        $errors = [];

        if ($this->new_name != '') {
            $methods = $this->fetchMethodsAssignedToUser($user_id);

            foreach ($methods as $method) {
                if (strtolower($method) == strtolower($this->new_name)) {
                    $errors[] = 'Nazwa metody płatności ' . $method . ' jest już zajęta';
                }
            }
            
        } else {
            $errors[] = 'Nazwa jest wymagana';
        }

        return $errors;
    }

    private function validateDelete($user_id)
    {
        $errors = [];

        if ($this->id != '') {
            $methods = $this->fetchMethodsAssignedToUser($user_id);

            $methodExist = false;

            foreach ($methods as $key => $method) {
                if ($key == $this->id) {
                    $methodExist = true;
                    $this->name = $method;
                }
            }

            if (!$methodExist) {
                $errors[] = 'Metoda płatności nie istnieje';
            }
            
        } else {
            $errors[] = 'Metoda płatności jest wymagana';
        }

        if (in_array($this->id, Expense::fetchExpenseIdAndMethodIdAssignedToUser())) {
            $errors[] = 'Metoda jest przypisana do wydatków. Usuń te wydatki lub zmień w nich metodę.';
        }

        return $errors;
    }
}