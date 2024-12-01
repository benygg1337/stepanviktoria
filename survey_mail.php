<?php

// Файлы phpmailer
require __DIR__ . '/php/PHPMailer.php';
require __DIR__ . '/php/SMTP.php';
require __DIR__ . '/php/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

// Определяем путь и имя файла для логов
$logFile = __DIR__ . '/log.txt';

// Функция для записи логов
function writeLog($message)
{
    global $logFile;
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function writeResponseLog($response)
{
    global $logFile;
    $logMessage = "[" . date("Y-m-d H:i:s") . " form_by_survey] Response: " . $response . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}


// // Актуальная функция для проверки reCAPTCHA
// function checkRecaptcha($response)
// {
//     define('SECRET_KEY', '6LceTPApAAAAADRkg0U_Hqb-LNobmxKAN4b4MKaV');
//     $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
//     $recaptcha_data = [
//         'secret' => SECRET_KEY,
//         'response' => $response
//     ];

//     $recaptcha_options = [
//         'http' => [
//             'header' => "Content-type: application/x-www-form-urlencoded\r\n",
//             'method' => 'POST',
//             'content' => http_build_query($recaptcha_data)
//         ]
//     ];

//     $recaptcha_context = stream_context_create($recaptcha_options);
//     $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
//     $recaptcha_json = json_decode($recaptcha_result);

//     return $recaptcha_json;

// }

// //Проверка reCAPTCHA
// if (isset($_POST['g-recaptcha-response'])) {
//     $recaptcha_response = $_POST['g-recaptcha-response'];
//     $recaptcha_json = checkRecaptcha($recaptcha_response);
//     $data['info_captcha'] = $recaptcha_json;

//     if (!$recaptcha_json->success || $recaptcha_json->score < 0.6) {
//         $data['result'] = "error";
//         $data['errorType'] = "captcha";
//         $data['info'] = "Ошибка проверки reCAPTCHA";
//         $data['desc'] = "Вы являетесь роботом!";
//         // Отправка результата
//         header('Content-Type: application/json');
//         echo json_encode($data);
//         writeLog("Ошибка отправки письма: {$data['desc']}");
//         writeResponseLog(json_encode($data));
//         exit();
//     }

// } else {
//     $data['result'] = "error";
//     $data['errorType'] = "captcha";
//     $data['info'] = "Ошибка проверки reCAPTCHA";
//     $data['desc'] = "Код reCAPTCHA не был отправлен";
//     // Отправка результата
//     header('Content-Type: application/json');
//     echo json_encode($data);
//     exit();
// }

// Функция для записи логов
function writeLog($message)
{
    global $logFile;
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function writeResponseLog($response)
{
    global $logFile;
    $logMessage = "[" . date("Y-m-d H:i:s") . " form_by_survey] Response: " . $response . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Обработка данных формы
$data = [];
$fioInput = trim($_POST['form']['name'] ?? ""); // Получаем и очищаем поле ФИО

// Проверка ФИО
if (!empty($fioInput)) {
    $fioResponse = "Ваше ФИО: " . htmlspecialchars($fioInput);
} else {
    $fioResponse = "Ошибка: ФИО не было заполнено.";
}

// Вопрос 1: Присутствие на торжестве
$visit = $_POST['radio-visit'] ?? null;
if ($visit === 'yes') {
    $visitResponse = "Сможет присутствовать";
} elseif ($visit === 'no') {
    $visitResponse = "Не сможет присутствовать";
} else {
    $visitResponse = "Ошибка: выбор не был сделан.";
}

// Вопрос 2: Предпочтения в еде
$dishInput = $_POST['form-dish'] ?? [];
if (!is_array($dishInput)) {
    $dishInput = [$dishInput];
}
$dishChoices = [
    'meat' => 'Мясо',
    'fish' => 'Рыба',
    'bird' => 'Птица',
    'vegetables' => 'Овощи'
];
$selectedDishes = array_map(function ($value) use ($dishChoices) {
    return $dishChoices[$value] ?? $value;
}, $dishInput);
$dishResponse = !empty($selectedDishes) ? implode(", ", $selectedDishes) : "Не выбрано";

// Вопрос 3: Предпочтения в напитках
$drinkInput = $_POST['radio-drinks'] ?? [];
if (!is_array($drinkInput)) {
    $drinkInput = [$drinkInput];
}
$drinkChoices = [
    'wine' => 'Вино',
    'champagne' => 'Шампанское',
    'cognac' => 'Коньяк',
    'alcoholic' => 'Виски',
    'vodka' => 'Водка',
    'no-alcoholic' => 'Безалкогольное'
];
$selectedDrinks = array_map(function ($value) use ($drinkChoices) {
    return $drinkChoices[$value] ?? $value;
}, $drinkInput);
$drinkResponse = !empty($selectedDrinks) ? implode(", ", $selectedDrinks) : "Не выбрано";

// Вопрос 4: Присутствие на регистрации брака
$registryInput = $_POST['radio-registry'] ?? null;
$registryResponse = $registryInput === 'yes' ? "Да" : ($registryInput === 'no' ? "Нет" : "Не выбрано");

// Вопрос 5: Предпочтения в музыке
$musicInput = $_POST['radio-music'] ?? [];
if (!is_array($musicInput)) {
    $musicInput = [$musicInput];
}
$musicChoices = [
    'pop' => 'Поп',
    'classic' => 'Классика',
    'retro' => 'Ретро',
    'jazz' => 'Джаз'
];
$selectedMusic = array_map(function ($value) use ($musicChoices) {
    return $musicChoices[$value] ?? $value;
}, $musicInput);
$musicResponse = !empty($selectedMusic) ? implode(", ", $selectedMusic) : "Не выбрано";

// Вопрос 6: Нужен ли транспорт
$transportInput = $_POST['radio-transport'] ?? null;
$transportResponse = $transportInput === 'yes' ? "Да" : ($transportInput === 'no' ? "Нет" : "Не выбрано");


//Отправка в таблицу
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/secret_new.json');

$client = new Client();
$client->useApplicationDefaultCredentials();
$client->setApplicationName("marryme");
$client->setScopes([
    'https://www.googleapis.com/auth/spreadsheets'
]);


try {
    $service = new Sheets($client);
    $spreadsheetId = '1-pUncMyLZzJwPHiT8CZYjdIlr8OcaF_d0P5VmUGL7r8'; // Ваш ID таблицы
    $date_time = date("Y-m-d H:i:s");

    // Данные для добавления
    $values = [
        [$fioResponse, $registryResponse, $visitResponse, $dishResponse, $drinkResponse, $musicResponse, $transportResponse, $date_time]
    ];
    
    $range = 'A2'; 
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    
    $params = [
        'valueInputOption' => 'RAW'
    ];

    $range = 'A2'; // Допустим, вы хотите начать добавление с A1
    $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
} catch (Exception $e) {
    // Обработка ошибки
    $data['result'] = "error";
    $data['info'] = "Произошла ошибка при добавлении данных в Google Sheets: " . $e->getMessage();
    writeLog("Ошибка Google Sheets: " . $e->getMessage());
    writeResponseLog(json_encode($data));
} 

// Формирование письма
$headers = "Content-Type: text/html; charset=UTF-8";
$title = "Результаты опроса";
$body = "<h1>Результаты опроса</h1>";
$body .= "<b>ФИО:</b> {$fioResponse}<br>";
$body .= "<b>Присутствие на торжестве:</b> {$visitResponse}<br>";
$body .= "<b>Предпочтения в еде:</b> {$dishResponse}<br>";
$body .= "<b>Предпочтения в напитках:</b> {$drinkResponse}<br>";
$body .= "<b>Присутствие на регистрации брака:</b> {$registryResponse}<br>";
$body .= "<b>Предпочтения в музыке:</b> {$musicResponse}<br>";
$body .= "<b>Нужен ли транспорт:</b> {$transportResponse}<br>";

// Отправка письма
$mail = new PHPMailer();
$mail->isSMTP();
$mail->CharSet = "UTF-8";
$mail->SMTPAuth = true;
$mail->Host = 'mail.marryme-invites.ru';
$mail->Username = 'noreply@marryme-invites.ru';
$mail->Password = '4638743aA';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->setFrom('noreply@marryme-invites.ru', 'Свадебный сайт');
$mail->addAddress('loko419@yandex.ru');
$mail->isHTML(true);
$mail->Subject = $title;
$mail->Body = $body;

// Проверка отправки письма
if ($mail->send()) {
    $data['result'] = "success";
    $data['info'] = "Сообщение успешно отправлено!";
    writeLog("Сообщение успешно отправлено!");
} else {
    $data['result'] = "error";
    $data['info'] = "Ошибка при отправке письма: " . $mail->ErrorInfo;
    writeLog("Ошибка отправки письма: " . $mail->ErrorInfo);
}

// Отправка результата
header('Content-Type: application/json');
echo json_encode($data);
