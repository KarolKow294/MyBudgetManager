<?php

namespace App\Models;

use PDO;
use \Core\View;

class ExpenseCategory extends \Core\Model
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

        $this->save_errors = $this->validate($user_id);


        if (empty($this->save_errors)) {
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
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

            $sql = 'UPDATE expenses_category_assigned_to_users
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

            $sql = 'DELETE FROM expenses_category_assigned_to_users
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

    private function validateNewName($user_id)
    {
        $errors = [];

        if ($this->new_name != '') {
            $categories = $this->fetchCategoriesAssignedToUser($user_id);

            foreach ($categories as $category) {
                if ($category == $this->new_name) {
                    $errors[] = 'Nazwa kategorii jest już zajęta';
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
            $categories = $this->fetchCategoriesAssignedToUser($user_id);

            $categoryExist = false;

            foreach ($categories as $key => $category) {
                if ($key == $this->id) {
                    $categoryExist = true;
                    $this->name = $category;
                }
            }

            if (!$categoryExist) {
                $errors[] = 'Kategoria nie istnieje';
            }
            
        } else {
            $errors[] = 'Kategoria jest wymagana';
        }

        return $errors;
    }
}