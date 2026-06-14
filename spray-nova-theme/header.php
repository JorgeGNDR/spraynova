<?php
/**
 * Site header.
 *
 * @package SprayNova
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="announcement">
	<p><?php echo esc_html( get_theme_mod( 'spray_nova_announcement', 'Envío gratis desde 60 € · Entrega 24/48 h en península' ) ); ?></p>
</div>

<header class="site-header">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Spray Nova, inicio', 'spray-nova' ); ?>">
		<?php if ( has_custom_logo() ) : ?>
			<?php echo wp_kses_post( wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo' ) ) ); ?>
		<?php else : ?>
			<img src="<?php echo esc_url( spray_nova_image( 'isotipo.jpg' ) ); ?>" alt="">
		<?php endif; ?>
		<span><?php bloginfo( 'name' ); ?></span>
	</a>

	<nav class="desktop-nav" aria-label="<?php esc_attr_e( 'Navegación principal', 'spray-nova' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'fallback_cb'    => 'spray_nova_primary_menu_fallback',
			'depth'          => 1,
		) );
		?>
	</nav>

	<div class="header-actions">
		<button class="icon-button search-toggle" type="button" aria-label="<?php esc_attr_e( 'Buscar', 'spray-nova' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
		</button>
		<button class="icon-button cart-toggle" type="button" aria-label="<?php esc_attr_e( 'Abrir carrito', 'spray-nova' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.3 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H7"></path><circle cx="10" cy="20" r="1"></circle><circle cx="18" cy="20" r="1"></circle></svg>
			<span class="cart-count"><?php echo function_exists( 'WC' ) && WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : '0'; ?></span>
		</button>
		<button class="menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Abrir menú', 'spray-nova' ); ?>" aria-expanded="false"><span></span><span></span></button>
	</div>

	<div class="search-panel">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="site-search"><?php esc_html_e( '¿Qué estás buscando?', 'spray-nova' ); ?></label>
			<div>
				<input id="site-search" name="s" type="search" placeholder="<?php esc_attr_e( 'Sprays, rotuladores, ceras...', 'spray-nova' ); ?>" value="<?php echo get_search_query(); ?>">
				<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
				<button class="search-submit" type="submit"><?php esc_html_e( 'Buscar', 'spray-nova' ); ?></button>
				<button class="search-close" type="button" aria-label="<?php esc_attr_e( 'Cerrar búsqueda', 'spray-nova' ); ?>">×</button>
			</div>
		</form>
	</div>
</header>
