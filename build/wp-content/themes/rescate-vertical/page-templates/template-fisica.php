<?php
/**
 * Template Name: Sección · Física del rescate
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 02', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted words. */
			esc_html__( 'La física detrás de una %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'caída', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'El factor de caída relaciona cuánto cae una persona con cuánta cuerda tiene disponible para absorberlo. Ajusta los valores y observa cómo cambia el riesgo.', 'rescate-vertical' ),
		'image'  => 'hero.jpg',
		'alt'    => __( 'Rescatista suspendido con cuerdas sobre una quebrada', 'rescate-vertical' ),
	)
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Herramienta interactiva', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Simulador de factor de caída', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Mueve los tres controles y el sistema recalcula el factor de caída, el nivel de riesgo y la recomendación técnica en tiempo real.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-reveal d1">
			<?php echo do_shortcode( '[rv_simulador_caso]' ); ?>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Segunda herramienta', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Simulador de carga en anclajes', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'El factor de caída explica la energía del impacto; este calcula cómo se reparte esa carga entre dos anclajes según el ángulo que forman. Abre el ángulo y observa cómo cada punto pasa de soportar la mitad de la carga a soportarla entera.', 'rescate-vertical' ); ?></p>
		</div>
		<div class="rv-reveal d1">
			<?php echo do_shortcode( '[rv_simulador_anclaje]' ); ?>
		</div>
	</div>
</section>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Cómo se lee', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Los tres factores que definen el impacto', 'rescate-vertical' ); ?></h2>
		</div>

		<div class="rv-cards">
			<div class="rv-card rv-reveal">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'rapel' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'Distancia de caída', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">01</span>
					</div>
					<p><?php esc_html_e( 'Cuánto recorre el cuerpo antes de que la cuerda empiece a frenarlo. Un anclaje intermedio acorta esa distancia y baja el factor de inmediato.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d1">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'polipasto' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'Cuerda disponible', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">02</span>
					</div>
					<p><?php esc_html_e( 'La longitud que puede elongarse para absorber energía. Más cuerda en juego significa una desaceleración más progresiva y menos fuerza de choque.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d2">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'anclaje' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'Ángulo entre anclajes', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">03</span>
					</div>
					<p><?php esc_html_e( 'Por encima de 60° la tensión por punto empieza a crecer rápido; cerca de 120° cada anclaje soporta prácticamente la carga completa.', 'rescate-vertical' ); ?></p>
				</div>
			</div>

			<div class="rv-card rv-reveal d3">
				<span class="rv-card-figure"><?php rv_technique_diagram( 'camilla' ); ?></span>
				<div class="rv-card-body">
					<div class="rv-card-top">
						<h3><?php esc_html_e( 'El límite del sistema', 'rescate-vertical' ); ?></h3>
						<span class="rv-card-num">04</span>
					</div>
					<p><?php esc_html_e( 'Un sistema de aseguramiento admite del orden de 12 kN de impacto. Superarlo compromete no solo al paciente, sino a todo el equipo suspendido.', 'rescate-vertical' ); ?></p>
				</div>
			</div>
		</div>

		<div class="rv-callout rv-reveal" style="margin-top:32px;max-width:760px;">
			<p class="rv-callout-k"><?php esc_html_e( 'Regla de campo', 'rescate-vertical' ); ?></p>
			<p>
				<?php
				printf(
					/* translators: 1: FC 0, 2: FC 1, 3: FC 2 */
					esc_html__( 'Un %1$s significa que el sistema está en tensión y no hay caída libre; un %2$s ya exige revisar el montaje; un %3$s es el máximo teórico y la situación que todo montaje busca evitar.', 'rescate-vertical' ),
					'<strong>FC 0</strong>',
					'<strong>FC 1</strong>',
					'<strong>FC 2</strong>'
				);
				?>
			</p>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
