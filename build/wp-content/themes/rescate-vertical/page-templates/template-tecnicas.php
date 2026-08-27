<?php
/**
 * Template Name: Sección · Técnicas
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

rv_page_hero(
	array(
		'kicker' => __( 'Sección 03 · Manual operativo', 'rescate-vertical' ),
		'title'  => sprintf(
			/* translators: %s: highlighted word. */
			esc_html__( 'Técnicas de %s', 'rescate-vertical' ),
			'<em>' . esc_html__( 'intervención', 'rescate-vertical' ) . '</em>'
		),
		'intro'  => __( 'Procedimientos estandarizados para operaciones en entornos verticales de alto riesgo: precisión técnica y redundancia operativa.', 'rescate-vertical' ),
		'image'  => 'tecnicas.jpg',
		'alt'    => __( 'Equipo de rescate preparando sistemas de cuerdas y mosquetones', 'rescate-vertical' ),
	)
);

$rv_procedures = array(
	array(
		'num'   => '01',
		'svg'   => 'rapel',
		'title' => __( 'Rapel y descenso controlado', 'rescate-vertical' ),
		'text'  => __( 'Técnicas de progresión vertical segura. Implementación de frenos de fricción variable y dispositivos de aseguramiento automático para descensos con carga controlada.', 'rescate-vertical' ),
	),
	array(
		'num'   => '02',
		'svg'   => 'polipasto',
		'title' => __( 'Sistemas de polipastos', 'rescate-vertical' ),
		'text'  => __( 'Construcción de sistemas de ventaja mecánica 3:1, 5:1 y 9:1 para el traslado eficiente de pacientes y personal médico en extracciones verticales prolongadas.', 'rescate-vertical' ),
	),
	array(
		'num'   => '03',
		'svg'   => 'anclaje',
		'title' => __( 'Anclajes de alta resistencia', 'rescate-vertical' ),
		'text'  => __( 'Configuración de puntos de seguro redundantes y ecualizados (SRENE). Evaluación estructural y distribución de cargas críticas manteniendo el ángulo bajo 60°.', 'rescate-vertical' ),
	),
	array(
		'num'   => '04',
		'svg'   => 'camilla',
		'title' => __( 'Empaquetamiento de paciente', 'rescate-vertical' ),
		'text'  => __( 'Protocolos de inmovilización y fijación en camillas tipo SKED y Nest. Maniobras de transición de horizontal a vertical manteniendo la integridad espinal.', 'rescate-vertical' ),
	),
);
?>

<section class="rv-section">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Procedimientos', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Cuatro maniobras base', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro"><?php esc_html_e( 'Cada procedimiento se apoya en el anterior: sin un anclaje válido no hay descenso seguro, y sin un empaquetamiento correcto la extracción puede agravar la lesión.', 'rescate-vertical' ); ?></p>
		</div>

		<div class="rv-cards">
			<?php
			$rv_delay = 0;
			foreach ( $rv_procedures as $proc ) :
				$rv_class = $rv_delay > 0 ? ' d' . $rv_delay : '';
				$rv_delay++;
				?>
				<div class="rv-card rv-reveal<?php echo esc_attr( $rv_class ); ?>">
					<span class="rv-card-figure"><?php rv_technique_diagram( $proc['svg'] ); ?></span>
					<div class="rv-card-body">
						<div class="rv-card-top">
							<h3><?php echo esc_html( $proc['title'] ); ?></h3>
							<span class="rv-card-num"><?php echo esc_html( $proc['num'] ); ?></span>
						</div>
						<p><?php echo esc_html( $proc['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="rv-section rv-section--soft">
	<div class="rv-shell">
		<div class="rv-section-head rv-reveal">
			<span class="rv-kicker"><?php esc_html_e( 'Nudos', 'rescate-vertical' ); ?></span>
			<h2><?php esc_html_e( 'Nudos que sostienen una vida', 'rescate-vertical' ); ?></h2>
			<p class="rv-section-intro">
				<?php
				printf(
					/* translators: 1: concientización, 2: operaciones, 3: técnico */
					esc_html__( 'Antes de ejecutar cualquier maniobra real, un rescatista avanza por niveles certificados: %1$s, %2$s y %3$s. Casi 6 de cada 10 lesiones registradas en personal de rescate en montaña ocurren durante el entrenamiento, no en operaciones reales.', 'rescate-vertical' ),
					'<strong>' . esc_html__( 'concientización', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( 'operaciones', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( 'técnico', 'rescate-vertical' ) . '</strong>'
				);
				?>
			</p>
		</div>

		<?php $rv_tecnicas = class_exists( 'RV_Content' ) ? RV_Content::items( RV_Content::TECNICA ) : array(); ?>

		<?php if ( $rv_tecnicas ) : ?>
			<div class="rv-knots">
				<?php
				$rv_k = 0;
				foreach ( $rv_tecnicas as $rv_t ) :
					$rv_kc  = ( $rv_k % 4 ) > 0 ? ' d' . ( $rv_k % 4 ) : '';
					$rv_k++;
					$rv_tag = get_post_meta( $rv_t->ID, 'rv_etiqueta', true );
					?>
					<figure class="rv-knot-card rv-reveal<?php echo esc_attr( $rv_kc ); ?>">
						<?php if ( has_post_thumbnail( $rv_t ) ) : ?>
							<div class="rv-knot-photo">
								<?php echo get_the_post_thumbnail( $rv_t, 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
								<?php if ( $rv_tag ) : ?>
									<span class="rv-knot-tag"><?php echo esc_html( $rv_tag ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<figcaption class="rv-knot-body">
							<h3><?php echo esc_html( get_the_title( $rv_t ) ); ?></h3>
							<?php if ( trim( $rv_t->post_content ) !== '' ) : ?>
								<?php echo wp_kses_post( wpautop( $rv_t->post_content ) ); ?>
							<?php endif; ?>
							<?php $rv_gal = RV_Content::gallery_ids( $rv_t->ID ); ?>
							<?php if ( $rv_gal ) : ?>
								<div class="rv-galeria rv-galeria--mini">
									<?php foreach ( $rv_gal as $rv_gid ) : ?>
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $rv_gid, 'full' ) ); ?>" target="_blank" rel="noopener">
											<?php echo wp_get_attachment_image( $rv_gid, 'medium', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?php echo wp_kses_post( RV_Content::video_embed( get_post_meta( $rv_t->ID, 'rv_video', true ) ) ); ?>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="rv-section-intro"><?php esc_html_e( 'Todavía no hay técnicas cargadas. Añádelas en Rescate Vertical → Técnicas y nudos.', 'rescate-vertical' ); ?></p>
		<?php endif; ?>

		<div class="rv-callout rv-reveal" style="margin-top:32px;max-width:760px;">
			<p class="rv-callout-k"><?php esc_html_e( 'Factor de seguridad del anclaje', 'rescate-vertical' ); ?></p>
			<p>
				<?php
				printf(
					/* translators: 1: 10 kN estáticos, 2: 6 kN dinámicos, 3: 10:1 */
					esc_html__( 'Todo punto de anclaje debe resistir al menos %1$s o %2$s, con un factor de seguridad mínimo de %3$s frente a la carga máxima esperada.', 'rescate-vertical' ),
					'<strong>' . esc_html__( '10 kN estáticos', 'rescate-vertical' ) . '</strong>',
					'<strong>' . esc_html__( '6 kN dinámicos', 'rescate-vertical' ) . '</strong>',
					'<strong>10:1</strong>'
				);
				?>
			</p>
		</div>

		<?php rv_editor_content(); ?>
	</div>
</section>

<?php get_footer(); ?>
