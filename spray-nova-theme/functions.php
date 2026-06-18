<?php
/**
 * Spray Nova theme functions.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPRAY_NOVA_VERSION', '1.3.15' );

require_once get_template_directory() . '/inc/customizer.php';

/**
 * Configure theme features.
 */
function spray_nova_setup() {
	load_theme_textdomain( 'spray-nova', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 120,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 700,
		'single_image_width'    => 900,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'max_rows'        => 8,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Menú principal', 'spray-nova' ),
		'footer'  => __( 'Menú del pie', 'spray-nova' ),
	) );
}
add_action( 'after_setup_theme', 'spray_nova_setup' );

/**
 * Load theme assets.
 */
function spray_nova_assets() {
	$script_dependencies = array( 'jquery' );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
		$script_dependencies[] = 'wc-add-to-cart';
		$script_dependencies[] = 'wc-cart-fragments';
	}

	wp_enqueue_style(
		'spray-nova-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo+Black&family=DM+Sans:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'spray-nova-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array(),
		SPRAY_NOVA_VERSION
	);
	wp_enqueue_script(
		'spray-nova-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		$script_dependencies,
		SPRAY_NOVA_VERSION,
		true
	);
	wp_localize_script( 'spray-nova-theme', 'sprayNova', array(
		'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ),
		'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' ),
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
		'nonce'       => wp_create_nonce( 'spray_nova_cart' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'spray_nova_assets' );

/**
 * Return a bundled image URL.
 *
 * @param string $filename Image filename.
 * @return string
 */
function spray_nova_image( $filename ) {
	return get_template_directory_uri() . '/assets/images/' . ltrim( $filename, '/' );
}

/**
 * Resolve the shop URL safely when WooCommerce is unavailable.
 *
 * @return string
 */
function spray_nova_shop_url() {
	return function_exists( 'wc_get_page_permalink' )
		? wc_get_page_permalink( 'shop' )
		: home_url( '/' );
}

/**
 * Header menu fallback.
 */
function spray_nova_primary_menu_fallback() {
	$links = array(
		__( 'Tienda', 'spray-nova' )     => spray_nova_shop_url(),
		__( 'Categorías', 'spray-nova' ) => home_url( '/#categorias' ),
		__( 'Novedades', 'spray-nova' )  => home_url( '/#novedades' ),
		__( 'Nosotros', 'spray-nova' )   => home_url( '/#nosotros' ),
	);

	echo '<ul class="menu">';
	foreach ( $links as $label => $url ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Replace WooCommerce wrappers with the theme layout.
 */
function spray_nova_woocommerce_wrapper_start() {
	echo '<main class="store-main"><div class="store-shell">';
}

function spray_nova_woocommerce_wrapper_end() {
	echo '</div></main>';
}

function spray_nova_woocommerce_integration() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	add_action( 'woocommerce_before_main_content', 'spray_nova_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'spray_nova_woocommerce_wrapper_end', 10 );
	add_action( 'woocommerce_before_shop_loop', 'spray_nova_shop_category_filters', 6 );
	add_action( 'woocommerce_after_shop_loop_item', 'spray_nova_loop_product_link', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	add_action( 'woocommerce_single_product_summary', 'spray_nova_single_product_description', 25 );
}
add_action( 'wp', 'spray_nova_woocommerce_integration' );
add_filter( 'loop_shop_columns', function() { return 4; } );

/**
 * Render a compact product description in the purchase summary.
 */
function spray_nova_single_product_description() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$description = $product->get_short_description();
	if ( ! $description ) {
		$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $product->get_id() ) ) ), 34, '...' );
	}

	if ( ! $description ) {
		return;
	}
	?>
	<div class="spray-product-description">
		<?php echo wp_kses_post( wpautop( $description ) ); ?>
	</div>
	<?php
}

/**
 * Render quick category filters on product archives.
 */
