<?php
/**
 * Single post template.
 *
 * @package SprayNova
 */

get_header();
?>
<main class="content-main">
	<div class="content-shell">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'page-content' ); ?>>
				<header class="content-header"><p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p><h1><?php the_title(); ?></h1></header>
				<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'post-hero-image' ) ); endif; ?>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
