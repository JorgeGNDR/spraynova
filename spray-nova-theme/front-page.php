<?php
/**
 * Front page template.
 *
 * @package SprayNova
 */

get_header();

$shop_url = spray_nova_shop_url();
$show_categories = get_theme_mod( 'spray_nova_show_categories', true );
$show_products   = get_theme_mod( 'spray_nova_show_products', true );
$newsletter_shortcode = get_theme_mod( 'spray_nova_newsletter_shortcode', '' );
$show_newsletter = get_theme_mod( 'spray_nova_show_newsletter', false ) && '' !== trim( $newsletter_shortcode );
$hero_secondary_target = $show_categories ? '#categorias' : ( $show_products ? '#catalogo' : $shop_url );

$clamp_theme_mod = static function ( $name, $default, $minimum, $maximum ) {
	return max( $minimum, min( $maximum, absint( get_theme_mod( $name, $default ) ) ) );
};

$hero_video_id     = absint( get_theme_mod( 'spray_nova_hero_video', 0 ) );
$hero_poster_id    = absint( get_theme_mod( 'spray_nova_hero_video_poster', 0 ) );
$hero_video_url    = $hero_video_id ? wp_get_attachment_url( $hero_video_id ) : '';
$hero_video_type   = $hero_video_id ? get_post_mime_type( $hero_video_id ) : 'video/mp4';
$hero_poster_url   = $hero_poster_id ? wp_get_attachment_image_url( $hero_poster_id, 'full' ) : '';
$hero_video_url    = $hero_video_url ? $hero_video_url : get_template_directory_uri() . '/assets/video/hero-nbq.mp4';
$hero_video_type   = $hero_video_type ? $hero_video_type : 'video/mp4';
$hero_poster_url   = $hero_poster_url ? $hero_poster_url : spray_nova_image( 'hero-nbq-poster.jpg' );

$hero_title_lines = preg_split( '/\r\n|\r|\n/', get_theme_mod( 'spray_nova_hero_title', "COLOR.\nCONTROL.\nACTITUD." ) );
$hero_title_lines = array_values( array_filter( array_map( 'trim', $hero_title_lines ) ) );
if ( ! $hero_title_lines ) {
	$hero_title_lines = array( 'COLOR.', 'CONTROL.', 'ACTITUD.' );
}

$ticker_items = array_values( array_filter( array_map( 'trim', explode( '|', get_theme_mod( 'spray_nova_ticker_text', '+250 COLORES | MARCAS SELECCIONADAS | PARA TODAS LAS SUPERFICIES' ) ) ) ) );
if ( ! $ticker_items ) {
	$ticker_items = array( '+250 COLORES', 'MARCAS SELECCIONADAS', 'PARA TODAS LAS SUPERFICIES' );
}
?>
<main>
	<section class="hero">
		<div class="hero-copy">
			<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_kicker', 'Material para dejar huella' ) ); ?></p>
			<h1>
				<?php foreach ( $hero_title_lines as $index => $line ) : ?>
					<?php echo 1 === $index ? '<span>' . esc_html( $line ) . '</span>' : esc_html( $line ); ?><?php echo $index < count( $hero_title_lines ) - 1 ? '<br>' : ''; ?>
				<?php endforeach; ?>
			</h1>
			<p class="hero-description"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_text', 'Sprays, markers y material para graffiti.' ) ); ?></p>
			<div class="hero-actions">
				<a class="button button-dark" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_primary_button', 'Ver productos' ) ); ?></a>
				<a class="text-link" href="<?php echo esc_url( $hero_secondary_target ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_secondary_button', 'Explorar categorías' ) ); ?></a>
			</div>
		</div>
		<div class="hero-art" aria-hidden="true">
			<video
				class="hero-video"
				autoplay
				muted
				loop
				playsinline
				preload="metadata"
				poster="<?php echo esc_url( $hero_poster_url ); ?>"
				tabindex="-1"
			>
				<source src="<?php echo esc_url( $hero_video_url ); ?>" type="<?php echo esc_attr( $hero_video_type ); ?>">
			</video>
		</div>
	</section>

	<?php if ( get_theme_mod( 'spray_nova_show_ticker', true ) ) : ?>
	<section class="ticker" aria-label="<?php esc_attr_e( 'Ventajas', 'spray-nova' ); ?>">
		<div>
			<?php for ( $loop = 0; $loop < 2; $loop++ ) : ?>
				<?php foreach ( $ticker_items as $item ) : ?>
					<span><?php echo esc_html( $item ); ?></span><i></i>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$content_order = 'categories_products' === get_theme_mod( 'spray_nova_content_order', 'products_categories' )
		? array( 'categories', 'products' ) : array( 'products', 'categories' );
	foreach ( $content_order as $section_key ) {
		include get_template_directory() . '/template-parts/home/' . $section_key . '.php';
	}
	?>

	<?php if ( $show_newsletter ) : ?>
		<section class="newsletter">
			<div>
				<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_kicker', 'Sin spam. Solo color.' ) ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_title', 'NOVEDADES EN TU BANDEJA' ) ); ?></h2>
			</div>
			<div class="newsletter-form"><?php echo do_shortcode( $newsletter_shortcode ); ?></div>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
