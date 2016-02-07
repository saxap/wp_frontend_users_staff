<?php
/**
 * Template Name: Запрос сброса пароля
 */
get_header(); // подключаем header.php ?>
<section>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
		<h1><?php the_title(); // заголовок ?></h1>
		<?php the_content(); // контент ?>
	</article>


<?php if (is_user_logged_in()) { // если юзер залогинен, то менять ему ничего не надо ?>
<p>Вы авторизованы.</p>
<?php } else { // если не залогинен, покажем форму для логина ?>
<form name="lostpasswordform" id="lostpasswordform" method="post" class="userform">
	<input type="text" name="user_login" placeholder="Ваш логин или email">
	<input type="submit" value="Сбросить">
	<input type="hidden" name="redirect_to" value="/"> <!-- можно не заполнять если редирект не нужен -->
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('lost_password'); ?>">
    <input type="hidden" name="action" value="lost_password">
    <div class="response"></div>
</form>
<?php } ?>

<?php endwhile; // конец цикла ?>
</section>
<?php get_sidebar(); // подключаем sidebar.php ?>
<?php get_footer(); // подключаем footer.php ?>