<?php

namespace App\Models;

use PDO;
use \Core\View;

class ExpenseCategory extends \Core\Model
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

    public static function copyDefaultCategories($user_id)
    {
        $sql = 'INSERT INTO expenses_category_assigned_to_users (name, user_id)
                SELECT expenses_category_default.name, :user_id
                FROM expenses_category_default';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function fetchCategoriesAssignedToUser($user_id)
    {
        $sql = 'SELECT id, name
                FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();

        $rows = $stmt->fetchAll();

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['id']] = $row['name'];
        }
        
        return $categories;
    }

    public function save()
    {
        $user_id = $_SESSION['user_id'];

        $this->$edit_errors = $this->validate($user_id);


        if (empty($this->edit_errors)) {
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);

            $this->edit_success = true;

            return $stmt->execute();
        }
        return false;
    }

    private function validate($user_id)
    {
        $errors = [];

        if ($this->name != '') {
            $categories = $this->fetchCategoriesAssignedToUser($user_id);

            foreach ($categories as $category) {
                if ($category == $this->name) {
                    $errors[] = 'Kategoria już istnieje';
                }
            }
            
        } else {
            $errors[] = 'Nazwa jest wymagana';
        }

        return $errors;
    }
}