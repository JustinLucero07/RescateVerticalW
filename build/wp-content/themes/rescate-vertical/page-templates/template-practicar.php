<?php
/**
 * Template Name: Sección · Practicar en digital
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 07', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted words. */
			esc_html__( 'Practicar en digital, antes de %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'subir a la cuerda', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Repetir un procedimiento tantas veces como haga falta, sin riesgo real para quien está aprendiendo.', 'rescate-vertical' ),
		'image'  => 'practicar.jpg',
		'alt'    => __( 'Entrenamiento de rescate con cuerdas en una estructura de prácticas', 'rescate-vertical' ),
	)
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-split">
			<div class="rv-prose rv-reveal">
				<span class="rv-kicker"><?php esc_html_e( 'El enfoque', 'rescate-vertical' ); ?></span>
				<h2><?php esc_html_e( 'El primer error no debería cometerse a 30 metros de altura', 'rescate-vertical' ); ?></h2>
				<p><?php esc_html_e( 'El rescate vertical se aprende haciendo. Un entorno de simulación permite repetir un procedimiento —armar un anclaje, calcular un factor de caída, elegir un protocolo— tantas veces como haga falta, sin riesgo real para quien está aprendiendo.', 'rescate-vertical' ); ?></p>
				<p><?php esc_html_e( 'No sustituye la práctica con cuerdas reales: la complementa, resolviendo la parte que sí puede entrenarse en cualquier momento y lugar — el razonamiento técnico detrás de cada decisión — antes de que ese razonamiento se ponga a prueba en el terreno.', 'rescate-vertical' ); ?></p>

				<div class="rv-callout">
					<p class="rv-callout-k"><?php esc_html_e( 'Lo que cambia con una plataforma digital', 'rescate-vertical' ); ?></p>
					<p><?php esc_html_e( 'Contenido siempre disponible, retroalimentación inmediata sobre cada caso, y la posibilidad de equivocarse las veces que haga falta antes de que el error tenga consecuencias reales.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-split-media rv-reveal d2">
				<?php
				rv_section_image(
					'protocolos.jpg',
					__( 'Equipo de rescate practicando el empaquetamiento de un paciente en camilla', 'rescate-vertical' )
				);
				?>
			</div>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Caso práctico', 'rescate-vertical' ); ?></span>
			<h2>
				<?php
				printf(
					/* translators: %s: highlighted words. */
					esc_html__( 'Presenta tu %s', 'rescate-vertical' ),
					'<em>' . esc_html__( 'caso', 'rescate-vertical' ) . '</em>'
				);
				?>
			</h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Describe un escenario de rescate —terreno, altura, estado del paciente, equipo disponible— y recibe retroalimentación educativa sobre los riesgos técnicos y las consideraciones médicas del caso.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-reveal d1">
			<?php echo do_shortcode( '[rv_presenta_caso]' ); ?>
		</div>
	</div>
</section>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Validación', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Validación por expertos', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Si trabajas en rescate vertical, emergencias médicas o formación técnica, tu evaluación ayuda a mejorar este recurso.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-reveal d1">
			<?php echo do_shortcode( '[rv_formulario_validacion]' ); ?>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
