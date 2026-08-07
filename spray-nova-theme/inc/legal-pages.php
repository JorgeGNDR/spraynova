<?php
/**
 * Create the legal information pages shipped with the theme.
 *
 * @package SprayNova
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legal pages and their initial content.
 *
 * The installer writes this content once per content version. After that, the
 * pages remain editable in WordPress and are not overwritten by normal theme
 * updates.
 *
 * @return array<string, array{title:string, content:string}>
 */
function spray_nova_legal_page_definitions() {
	return array(
		'aviso-legal' => array(
			'title'   => 'Aviso legal',
			'content' => <<<'HTML'
<h2>Titular</h2>
<ul>
<li><strong>Titular:</strong> Spray Nova</li>
<li><strong>Domicilio:</strong> Calle Almudafer, 20, bajo izquierda, 46520 Puerto de Sagunto (Valencia)</li>
<li><strong>Correo electrónico:</strong> <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a></li>
<li><strong>Sitio web:</strong> <a href="https://spraynova.es">https://spraynova.es</a></li>
</ul>
<h2>Uso y propiedad del sitio</h2>
<p>El usuario debe utilizar el sitio de forma lícita y respetar los derechos de terceros. Los textos, diseños, fotografías, logotipos y demás contenidos pertenecen a Spray Nova o se utilizan con autorización. No se permite su explotación sin autorización, salvo en los casos permitidos legalmente.</p>
<h2>Legislación</h2>
<p>El sitio se rige por la legislación española. Cuando el usuario sea consumidor, serán competentes los órganos que determine la normativa de protección de consumidores.</p>
HTML,
		),
		'condiciones-de-compra' => array(
			'title'   => 'Condiciones de compra, envío y devoluciones',
			'content' => <<<'HTML'
<h2>Vendedor</h2>
<ul>
<li><strong>Vendedor:</strong> Spray Nova</li>
<li><strong>Domicilio:</strong> Calle Almudafer, 20, bajo izquierda, 46520 Puerto de Sagunto (Valencia)</li>
<li><strong>Correo electrónico:</strong> <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a></li>
<li><strong>Teléfono:</strong> <a href="tel:+34634787788">+34 634 787 788</a></li>
</ul>
<h2>Productos, precios y pago</h2>
<p>Las características y el precio de cada producto se muestran en su ficha. Los precios están expresados en euros e incluyen los impuestos aplicables. Los gastos de envío aparecen antes de confirmar la compra.</p>
<p>El pago se procesa mediante Stripe utilizando los métodos disponibles en el checkout. Spray Nova no almacena los datos completos de la tarjeta. El cliente recibirá la confirmación del pedido por correo electrónico y el contrato se formaliza en español.</p>
<h2>Envío</h2>
<ul>
<li><strong>Zonas de envío ordinario:</strong> España y Portugal.</li>
<li><strong>Gastos de envío:</strong> 5,99 €.</li>
<li><strong>Envío gratuito:</strong> pedidos superiores a 99 €, excepto Canarias, Ceuta y Melilla.</li>
<li><strong>Preparación:</strong> de 2 a 4 días laborables.</li>
<li><strong>Transporte después de la expedición:</strong> de 2 a 5 días laborables.</li>
<li><strong>Plazo total habitual:</strong> de 4 a 9 días laborables.</li>
</ul>
<p>Los pedidos se entregan mediante una empresa de transporte contratada por Spray Nova. Canarias, Ceuta, Melilla y otros destinos no habilitados en el checkout requieren consulta previa para confirmar disponibilidad y precio. No se realizan entregas en domingos ni festivos. Si se produce una demora relevante, se informará al cliente.</p>
<h2>Desistimiento</h2>
<p>El consumidor puede desistir de la compra durante los <strong>14 días naturales</strong> siguientes a la recepción, comunicándolo a <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a> mediante una declaración inequívoca o el formulario incluido al final.</p>
<p>Después deberá devolver el producto, en un máximo de 14 días desde la comunicación, a:</p>
<p><strong>Spray Nova — Calle Almudafer, 20, bajo izquierda, 46520 Puerto de Sagunto (Valencia)</strong></p>
<p>El cliente soportará el coste directo de la devolución cuando el desistimiento no se deba a un defecto o error de Spray Nova. Podrá:</p>
<ol>
<li>Organizar el envío mediante un servicio autorizado y con embalaje adecuado.</li>
<li>Solicitar que Spray Nova gestione la recogida, descontándose del reembolso el coste directo comunicado previamente.</li>
</ol>
<p>Para productos que no puedan devolverse normalmente por correo, el coste máximo estimado de la recogida será de <strong>15 €</strong>.</p>
<p>Spray Nova reembolsará el importe pagado y el coste de la modalidad ordinaria de entrega, mediante el mismo medio de pago salvo acuerdo distinto. El reembolso se realizará dentro de los 14 días siguientes a la comunicación, aunque podrá retenerse hasta recibir los productos o una prueba de su devolución.</p>
<p>El cliente responderá de la pérdida de valor causada por una manipulación superior a la necesaria para comprobar el producto. El desistimiento no se aplica a bienes confeccionados conforme a las especificaciones del consumidor o claramente personalizados.</p>
<p>Spray Nova no ofrece cambios ni devoluciones voluntarias adicionales a los derechos exigidos legalmente.</p>
<h2>Productos defectuosos o incorrectos</h2>
<p>Los bienes nuevos tienen una garantía legal de conformidad de <strong>tres años desde su entrega</strong>. Si un producto es defectuoso, llega dañado o no corresponde con lo pedido, el cliente debe escribir a <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a> indicando el número de pedido. Spray Nova asumirá el transporte y aplicará la solución que corresponda legalmente.</p>
<h2>Formulario de desistimiento</h2>
<p>A la atención de <strong>Spray Nova, Calle Almudafer, 20, bajo izquierda, 46520 Puerto de Sagunto (Valencia)</strong> — correo: <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a></p>
<p>Comunico que desisto de la compra del siguiente producto: <strong>[PRODUCTO]</strong><br>
Número de pedido: <strong>[NÚMERO]</strong><br>
Pedido el / recibido el: <strong>[FECHA]</strong><br>
Nombre: <strong>[NOMBRE]</strong><br>
Domicilio: <strong>[DOMICILIO]</strong><br>
Fecha: <strong>[FECHA]</strong><br>
Firma: <strong>[SOLO SI SE PRESENTA EN PAPEL]</strong></p>
HTML,
		),
		'politica-de-privacidad' => array(
			'title'   => 'Política de privacidad',
			'content' => <<<'HTML'
<h2>Responsable</h2>
<ul>
<li><strong>Responsable:</strong> Spray Nova</li>
<li><strong>Domicilio:</strong> Calle Almudafer, 20, bajo izquierda, 46520 Puerto de Sagunto (Valencia)</li>
<li><strong>Correo electrónico:</strong> <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a></li>
</ul>
<h2>Tratamientos</h2>
<h3>Pedidos y cuentas</h3>
<p>Se tratan los datos identificativos, de contacto, facturación, envío y compra necesarios para gestionar pedidos, pagos, entregas, facturas e incidencias. La base jurídica es la ejecución del contrato y el cumplimiento de obligaciones legales. Los datos se conservarán durante la relación contractual y los plazos legales aplicables.</p>
<h3>Consultas</h3>
<p>Se tratan el nombre, correo y mensaje enviados mediante el formulario para responder a la consulta. La base jurídica es el consentimiento o la aplicación de medidas precontractuales. Los datos se conservarán mientras sean necesarios para atender la solicitud y las posibles responsabilidades.</p>
<h3>Seguridad</h3>
<p>Se pueden tratar direcciones IP y registros técnicos para proteger la tienda y prevenir abusos o fraude, sobre la base del interés legítimo en mantener el servicio seguro.</p>
<p>No se enviarán comunicaciones comerciales sin consentimiento.</p>
<h2>Destinatarios</h2>
<p>Los datos se comunicarán únicamente cuando sea necesario a:</p>
<ul>
<li>El proveedor de pagos Stripe.</li>
<li>La empresa de transporte encargada del pedido o devolución.</li>
<li>Proveedores de alojamiento, seguridad y correo que actúan por cuenta de Spray Nova.</li>
<li>Administraciones públicas cuando exista obligación legal.</li>
</ul>
<p>No se venden datos personales. Algunos proveedores pueden tratar datos fuera del Espacio Económico Europeo utilizando una decisión de adecuación, cláusulas contractuales tipo u otra garantía válida.</p>
<h2>Datos obligatorios</h2>
<p>Los campos indicados como obligatorios son necesarios para tramitar el pedido o responder a la consulta. Sin ellos no podrá prestarse el servicio solicitado.</p>
<h2>Derechos</h2>
<p>El interesado puede solicitar el acceso, rectificación, supresión, oposición, limitación o portabilidad de sus datos y retirar el consentimiento escribiendo a <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a>. Solo se solicitará documentación identificativa cuando sea necesaria para verificar su identidad.</p>
<p>También puede presentar una reclamación ante la <a href="https://www.aepd.es/" rel="noopener noreferrer">Agencia Española de Protección de Datos</a>.</p>
HTML,
		),
		'politica-de-cookies' => array(
			'title'   => 'Política de cookies',
			'content' => <<<'HTML'
<h2>Uso de cookies</h2>
<p>Spray Nova utiliza únicamente cookies técnicas necesarias para la cesta, la cuenta, la seguridad, la caché y el pago. Actualmente no utiliza cookies de analítica, publicidad o seguimiento comercial, por lo que no solicita consentimiento para estas finalidades.</p>
<div class="legal-table-wrap"><table>
<thead><tr><th>Cookie o patrón</th><th>Finalidad</th><th>Duración habitual</th></tr></thead>
<tbody>
<tr><td><code>woocommerce_cart_hash</code>, <code>woocommerce_items_in_cart</code></td><td>Gestionar el carrito.</td><td>Sesión</td></tr>
<tr><td><code>wp_woocommerce_session_*</code></td><td>Mantener la sesión de compra.</td><td>2 días</td></tr>
<tr><td><code>wordpress_test_cookie</code></td><td>Comprobar si el navegador admite cookies.</td><td>Sesión</td></tr>
<tr><td><code>wordpress_logged_in_*</code></td><td>Mantener la sesión de usuarios identificados.</td><td>2 días o 14 días con “recordarme”</td></tr>
<tr><td><code>wp-settings-*</code>, <code>wp-settings-time-*</code></td><td>Recordar preferencias de usuarios identificados.</td><td>1 año</td></tr>
<tr><td><code>_lscache_vary</code></td><td>Servir la versión correcta de una página.</td><td>Según sesión y configuración</td></tr>
<tr><td><code>wfwaf-authcookie-*</code></td><td>Proteger el acceso y distinguir permisos cuando proceda.</td><td>12 horas</td></tr>
<tr><td><code>__stripe_mid</code></td><td>Seguridad y prevención del fraude en el pago.</td><td>1 año</td></tr>
<tr><td><code>__stripe_sid</code></td><td>Seguridad durante la sesión de pago.</td><td>30 minutos</td></tr>
<tr><td><code>m</code> y cookies de autenticación de Stripe</td><td>Procesar y proteger el pago.</td><td>Según la función utilizada</td></tr>
</tbody>
</table></div>
<p>Las cookies de Stripe se rigen también por su <a href="https://stripe.com/es/legal/cookies-policy" rel="noopener noreferrer">política de cookies</a>.</p>
<p>El navegador permite bloquear o eliminar cookies, aunque hacerlo puede impedir que funcionen el carrito, la cuenta o el pago.</p>
<p>Si en el futuro se incorporan cookies no necesarias, esta política se actualizará y se solicitará consentimiento antes de instalarlas.</p>
<p>Contacto: <a href="mailto:spraynova26@gmail.com">spraynova26@gmail.com</a>.</p>
HTML,
		),
	);
}

