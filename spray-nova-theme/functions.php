<?php
/**
 * Spray Nova theme functions.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPRAY_NOVA_VERSION', '1.0.0' );

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
	add_action( 'woocommerce_before_main_content', 'spray_nova_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'spray_nova_woocommerce_wrapper_end', 10 );
}
add_action( 'wp', 'spray_nova_woocommerce_integration' );
add_filter( 'loop_shop_columns', function() { return 4; } );

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
	$button_class   = $product->supports( 'ajax_add_to_cart' ) ? ' ajax_add_to_cart' : '';
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
			class="quick-add button add_to_cart_button product_type_<?php echo esc_attr( $product->get_type() . $button_class ); ?>"
			href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
			data-quantity="1"
			data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
			data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
			aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
			rel="nofollow"
		>
			<?php echo esc_html( $product->add_to_cart_text() ); ?><span>+</span>
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
