<?php
/**
 * Default template.
 *
 * @package SprayNova
 */

get_header();
?>
<main class="content-main">
	<div class="content-shell">
		<?php if ( have_posts() ) : ?>
			<header class="content-header"><h1><?php single_post_title(); ?></h1></header>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'content-card' ); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No se ha encontrado contenido.', 'spray-nova' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