function spray_nova_shop_category_filters() {
	if ( ! function_exists( 'wc_get_page_permalink' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );

	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	$current_term = is_product_category() ? get_queried_object() : null;
	$shop_url     = spray_nova_shop_url();
	?>
	<nav class="spray-shop-filters" aria-label="<?php esc_attr_e( 'Filtrar tienda por categoría', 'spray-nova' ); ?>">
		<a class="<?php echo $current_term ? '' : 'active'; ?>" href="<?php echo esc_url( $shop_url ); ?>">
			<?php esc_html_e( 'Todo', 'spray-nova' ); ?>
		</a>
		<?php foreach ( $terms as $term ) :
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}
			$is_active = $current_term && (int) $current_term->term_id === (int) $term->term_id;
			?>
			<a class="<?php echo $is_active ? 'active' : ''; ?>" href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $term->name ); ?>
				<span><?php echo esc_html( $term->count ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Replace loop add-to-cart buttons with a clean product link.
 */
function spray_nova_loop_product_link() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	printf(
		'<a class="button spray-loop-product-link" href="%1$s">%2$s</a>',
		esc_url( $product->get_permalink() ),
		esc_html__( 'Ver producto', 'spray-nova' )
	);
}

/**
 * Hide default WooCommerce add-to-cart success notices. The theme opens the cart drawer instead.
 *
 * @return string
 */
function spray_nova_hide_add_to_cart_notice() {
	return '';
}
add_filter( 'wc_add_to_cart_message_html', 'spray_nova_hide_add_to_cart_notice' );

/**
 * Determine whether a product should use the spray color wall.
 *
 * @param WC_Product|null $product Product object.
 * @return bool
 */
function spray_nova_is_spray_product( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		return false;
	}

	return has_term( 'sprays', 'product_cat', $product->get_id() );
}

/**
 * Add product-specific body classes.
 *
 * @param array $classes Body classes.
 * @return array
 */
function spray_nova_body_classes( $classes ) {
	if ( function_exists( 'is_product' ) && function_exists( 'wc_get_product' ) && is_product() ) {
		$product = wc_get_product( get_the_ID() );
		if ( spray_nova_is_spray_product( $product ) ) {
			$classes[] = 'spray-nova-spray-product';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'spray_nova_body_classes' );

/**
 * Replace the default variation selector with the custom spray color wall.
 */
function spray_nova_prepare_spray_product_summary() {
	global $product;

	if ( ! spray_nova_is_spray_product( $product ) ) {
		return;
	}

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_after_single_product_summary', 'spray_nova_spray_color_selector', 5 );
}
add_action( 'woocommerce_single_product_summary', 'spray_nova_prepare_spray_product_summary', 1 );

/**
 * Get a readable color label from a variation.
 *
 * @param WC_Product_Variation $variation Variation object.
 * @return string
 */
function spray_nova_get_variation_color_label( $variation ) {
	foreach ( $variation->get_attributes() as $name => $value ) {
		if ( false !== strpos( $name, 'color' ) || false !== strpos( $name, 'colour' ) ) {
			$taxonomy = 0 === strpos( $name, 'pa_' ) ? $name : '';
			if ( $taxonomy && taxonomy_exists( $taxonomy ) ) {
				$term = get_term_by( 'slug', $value, $taxonomy );
				if ( $term ) {
					return $term->name;
				}
			}
			return ucwords( str_replace( array( '-', '_' ), ' ', $value ) );
		}
	}

	return $variation->get_name();
}

/**
 * Infer a simple color family for filtering.
 *
 * @param string $label Color label.
 * @param string $hex Hex color.
 * @return string
 */
function spray_nova_infer_color_family( $label, $hex = '' ) {
	$text = strtolower( remove_accents( $label . ' ' . $hex ) );

	$families = array(
		'negros'    => array( 'black', 'negro', 'asfalto', 'carbon' ),
		'blancos'   => array( 'white', 'blanco', 'cream', 'crema' ),
		'grises'    => array( 'grey', 'gray', 'gris', 'plata', 'silver', 'chrome', 'cromo' ),
		'rojos'     => array( 'red', 'rojo', 'burdeos', 'granate', 'magenta' ),
		'naranjas'  => array( 'orange', 'naranja', 'mandarina' ),
		'amarillos' => array( 'yellow', 'amarillo', 'ocre' ),
		'verdes'    => array( 'green', 'verde', 'oliva', 'lime' ),
		'azules'    => array( 'blue', 'azul', 'cyan', 'cian' ),
		'morados'   => array( 'purple', 'violet', 'violeta', 'morado', 'lila' ),
		'marrones'  => array( 'brown', 'marron', 'marrón', 'siena', 'tierra' ),
		'rosas'     => array( 'pink', 'rosa', 'fucsia' ),
	);

	foreach ( $families as $family => $needles ) {
		foreach ( $needles as $needle ) {
			if ( false !== strpos( $text, $needle ) ) {
				return $family;
			}
		}
	}

	return 'otros';
}

/**
 * Return a fallback swatch color based on the family.
 *
 * @param string $family Family slug.
 * @return string
 */
function spray_nova_family_hex( $family ) {
	$colors = array(
		'negros'    => '#161616',
		'blancos'   => '#f7f2e8',
		'grises'    => '#9b9b9b',
		'rojos'     => '#c73737',
		'naranjas'  => '#ef7d22',
		'amarillos' => '#f1c83b',
		'verdes'    => '#4b9b59',
		'azules'    => '#3977c9',
		'morados'   => '#9d6dca',
		'marrones'  => '#8a5a35',
		'rosas'     => '#e77ab8',
		'otros'     => '#c6a0eb',
	);

	return isset( $colors[ $family ] ) ? $colors[ $family ] : $colors['otros'];
}

/**
 * Convert a hex color to HSL pieces for perceptual sorting.
 *
 * @param string $hex Hex color.
 * @return array
 */
function spray_nova_hex_to_hsl( $hex ) {
	$hex = ltrim( trim( $hex ), '#' );
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return array( 'h' => 999, 's' => 0, 'l' => 0 );
	}

	$r   = hexdec( substr( $hex, 0, 2 ) ) / 255;
	$g   = hexdec( substr( $hex, 2, 2 ) ) / 255;
	$b   = hexdec( substr( $hex, 4, 2 ) ) / 255;
	$max = max( $r, $g, $b );
	$min = min( $r, $g, $b );
	$l   = ( $max + $min ) / 2;
	$h   = 0;
	$s   = 0;

	if ( $max !== $min ) {
		$d = $max - $min;
		$s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );

		if ( $max === $r ) {
			$h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
		} elseif ( $max === $g ) {
			$h = ( $b - $r ) / $d + 2;
		} else {
			$h = ( $r - $g ) / $d + 4;
		}

		$h /= 6;
	}

	return array(
		'h' => $h * 360,
		's' => $s,
		'l' => $l,
	);
}

