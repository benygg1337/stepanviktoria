<?php
// Подключаем файл wp-load.php для работы с WordPress функциями
require_once('../../../wp-load.php');

// Проверяем, был ли передан идентификатор пожелания
if (isset($_GET['wish_id'])) {
    $wish_id = intval($_GET['wish_id']);
    // Обновляем статус пожелания на "Опубликован"
    wp_update_post(array('ID' => $wish_id, 'post_status' => 'publish'));
    // Редиректим обратно на страницу администратора или на страницу пожеланий
    wp_safe_redirect(admin_url('edit.php?post_type=wish'));
    exit;
}
