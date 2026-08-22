<?php
/**
 * Template Name: Sección · Equipos
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 04', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted words. */
			esc_html__( 'El equipo, %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'pieza por pieza', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Cada elemento del sistema tiene una norma que lo respalda y una resistencia mínima verificable. Sin certificación, no entra a la maniobra.', 'rescate-vertical' ),
		'image'  => 'equipos.jpg',
		'alt'    => __( 'Primer plano de un mosquetón de seguro conectado a una línea de vida', 'rescate-vertical' ),
	)
);

$rv_gear = array(
	array(
		'icon'  => 'cuerda',
		'name'  => __( 'Cuerda dinámica', 'rescate-vertical' ),
		'desc'  => __( 'Absorbe energía de caída mediante elongación controlada.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Norma', 'rescate-vertical' ), 'EN 892' ),
			array( __( 'Resistencia mín.', 'rescate-vertical' ), '22 kN' ),
		),
	),
	array(
		'icon'  => 'arnes',
		'name'  => __( 'Arnés integral', 'rescate-vertical' ),
		'desc'  => __( 'Distribuye la carga entre tronco, hombros y muslos.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Norma', 'rescate-vertical' ), 'EN 361' ),
			array( __( 'Reduce', 'rescate-vertical' ), __( 'síndrome de suspensión', 'rescate-vertical' ) ),
		),
	),
	array(
		'icon'  => 'mosqueton',
		'name'  => __( 'Mosquetón', 'rescate-vertical' ),
		'desc'  => __( 'Cierre de seguro automático en anclajes críticos.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Norma', 'rescate-vertical' ), 'EN 362' ),
			array( __( 'Eje mayor', 'rescate-vertical' ), '20 kN' ),
		),
	),
	array(
		'icon'  => 'descensor',
		'name'  => __( 'Descensor', 'rescate-vertical' ),
		'desc'  => __( 'Fricciona la cuerda para controlar la velocidad de bajada.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Norma', 'rescate-vertical' ), 'EN 341' ),
			array( __( 'Tipo', 'rescate-vertical' ), 'ID / RIG / ' . __( 'ocho', 'rescate-vertical' ) ),
		),
	),
	array(
		'icon'  => 'casco',
		'name'  => __( 'Casco', 'rescate-vertical' ),
		'desc'  => __( 'Protege contra impacto de objetos en caída durante la maniobra.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Norma', 'rescate-vertical' ), 'EN 397' ),
			array( __( 'Uso', 'rescate-vertical' ), __( 'obligatorio', 'rescate-vertical' ) ),
		),
	),
	array(
		'icon'  => 'camilla',
		'name'  => __( 'Camilla Stokes', 'rescate-vertical' ),
		'desc'  => __( 'Inmoviliza al paciente en posición horizontal o inclinada.', 'rescate-vertical' ),
		'specs' => array(
			array( __( 'Enganches', 'rescate-vertical' ), __( 'certificados', 'rescate-vertical' ) ),
			array( __( 'Prioriza', 'rescate-vertical' ), __( 'restricción de movimiento', 'rescate-vertical' ) ),
		),
	),
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Fichas técnicas', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Seis piezas, seis certificaciones', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'La norma europea EN define el ensayo que cada pieza debe superar. Conocerla permite verificar en campo si el equipo disponible sirve para la maniobra que se va a ejecutar.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-gear">
			<?php
			$rv_i = 0;
			foreach ( $rv_gear as $item ) :
				$rv_class = ( $rv_i % 3 ) > 0 ? ' d' . ( $rv_i % 3 ) : '';
				$rv_i++;
				?>
				<div class="rv-gear-card rv-reveal<?php echo esc_attr( $rv_class ); ?>">
					<div class="rv-gear-figure"><?php rv_gear_icon( $item['icon'] ); ?></div>
					<div class="rv-gear-body">
						<span class="rv-badge"><?php esc_html_e( 'Certificado', 'rescate-vertical' ); ?></span>
						<h3><?php echo esc_html( $item['name'] ); ?></h3>
						<p><?php echo esc_html( $item['desc'] ); ?></p>
						<?php foreach ( $item['specs'] as $spec ) : ?>
							<div class="rv-spec">
								<span><?php echo esc_html( $spec[0] ); ?></span>
								<span><?php echo esc_html( $spec[1] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
