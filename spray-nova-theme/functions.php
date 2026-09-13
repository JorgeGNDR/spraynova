<?php
/**
 * Spray Nova theme functions.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$spray_nova_theme_version = wp_get_theme()->get( 'Version' );
define( 'SPRAY_NOVA_VERSION', $spray_nova_theme_version ?: '1.0.0' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/home.php';
require_once get_template_directory() . '/inc/legal-pages.php';

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
		wp_enqueue_script( 'wc-cart-fragments' );
		$script_dependencies[] = 'wc-cart-fragments';
		if ( function_exists( 'is_product' ) && is_product() ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'spray-nova-variations', get_template_directory_uri() . '/assets/js/variations.js', array( 'jquery', 'wc-add-to-cart-variation' ), SPRAY_NOVA_VERSION, true );
			$script_dependencies[] = 'wc-add-to-cart-variation';
		}
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

	if ( is_front_page() ) {
		spray_nova_home_assets();
	}

	wp_enqueue_script(
		'spray-nova-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		$script_dependencies,
		SPRAY_NOVA_VERSION,
		true
	);
	wp_script_add_data( 'spray-nova-theme', 'strategy', 'defer' );
	wp_localize_script( 'spray-nova-theme', 'sprayNova', array(
		'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ),
		'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' ),
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
		'nonce'       => wp_create_nonce( 'spray_nova_cart' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'spray_nova_assets' );

/**
 * Establish early connections to the external font hosts used by the theme.
 *
 * @param array  $urls          Resource hint URLs.
 * @param string $relation_type Hint relation type.
 * @return array
 */
function spray_nova_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' !== $relation_type ) {
		return $urls;
	}

	$urls[] = 'https://fonts.googleapis.com';
	$urls[] = array(
		'href'        => 'https://fonts.gstatic.com',
		'crossorigin' => 'anonymous',
	);

	return $urls;
}
add_filter( 'wp_resource_hints', 'spray_nova_resource_hints', 10, 2 );

/**
 * Use the dog logo from the site header as a favicon until a Site Icon is
 * selected in WordPress. A configured Site Icon always takes priority.
 */
function spray_nova_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );
	$favicon_url    = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : '';

	if ( ! $favicon_url ) {
		$favicon_url = spray_nova_image( 'isotipo.jpg' );
	}
	?>
	<link rel="icon" href="<?php echo esc_url( $favicon_url ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $favicon_url ); ?>">
	<?php
}
add_action( 'wp_head', 'spray_nova_favicon', 2 );

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
 * Resolve the image used by a category card on the home page.
 *
 * A Customizer image takes priority. When it is not set, use the native
 * WooCommerce product category thumbnail.
 *
 * @param string $category_slug Product category slug.
 * @param string $theme_mod      Customizer setting name.
 * @return int Attachment ID, or 0 when no image is available.
 */
function spray_nova_category_image_id( $category_slug, $theme_mod ) {
	$image_id = absint( get_theme_mod( $theme_mod, 0 ) );

	if ( $image_id && wp_attachment_is_image( $image_id ) ) {
		return $image_id;
	}

	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return 0;
	}

	$term = get_term_by( 'slug', $category_slug, 'product_cat' );
	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$image_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

	return $image_id && wp_attachment_is_image( $image_id ) ? $image_id : 0;
}

/**
 * Header menu fallback.
 */
