<?php
// Подключаем файл wp-load.php для работы с WordPress функциями
require_once('../../../wp-load.php');

// Проверяем, был ли передан идентификатор пожелания
if (isset($_GET['wish_id'])) {
    $wish_id = intval($_GET['wish_id']);
    // Удаляем пожелание
    wp_delete_post($wish_id, true);
    // Редиректим обратно на страницу администратора или на страницу пожеланий
    wp_safe_redirect(admin_url('edit.php?post_type=wish'));
    exit;
}
