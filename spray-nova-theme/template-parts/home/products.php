<?php
/** Home products section. Included in the front-page template scope. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
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
				$product_count = $clamp_theme_mod( 'spray_nova_products_count', 4, 1, 12 );
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
				} else {
					echo '<p class="home-products-empty">' . esc_html__( 'No hay productos disponibles en esta selección.', 'spray-nova' ) . '</p>';
				}
				?>
			</div>
			<p class="empty-results"><?php esc_html_e( 'No hemos encontrado productos en esta categoría.', 'spray-nova' ); ?></p>
		</section>
	<?php endif; ?>