function spray_nova_primary_menu_fallback() {
	$links = array(
		__( 'Tienda', 'spray-nova' )     => spray_nova_shop_url(),
		__( 'Categorías', 'spray-nova' ) => home_url( '/#categorias' ),
		__( 'Contacto', 'spray-nova' )   => home_url( '/contacto/' ),
	);

	echo '<ul class="menu">';
	foreach ( $links as $label => $url ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Hide retired About links from menus that were configured before this
 * simplified version of the theme.
 *
 * @param array $items Menu items.
 * @return array
 */
function spray_nova_remove_about_menu_items( $items ) {
	foreach ( $items as $key => $item ) {
		$is_about_page = 'page' === $item->object && 'nosotros' === get_post_field( 'post_name', $item->object_id );
		$is_about_url  = false !== strpos( $item->url, '/nosotros' ) || false !== strpos( $item->url, '#nosotros' );

		if ( $is_about_page || $is_about_url ) {
			unset( $items[ $key ] );
		}
	}

	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'spray_nova_remove_about_menu_items' );

/**
 * Create the contact page once after the theme update, without overwriting an
 * existing page or its content.
 */
function spray_nova_ensure_contact_page() {
	if ( ! current_user_can( 'edit_pages' ) || get_option( 'spray_nova_contact_page_created' ) ) {
		return;
	}

	$page  = get_page_by_path( 'contacto', OBJECT, 'page' );
	$ready = false;
	if ( ! $page ) {
		$page_id = wp_insert_post( array(
			'post_title'  => __( 'Contacto', 'spray-nova' ),
			'post_name'   => 'contacto',
			'post_status' => 'publish',
			'post_type'   => 'page',
		), true );

		if ( ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', 'page-contacto.php' );
			$ready = true;
		}
	} elseif ( 'default' === get_page_template_slug( $page->ID ) ) {
		update_post_meta( $page->ID, '_wp_page_template', 'page-contacto.php' );
		$ready = true;
	} else {
		$ready = true;
	}

	if ( $ready ) {
		update_option( 'spray_nova_contact_page_created', 1, false );
	}
}
add_action( 'admin_init', 'spray_nova_ensure_contact_page' );

/**
 * Seed useful category copy once while keeping WooCommerce as the source of
 * truth. Existing descriptions are never overwritten and the generated copy
 * remains fully editable from Products > Categories.
 */
function spray_nova_seed_product_category_descriptions() {
	$seed_option = 'spray_nova_category_descriptions_seeded_1_3_40';
	if ( ! current_user_can( 'manage_woocommerce' ) || get_option( $seed_option ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$descriptions = array(
		'sprays'      => 'Sprays para graffiti en distintos formatos, presiones, acabados y colores. Elige la lata que mejor se adapte a trazos rápidos, rellenos, detalles y trabajos sobre diferentes superficies.',
		'rotuladores' => 'Markers y rotuladores para graffiti pensados para tags, contornos y detalles. Consulta las características de cada modelo para elegir el formato adecuado a tu forma de pintar.',
		'ceras'       => 'Ceras y pintura sólida para marcar sobre superficies donde un rotulador convencional puede no ser suficiente. Una opción compacta y resistente para realizar trazos directos.',
	);

	foreach ( $descriptions as $slug => $description ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) || '' !== trim( (string) $term->description ) ) {
			continue;
		}

		wp_update_term( $term->term_id, 'product_cat', array( 'description' => $description ) );
	}

	update_option( $seed_option, 1, false );
}
add_action( 'admin_init', 'spray_nova_seed_product_category_descriptions' );

/**
 * Process the simple contact form and send it to the WordPress admin email.
 */
function spray_nova_handle_contact_form() {
	$redirect_url = home_url( '/contacto/' );
	$nonce        = isset( $_POST['spray_nova_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['spray_nova_contact_nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'spray_nova_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'estado', 'error', $redirect_url ) . '#formulario-contacto' );
		exit;
	}

	// A filled honeypot is treated as a successful submission to discourage bots.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'estado', 'enviado', $redirect_url ) . '#formulario-contacto' );
		exit;
	}

	$name    = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['mensaje'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ) ) : '';
	$privacy = ! empty( $_POST['privacidad'] );

	if ( ! $name || ! is_email( $email ) || ! $message || ! $privacy ) {
		wp_safe_redirect( add_query_arg( 'estado', 'incompleto', $redirect_url ) . '#formulario-contacto' );
		exit;
	}

	$recipient   = sanitize_email( get_option( 'admin_email' ) );
	$subject     = sprintf( __( '[%s] Nuevo mensaje de contacto', 'spray-nova' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$mail_body   = sprintf( "Nombre: %1\$s\nCorreo: %2\$s\n\nMensaje:\n%3\$s", $name, $email, $message );
	$header_name = str_replace( array( '<', '>' ), '', $name );
	$headers     = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $header_name . ' <' . $email . '>' );
	$sent        = $recipient && wp_mail( $recipient, $subject, $mail_body, $headers );

	wp_safe_redirect( add_query_arg( 'estado', $sent ? 'enviado' : 'error', $redirect_url ) . '#formulario-contacto' );
	exit;
}
add_action( 'admin_post_spray_nova_contact', 'spray_nova_handle_contact_form' );
add_action( 'admin_post_nopriv_spray_nova_contact', 'spray_nova_handle_contact_form' );

require_once get_template_directory() . '/inc/woocommerce.php';