/**
 * Remove repeated product code from the color display name.
 *
 * @param string $label Color label.
 * @param string $code Color code.
 * @return string
 */
function spray_nova_clean_color_label( $label, $code ) {
	$label = trim( $label );
	$code  = trim( $code );

	if ( $code && 0 === stripos( $label, $code ) ) {
		$label = trim( substr( $label, strlen( $code ) ) );
		$label = ltrim( $label, " -–—·\t\n\r\0\x0B" );
	}

	if ( $code ) {
		$code_pattern = preg_quote( $code, '/' );
		$code_pattern = str_replace( array( '\-', '\_' ), '[\s\-_]*', $code_pattern );
		$label        = preg_replace( '/^' . $code_pattern . '[\s\-_–—·]*/i', '', $label );
	}

	$label = preg_replace( '/^(NBQ|DOPE|D)[\s\-_]*(F400|E400|E800|600)?[\s\-_]*[A-Z]?\d{2,6}[\s\-_–—·]*/i', '', $label );
	$label = trim( $label );

	return $label ? $label : $code;
}

/**
 * Render the spray color wall on variable spray products.
 */
function spray_nova_spray_color_selector() {
	global $product;

	if ( ! spray_nova_is_spray_product( $product ) ) {
		return;
	}

	$variation_ids = $product->get_children();
	$colors        = array();
	$families      = array();

	foreach ( $variation_ids as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation instanceof WC_Product_Variation || ! $variation->exists() ) {
			continue;
		}

		$label  = spray_nova_get_variation_color_label( $variation );
		$hex    = get_post_meta( $variation_id, '_spray_nova_color_hex', true );
		$code   = get_post_meta( $variation_id, '_spray_nova_color_code', true );
		$family = get_post_meta( $variation_id, '_spray_nova_color_family', true );

		if ( ! $code ) {
			$code = $variation->get_sku() ? $variation->get_sku() : $label;
		}
		if ( ! $family ) {
			$family = spray_nova_infer_color_family( $label, $hex );
		}
		if ( ! $hex ) {
			$hex = spray_nova_family_hex( $family );
		}

		$families[ $family ] = $family;
		$hsl                 = spray_nova_hex_to_hsl( $hex );
		$colors[] = array(
			'id'          => $variation_id,
			'label'       => $label,
			'display'     => spray_nova_clean_color_label( $label, $code ),
			'code'        => $code,
			'hex'         => $hex,
			'family'      => $family,
			'hue'         => $hsl['h'],
			'saturation'  => $hsl['s'],
			'lightness'   => $hsl['l'],
			'price'       => (float) $variation->get_price(),
			'price_html'  => $variation->get_price_html(),
			'is_enabled'  => $variation->is_purchasable() && $variation->is_in_stock(),
			'stock_label' => $variation->is_in_stock() ? __( 'Disponible', 'spray-nova' ) : __( 'Agotado', 'spray-nova' ),
		);
	}

	usort( $colors, function( $a, $b ) {
		$family_order = array(
			'blancos'   => 0,
			'amarillos' => 1,
			'naranjas'  => 2,
			'rojos'     => 3,
			'rosas'     => 4,
			'morados'   => 5,
			'azules'    => 6,
			'verdes'    => 7,
			'marrones'  => 8,
			'grises'    => 9,
			'negros'    => 10,
			'otros'     => 11,
		);
		$a_family     = isset( $family_order[ $a['family'] ] ) ? $family_order[ $a['family'] ] : 99;
		$b_family     = isset( $family_order[ $b['family'] ] ) ? $family_order[ $b['family'] ] : 99;

		if ( $a_family !== $b_family ) {
			return $a_family - $b_family;
		}
		if ( abs( $a['hue'] - $b['hue'] ) > 0.01 ) {
			return $a['hue'] <=> $b['hue'];
		}
		if ( abs( $a['lightness'] - $b['lightness'] ) > 0.01 ) {
			return $b['lightness'] <=> $a['lightness'];
		}

		return strcmp( $a['code'], $b['code'] );
	} );

	if ( ! $colors ) {
		echo '<div class="spray-color-selector spray-color-selector-empty"><p>' . esc_html__( 'Crea variaciones de color para mostrar la carta de sprays.', 'spray-nova' ) . '</p></div>';
		return;
	}

	$specs = array_filter( array(
		__( 'Marca', 'spray-nova' )   => get_post_meta( $product->get_id(), '_spray_nova_brand', true ),
		__( 'Formato', 'spray-nova' ) => get_post_meta( $product->get_id(), '_spray_nova_format', true ),
		__( 'Presión', 'spray-nova' ) => get_post_meta( $product->get_id(), '_spray_nova_pressure', true ),
		__( 'Acabado', 'spray-nova' ) => get_post_meta( $product->get_id(), '_spray_nova_finish', true ),
	) );

	$family_labels = array(
		'negros'    => __( 'Negros', 'spray-nova' ),
		'blancos'   => __( 'Blancos', 'spray-nova' ),
		'grises'    => __( 'Grises', 'spray-nova' ),
		'rojos'     => __( 'Rojos', 'spray-nova' ),
		'naranjas'  => __( 'Naranjas', 'spray-nova' ),
		'amarillos' => __( 'Amarillos', 'spray-nova' ),
		'verdes'    => __( 'Verdes', 'spray-nova' ),
		'azules'    => __( 'Azules', 'spray-nova' ),
		'morados'   => __( 'Morados', 'spray-nova' ),
		'marrones'  => __( 'Marrones', 'spray-nova' ),
		'rosas'     => __( 'Rosas', 'spray-nova' ),
		'otros'     => __( 'Otros', 'spray-nova' ),
	);
	?>
	<section class="spray-color-selector" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
		<div class="spray-selector-head">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Carta de colores', 'spray-nova' ); ?></p>
				<h2><?php esc_html_e( 'ELIGE COLORES Y CANTIDADES', 'spray-nova' ); ?></h2>
			</div>
		</div>

		<?php if ( $specs ) : ?>
			<div class="spray-product-specs" aria-label="<?php esc_attr_e( 'Características del spray', 'spray-nova' ); ?>">
				<?php foreach ( $specs as $label => $value ) : ?>
					<span><strong><?php echo esc_html( $label ); ?></strong><?php echo esc_html( $value ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="spray-selector-tools">
			<label class="spray-color-search">
				<span><?php esc_html_e( 'Buscar color o código', 'spray-nova' ); ?></span>
				<input type="search" placeholder="<?php esc_attr_e( 'Ej. negro, 101, violeta...', 'spray-nova' ); ?>">
			</label>
			<div class="spray-family-filters" aria-label="<?php esc_attr_e( 'Filtrar familias de color', 'spray-nova' ); ?>">
				<button type="button" class="active" data-family="todos"><?php esc_html_e( 'Todos', 'spray-nova' ); ?></button>
				<?php foreach ( array_keys( $families ) as $family ) : ?>
					<button type="button" data-family="<?php echo esc_attr( $family ); ?>"><?php echo esc_html( isset( $family_labels[ $family ] ) ? $family_labels[ $family ] : ucfirst( $family ) ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="spray-color-grid">
			<?php foreach ( $colors as $color ) : ?>
				<article
					class="spray-color-card<?php echo $color['is_enabled'] ? '' : ' is-disabled'; ?>"
					data-variation-id="<?php echo esc_attr( $color['id'] ); ?>"
					data-family="<?php echo esc_attr( $color['family'] ); ?>"
					data-label="<?php echo esc_attr( strtolower( remove_accents( $color['label'] . ' ' . $color['code'] ) ) ); ?>"
					data-price="<?php echo esc_attr( $color['price'] ); ?>"
				>
					<button class="spray-swatch" type="button" style="--swatch: <?php echo esc_attr( $color['hex'] ); ?>" <?php disabled( ! $color['is_enabled'] ); ?>>
						<span></span>
					</button>
					<div class="spray-color-info">
						<strong><?php echo esc_html( $color['display'] ); ?></strong>
						<small><span><?php echo esc_html( $color['code'] ); ?></span><?php echo wp_kses_post( $color['price_html'] ); ?></small>
					</div>
					<div class="spray-qty" aria-label="<?php esc_attr_e( 'Cantidad', 'spray-nova' ); ?>">
						<button type="button" class="spray-qty-minus" <?php disabled( ! $color['is_enabled'] ); ?>>−</button>
						<input type="number" min="0" step="1" value="0" inputmode="numeric" <?php disabled( ! $color['is_enabled'] ); ?>>
						<button type="button" class="spray-qty-plus" <?php disabled( ! $color['is_enabled'] ); ?>>+</button>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<p class="spray-color-empty"><?php esc_html_e( 'No hay colores con ese filtro.', 'spray-nova' ); ?></p>

		<div class="spray-selector-bar">
			<div>
				<strong class="spray-selected-count">0 <?php esc_html_e( 'latas seleccionadas', 'spray-nova' ); ?></strong>
				<span class="spray-selected-total"><?php echo wp_kses_post( wc_price( 0 ) ); ?></span>
			</div>
			<button type="button" class="button button-dark spray-add-pack" disabled><?php esc_html_e( 'Añadir selección al carrito', 'spray-nova' ); ?></button>
		</div>
		<p class="spray-selector-message" aria-live="polite"></p>
	</section>
	<?php
}

/**
 * Add optional variation fields for a better color wall.
 */
function spray_nova_variation_color_fields( $loop, $variation_data, $variation ) {
	echo '<div class="options_group spray-nova-variation-fields">';
	woocommerce_wp_text_input( array(
		'id'          => "_spray_nova_color_hex[{$loop}]",
		'name'        => "_spray_nova_color_hex[{$loop}]",
		'value'       => get_post_meta( $variation->ID, '_spray_nova_color_hex', true ),
		'label'       => __( 'Color visual HEX', 'spray-nova' ),
		'placeholder' => '#c6a0eb',
		'desc_tip'    => true,
		'description' => __( 'Opcional. Se usa para pintar el swatch en la carta de colores.', 'spray-nova' ),
	) );
	woocommerce_wp_text_input( array(
		'id'          => "_spray_nova_color_code[{$loop}]",
		'name'        => "_spray_nova_color_code[{$loop}]",
		'value'       => get_post_meta( $variation->ID, '_spray_nova_color_code', true ),
		'label'       => __( 'Código de color', 'spray-nova' ),
		'placeholder' => 'NBQ-101',
	) );
	woocommerce_wp_select( array(
		'id'      => "_spray_nova_color_family[{$loop}]",
		'name'    => "_spray_nova_color_family[{$loop}]",
		'value'   => get_post_meta( $variation->ID, '_spray_nova_color_family', true ),
		'label'   => __( 'Familia de color', 'spray-nova' ),
		'options' => array(
			''          => __( 'Detectar automáticamente', 'spray-nova' ),
			'negros'    => __( 'Negros', 'spray-nova' ),
			'blancos'   => __( 'Blancos', 'spray-nova' ),
			'grises'    => __( 'Grises / plata', 'spray-nova' ),
			'rojos'     => __( 'Rojos', 'spray-nova' ),
			'naranjas'  => __( 'Naranjas', 'spray-nova' ),
			'amarillos' => __( 'Amarillos', 'spray-nova' ),
			'verdes'    => __( 'Verdes', 'spray-nova' ),
			'azules'    => __( 'Azules', 'spray-nova' ),
			'morados'   => __( 'Morados', 'spray-nova' ),
			'marrones'  => __( 'Marrones', 'spray-nova' ),
			'rosas'     => __( 'Rosas', 'spray-nova' ),
			'otros'     => __( 'Otros', 'spray-nova' ),
		),
	) );
	echo '</div>';
}
add_action( 'woocommerce_product_after_variable_attributes', 'spray_nova_variation_color_fields', 10, 3 );

/**
 * Save optional variation color fields.
 *
 * @param int $variation_id Variation ID.
 * @param int $loop Variation loop index.
 */
function spray_nova_save_variation_color_fields( $variation_id, $loop ) {
	$fields = array(
		'_spray_nova_color_hex'    => 'sanitize_hex_color',
		'_spray_nova_color_code'   => 'sanitize_text_field',
		'_spray_nova_color_family' => 'sanitize_key',
	);

	foreach ( $fields as $field => $sanitize_callback ) {
		if ( ! isset( $_POST[ $field ][ $loop ] ) ) {
			continue;
		}
		$value = call_user_func( $sanitize_callback, wp_unslash( $_POST[ $field ][ $loop ] ) );
		if ( $value ) {
			update_post_meta( $variation_id, $field, $value );
		} else {
			delete_post_meta( $variation_id, $field );
		}
	}
}
add_action( 'woocommerce_save_product_variation', 'spray_nova_save_variation_color_fields', 10, 2 );

/**
 * Add several spray color variations to the cart in one request.
 */
function spray_nova_add_spray_pack_to_cart() {
	check_ajax_referer( 'spray_nova_cart', 'nonce' );

	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => __( 'WooCommerce no está disponible.', 'spray-nova' ) ), 400 );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$items_json = isset( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : '[]';
	$items      = json_decode( $items_json, true );
	$product    = wc_get_product( $product_id );

	if ( ! spray_nova_is_spray_product( $product ) || ! is_array( $items ) ) {
		wp_send_json_error( array( 'message' => __( 'Selección no válida.', 'spray-nova' ) ), 400 );
	}

	$added = 0;
	foreach ( $items as $item ) {
		$variation_id = isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0;
		$quantity     = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 0;
		$variation    = wc_get_product( $variation_id );

		if ( ! $quantity || ! $variation instanceof WC_Product_Variation || (int) $variation->get_parent_id() !== (int) $product_id ) {
			continue;
		}
		if ( ! $variation->is_purchasable() || ! $variation->is_in_stock() ) {
			continue;
		}

		$cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation->get_variation_attributes() );
		if ( $cart_key ) {
			$added += $quantity;
		}
	}

	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'No se pudo añadir ningún color al carrito.', 'spray-nova' ) ), 400 );
	}

	wp_send_json_success( array(
		'message'   => sprintf( _n( '%s lata añadida al carrito.', '%s latas añadidas al carrito.', $added, 'spray-nova' ), number_format_i18n( $added ) ),
		'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
		'cart_hash' => WC()->cart->get_cart_hash(),
	) );
}
add_action( 'wp_ajax_spray_nova_add_spray_pack', 'spray_nova_add_spray_pack_to_cart' );
add_action( 'wp_ajax_nopriv_spray_nova_add_spray_pack', 'spray_nova_add_spray_pack_to_cart' );

