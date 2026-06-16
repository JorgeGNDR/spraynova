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
$show_story      = get_theme_mod( 'spray_nova_show_story', true );
$show_newsletter = get_theme_mod( 'spray_nova_show_newsletter', true );
$hero_secondary_target = $show_categories ? '#categorias' : ( $show_products ? '#catalogo' : $shop_url );

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
			<p class="hero-description"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_text', 'Sprays, rotuladores y ceras seleccionados para que tu idea llegue del boceto al muro.' ) ); ?></p>
			<div class="hero-actions">
				<a class="button button-dark" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_primary_button', 'Ver productos' ) ); ?></a>
				<a class="text-link" href="<?php echo esc_url( $hero_secondary_target ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_hero_secondary_button', 'Explorar categorías' ) ); ?> <span>↗</span></a>
			</div>
		</div>
		<div class="hero-art" aria-hidden="true">
			<div class="paint-blob"></div>
			<p class="outline-word">NOVA</p>
			<div class="spray-can spray-purple"><span class="can-cap"></span><span class="can-label">SN</span><small>400 ML</small></div>
			<div class="spray-can spray-black"><span class="can-cap"></span><span class="can-label">SN</span><small>400 ML</small></div>
			<img src="<?php echo esc_url( spray_nova_image( 'isotipo.jpg' ) ); ?>" alt="">
			<span class="spark spark-one">✦</span><span class="spark spark-two">✦</span>
		</div>
	</section>

	<section class="ticker" aria-label="<?php esc_attr_e( 'Ventajas', 'spray-nova' ); ?>">
		<div>
			<?php for ( $loop = 0; $loop < 2; $loop++ ) : ?>
				<?php foreach ( $ticker_items as $item ) : ?>
					<span><?php echo esc_html( $item ); ?></span><i>✦</i>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</section>

	<?php if ( $show_categories ) : ?>
		<section class="categories section" id="categorias">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_categories_kicker', 'Encuentra tu herramienta' ) ); ?></p>
					<h2><?php echo esc_html( get_theme_mod( 'spray_nova_categories_title', 'COMPRA POR CATEGORÍA' ) ); ?></h2>
				</div>
				<a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Ver todo', 'spray-nova' ); ?> <span>↗</span></a>
			</div>
			<div class="category-grid">
				<?php
				$categories = array(
					array( 'slug' => 'sprays', 'name' => get_theme_mod( 'spray_nova_category_sprays_label', 'SPRAYS' ), 'number' => '01', 'class' => 'category-sprays', 'visual' => '<i class="mini-can one"></i><i class="mini-can two"></i><i class="mini-can three"></i>' ),
					array( 'slug' => 'rotuladores', 'name' => get_theme_mod( 'spray_nova_category_markers_label', 'ROTULADORES' ), 'number' => '02', 'class' => 'category-markers', 'visual' => '<i class="marker one"></i><i class="marker two"></i><i class="marker three"></i>' ),
					array( 'slug' => 'ceras', 'name' => get_theme_mod( 'spray_nova_category_wax_label', 'CERAS' ), 'number' => '03', 'class' => 'category-wax', 'visual' => '<i class="wax one"></i><i class="wax two"></i><i class="wax three"></i>' ),
				);
				foreach ( $categories as $category ) :
					$term = term_exists( $category['slug'], 'product_cat' );
					$url  = $term ? get_term_link( (int) $term['term_id'], 'product_cat' ) : add_query_arg( 'product_cat', $category['slug'], $shop_url );
					if ( is_wp_error( $url ) ) {
						$url = $shop_url;
					}
					?>
					<a class="category-card <?php echo esc_attr( $category['class'] ); ?>" href="<?php echo esc_url( $url ); ?>">
						<span class="category-number"><?php echo esc_html( $category['number'] ); ?></span>
						<span class="category-visual"><?php echo wp_kses_post( $category['visual'] ); ?></span>
						<span class="category-name"><?php echo esc_html( $category['name'] ); ?></span>
						<span class="category-arrow">↗</span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_products ) : ?>
		<section class="products section" id="catalogo">
			<div class="section-heading">
				<div>
					<p class="eyebrow" id="novedades"><?php echo esc_html( get_theme_mod( 'spray_nova_products_kicker', 'Lo último en llegar' ) ); ?></p>
					<h2><?php echo esc_html( get_theme_mod( 'spray_nova_products_title', 'PRODUCTOS DESTACADOS' ) ); ?></h2>
				</div>
				<div class="product-filters" aria-label="<?php esc_attr_e( 'Filtrar productos', 'spray-nova' ); ?>">
					<button class="active" type="button" data-filter="todos"><?php esc_html_e( 'Todos', 'spray-nova' ); ?></button>
					<button type="button" data-filter="sprays"><?php esc_html_e( 'Sprays', 'spray-nova' ); ?></button>
					<button type="button" data-filter="rotuladores"><?php esc_html_e( 'Rotuladores', 'spray-nova' ); ?></button>
					<button type="button" data-filter="ceras"><?php esc_html_e( 'Ceras', 'spray-nova' ); ?></button>
				</div>
			</div>
			<div class="product-grid">
				<?php
				$products = array();
				if ( class_exists( 'WooCommerce' ) ) {
					$product_map = array();
					$featured    = wc_get_products( array( 'status' => 'publish', 'featured' => true, 'limit' => 4 ) );
					foreach ( $featured as $product ) {
						$product_map[ $product->get_id() ] = $product;
					}
					foreach ( array( 'sprays', 'rotuladores', 'ceras' ) as $category_slug ) {
						$category_products = wc_get_products( array(
							'status'   => 'publish',
							'category' => array( $category_slug ),
							'limit'    => 4,
							'orderby'  => 'date',
							'order'    => 'DESC',
						) );
						foreach ( $category_products as $product ) {
							$product_map[ $product->get_id() ] = $product;
						}
					}
					if ( count( $product_map ) < 4 ) {
						$latest = wc_get_products( array( 'status' => 'publish', 'orderby' => 'date', 'order' => 'DESC', 'limit' => 12 ) );
						foreach ( $latest as $product ) {
							$product_map[ $product->get_id() ] = $product;
						}
					}
					$products = array_slice( array_values( $product_map ), 0, 12 );
				}

				if ( $products ) {
					foreach ( $products as $product ) {
						spray_nova_product_card( $product );
					}
				} else {
					$demo_products = array(
						array( 'category' => 'sprays', 'class' => 'purple-product', 'visual' => 'product-can', 'label' => 'NOVA', 'badge' => 'Nuevo', 'type' => 'Sprays · 400 ml', 'name' => 'Spray Nova 400 Violeta', 'price' => '4,50 €' ),
						array( 'category' => 'sprays', 'class' => 'black-product', 'visual' => 'product-can', 'label' => 'NOVA', 'badge' => '', 'type' => 'Sprays · 400 ml', 'name' => 'Spray Nova 400 Negro Mate', 'price' => '4,50 €' ),
						array( 'category' => 'rotuladores', 'class' => 'marker-product', 'visual' => 'product-marker', 'label' => 'NOVA MARKER', 'badge' => 'Top ventas', 'type' => 'Rotuladores · Punta 15 mm', 'name' => 'Marker Nova 15 mm', 'price' => '5,95 €' ),
						array( 'category' => 'ceras', 'class' => 'wax-product', 'visual' => 'product-wax', 'label' => 'NOVA', 'badge' => '', 'type' => 'Ceras · Permanente', 'name' => 'Cera sólida Nova', 'price' => '3,25 €' ),
					);
					foreach ( $demo_products as $item ) {
						spray_nova_demo_product_card( $item );
					}
				}
				?>
			</div>
			<p class="empty-results"><?php esc_html_e( 'No hemos encontrado productos en esta categoría.', 'spray-nova' ); ?></p>
		</section>
	<?php endif; ?>

	<?php if ( $show_story ) : ?>
		<section class="brand-story section" id="nosotros">
			<div class="story-logo"><img src="<?php echo esc_url( spray_nova_image( 'logo-redondo.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Spray Nova Graffiti Shop', 'spray-nova' ); ?>"></div>
			<div class="story-copy">
				<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_story_kicker', 'Spray Nova Graffiti Shop' ) ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'spray_nova_story_title', 'HECHO PARA QUIENES NO DEJAN EL MURO EN BLANCO.' ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'spray_nova_story_text', 'Material fiable, colores que responden y atención cercana. Seleccionamos cada producto pensando en escritores, ilustradores y gente que crea a su manera.' ) ); ?></p>
				<a class="button button-outline" href="<?php echo esc_url( home_url( '/nosotros/' ) ); ?>"><?php echo esc_html( get_theme_mod( 'spray_nova_story_button', 'Conócenos' ) ); ?></a>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_newsletter ) : ?>
		<section class="newsletter">
			<div>
				<p class="eyebrow"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_kicker', 'Sin spam. Solo color.' ) ); ?></p>
				<h2><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_title', 'NOVEDADES EN TU BANDEJA' ) ); ?></h2>
			</div>
			<form class="newsletter-form">
				<label class="sr-only" for="newsletter-email"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_placeholder', 'Tu email' ) ); ?></label>
				<input id="newsletter-email" type="email" placeholder="<?php echo esc_attr( get_theme_mod( 'spray_nova_newsletter_placeholder', 'Tu email' ) ); ?>" required>
				<button type="submit"><?php echo esc_html( get_theme_mod( 'spray_nova_newsletter_button', 'Suscribirme' ) ); ?> ↗</button>
			</form>
			<p class="newsletter-message" aria-live="polite"></p>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
