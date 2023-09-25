<?php

namespace App\Models;

use PDO;
use \Core\View;

class PaymentMethod extends \Core\Model
{
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
}