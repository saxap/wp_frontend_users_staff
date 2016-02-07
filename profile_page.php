<?php
/**
 * Template Name: Страница профиля
 */
get_header(); // подключаем header.php ?>
<section>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
		<h1><?php the_title(); // заголовок ?></h1>
		<?php the_content(); // контент ?>
	</article>

<?php if (!is_user_logged_in()) { // если юзер не залогинен, форму показывать не будем ?>
<p>У вас нет доступа к этой странице.</p>
<?php } else { // если залогинен, показываем страницу профиля 
global $current_user; // заглобалим переменную с объектом данных текущего пользователя 
?>
<p>Страница профиля пользователя: <?php echo $current_user->user_login; ?></p>
<form name="profileform" id="profileform" method="post" class="userform" action=""> <!-- обратите внимание на класс, по этому классу на форму вешается обработка из первой статьи -->

	<input type="email" name="user_email" id="user_email" placeholder="Email" value="<?php echo $current_user->user_email; ?>" required><!-- ну тут всякие обычные поля -->
	<input type="text" name="first_name" id="first_name" placeholder="Имя" value="<?php echo $current_user->first_name; ?>">
	<input type="text" name="last_name" id="last_name" placeholder="Фамилия" value="<?php echo $current_user->last_name; ?>">
	<input type="text" name="user_url" id="user_url" placeholder="Сайт" value="<?php echo $current_user->user_url; ?>">
	<textarea name="description" placeholder="Обо мне"><?php echo $current_user->description; ?></textarea>

	<p>Изменить пароль:</p>
	<input type="password" name="current_pass" id="current_pass" placeholder="Текущий пароль"><!-- если захотят поменять пароль, надо будет заполнить все 3 поля -->
	<input type="password" name="pass1" id="pass1" placeholder="Новый пароль">
	<input type="password" name="pass2" id="pass2" placeholder="Повторите новый пароль">

    <p>Аватар:</p>
    <?php echo get_avatar($current_user->ID,64); ?>
    Загрузить новый: <input type="file" name="avatar" id="avatar">

	<input type="submit" value="Изменить"> <!-- субмит -->
	<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"> <!-- куда отправим юзера если все прошло ок, в нашем случае это не понадобиться, а вообще может если форма сквозная -->
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('edit_profile_nonce'); ?>"> <!-- поле со строкой безопасности, будем проверять её в обработчике чтобы убедиться, что форма отправлена откуда надо -->
	<input type="hidden" name="action" value="edit_profile"> <!-- обязательное поле, по нему запустится нужная функция -->
	<div class="response"></div> <!-- ну сюда будем пихать ответ от сервера -->
</form>
<?php } ?>

<?php endwhile; // конец цикла ?>
</section>
<?php get_sidebar(); // подключаем sidebar.php ?>
<?php get_footer(); // подключаем footer.php ?>