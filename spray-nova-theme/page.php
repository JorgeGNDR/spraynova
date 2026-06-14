<?php
/**
 * Page template.
 *
 * @package SprayNova
 */

get_header();
?>
<main class="content-main">
	<div class="content-shell">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'page-content' ); ?>>
				<header class="content-header"><h1><?php the_title(); ?></h1></header>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
