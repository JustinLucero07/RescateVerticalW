<?php
/**
 * Template Name: Sección · Protocolos y normativas
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 05 · 06', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted words. */
			esc_html__( 'Cuando la técnica se cruza con la %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'clínica', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Los protocolos médicos aplicados durante la extracción, y el marco normativo que respalda cada maniobra.', 'rescate-vertical' ),
		'image'  => 'protocolos.jpg',
		'alt'    => __( 'Equipo de rescate asegurando a un paciente en una camilla de extracción vertical', 'rescate-vertical' ),
	)
);

$rv_protocols = array(
	array( 'PHTLS', __( 'Evaluación ABCDE del paciente politraumatizado, adaptada para ejecutarse mientras avanza la extracción técnica, no después de ella.', 'rescate-vertical' ) ),
	array( 'TECC', __( 'Control de hemorragias y vía aérea cuando el entorno restringe el acceso normal del equipo médico.', 'rescate-vertical' ) ),
	array( 'TCCC', __( 'Equilibra la atención clínica con la seguridad operativa cuando la evacuación inmediata no es posible.', 'rescate-vertical' ) ),
	array( __( 'Integración', 'rescate-vertical' ), __( 'El equipo técnico y el médico actúan en simultáneo, no en secuencia: reduce el tiempo total de exposición al riesgo.', 'rescate-vertical' ) ),
);

$rv_norms = array(
	array( 'NFPA 1983', __( 'cuerdas y accesorios', 'rescate-vertical' ) ),
	array( 'NFPA 2500', __( 'operación y entrenamiento', 'rescate-vertical' ) ),
	array( 'OSHA 1926.502', __( 'protección contra caídas', 'rescate-vertical' ) ),
	array( 'ANSI Z359', __( 'sistemas anticaídas', 'rescate-vertical' ) ),
	array( 'IRATA', __( 'certificación por niveles', 'rescate-vertical' ) ),
	array( 'SPRAT', __( 'acceso con cuerdas', 'rescate-vertical' ) ),
	array( 'OSHA 1910.146', __( 'espacios confinados', 'rescate-vertical' ) ),
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Protocolos médicos', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'La atención no espera a la extracción', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Tres marcos clínicos distintos, con un principio común: estabilizar al paciente sin detener la operación técnica que lo está sacando de ahí.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-proto">
			<?php
			$rv_d = 0;
			foreach ( $rv_protocols as $proto ) :
				$rv_c = $rv_d > 0 ? ' d' . min( $rv_d, 3 ) : '';
				$rv_d++;
				?>
				<div class="rv-proto-row rv-reveal<?php echo esc_attr( $rv_c ); ?>">
					<div class="rv-proto-code"><?php echo esc_html( $proto[0] ); ?></div>
					<p><?php echo esc_html( $proto[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft" id="normativas">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Normativas', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'El marco que respalda cada maniobra', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Estas referencias definen qué equipo es admisible, cómo debe operarse y qué nivel de certificación necesita quien lo usa.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-chips rv-reveal d1">
			<?php foreach ( $rv_norms as $norm ) : ?>
				<div class="rv-chip">
					<b><?php echo esc_html( $norm[0] ); ?></b>
					<span><?php echo esc_html( $norm[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
