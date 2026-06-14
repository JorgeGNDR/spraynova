<?php
/**
 * Not found template.
 *
 * @package SprayNova
 */

get_header();
?>
<main class="not-found">
	<p class="eyebrow">Error 404</p>
	<h1><?php esc_html_e( 'ESTE MURO ESTÁ EN BLANCO.', 'spray-nova' ); ?></h1>
	<p><?php esc_html_e( 'La página que buscas no existe o se ha movido.', 'spray-nova' ); ?></p>
	<a class="button button-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'spray-nova' ); ?></a>
</main>
<?php get_footer(); ?>
