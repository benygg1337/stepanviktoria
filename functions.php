<?php  

add_action( 'wp_enqueue_scripts', function(){

	wp_enqueue_style( 'stylesheet', get_template_directory_uri() . '/assets/css/style.min.css');

    wp_enqueue_script( 'vendors', get_template_directory_uri() . '/assets/js/vendors.min.js', [], '5.0.4', 'true' );
    wp_enqueue_script( 'app.min', get_template_directory_uri() . '/assets/js/app.min.js', array('vendors'), '6.4', 'true' );
    // wp_enqueue_script( 'gcaptcha', 'https://www.google.com/recaptcha/enterprise.js?render=6LcEYu0pAAAAAID87Wn-jeSSxObP1xRL70d2-W_R', array(), '3', false );

});

// add_theme_support('post-thumbnails');
// // add_theme_support('title-tag');
// add_theme_support('custom-logo');

// // Добавляем SVG в список разрешенных для загрузки файлов
// add_filter('upload_mimes', 'svg_upload_allow');
// function svg_upload_allow($mimes)
// {
//     $mimes['svg']  = 'image/svg+xml';
//     return $mimes;
// }

// function custom_404_template($template) {
//     if (is_404()) {
//         // Здесь указывайте путь к вашему файлу 404.php
//         $template = __DIR__ . '/404.php';
//     }
//     return $template;
// }
// add_filter('template_include', 'custom_404_template');


// # Исправление MIME типа для SVG файлов
// add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);
// function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
// {
//     if (version_compare($GLOBALS['wp_version'], '5.1.0', '>='))
//         $dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
//     else
//         $dosvg = ('.svg' === strtolower(substr($filename, -4)));

//     if ($dosvg) {
//         if (current_user_can('manage_options')) {
//             $data['ext']  = 'svg';
//             $data['type'] = 'image/svg+xml';
//         } else {
//             $data['ext'] = $type_and_ext['type'] = false;
//         }
//     }

//     return $data;
// }

add_action('wp_default_scripts', function ($scripts) {
    if (!empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
    }
});

// // В этом коде мы использовали функцию get_post() для получения объекта блока по его ID
// // Затем мы извлекаем содержимое блока из свойства post_content объекта блока и выводим его.

// function my_custom_block_shortcode($atts) {
//     $block_id = $atts['id'];
//     $block = get_post($block_id);

//     if ($block) {
//         $block_content = $block->post_content;
//         return $block_content;
//     }

//     return ''; // Возвращаем пустую строку, если блок не найден
// }
// add_shortcode('my_custom_block', 'my_custom_block_shortcode');
// add_shortcode('my_custom_block_2', 'my_custom_block_shortcode');
// add_shortcode('my_custom_block_3', 'my_custom_block_shortcode');

// Создаем тип записи для пожеланий
function create_wish_post_type() {
    $labels = array(
        'name' => 'Пожелания',
        'singular_name' => 'Пожелание',
        'menu_name' => 'Книга пожеланий',
        'add_new' => 'Добавить пожелание',
        'add_new_item' => 'Добавить новое пожелание',
        'edit_item' => 'Редактировать пожелание',
        'new_item' => 'Новое пожелание',
        'view_item' => 'Просмотреть пожелание',
        'search_items' => 'Поиск пожеланий',
        'not_found' => 'Пожелания не найдены',
        'not_found_in_trash' => 'Пожелания не найдены в корзине',
    );

    $args = array(
        'labels' => $labels,
        'public' => false, // Ставим false, чтобы пожелания были видны только администратору
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'wish'), // URL для страницы пожеланий
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor'),
    );

    register_post_type('wish', $args);
}

add_action('init', 'create_wish_post_type');

function send_notification_to_admin($post_id) {
    // Проверяем, что пост является пожеланием и имеет статус "Опубликован"
    if (get_post_type($post_id) === 'wish' && get_post_status($post_id) === 'publish') {
        $admin_email = get_option('admin_email');
        $subject = 'Новое пожелание опубликовано';
        $message = 'На сайте было опубликовано новое пожелание. Пожелание можно посмотреть здесь: ' . get_permalink($post_id);
        wp_mail($admin_email, $subject, $message);
    }
}
add_action('publish_post', 'send_notification_to_admin');


?>