/**
 * Add a simple product to cart via AJAX.
 */
function spray_nova_add_simple_product_to_cart() {
	check_ajax_referer( 'spray_nova_cart', 'nonce' );

	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => __( 'WooCommerce no está disponible.', 'spray-nova' ) ), 400 );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$product    = wc_get_product( $product_id );

	if ( ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		wp_send_json_error( array( 'message' => __( 'Este producto no se puede añadir ahora mismo.', 'spray-nova' ) ), 400 );
	}

	$cart_key = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( ! $cart_key ) {
		wp_send_json_error( array( 'message' => __( 'No se pudo añadir al carrito.', 'spray-nova' ) ), 400 );
	}

	wp_send_json_success( array(
		'message'   => sprintf( _n( '%s unidad añadida al carrito.', '%s unidades añadidas al carrito.', $quantity, 'spray-nova' ), number_format_i18n( $quantity ) ),
		'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
		'cart_hash' => WC()->cart->get_cart_hash(),
	) );
}
add_action( 'wp_ajax_spray_nova_add_simple_product', 'spray_nova_add_simple_product_to_cart' );
add_action( 'wp_ajax_nopriv_spray_nova_add_simple_product', 'spray_nova_add_simple_product_to_cart' );

/**
 * Render compact navigation between products.
 */
