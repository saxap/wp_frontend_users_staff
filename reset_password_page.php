<?php
/**
 * Template Name: Форма сброса пароля
 */
get_header(); // подключаем header.php ?>
<section>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
		<h1><?php the_title(); // заголовок ?></h1>
		<?php the_content(); // контент ?>
	</article>

	<?php if (!isset($_GET['key']) || !isset($_GET['login']) || is_wp_error(check_password_reset_key($_GET['key'], $_GET['login']))) { // если параметры не передали или ф-я проверки вернула ошибку
		echo '<p>Ключ и (или) логин ни были переданы, либо не верны.</p>';
		//resetpass
	} else { // если все ок показываем форму ?>
		<form name="resetpassform" id="resetpassform" action="" method="post" class="userform">
			<input type="password" name="pass1" id="pass1" placeholder="Новый пароль">
			<input type="password" name="pass2" id="pass2" placeholder="Повторите новый пароль">
			
			<input type="hidden" name="key" value="<?php echo esc_attr($_GET['key']); ?>"><!-- переданные параметры сунем в скрытые поля -->
			<input type="hidden" name="login" value="<?php echo esc_attr($_GET['login']); ?>">
			<input type="submit" value="Изменить пароль">
		    <input type="hidden" name="redirect_to" value="<?php echo isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '/'; ?>">
		    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('reset_password'); ?>">
		    <input type="hidden" name="action" value="reset_password_front">
		    <div class="response"></div>
		</form>
	<?php } ?>

<?php endwhile; // конец цикла ?>
</section>
<?php get_sidebar(); // подключаем sidebar.php ?>
<?php get_footer(); // подключаем footer.php ?>