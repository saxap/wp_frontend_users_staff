<?php
$nonce = $_POST['nonce'];
if (!wp_verify_nonce($nonce, 'lost_password')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); // сначала проверим что форма отправлена откуда надо

$user_login = $_POST['user_login']; // запишем данные в переменные
$redirect_to = $_POST['redirect_to'];

if (!$user_login) wp_send_json_error(array('message' => 'Вы не заполнили поле', 'redirect' => false)); // если не заполнили

global $wpdb, $current_site; // это надо заглобалить

if (strpos($user_login,'@')) { // если передали email
    $user = get_user_by('email',trim($user_login)); // пробуеми получить юзера по мылу
} else { // инапче передали логин
    $user = get_user_by('login', trim($user_login)); // пробуем получить юзера по логину
}

if (!$user) wp_send_json_error(array('message' => 'Пользователя с таким email не существует.', 'redirect' => false)); // если юера не нашли то ошибка
if (get_user_meta( $user->ID, 'has_to_be_activated', true ) != false) wp_send_json_error(array('message' => 'Пользователь еще не активирован.', 'redirect' => false)); // если юзер еще не активировался, расскомментить если используется активация, см. след. статьи


do_action('lostpassword_post'); // чтобы работали всякие другие хуки

$user_login = $user->user_login; // запишим данные которые достали 
$user_email = $user->user_email;

do_action('retrieve_password', $user_login); // чтобы работали всякие другие хуки

$allow = apply_filters('allow_password_reset', true, $user->ID); // проверим возможность сброса пароля

if (!$allow) wp_send_json_error(array('message' => 'Сброс пароля запрещено. Пожалуйста свяжитесь с администратором сайта.', 'redirect' => false)); // значит нельзя
else if (is_wp_error($allow)) wp_send_json_error(array('message' => $allow->get_error_message(), 'redirect' => false)); // если какая либо другая ошибка

$key = wp_generate_password(20, false); // генерируем уникальный строку-ключ

do_action('retrieve_password_key', $user_login, $key); // чтобы работали всякие другие хуки

if ( empty( $wp_hasher ) ) { 
    require_once ABSPATH . WPINC . '/class-phpass.php'; // подключим спец.либу для создания хэшей для сброса
    $wp_hasher = new PasswordHash( 8, true ); // создаем экзепляр класса
}

//создаем хэш
//$hashed = $wp_hasher->HashPassword($key); // создание хэша для версий ниже 4.3
$hashed = time() . ':' . $wp_hasher->HashPassword( $key ); // создание хэша для версий выше 4.3

$wpdb->update( $wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user_login)); // пишим в базу что для данный юзер запросил смену пароля

//отправляем письмо с сылкой на сброс пароля
$reset_link = home_url().'/reset-password/?key='.$key.'&login='.rawurlencode($user_login).'&redirect_to='.esc_attr($redirect_to); // создадаем ссылку на сброс пароля, подразумевается что на странице с урлом /reset-password/ у вас будет форма залания нового пароля
$txt = '<h3>Доброго времени.</h3><p>Кто-то запросил сброс пароля на сайте: '.home_url().', чтобы сбросить пароль перейдите по ссылке: <a href="'.$reset_link.'">'.$reset_link.'</a>, иначе проигнорируйте это письмо.</p>'; // это текст письма
add_filter( 'wp_mail_content_type', 'set_html_content_type' ); // включаем формат письма в хтмл
wp_mail( $user_email, 'Сброс пароля пользователя '.$user_login, $txt ); // отправляем письмо юзеру
remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); // выключаем формат письма в хтмл

wp_send_json_success(array('message' => 'Письмо со ссылкой на страницу изменения пароля отправлено на адрес, указанный при регистрации. Если вы не получили письмо, проверьте папку "Спам" или попробуйте еще раз.', 'redirect' => false)); // пишим что все ок
?>