function spray_nova_product_navigation() {
	if ( ! is_product() ) {
		return;
	}

	$previous = get_previous_post( true, '', 'product_cat' );
	$next     = get_next_post( true, '', 'product_cat' );
	$terms    = get_the_terms( get_the_ID(), 'product_cat' );
	$back_url = spray_nova_shop_url();

	if ( $terms && ! is_wp_error( $terms ) ) {
		$term_link = get_term_link( $terms[0] );
		if ( ! is_wp_error( $term_link ) ) {
			$back_url = $term_link;
		}
	}

	if ( ! $previous && ! $next ) {
		return;
	}
	?>
	<nav class="spray-product-nav" aria-label="<?php esc_attr_e( 'Navegación entre productos', 'spray-nova' ); ?>">
		<a class="spray-product-nav-back" href="<?php echo esc_url( $back_url ); ?>"><?php esc_html_e( 'Volver a la categoría', 'spray-nova' ); ?></a>
		<div>
			<?php if ( $previous ) : ?>
				<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>"><span><?php esc_html_e( 'Anterior', 'spray-nova' ); ?></span><?php echo esc_html( get_the_title( $previous ) ); ?></a>
			<?php endif; ?>
			<?php if ( $next ) : ?>
				<a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span><?php esc_html_e( 'Siguiente', 'spray-nova' ); ?></span><?php echo esc_html( get_the_title( $next ) ); ?></a>
			<?php endif; ?>
		</div>
	</nav>
	<?php
}
add_action( 'woocommerce_after_single_product_summary', 'spray_nova_product_navigation', 8 );

