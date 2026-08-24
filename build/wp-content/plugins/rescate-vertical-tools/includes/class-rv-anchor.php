<?php
/**
 * Shortcode [rv_simulador_anclaje] — calcula la fuerza que soporta cada
 * anclaje de un sistema de dos ramas en función del ángulo entre ellas.
 *
 * Fórmula: F = L / (2 · cos(θ/2))
 * Con θ = 0° cada anclaje soporta la mitad de la carga; con θ = 120° soporta
 * la carga completa; por encima de ahí, más que la carga total.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Anchor {

	private static $instances = 0;

	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_shortcode( 'rv_simulador_anclaje', array( __CLASS__, 'render' ) );
	}

	public static function enqueue_assets() {
		// Comparte hoja de estilos con el simulador de factor de caída.
		wp_enqueue_style(
			'rv-tools-simulator',
			RV_TOOLS_URL . 'assets/css/rv-simulator.css',
			array(),
			RV_TOOLS_VERSION
		);

		wp_enqueue_script(
			'rv-tools-anchor',
			RV_TOOLS_URL . 'assets/js/rv-anchor.js',
			array(),
			RV_TOOLS_VERSION,
			true
		);
	}

	/**
	 * Sustituye los marcadores editables por sus valores numéricos.
	 *
	 * @param string $text     Texto con marcadores.
	 * @param array  $settings Ajustes del plugin.
	 * @return string
	 */
	private static function apply_tokens( $text, $settings ) {
		return strtr(
			$text,
			array(
				'{angle_warn}'     => $settings['angle_warn'],
				'{angle_critical}' => $settings['angle_critical'],
				'{anchor}'         => $settings['anchor_rating'],
			)
		);
	}

	public static function render( $atts ) {
		self::$instances++;
		$uid      = 'rv-anc-' . self::$instances;
		$settings = rv_get_settings();

		$data = array(
			'angle-warn'     => $settings['angle_warn'],
			'angle-critical' => $settings['angle_critical'],
			'anchor-rating'  => $settings['anchor_rating'],
			'msg-safe'       => self::apply_tokens( $settings['msg_anchor_safe'], $settings ),
			'msg-warn'       => self::apply_tokens( $settings['msg_anchor_warn'], $settings ),
			'msg-critical'   => self::apply_tokens( $settings['msg_anchor_critical'], $settings ),
		);

		ob_start();
		?>
		<div class="rv-sim rv-anc"
			id="<?php echo esc_attr( $uid ); ?>"
			<?php foreach ( $data as $key => $value ) : ?>
				data-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
			<?php endforeach; ?>
		>
			<div class="rv-anc-layout">
				<div class="rv-anc-controls">
					<div class="rv-sim-row">
						<label for="<?php echo esc_attr( $uid ); ?>-load"><?php esc_html_e( 'Carga total del sistema (kN)', 'rescate-vertical-tools' ); ?></label>
						<input type="range" min="1" max="15" step="0.5" value="4" id="<?php echo esc_attr( $uid ); ?>-load" class="rv-anc-load">
						<span class="rv-sim-val rv-anc-load-out">4.0</span>
					</div>
					<div class="rv-sim-row">
						<label for="<?php echo esc_attr( $uid ); ?>-angle"><?php esc_html_e( 'Ángulo entre las ramas (°)', 'rescate-vertical-tools' ); ?></label>
						<input type="range" min="0" max="170" step="5" value="45" id="<?php echo esc_attr( $uid ); ?>-angle" class="rv-anc-angle">
						<span class="rv-sim-val rv-anc-angle-out">45</span>
					</div>

					<div class="rv-anc-readout">
						<div class="rv-anc-cell">
							<span class="rv-anc-k"><?php esc_html_e( 'Fuerza por anclaje', 'rescate-vertical-tools' ); ?></span>
							<span class="rv-anc-v rv-anc-force">2.16 kN</span>
						</div>
						<div class="rv-anc-cell">
							<span class="rv-anc-k"><?php esc_html_e( 'Respecto a la carga', 'rescate-vertical-tools' ); ?></span>
							<span class="rv-anc-v rv-anc-pct">54 %</span>
						</div>
						<div class="rv-anc-cell">
							<span class="rv-anc-k"><?php esc_html_e( 'Margen del anclaje', 'rescate-vertical-tools' ); ?></span>
							<span class="rv-anc-v rv-anc-margin">10,2×</span>
						</div>
					</div>
				</div>

				<div class="rv-anc-figure">
					<svg viewBox="0 0 260 200" role="img"
						aria-label="<?php esc_attr_e( 'Esquema de dos ramas de anclaje que se abren según el ángulo seleccionado', 'rescate-vertical-tools' ); ?>">
						<!-- ramas -->
						<line class="rv-anc-leg-l" x1="130" y1="160" x2="86" y2="52" stroke="#D9502B" stroke-width="3" stroke-linecap="round"/>
						<line class="rv-anc-leg-r" x1="130" y1="160" x2="174" y2="52" stroke="#D9502B" stroke-width="3" stroke-linecap="round"/>
						<!-- estructura en cada extremo -->
						<g class="rv-anc-cap-l">
							<line x1="66" y1="42" x2="106" y2="62" stroke="#101820" stroke-width="3" stroke-linecap="round"/>
							<circle cx="86" cy="52" r="5" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
						</g>
						<g class="rv-anc-cap-r">
							<line x1="154" y1="62" x2="194" y2="42" stroke="#101820" stroke-width="3" stroke-linecap="round"/>
							<circle cx="174" cy="52" r="5" fill="#FFFFFF" stroke="#101820" stroke-width="2.4"/>
						</g>
						<!-- arco del ángulo -->
						<path class="rv-anc-arc" d="M112 116 A 48 48 0 0 0 148 116" fill="none" stroke="#1B7FC4" stroke-width="1.8" stroke-dasharray="4 3"/>
						<text class="rv-anc-arc-label" x="130" y="108" text-anchor="middle"
							font-family="Archivo, Segoe UI, sans-serif" font-size="13" font-weight="700" fill="#1B7FC4">45°</text>
						<!-- punto maestro y carga -->
						<circle cx="130" cy="160" r="8" fill="#FFFFFF" stroke="#101820" stroke-width="2.6"/>
						<line x1="130" y1="168" x2="130" y2="186" stroke="#101820" stroke-width="2.6" stroke-linecap="round"/>
						<path d="M124 180 l6 7 6 -7" fill="none" stroke="#101820" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
			</div>

			<div class="rv-sim-result rv-anc-result">
				<div class="rv-sim-fc-line">
					<span class="rv-sim-fc-label"><?php esc_html_e( 'Reparto de carga', 'rescate-vertical-tools' ); ?></span>
					<span class="rv-sim-fc-num rv-anc-force-big">2.16 kN</span>
					<span class="rv-sim-fc-tag rv-anc-tag"><?php esc_html_e( 'eficiente', 'rescate-vertical-tools' ); ?></span>
				</div>
				<p class="rv-sim-feedback rv-anc-feedback"><?php esc_html_e( 'Ajusta los controles para ver la recomendación.', 'rescate-vertical-tools' ); ?></p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
