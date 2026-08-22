<?php
/**
 * Shortcode [rv_simulador_caso] — interactive fall-factor simulator.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Simulator {

	private static $instance_count = 0;

	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_shortcode( 'rv_simulador_caso', array( __CLASS__, 'render' ) );
	}

	public static function enqueue_assets() {
		wp_enqueue_style(
			'rv-tools-simulator',
			RV_TOOLS_URL . 'assets/css/rv-simulator.css',
			array(),
			RV_TOOLS_VERSION
		);

		wp_enqueue_script(
			'rv-tools-simulator',
			RV_TOOLS_URL . 'assets/js/rv-simulator.js',
			array(),
			RV_TOOLS_VERSION,
			true
		);
	}

	/**
	 * Replaces {kn}, {angle_warn} and {angle_critical} tokens in admin-edited
	 * feedback text with the currently configured numeric values.
	 *
	 * @param string $text     Raw text possibly containing tokens.
	 * @param array  $settings Plugin settings.
	 * @return string
	 */
	private static function apply_tokens( $text, $settings ) {
		return strtr(
			$text,
			array(
				'{kn}'             => $settings['kn_limit'],
				'{angle_warn}'     => $settings['angle_warn'],
				'{angle_critical}' => $settings['angle_critical'],
			)
		);
	}

	public static function render( $atts ) {
		self::$instance_count++;
		$uid = 'rv-sim-' . self::$instance_count;

		$settings = rv_get_settings();

		$data = array(
			'safe-max'           => $settings['fc_safe_max'],
			'moderate-max'       => $settings['fc_moderate_max'],
			'kn'                 => $settings['kn_limit'],
			'angle-warn'         => $settings['angle_warn'],
			'angle-critical'     => $settings['angle_critical'],
			'msg-safe'           => self::apply_tokens( $settings['msg_safe'], $settings ),
			'msg-moderate'       => self::apply_tokens( $settings['msg_moderate'], $settings ),
			'msg-dangerous'      => self::apply_tokens( $settings['msg_dangerous'], $settings ),
			'msg-angle-warn'     => self::apply_tokens( $settings['msg_angle_warn'], $settings ),
			'msg-angle-critical' => self::apply_tokens( $settings['msg_angle_critical'], $settings ),
		);

		ob_start();
		?>
		<div class="rv-sim"
			id="<?php echo esc_attr( $uid ); ?>"
			<?php foreach ( $data as $key => $value ) : ?>
				data-<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
			<?php endforeach; ?>
		>
			<?php // uid deliberately excludes user input; all $data values pass through esc_attr above. ?>
			<div class="rv-sim-row">
				<label for="<?php echo esc_attr( $uid ); ?>-fall"><?php esc_html_e( 'Distancia de caída (m)', 'rescate-vertical-tools' ); ?></label>
				<input type="range" min="0" max="10" step="0.5" value="4" id="<?php echo esc_attr( $uid ); ?>-fall" class="rv-sim-fall">
				<span class="rv-sim-val rv-sim-fall-out">4.0</span>
			</div>
			<div class="rv-sim-row">
				<label for="<?php echo esc_attr( $uid ); ?>-rope"><?php esc_html_e( 'Cuerda disponible (m)', 'rescate-vertical-tools' ); ?></label>
				<input type="range" min="1" max="10" step="0.5" value="5" id="<?php echo esc_attr( $uid ); ?>-rope" class="rv-sim-rope">
				<span class="rv-sim-val rv-sim-rope-out">5.0</span>
			</div>
			<div class="rv-sim-row">
				<label for="<?php echo esc_attr( $uid ); ?>-angle"><?php esc_html_e( 'Ángulo entre anclajes (°)', 'rescate-vertical-tools' ); ?></label>
				<input type="range" min="0" max="150" step="5" value="40" id="<?php echo esc_attr( $uid ); ?>-angle" class="rv-sim-angle">
				<span class="rv-sim-val rv-sim-angle-out">40</span>
			</div>
			<div class="rv-sim-result">
				<div class="rv-sim-fc-line">
					<span class="rv-sim-fc-label mono"><?php esc_html_e( 'Factor de caída', 'rescate-vertical-tools' ); ?></span>
					<span class="rv-sim-fc-num">0.80</span>
					<span class="rv-sim-fc-tag"><?php esc_html_e( 'moderado', 'rescate-vertical-tools' ); ?></span>
				</div>
				<p class="rv-sim-feedback"><?php esc_html_e( 'Ajusta los controles para ver la recomendación.', 'rescate-vertical-tools' ); ?></p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
