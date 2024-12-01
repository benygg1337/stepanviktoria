<?php
// Файлы phpmailer
require __DIR__ . '/php/PHPMailer.php';
require __DIR__ . '/php/SMTP.php';
require __DIR__ . '/php/Exception.php';

// Подключаем файл wp-load.php для работы с WordPress функциями
require_once('../../../wp-load.php');

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, есть ли данные в форме
    if (isset($_POST['form-name_book']) && isset($_POST['form-message'])) {
        // Получаем данные из формы
        $name = sanitize_text_field($_POST['form-name_book']);
        $message = sanitize_text_field($_POST['form-message']);

        // Создаем новый черновик пожелания
        $post_data = array(
            'post_title' => $name,
            'post_content' => $message,
            'post_status' => 'draft',
            // Статус черновика
            'post_type' => 'wish', // Название созданного типа записи 
        );

        // Вставляем пожелание как черновик
        $post_id = wp_insert_post($post_data);

        // Проверяем, было ли успешно создано пожелание
        if ($post_id !== 0) {
            // Настройки PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer();

            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPAuth = true;
            $mail->Debugoutput = function ($str, $level) {
                $GLOBALS['data']['debug'][] = $str;
            };

            // Настройки вашей почты
            $mail->Host = 'mail.marryme-invites.ru';
            $mail->Username = 'noreply@marryme-invites.ru';
            $mail->Password = '4638743aA';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('noreply@marryme-invites.ru', 'Свадебный сайт');

            // Получатель письма
            $mail->addAddress('loko419@yandex.ru');
            $mail->addAddress('dimon-951@mail.ru');
            $mail->addAddress('1337beny@gmail.com');

            // Формируем ссылки на подтверждение и удаление
            $theme_url = get_stylesheet_directory_uri();
            $confirmUrl = $theme_url . '/confirm-wish.php?wish_id=' . $post_id;
            $deleteUrl = $theme_url . '/delete-wish.php?wish_id=' . $post_id;

            $theme_uri = get_stylesheet_directory_uri();

            // Вставляем HTML-шаблон письма здесь
            $html_message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>HTML Template</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <style>
                    /* Ваш CSS стиль */
                    body {
                        width: 100% !important;
                        -webkit-text-size-adjust: 100%;
                        -ms-text-size-adjust: 100%;
                        margin: 0;
                        padding: 0;
                        line-height: 100%;
                    }
                    [style*="Open Sans"] {font-family: \'Open Sans\', arial, sans-serif !important;}
                    img {
                        outline: none;
                        text-decoration: none;
                        border:none;
                        -ms-interpolation-mode: bicubic;
                        max-width: 100%!important;
                        margin: 0;
                        padding: 0;
                        display: block;
                    }
                    table td {
                        border-collapse: collapse;
                    }
                    table {
                        border-collapse: collapse;
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                    }
                    @media (max-width: 650px) {
                      .table-650 {
                        width: 280px !important;
                      }
                    }
                </style>
            </head>
            <body style="margin: 0; padding: 0;">
                <div style="font-size:0px;font-color:#ffffff;opacity:0;visibility:hidden;width:0;height:0;display:none;">
                    Тестовое письмо
                </div>
                <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ededed">
                    <tr>
                        <td>
                            <table align="center" class="table-700" cellpadding="0" cellspacing="0" width="700" bgcolor="#F2EEEB">
                                <tr>
                                    <td>
                                        <table align="center" class="table-650" cellpadding="0" cellspacing="0" width="650">
                                            <tr>
                                                <td align="center" style="padding-top: 40px; padding-bottom: 40px;">
                                                    <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/merry-me.png" alt="Marry me <3">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 100px; border-bottom: 1px solid #00000059;">
                            <table align="center" class="table-700" cellpadding="0" cellspacing="0" width="700" bgcolor="#F2EEEB">
                                <tr>
                                    <td style="border: 1px solid #000000;">
                                        <table align="center" class="table-650" cellpadding="0" cellspacing="0" width="650">
                                            <tr>
                                                <td align="center" style="padding-top: 25px; padding-bottom: 40px;">
                                                    <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/img-1.png" alt="main-img" width="650">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table align="center" class="table-620" cellpadding="0" cellspacing="0" width="620">
                                                        <tr>
                                                            <td>
                                                                <p style="font-family: Verdana, Geneva, Tahoma, sans-serif; color: #000000; margin-top: 0; margin-bottom: 0; padding-bottom: 32px; font-size: 18px; line-height: 20px;">
                                                                    К Вам на сайт пришло новое сообщение!
                                                                </p>
                                                                <p style="font-family: Verdana, Geneva, Tahoma, sans-serif; color: #000000; margin-top: 0; margin-bottom: 0; padding-bottom: 10px; font-size: 18px; line-height: 20px;">
                                                                    Имя отправителя: ' . $name . '
                                                                </p>
                                                                <p style="font-family: Verdana, Geneva, Tahoma, sans-serif; color: #000000; margin-top: 0; margin-bottom: 0; padding-bottom: 32px; font-size: 18px; line-height: 20px;">
                                                                    Текст пожелания: ' . $message . '
                                                                </p>
                                                                <p style="font-family: Verdana, Geneva, Tahoma, sans-serif; color: #000000; margin-top: 0; margin-bottom: 0; padding-bottom: 32px; font-size: 18px; line-height: 20px;">
                                                                    Необходимо предпринять действие!
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding-bottom: 32px;">
                                                                <a href="' . $confirmUrl . '" style="display: inline-block; width: 270px; margin-right: 32px; padding-top: 12px; padding-bottom: 12px; border: 1px solid #03CE48; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-top-left-radius: 10px; border-bottom-left-radius: 10px; background-color: #03CE48; font-family: Verdana, Geneva, Tahoma, sans-serif; color: #FFFFFF; font-size: 18px; line-height: 20px; text-align: center; text-decoration: none;">
                                                                    Опубликовать пожелание
                                                                </a>
                                                                <a href="' . $deleteUrl . '" style="display: inline-block; width: 270px; padding-top: 12px; padding-bottom: 12px; border: 1px solid #C92222; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-top-left-radius: 10px; border-bottom-left-radius: 10px; background-color: #FF0707; font-family: Verdana, Geneva, Tahoma, sans-serif; color: #FFFFFF; font-size: 18px; line-height: 20px; text-align: center; text-decoration: none;">
                                                                    Удалить пожелание
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top: 50px; padding-bottom: 70px;">
                            <table align="center" class="table-650" cellpadding="0" cellspacing="0" width="650" bgcolor="#F2EEEB">
                                <tr>
                                    <td>
                                        <table align="center" class="table-600" cellpadding="0" cellspacing="0" width="600">
                                            <tr>
                                                <td align="center">
                                                    <p style="display: block; width: 150px; font-family: Verdana, Geneva, Tahoma, sans-serif; color: #00000070; font-size: 18px; line-height: 20px;">
                                                        Мы в соцсетях
                                                    </p>
                                                    <a href="#" style="display: inline-block; margin-right: 20px;"> 
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/vk.png" alt="vk">
                                                    </a>
                                                    <a href="#" style="display: inline-block;">
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/telegram.png" alt="vk">
                                                    </a>
                                                </td>
                                                <td align="center">
                                                    <p style="display: block; width: 350px; font-family: Verdana, Geneva, Tahoma, sans-serif; color: #00000070; font-size: 18px; line-height: 20px;">
                                                        Если у Вас есть вопросы, напишите нам удобным для Вас способом
                                                    </p>
                                                    <a href="#" style="display: inline-block; margin-right: 20px;">
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/whatsapp.png" alt="whatsapp">
                                                    </a>
                                                    <a href="#" style="display: inline-block; margin-right: 20px;">
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/vk.png" alt="vk">
                                                    </a>
                                                    <a href="#" style="display: inline-block; margin-right: 20px;">
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/telegram.png" alt="telegram">
                                                    </a>
                                                    <a href="#" style="display: inline-block;">
                                                        <img src="https://stepanviktoria.ru//wp-content/themes/stepanviktoria/assets/img/email.png" alt="email">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';

            // Теперь вставляем HTML-шаблон письма в тело письма
            $mail->isHTML(true);
            $mail->Subject = 'Пришло новое пожелание';
            $mail->Body = $html_message;

            try {
                $mail->send();
                // Письмо успешно отправлено
            } catch (Exception $e) {
                // Ошибка отправки письма
            }

            // Возвращаем ответ в формате JSON
            echo json_encode(array('success' => true, 'message' => 'Пожелание успешно создано и будет опубликовано в ближайшее время!'));
        } else {
            // Возвращаем ответ в формате JSON в случае ошибки
            echo json_encode(array('success' => false, 'message' => 'Ошибка при создании пожелания.'));
        }
        exit; // Обязательно завершите выполнение скрипта после вывода JSON
    }
}
?>