/**
 * Publish the legal pages once and select the WordPress privacy page.
 */
function spray_nova_ensure_legal_pages() {
	$content_version = '2026-08-07-2';

	if ( ! current_user_can( 'edit_pages' ) || $content_version === get_option( 'spray_nova_legal_pages_version' ) ) {
		return;
	}

	$created_pages = array();
	$all_ready     = true;

	foreach ( spray_nova_legal_page_definitions() as $slug => $definition ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$data = array(
			'post_title'     => $definition['title'],
			'post_name'      => $slug,
			'post_content'   => $definition['content'],
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
		);

		if ( $page ) {
			$data['ID'] = $page->ID;
			$page_id    = wp_update_post( $data, true );
		} else {
			$page_id = wp_insert_post( $data, true );
		}

		if ( is_wp_error( $page_id ) ) {
			$all_ready = false;
			continue;
		}

		$created_pages[ $slug ] = (int) $page_id;
		update_post_meta( $page_id, '_spray_nova_legal_page_version', $content_version );
	}

	if ( isset( $created_pages['politica-de-privacidad'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $created_pages['politica-de-privacidad'] );
	}

	if ( $all_ready ) {
		update_option( 'spray_nova_legal_pages_version', $content_version, false );
	}
}
add_action( 'admin_init', 'spray_nova_ensure_legal_pages' );
