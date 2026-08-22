<?php
/**
 * Template Name: Sección · Qué es el rescate vertical
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 01', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted words. */
			esc_html__( 'Qué es el %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'rescate vertical', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Una disciplina propia, con sus propios márgenes de seguridad, para intervenir donde el terreno no permite un rescate convencional.', 'rescate-vertical' ),
		'image'  => 'que-es.jpg',
		'alt'    => __( 'Quebrada profunda con paredes rocosas verticales y un río al fondo', 'rescate-vertical' ),
	)
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-split">
			<div class="rv-prose rv-reveal">
				<p><?php esc_html_e( 'Es el conjunto de procedimientos con cuerdas, anclajes y equipo certificado usado para acceder, estabilizar y evacuar a una persona cuando el terreno no permite un rescate convencional: laderas escarpadas, quebradas, pozos, torres o edificios en altura.', 'rescate-vertical' ); ?></p>
				<p><?php esc_html_e( 'No es una técnica de montañismo aplicada a emergencias por casualidad: es una disciplina propia, con sus propios márgenes de seguridad, porque aquí el paciente no puede caminar hacia la ambulancia. El equipo tiene que ir hacia él, estabilizarlo suspendido en el aire, y traerlo de vuelta sin agravar sus lesiones.', 'rescate-vertical' ); ?></p>
				<p><?php esc_html_e( 'El cuerpo humano en altura además se comporta distinto: la baja presión de oxígeno acelera el deterioro neurológico, el frío avanza más rápido de lo esperado, y un politraumatismo mal manejado durante la extracción puede convertir una lesión estable en una fatal.', 'rescate-vertical' ); ?></p>

				<div class="rv-callout">
					<p class="rv-callout-k"><?php esc_html_e( 'Por qué se enseña aparte', 'rescate-vertical' ); ?></p>
					<p><?php esc_html_e( 'Requiere dominar simultáneamente tres lenguajes: la física de las cargas, la técnica de cuerdas y el protocolo clínico del paciente politraumatizado. Fallar en cualquiera de los tres compromete a todo el equipo.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-split-media rv-reveal d2">
				<?php
				rv_section_image(
					'tecnicas.jpg',
					__( 'Equipo de rescate preparando sistemas de cuerdas antes de una maniobra', 'rescate-vertical' )
				);
				?>
			</div>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Los tres lenguajes', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Lo que el equipo debe dominar a la vez', 'rescate-vertical' ); ?></h2>
		</div>

		<div class="rv-cards">
			<div class="rv-card rv-reveal">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'rapel' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'La física de las cargas', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">01</span>
					</div>
					<p><?php esc_html_e( 'Factor de caída, fuerza de choque y ángulos entre anclajes: la diferencia entre un sistema que absorbe el impacto y uno que lo multiplica.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d1">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'anclaje' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'La técnica de cuerdas', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">02</span>
					</div>
					<p><?php esc_html_e( 'Nudos inspeccionables, anclajes redundantes y ecualizados, y sistemas de descenso y ascenso controlados bajo carga real.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d2">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'camilla' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'El protocolo clínico', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">03</span>
					</div>
					<p><?php esc_html_e( 'Evaluación y estabilización del paciente politraumatizado mientras avanza la extracción técnica, no después de ella.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d3">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'polipasto' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'La coordinación del equipo', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">04</span>
					</div>
					<p><?php esc_html_e( 'Técnico y médico actúan en simultáneo, no en secuencia: es lo que reduce el tiempo total de exposición al riesgo.', 'rescate-vertical' ); ?></p>
				</div>
			</div>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