/**
 * Product card used on the home page.
 *
 * @param WC_Product $product Product object.
 */
function spray_nova_product_card( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$category_names = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
	$category_slugs = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );
	$type_label     = $category_names ? implode( ' · ', array_slice( $category_names, 0, 2 ) ) : __( 'Spray Nova', 'spray-nova' );
	?>
	<article <?php wc_product_class( 'product-card', $product ); ?> data-categories="<?php echo esc_attr( implode( ' ', $category_slugs ) ); ?>" data-name="<?php echo esc_attr( $product->get_name() ); ?>">
		<div class="product-card-visual">
		<a class="product-image real-product-image" href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="product-badge"><?php esc_html_e( 'Oferta', 'spray-nova' ); ?></span>
			<?php elseif ( $product->is_featured() ) : ?>
				<span class="product-badge beige"><?php esc_html_e( 'Destacado', 'spray-nova' ); ?></span>
			<?php endif; ?>
			<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
		</a>
		<a
			class="quick-add button spray-view-product"
			href="<?php echo esc_url( $product->get_permalink() ); ?>"
		>
			<?php esc_html_e( 'Ver producto', 'spray-nova' ); ?><span></span>
		</a>
		</div>
		<p class="product-type"><?php echo esc_html( $type_label ); ?></p>
		<h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<p class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
	</article>
	<?php
}

