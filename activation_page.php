<?php
/**
 * Template Name: Активация юзера
 */
get_header(); // подключаем header.php ?>
<section>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // контэйнер с классами и id ?>
		<h1><?php the_title(); // заголовок ?></h1>
		<?php the_content(); // контент ?>
	</article>

<?php if (is_user_logged_in()) { // если юзер залогинен, значит уже активирован ?>
<p>Вы уже активированы.</p>
<?php } else { // если не залогинен
	$user_id = isset($_GET['user']) ? (int)$_GET['user'] : ''; // возьмем юзер ид
	$key = isset($_GET['key']) ? $_GET['key'] : ''; // возьмем случайную строку
	if (!$user_id || !$key) { // если чего то из этого нет
		echo '<p>Не переданы параметры активации.</p>'; // напишем ошибку
	} else {
		$code = get_user_meta( $user_id, 'has_to_be_activated', true ); // получаем случайную строку по ид юзера
		if ( $code == $key ) { // и сравниваем её с переданной строкой и если все ок
        	delete_user_meta( $user_id, 'has_to_be_activated' ); // удаляем эту строку у юзера
        	echo '<p>Активация прошла успешно. Теперь вы можете войти.</p>'; // пишем что все ок
    	} else {
    		echo '<p>Данные активации не верны или вы уже активированы.</p>'; // если строки не совпали
    	}
	}
} ?>

<?php endwhile; // конец цикла ?>
</section>
<?php get_sidebar(); // подключаем sidebar.php ?>
<?php get_footer(); // подключаем footer.php ?>