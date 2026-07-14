<?php
/**
 * Site footer and cart drawer.
 *
 * @package SprayNova
 */
?>
<footer class="site-footer">
	<div class="footer-primary">
		<a class="footer-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span><?php bloginfo( 'name' ); ?></span>
			<small><?php esc_html_e( 'Graffiti shop', 'spray-nova' ); ?></small>
		</a>
		<nav class="footer-links" aria-label="<?php esc_attr_e( 'Navegación del pie', 'spray-nova' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'fallback_cb'    => 'spray_nova_primary_menu_fallback',
				'depth'          => 1,
			) );
			?>
		</nav>
		<div class="footer-social">
			<?php if ( get_theme_mod( 'spray_nova_instagram_url' ) ) : ?><a href="<?php echo esc_url( get_theme_mod( 'spray_nova_instagram_url' ) ); ?>" rel="noopener noreferrer" target="_blank" aria-label="Instagram">IG</a><?php endif; ?>
			<?php if ( get_theme_mod( 'spray_nova_tiktok_url' ) ) : ?><a href="<?php echo esc_url( get_theme_mod( 'spray_nova_tiktok_url' ) ); ?>" rel="noopener noreferrer" target="_blank" aria-label="TikTok">TK</a><?php endif; ?>
		</div>
	</div>
	<div class="footer-bottom">
		<p><span aria-hidden="true">&copy;</span> <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
		<nav class="footer-legal" aria-label="<?php esc_attr_e( 'Información legal', 'spray-nova' ); ?>">
			<a href="<?php echo esc_url( home_url( '/aviso-legal/' ) ); ?>"><?php esc_html_e( 'Aviso legal', 'spray-nova' ); ?></a>
			<a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/politica-de-privacidad/' ) ); ?>"><?php esc_html_e( 'Privacidad', 'spray-nova' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/politica-de-cookies/' ) ); ?>"><?php esc_html_e( 'Cookies', 'spray-nova' ); ?></a>
		</nav>
	</div>
</footer>

<div class="drawer-backdrop"></div>
<aside class="cart-drawer" aria-label="<?php esc_attr_e( 'Carrito', 'spray-nova' ); ?>">
	<?php spray_nova_cart_drawer_inner(); ?>
</aside>

<?php wp_footer(); ?>
</body>
</html>