/**
 * Demo card displayed until real WooCommerce products are created.
 *
 * @param array $item Demo product data.
 */
function spray_nova_demo_product_card( $item ) {
	?>
	<article class="product-card spray-nova-demo-product" data-categories="<?php echo esc_attr( $item['category'] ); ?>" data-name="<?php echo esc_attr( $item['name'] ); ?>">
		<div class="product-card-visual">
		<div class="product-image <?php echo esc_attr( $item['class'] ); ?>">
			<?php if ( ! empty( $item['badge'] ) ) : ?>
				<span class="product-badge"><?php echo esc_html( $item['badge'] ); ?></span>
			<?php endif; ?>
			<div class="<?php echo esc_attr( $item['visual'] ); ?>"><i></i><b><?php echo esc_html( $item['label'] ); ?></b><?php if ( 'product-can' === $item['visual'] ) : ?><small>400</small><?php endif; ?></div>
			<a class="quick-add" href="<?php echo esc_url( spray_nova_shop_url() ); ?>"><?php esc_html_e( 'Ver tienda', 'spray-nova' ); ?><span>+</span></a>
		</div>
		</div>
		<p class="product-type"><?php echo esc_html( $item['type'] ); ?></p>
		<h3><?php echo esc_html( $item['name'] ); ?></h3>
		<p class="price"><?php echo esc_html( $item['price'] ); ?></p>
	</article>
	<?php
}

