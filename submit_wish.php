<?php
// Файлы phpmailer
require __DIR__ . '/php/PHPMailer.php';
require __DIR__ . '/php/SMTP.php';
require __DIR__ . '/php/Exception.php';

// Подключаем файл wp-load.php для работы с WordPress функциями
require_once('../../../wp-load.php');

// Функция для записи логов
function writeLog($message) {
    $logFile = __DIR__ . '/logfilewish.txt'; // Файл будет создан в том же каталоге, что и ваш PHP-скрипт
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function writeResponseLog($response)
{
    $logFile = __DIR__ . '/logfilewish.txt'; // Убедитесь, что файл существует
    $logMessage = "[" . date("Y-m-d H:i:s") . " form_by_survey] Response: " . $response . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Актуальная функция для проверки reCAPTCHA
function checkRecaptcha($response)
{
    define('SECRET_KEY', '6LePpo8qAAAAAE6OzuRMBG1W4HT2HIDNHwfXZ7fV');
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => SECRET_KEY,
        'response' => $response
    ];

    $recaptcha_options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];

    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_json = json_decode($recaptcha_result);

    return $recaptcha_json;
}

// Проверка reCAPTCHA
if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_json = checkRecaptcha($recaptcha_response);
    $data['info_captcha'] = $recaptcha_json;

    if (!$recaptcha_json->success || (isset($recaptcha_json->score) && $recaptcha_json->score < 0.6)) {
        $data['result'] = "error";
        $data['errorType'] = "captcha";
        $data['info'] = "Ошибка проверки reCAPTCHA";
        $data['desc'] = "Вы являетесь роботом или уровень доверия слишком низкий.";
        writeLog("Ошибка reCAPTCHA: " . json_encode($recaptcha_json));
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
} else {
    $data['result'] = "error";
    $data['errorType'] = "captcha";
    $data['info'] = "Ошибка проверки reCAPTCHA";
    $data['desc'] = "Код reCAPTCHA не был отправлен.";
    writeLog("Ошибка reCAPTCHA: код не отправлен.");
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

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
                                                    </table>
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
                if ($mail->send()) {
                    $data['result'] = "success";
                    $data['info'] = "Пожелание успешно создано и будет опубликовано.";
                } else {
                    $data['result'] = "error";
                    $data['info'] = "Ошибка отправки письма: " . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                $data['result'] = "error";
                $data['info'] = "Ошибка: " . $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }
}
?>
