<?php

namespace App\Models;

use PDO;

class Balance extends \Core\Model {
    public static function fetchIncomesByDate($user_id) {
        $sql = 'SELECT incomes.amount, incomes.date_of_income, incomes.income_comment, incomes_category_assigned_to_users.name
                FROM incomes
                INNER JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
                WHERE incomes.user_id = :user_id
                ORDER BY incomes.date_of_income';
        //, incomes.date_of_income BETWEEN :start_date AND :end_date

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchTotalIncomesByCategoryAndDate($user_id) {
        $sql = 'SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) total_income
                FROM incomes_category_assigned_to_users
                INNER JOIN incomes ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                WHERE incomes.user_id = :user_id
                GROUP BY incomes_category_assigned_to_users.name
                ORDER BY total_income DESC';
            //, incomes.date_of_income BETWEEN :start_date AND :end_date

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}