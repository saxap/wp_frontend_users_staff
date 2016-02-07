<?php
/**
 * Template Name: Для логинов
 */
get_header(); // подключаем header.php ?>
<section>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
		<h1><?php the_title(); // заголовок ?></h1>
		<?php the_content(); // контент ?>
	</article>


<?php if (is_user_logged_in()) { // если юзер залогинен, стандартная ф-я вп 
	$current_user = wp_get_current_user(); // получим данные о текущем залогиненом юзере ?>
<p>Приветик, <?php echo $current_user->display_name; ?>. <a href="#" class="logout" data-nonce="<?php echo wp_create_nonce('logout_me_nonce'); ?>">Выйти</a></p> <!-- покажем приветствие и ссылку на выход, в атрибут data-nonce запишем строку для проверки безопасности -->
<?php } else { // если не залогинен, покажем форму для логина ?>
<form name="loginform" id="loginform" method="post" class="userform" action=""> <!-- обычная форма, по сути нам важен только класс -->
	<input type="text" name="log" id="user_login" placeholder="Логин или email"> <!-- сюда будут писать логин или email -->
	<input type="password" name="pwd" id="user_pass" placeholder="Пароль"> <!-- ну пароль -->
	<input name="rememberme" type="checkbox" value="forever"> Запомнить меня <!-- запомнить ли сессию, forever - навсегда,  -->
	<input type="submit" value="Войти"> <!-- субмит -->
	<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"> <!-- куда отправим юзера если все прошло ок -->
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('login_me_nonce'); ?>"> <!-- поле со строкой безопасности, будем проверим её в обработчике чтобы убедиться, что форма отправлена откуда надо, аргумент login_me_nonce, конечно, лучше поменять на свой -->
	<input type="hidden" name="action" value="login_me"> <!-- обязательное поле, по нему запустится нужная функция -->
	<div class="response"></div> <!-- ну сюда будем пихать ответ от сервера -->
</form>
<?php } ?>

<?php endwhile; // конец цикла ?>
</section>
<?php get_sidebar(); // подключаем sidebar.php ?>
<?php get_footer(); // подключаем footer.php ?>