/**
 * Render cart drawer contents.
 */
function spray_nova_cart_drawer_inner() {
	$count = 0;
	$total = function_exists( 'wc_price' ) ? wc_price( 0 ) : '0,00 €';

	if ( function_exists( 'WC' ) && WC()->cart ) {
		$count = WC()->cart->get_cart_contents_count();
		$total = WC()->cart->get_cart_subtotal();
	}
	?>
	<div class="spray-nova-cart-drawer-inner">
		<div class="cart-header">
			<h2><?php esc_html_e( 'Tu carrito', 'spray-nova' ); ?> <span class="drawer-count">(<?php echo esc_html( $count ); ?>)</span></h2>
			<button class="cart-close" type="button" aria-label="<?php esc_attr_e( 'Cerrar carrito', 'spray-nova' ); ?>">×</button>
		</div>
		<div class="cart-items">
			<?php if ( $count && function_exists( 'WC' ) ) : ?>
				<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
					$product = $cart_item['data'];
					if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
						continue;
					}
					?>
					<div class="cart-line">
						<a class="cart-thumb" href="<?php echo esc_url( $product->get_permalink( $cart_item ) ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<div>
							<h3><a href="<?php echo esc_url( $product->get_permalink( $cart_item ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
							<p><?php echo esc_html( $cart_item['quantity'] ); ?> × <?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?></p>
						</div>
						<a class="remove-item remove_from_cart_button" href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Eliminar producto', 'spray-nova' ); ?>">×</a>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="empty-cart">
					<span>0</span>
					<h3><?php esc_html_e( 'Tu carrito está vacío', 'spray-nova' ); ?></h3>
					<p><?php esc_html_e( 'Añade un poco de color.', 'spray-nova' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<div class="cart-footer">
			<div><span><?php esc_html_e( 'Subtotal', 'spray-nova' ); ?></span><strong class="cart-total"><?php echo wp_kses_post( $total ); ?></strong></div>
			<?php if ( function_exists( 'wc_get_checkout_url' ) ) : ?>
				<a class="button button-dark" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Finalizar compra', 'spray-nova' ); ?></a>
			<?php endif; ?>
			<small><?php esc_html_e( 'Impuestos incluidos. Envío calculado al finalizar.', 'spray-nova' ); ?></small>
		</div>
	</div>
	<?php
}

/**
 * Refresh cart UI after AJAX additions/removals.
 *
 * @param array $fragments Existing fragments.
 * @return array
 */
function spray_nova_cart_fragments( $fragments ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $fragments;
	}

	$fragments['.cart-count'] = '<span class="cart-count">' . esc_html( WC()->cart->get_cart_contents_count() ) . '</span>';
	ob_start();
	spray_nova_cart_drawer_inner();
	$fragments['.spray-nova-cart-drawer-inner'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'spray_nova_cart_fragments' );

/**
 * Add a notice when WooCommerce is not active.
 */
function spray_nova_woocommerce_notice() {
	if ( ! current_user_can( 'activate_plugins' ) || class_exists( 'WooCommerce' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Spray Nova necesita WooCommerce activo para gestionar productos, carrito, pagos y stock.', 'spray-nova' ) . '</p></div>';
}
add_action( 'admin_notices', 'spray_nova_woocommerce_notice' );
