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
$show_newsletter = get_theme_mod( 'spray_nova_show_newsletter', true );
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
		<div class="hero-art hero-art-video" aria-hidden="true">
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
	$home_content_sections = array();
	ob_start();
	?>
	<?php if ( $show_categories ) : ?>
		<section class="categories section" id="categorias">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_categories_kicker', 'Encuentra tu herramienta' ) ); ?></p>
					<h2><?php echo esc_html( get_theme_mod( 'spray_nova_categories_title', 'COMPRA POR CATEGORÍA' ) ); ?></h2>
					<p class="section-description"><?php echo esc_html( get_theme_mod( 'spray_nova_categories_description', 'Explora sprays, rotuladores y ceras para distintas superficies, estilos y formas de trabajar.' ) ); ?></p>
				</div>
				<a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_categories_all_label', 'Ver todo' ) ); ?></a>
			</div>
			<div class="category-grid">
				<?php
				$categories = array(
					'sprays' => array( 'slug' => get_theme_mod( 'spray_nova_category_sprays_slug', 'sprays' ), 'name' => get_theme_mod( 'spray_nova_category_sprays_label', 'SPRAYS' ), 'number' => get_theme_mod( 'spray_nova_category_sprays_number', '01' ), 'class' => 'category-sprays', 'image_mod' => 'spray_nova_category_sprays_image', 'visual' => '<i class="mini-can one"></i><i class="mini-can two"></i><i class="mini-can three"></i>' ),
					'markers' => array( 'slug' => get_theme_mod( 'spray_nova_category_markers_slug', 'rotuladores' ), 'name' => get_theme_mod( 'spray_nova_category_markers_label', 'ROTULADORES' ), 'number' => get_theme_mod( 'spray_nova_category_markers_number', '02' ), 'class' => 'category-markers', 'image_mod' => 'spray_nova_category_markers_image', 'visual' => '<i class="marker one"></i><i class="marker two"></i><i class="marker three"></i>' ),
					'wax' => array( 'slug' => get_theme_mod( 'spray_nova_category_wax_slug', 'ceras' ), 'name' => get_theme_mod( 'spray_nova_category_wax_label', 'CERAS' ), 'number' => get_theme_mod( 'spray_nova_category_wax_number', '03' ), 'class' => 'category-wax', 'image_mod' => 'spray_nova_category_wax_image', 'visual' => '<i class="wax one"></i><i class="wax two"></i><i class="wax three"></i>' ),
				);
				$category_orders = array(
					'sprays_markers_wax' => array( 'sprays', 'markers', 'wax' ),
					'sprays_wax_markers' => array( 'sprays', 'wax', 'markers' ),
					'markers_sprays_wax' => array( 'markers', 'sprays', 'wax' ),
					'markers_wax_sprays' => array( 'markers', 'wax', 'sprays' ),
					'wax_sprays_markers' => array( 'wax', 'sprays', 'markers' ),
					'wax_markers_sprays' => array( 'wax', 'markers', 'sprays' ),
				);
				$category_order_key = get_theme_mod( 'spray_nova_category_order', 'sprays_markers_wax' );
				$category_order     = isset( $category_orders[ $category_order_key ] ) ? $category_orders[ $category_order_key ] : $category_orders['sprays_markers_wax'];
				foreach ( $category_order as $category_key ) :
					$category = $categories[ $category_key ];
					$term     = term_exists( $category['slug'], 'product_cat' );
					$url      = $term ? get_term_link( (int) $term['term_id'], 'product_cat' ) : add_query_arg( 'product_cat', $category['slug'], $shop_url );
					$image_id = spray_nova_category_image_id( $category['slug'], $category['image_mod'] );
					if ( is_wp_error( $url ) ) {
						$url = $shop_url;
					}
					?>
					<a class="category-card <?php echo esc_attr( $category['class'] ); ?>" href="<?php echo esc_url( $url ); ?>">
						<span class="category-number"><?php echo esc_html( $category['number'] ); ?></span>
						<span class="category-visual<?php echo $image_id ? ' has-image' : ''; ?>">
							<?php if ( $image_id ) : ?>
								<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'category-image', 'sizes' => '(max-width: 700px) 100vw, 33vw', 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
							<?php else : ?>
								<?php echo wp_kses_post( $category['visual'] ); ?>
							<?php endif; ?>
						</span>
						<span class="category-name"><?php echo esc_html( $category['name'] ); ?></span>
						<span class="category-arrow"></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
	<?php $home_content_sections['categories'] = ob_get_clean(); ?>

	<?php ob_start(); ?>
	<?php if ( $show_products ) : ?>
		<section class="products section" id="catalogo">
			<div class="section-heading">
				<div>
					<p class="eyebrow" id="novedades"><?php echo esc_html( get_theme_mod( 'spray_nova_products_kicker', 'Lo último en llegar' ) ); ?></p>
					<h2><?php echo esc_html( get_theme_mod( 'spray_nova_products_title', 'PRODUCTOS DESTACADOS' ) ); ?></h2>
					<p class="section-description"><?php echo esc_html( get_theme_mod( 'spray_nova_products_description', 'Una selección de material para graffiti con formatos, acabados y colores para cada proyecto.' ) ); ?></p>
				</div>
				<?php if ( get_theme_mod( 'spray_nova_products_show_filters', true ) ) : ?>
				<div class="product-filters" aria-label="<?php esc_attr_e( 'Filtrar productos', 'spray-nova' ); ?>">
					<button class="active" type="button" data-filter="todos"><?php echo esc_html( get_theme_mod( 'spray_nova_products_all_filter_label', 'Todos' ) ); ?></button>
					<button type="button" data-filter="<?php echo esc_attr( get_theme_mod( 'spray_nova_category_sprays_slug', 'sprays' ) ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_category_sprays_label', 'SPRAYS' ) ); ?></button>
					<button type="button" data-filter="<?php echo esc_attr( get_theme_mod( 'spray_nova_category_markers_slug', 'rotuladores' ) ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_category_markers_label', 'ROTULADORES' ) ); ?></button>
					<button type="button" data-filter="<?php echo esc_attr( get_theme_mod( 'spray_nova_category_wax_slug', 'ceras' ) ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_category_wax_label', 'CERAS' ) ); ?></button>
				</div>
				<?php endif; ?>
			</div>
			<div class="product-grid">
				<?php
				$products      = array();
				$product_count = $clamp_theme_mod( 'spray_nova_products_count', 8, 1, 12 );
				if ( class_exists( 'WooCommerce' ) ) {
					$manual_ids = array_filter( array_map( 'absint', explode( ',', get_theme_mod( 'spray_nova_products_ids', '' ) ) ) );

					if ( $manual_ids ) {
						foreach ( array_slice( $manual_ids, 0, $product_count ) as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( $product && 'publish' === $product->get_status() ) {
								$products[] = $product;
							}
						}
					} else {
						$query = array(
							'status'  => 'publish',
							'limit'   => $product_count,
							'orderby' => 'date',
							'order'   => 'DESC',
						);
						if ( 'recent' !== get_theme_mod( 'spray_nova_products_source', 'featured' ) ) {
							$query['featured'] = true;
						}
						$products = wc_get_products( $query );
					}
				}

				if ( $products ) {
					foreach ( $products as $product ) {
						spray_nova_product_card( $product );
					}
				} elseif ( ! class_exists( 'WooCommerce' ) ) {
					$demo_products = array(
						array( 'category' => 'sprays', 'class' => 'purple-product', 'visual' => 'product-can', 'label' => 'NOVA', 'badge' => 'Nuevo', 'type' => 'Sprays · 400 ml', 'name' => 'Spray Nova 400 Violeta', 'price' => '4,50 €' ),
						array( 'category' => 'sprays', 'class' => 'black-product', 'visual' => 'product-can', 'label' => 'NOVA', 'badge' => '', 'type' => 'Sprays · 400 ml', 'name' => 'Spray Nova 400 Negro Mate', 'price' => '4,50 €' ),
						array( 'category' => 'rotuladores', 'class' => 'marker-product', 'visual' => 'product-marker', 'label' => 'NOVA MARKER', 'badge' => 'Top ventas', 'type' => 'Rotuladores · Punta 15 mm', 'name' => 'Marker Nova 15 mm', 'price' => '5,95 €' ),
						array( 'category' => 'ceras', 'class' => 'wax-product', 'visual' => 'product-wax', 'label' => 'NOVA', 'badge' => '', 'type' => 'Ceras · Permanente', 'name' => 'Cera sólida Nova', 'price' => '3,25 €' ),
					);
					foreach ( $demo_products as $item ) {
						spray_nova_demo_product_card( $item );
					}
				} else {
					echo '<p class="home-products-empty">' . esc_html__( 'Marca productos como destacados en WooCommerce o introduce sus ID en el Personalizador.', 'spray-nova' ) . '</p>';
				}
				?>
			</div>
			<p class="empty-results"><?php esc_html_e( 'No hemos encontrado productos en esta categoría.', 'spray-nova' ); ?></p>
		</section>
	<?php endif; ?>
	<?php
	$home_content_sections['products'] = ob_get_clean();
	$content_order = 'categories_products' === get_theme_mod( 'spray_nova_content_order', 'products_categories' )
		? array( 'categories', 'products' )
		: array( 'products', 'categories' );
	foreach ( $content_order as $section_key ) {
		echo $home_content_sections[ $section_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Buffered theme markup.
	}
	?>

	<?php if ( $show_newsletter ) : ?>
		<section class="newsletter">
			<div>
				<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_kicker', 'Sin spam. Solo color.' ) ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_title', 'NOVEDADES EN TU BANDEJA' ) ); ?></h2>
			</div>
			<form class="newsletter-form">
				<label class="sr-only" for="newsletter-email"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_placeholder', 'Tu email' ) ); ?></label>
				<input id="newsletter-email" type="email" placeholder="<?php echo esc_attr( get_theme_mod( 'spray_nova_newsletter_placeholder', 'Tu email' ) ); ?>" required>
				<button type="submit"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_button', 'Suscribirme' ) ); ?></button>
			</form>
			<p class="newsletter-message" aria-live="polite"></p>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
