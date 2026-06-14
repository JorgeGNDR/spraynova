<?php
/**
 * Site footer and cart drawer.
 *
 * @package SprayNova
 */
?>
<footer class="site-footer">
	<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img src="<?php echo esc_url( spray_nova_image( 'isotipo.jpg' ) ); ?>" alt="">
		<span><?php bloginfo( 'name' ); ?></span>
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
	<p class="footer-bottom">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> · <?php esc_html_e( 'Aviso legal · Privacidad · Cookies', 'spray-nova' ); ?></p>
</footer>

<div class="drawer-backdrop"></div>
<aside class="cart-drawer" aria-label="<?php esc_attr_e( 'Carrito', 'spray-nova' ); ?>">
	<?php spray_nova_cart_drawer_inner(); ?>
</aside>

<?php wp_footer(); ?>
</body>
</html>
