<?php
/** Home categories section. Included in the front-page template scope. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
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
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
