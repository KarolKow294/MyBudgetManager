<?php

namespace App\Models;

use PDO;

class Balance extends \Core\Model {
    public static function fetchIncomesByDate($user_id, $start_date, $end_date) {
        $sql = 'SELECT incomes.amount, incomes.date_of_income, incomes.income_comment, incomes_category_assigned_to_users.name
                FROM incomes
                INNER JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
                WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :start_date AND :end_date
                ORDER BY incomes.date_of_income';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchTotalIncomesByCategoryAndDate($user_id, $start_date, $end_date) {
        $sql = 'SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) total_amount_by_category
                FROM incomes_category_assigned_to_users
                INNER JOIN incomes ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :start_date AND :end_date
                GROUP BY incomes_category_assigned_to_users.name
                ORDER BY total_amount_by_category DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function fetchExpensesByDate($user_id, $start_date, $end_date) {
        $sql = 'SELECT expenses.amount, expenses.date_of_expense, expenses.expense_comment, expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS payment_method
                FROM expenses
                INNER JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
                INNER JOIN payment_methods_assigned_to_users ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id
                WHERE expenses.user_id = :user_id AND expenses.date_of_expense BETWEEN :start_date AND :end_date
                ORDER BY expenses.date_of_expense';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchTotalExpensesByCategoryAndDate($user_id, $start_date, $end_date) {
        $sql = 'SELECT expenses_category_assigned_to_users.name, SUM(expenses.amount) total_amount_by_category
                FROM expenses_category_assigned_to_users
                INNER JOIN expenses ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                WHERE expenses.user_id = :user_id AND expenses.date_of_expense BETWEEN :start_date AND :end_date
                GROUP BY expenses_category_assigned_to_users.name
                ORDER BY total_amount_by_category DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam('start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam('end_date', $end_date, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}