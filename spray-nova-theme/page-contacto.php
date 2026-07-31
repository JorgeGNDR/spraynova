<?php
/**
 * Template Name: Contacto
 *
 * @package SprayNova
 */

get_header();

$status      = isset( $_GET['estado'] ) ? sanitize_key( wp_unslash( $_GET['estado'] ) ) : '';
$privacy_url = get_privacy_policy_url() ?: home_url( '/politica-de-privacidad/' );
?>
<main class="content-main contact-main">
	<div class="content-shell contact-layout">
		<section class="contact-copy">
			<p class="eyebrow"><?php esc_html_e( 'Hablemos', 'spray-nova' ); ?></p>
			<h1><?php esc_html_e( 'CONTACTO', 'spray-nova' ); ?></h1>
			<p><?php esc_html_e( '¿Tienes una duda sobre un producto o un pedido? Escríbenos y te responderemos lo antes posible.', 'spray-nova' ); ?></p>
		</section>

		<section class="contact-form-panel" id="formulario-contacto">
			<?php if ( 'enviado' === $status ) : ?>
				<div class="contact-notice is-success" role="status"><?php esc_html_e( 'Mensaje enviado. Te responderemos pronto.', 'spray-nova' ); ?></div>
			<?php elseif ( 'incompleto' === $status ) : ?>
				<div class="contact-notice is-error" role="alert"><?php esc_html_e( 'Revisa los campos y acepta la política de privacidad.', 'spray-nova' ); ?></div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="contact-notice is-error" role="alert"><?php esc_html_e( 'No se pudo enviar el mensaje. Inténtalo de nuevo más tarde.', 'spray-nova' ); ?></div>
			<?php endif; ?>

			<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="spray_nova_contact">
				<?php wp_nonce_field( 'spray_nova_contact', 'spray_nova_contact_nonce' ); ?>

				<p class="contact-honeypot" aria-hidden="true">
					<label for="contact-website">Web</label>
					<input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
				</p>

				<label class="contact-field" for="contact-name">
					<span><?php esc_html_e( 'Nombre', 'spray-nova' ); ?></span>
					<input id="contact-name" name="nombre" type="text" autocomplete="name" maxlength="120" required>
				</label>

				<label class="contact-field" for="contact-email">
					<span><?php esc_html_e( 'Correo electrónico', 'spray-nova' ); ?></span>
					<input id="contact-email" name="email" type="email" autocomplete="email" maxlength="190" required>
				</label>

				<label class="contact-field" for="contact-message">
					<span><?php esc_html_e( 'Mensaje', 'spray-nova' ); ?></span>
					<textarea id="contact-message" name="mensaje" rows="5" maxlength="3000" required></textarea>
				</label>

				<label class="contact-consent">
					<input name="privacidad" type="checkbox" value="1" required>
					<span><?php printf( wp_kses_post( __( 'Autorizo el uso de mis datos para responder a esta consulta y he leído la <a href="%s">política de privacidad</a>.', 'spray-nova' ) ), esc_url( $privacy_url ) ); ?></span>
				</label>

				<button class="button button-dark" type="submit"><?php esc_html_e( 'Enviar mensaje', 'spray-nova' ); ?></button>
			</form>
		</section>
	</div>
</main>
<?php get_footer(); ?>
