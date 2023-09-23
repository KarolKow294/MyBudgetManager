<?php

namespace App\Models;

use PDO;
use \Core\View;

class IncomeCategory extends \Core\Model {
    public $categories = [];
    
    public static function copyDefaultCategories($user_id) {
        $sql = 'INSERT INTO incomes_category_assigned_to_users (name, user_id)
                SELECT incomes_category_default.name, :user_id
                FROM incomes_category_default';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function fetchCategoriesAssignedToUser($user_id) {
        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id';

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
}