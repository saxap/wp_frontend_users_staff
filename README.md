# wp_frontend_users_staff
Набор функций и шаблонов для организации работы с юзерами во фронт-энде сайтов на WordPress

1) Папки /for_users/ и /js/ кидаем в шаблон, в первой папке обработчики форм, во второй папке js скрипт для перехвата и отправки форм аяксом.

2) В functions.php добавляем:
<code>require_once dirname(__FILE__) . '/for_users/route.php';</code>

3) Остальные файлы это кастомные шаблоны с примерами форм.

Полное описание работы в цикле статей:

Часть 1: <a href="http://dontforget.pro/wordpress/avtorizatsiya-v-frontende-wordpress/" target="_blank">Front-end авторизация и логаут в WordPress</a>

Часть 2: <a href="http://dontforget.pro/wordpress/frontend-registratsiya-v-wordpress/" target="_blank">Front-end регистрация и активация пользователей в WordPress</a>

Часть 3: <a href="http://dontforget.pro/wordpress/front-end-avtorizatsiya-i-logaut-v-wordpress/" target="_blank">Front-end редактирование профиля в WordPress</a>

Часть 4: <a href="http://dontforget.pro/wordpress/front-end-vosstanovlenie-parolya-v-wordpress/" target="_blank">Front-end восстановление пароля в WordPress</a>
