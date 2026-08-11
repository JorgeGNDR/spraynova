<?php
/**
 * Default template.
 *
 * @package SprayNova
 */

get_header();

if ( is_search() ) {
	$archive_title = sprintf(
		/* translators: %s: search query. */
		__( 'Resultados para: %s', 'spray-nova' ),
		get_search_query()
	);
} elseif ( is_archive() ) {
	$archive_title = get_the_archive_title();
} else {
	$archive_title = single_post_title( '', false );
}

$archive_title = $archive_title ?: get_bloginfo( 'name' );
?>
<main class="content-main">
	<div class="content-shell">
		<?php if ( have_posts() ) : ?>
			<header class="content-header"><h1><?php echo wp_kses_post( $archive_title ); ?></h1></header>
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
