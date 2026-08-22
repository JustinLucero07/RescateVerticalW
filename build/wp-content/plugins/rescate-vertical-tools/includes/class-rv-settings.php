<?php
/**
 * Settings page: "Rescate Vertical" top-level admin menu with the
 * fall-factor thresholds, kN limit and risk-level feedback text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RV_Settings {

	const GROUP = 'rv_settings_group';

	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function add_menu() {
		add_menu_page(
			__( 'Rescate Vertical', 'rescate-vertical-tools' ),
			__( 'Rescate Vertical', 'rescate-vertical-tools' ),
			'manage_options',
			RV_TOOLS_MENU_SLUG,
			array( __CLASS__, 'render_settings_page' ),
			'dashicons-networking',
			25
		);

		add_submenu_page(
			RV_TOOLS_MENU_SLUG,
			__( 'Ajustes del simulador', 'rescate-vertical-tools' ),
			__( 'Ajustes', 'rescate-vertical-tools' ),
			'manage_options',
			RV_TOOLS_MENU_SLUG,
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			self::GROUP,
			RV_TOOLS_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => rv_default_settings(),
			)
		);
	}

	/**
	 * Sanitizes every settings field before it is persisted.
	 *
	 * @param array $input Raw input from the settings form.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$defaults = rv_default_settings();
		$output   = array();

		$numeric_fields = array( 'fc_safe_max', 'fc_moderate_max', 'kn_limit', 'angle_warn', 'angle_critical' );
		foreach ( $numeric_fields as $field ) {
			$value            = isset( $input[ $field ] ) ? $input[ $field ] : $defaults[ $field ];
			$output[ $field ] = is_numeric( $value ) ? (string) floatval( $value ) : $defaults[ $field ];
		}

		$text_fields = array( 'msg_safe', 'msg_moderate', 'msg_dangerous', 'msg_angle_warn', 'msg_angle_critical', 'gemini_prompt' );
		foreach ( $text_fields as $field ) {
			$output[ $field ] = isset( $input[ $field ] )
				? sanitize_textarea_field( wp_unslash( $input[ $field ] ) )
				: $defaults[ $field ];
		}

		$output['gemini_api_key'] = isset( $input['gemini_api_key'] )
			? sanitize_text_field( wp_unslash( $input['gemini_api_key'] ) )
			: $defaults['gemini_api_key'];

		$output['gemini_model'] = isset( $input['gemini_model'] ) && '' !== trim( $input['gemini_model'] )
			? sanitize_text_field( wp_unslash( $input['gemini_model'] ) )
			: $defaults['gemini_model'];

		return $output;
	}

	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = rv_get_settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Rescate Vertical — Ajustes del simulador', 'rescate-vertical-tools' ); ?></h1>
			<p><?php esc_html_e( 'Estos valores controlan el simulador de factor de caída (shortcode [rv_simulador_caso]) sin necesidad de editar código. Usa los marcadores {kn} y {angle_warn} dentro de los textos para insertar los valores numéricos configurados abajo.', 'rescate-vertical-tools' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( self::GROUP ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="rv_fc_safe_max"><?php esc_html_e( 'Umbral "seguro" (factor de caída máx.)', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="number" step="0.05" min="0" max="2" id="rv_fc_safe_max" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[fc_safe_max]" value="<?php echo esc_attr( $settings['fc_safe_max'] ); ?>" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_fc_moderate_max"><?php esc_html_e( 'Umbral "moderado" (factor de caída máx.)', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="number" step="0.05" min="0" max="2" id="rv_fc_moderate_max" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[fc_moderate_max]" value="<?php echo esc_attr( $settings['fc_moderate_max'] ); ?>" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_kn_limit"><?php esc_html_e( 'Límite de kN del sistema de aseguramiento', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="number" step="1" min="1" id="rv_kn_limit" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[kn_limit]" value="<?php echo esc_attr( $settings['kn_limit'] ); ?>" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_angle_warn"><?php esc_html_e( 'Ángulo de advertencia (°)', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="number" step="1" min="0" max="180" id="rv_angle_warn" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[angle_warn]" value="<?php echo esc_attr( $settings['angle_warn'] ); ?>" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_angle_critical"><?php esc_html_e( 'Ángulo crítico (°)', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="number" step="1" min="0" max="180" id="rv_angle_critical" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[angle_critical]" value="<?php echo esc_attr( $settings['angle_critical'] ); ?>" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_msg_safe"><?php esc_html_e( 'Mensaje: nivel seguro', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_msg_safe" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[msg_safe]" rows="2" class="large-text"><?php echo esc_textarea( $settings['msg_safe'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_msg_moderate"><?php esc_html_e( 'Mensaje: nivel moderado', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_msg_moderate" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[msg_moderate]" rows="2" class="large-text"><?php echo esc_textarea( $settings['msg_moderate'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_msg_dangerous"><?php esc_html_e( 'Mensaje: nivel peligroso', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_msg_dangerous" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[msg_dangerous]" rows="2" class="large-text"><?php echo esc_textarea( $settings['msg_dangerous'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_msg_angle_warn"><?php esc_html_e( 'Mensaje adicional: ángulo de advertencia', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_msg_angle_warn" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[msg_angle_warn]" rows="2" class="large-text"><?php echo esc_textarea( $settings['msg_angle_warn'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_msg_angle_critical"><?php esc_html_e( 'Mensaje adicional: ángulo crítico', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_msg_angle_critical" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[msg_angle_critical]" rows="2" class="large-text"><?php echo esc_textarea( $settings['msg_angle_critical'] ); ?></textarea></td>
					</tr>
				</table>

				<h2><?php esc_html_e( '"Presenta tu caso" — retroalimentación con IA (Gemini)', 'rescate-vertical-tools' ); ?></h2>
				<p><?php esc_html_e( 'Controla el shortcode [rv_presenta_caso]. Pega aquí tu clave de la API de Gemini (Google AI Studio) para activarlo; no es necesario editar ningún archivo del tema ni del plugin.', 'rescate-vertical-tools' ); ?></p>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="rv_gemini_api_key"><?php esc_html_e( 'Clave de la API de Gemini', 'rescate-vertical-tools' ); ?></label></th>
						<td>
							<input type="password" autocomplete="off" id="rv_gemini_api_key" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[gemini_api_key]" value="<?php echo esc_attr( $settings['gemini_api_key'] ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Se guarda en la base de datos de tu WordPress, nunca en archivos del tema o del plugin.', 'rescate-vertical-tools' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_gemini_model"><?php esc_html_e( 'Modelo de Gemini', 'rescate-vertical-tools' ); ?></label></th>
						<td><input type="text" id="rv_gemini_model" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[gemini_model]" value="<?php echo esc_attr( $settings['gemini_model'] ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="rv_gemini_prompt"><?php esc_html_e( 'Instrucciones para la IA (prompt del sistema)', 'rescate-vertical-tools' ); ?></label></th>
						<td><textarea id="rv_gemini_prompt" name="<?php echo esc_attr( RV_TOOLS_OPTION ); ?>[gemini_prompt]" rows="6" class="large-text"><?php echo esc_textarea( $settings['gemini_prompt'] ); ?></textarea></td>
					</tr>
				</table>

				<?php submit_button( __( 'Guardar ajustes', 'rescate-vertical-tools' ) ); ?>
			</form>
		</div>
		<?php
	}
}
