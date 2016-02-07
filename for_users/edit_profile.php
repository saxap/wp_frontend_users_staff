<?php
$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : ''; // сначала возьмем скрытое поле nonce
if (!wp_verify_nonce($nonce, 'edit_profile_nonce')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); // проверим его, и если вернулся фолс - исаользуем wp_send_json_error и умираем

if (!is_user_logged_in()) wp_send_json_error(array('message' => 'Вы не залогинены.', 'redirect' => false)); // если юзера как то разлогинело, то ничего не делаем

// теперь возьмем все поля и рассуем по переменным
$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';

$user_url = isset($_POST['user_url']) ? $_POST['user_url'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : ''; // стандртное мета поле у юзера

$pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : ''; // поля с паролями
$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
$current_pass = isset($_POST['current_pass']) ? $_POST['current_pass'] : '';

$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : false;

// теперь проверим обязательные поля на заполненность и валидность - у нас это только поле с почтой
if (!$user_email) wp_send_json_error(array('message' => 'Email - обязательное поле.', 'redirect' => false));
if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email)) wp_send_json_error(array('message' => 'Ошибочный формат email', 'redirect' => false));

global $current_user; // получим текущие данные пользователя
get_currentuserinfo(); 

if ($current_user->user_email != $user_email && get_user_by('email', $user_email)) wp_send_json_error(array('message' => 'Пользователь с таким email уже существует', 'redirect' => false)); // если попытались изменить email то проверим что новый email не занят

$fields = array(); // подготовим массив для обновления данных пользователя
$fields['ID'] = $current_user->ID;
$fields['user_email'] = $user_email;
$fields['first_name'] = $first_name;
$fields['last_name'] = $last_name;
$fields['user_url'] = $user_url;

if ($pass1 && $pass2 && $current_pass) { // если заполнены все 3 поля с паролями
    if ($pass1 != $pass2) wp_send_json_error(array('message' => 'Пароли не совпадают', 'redirect' => false)); // проверим что новые пароли совпадают
    if (strlen($pass1) < 4) wp_send_json_error(array('message' => 'Слишком короткий пароль', 'redirect' => false)); // проверим длину пароля
    if (false !== strpos(wp_unslash($pass1), "\\" ) ) wp_send_json_error(array('message' => 'Пароль не может содержать обратные слэши "\\"', 'redirect' => false)); // это для безопасности
    if (!wp_check_password($current_pass, $current_user->user_pass, $current_user->ID)) wp_send_json_error(array('message' => 'Текущий пароль не верный.', 'redirect' => false)); // проверим что текущий пароль введен правильно
    $fields['user_pass'] = esc_attr($pass1); // добавим новый пароль в массив данных для изменения
} elseif ($pass1 || $pass2 || $current_pass) { // если были заполнены не все поля с паролями
    wp_send_json_error(array('message' => 'Для изменения пароля заполните все поля с паролями.', 'redirect' => false)); // покажим ошибку
}

// теперь проверим все ли ок с новой аватаркой, если она передана
if (isset($_FILES['avatar'])) { // если в глобальном массиве $_FILES есть элемент с индексом avatar
    if ($_FILES['avatar']['error']) wp_send_json_error(array('message' => "Ошибка загрузки: " . $_FILES['avatar']['error'].". (".$_FILES['avatar']['name'].") ", 'redirect' => false)); // если произошла серверная ошибка при загрузке файла
    $type = $_FILES['avatar']['type']; // теперь возьмем расширение файла
    if (($type != "image/jpg") && ($type != "image/jpeg") && ($type != "image/png")) wp_send_json_error(array('message' => "Формат файла может быть только jpg или png. (".$_FILES['avatar']['name'].")", 'redirect' => false)); // если формат плохой
    

    // если скрипт до сих пор не умер то продолжаем
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id_img = media_handle_upload('avatar', 0); // пробуем залить файл в медиабиблиотеку и получить его id, первый аргумент это индекс файла в глобальном массиве $_FILES, у нас это avatar, второй это id поста к чему файл привязывается, нам это не надо поэтому 0
    
    if (is_wp_error($attach_id_img)) wp_send_json_error(array('message' => "Что-то не так с загрузкой аватарки.", 'redirect' => false)); // если добавление в медиабиблиотеку вернуло ошибку

    $avatar = array(); // подготовим массив с данными для привязки аватарки к юзеру (только для плагина simple local avatars)
    $avatar['media_id'] = $attach_id_img; // сюда id аватарки
    $avatar['full'] = wp_get_attachment_url($attach_id_img); // а сюда её url
    
    update_user_meta($current_user->ID, 'simple_local_avatar', $avatar ); // привяжем новый аватар

}

update_user_meta($current_user->ID, 'description', $description); // обновим мета поле "Обо мне", остальные мета поля обновляются по такому же принципу

$update_user = wp_update_user($fields); // обновляем данные юзера

if (is_wp_error($update_user)) wp_send_json_error(array('message' => 'Системная ошибка: '.$update_user->get_error_code(), 'redirect' => false)); // если что-то пошло не так

wp_send_json_success(array('message' => 'Данные успешно изменены. Обновляемся...', 'redirect' => $redirect_to)); // если все ок, напишем об этом и обновимся
?>