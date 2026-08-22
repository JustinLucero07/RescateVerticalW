<?php
/**
 * Plugin Name: Rescate Vertical — Herramientas
 * Plugin URI: https://rescatevertical.local
 * Description: Simulador de factor de caída, formulario de validación por expertos y ajustes de umbrales de riesgo para el sitio educativo "Rescate vertical en emergencias médicas".
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Rescate Vertical
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rescate-vertical-tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RV_TOOLS_VERSION', '1.0.0' );
define( 'RV_TOOLS_FILE', __FILE__ );
define( 'RV_TOOLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'RV_TOOLS_URL', plugin_dir_url( __FILE__ ) );
define( 'RV_TOOLS_OPTION', 'rv_settings' );
define( 'RV_TOOLS_CPT', 'rv_validacion' );
define( 'RV_TOOLS_MENU_SLUG', 'rescate-vertical' );

require_once RV_TOOLS_PATH . 'includes/class-rv-settings.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-cpt.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-simulator.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-validation-form.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-export.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-case-review.php';

/**
 * Default settings values. Central source of truth used on activation
 * and whenever an option is missing.
 */
function rv_default_settings() {
	return array(
		'fc_safe_max'        => '0.3',
		'fc_moderate_max'    => '1',
		'kn_limit'           => '12',
		'angle_warn'         => '60',
		'angle_critical'     => '120',
		'msg_safe'           => 'Factor de caída bajo: el sistema absorbe el impacto lejos del límite de {kn} kN.',
		'msg_moderate'       => 'Riesgo moderado. Un anclaje intermedio acortaría la distancia de caída potencial.',
		'msg_dangerous'      => 'Factor de caída alto, cerca del máximo de 2. La fuerza puede superar los {kn} kN: instala un anclaje intermedio antes de continuar.',
		'msg_angle_warn'     => 'El ángulo entre anclajes ya supera los {angle_warn}° recomendados.',
		'msg_angle_critical' => 'El ángulo entre anclajes además duplica la tensión por punto: bájalo de {angle_warn}°.',
		'gemini_api_key'     => '',
		'gemini_model'       => 'gemini-2.0-flash',
		'gemini_prompt'      => "Eres un instructor certificado de rescate vertical y emergencias médicas. Un estudiante te va a describir un caso o escenario de rescate. Da una retroalimentación breve y educativa en español, organizada en: 1) riesgos técnicos (anclajes, factor de caída, ángulos), 2) consideraciones médicas relevantes (PHTLS/TECC/TCCC), 3) qué harías distinto o qué confirmarías antes de actuar. Sé concreto y no repitas el caso. Cierra siempre recordando que es una retroalimentación educativa y no sustituye la supervisión de un instructor certificado en campo real.",
	);
}

/**
 * Returns the current plugin settings merged with defaults, so newly
 * added keys always have a safe fallback.
 */
function rv_get_settings() {
	$saved = get_option( RV_TOOLS_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return wp_parse_args( $saved, rv_default_settings() );
}

/**
 * Plugin activation: register the CPT so rewrite rules can be flushed,
 * seed default settings, and flush.
 */
function rv_tools_activate() {
	RV_CPT::register_post_type();

	if ( false === get_option( RV_TOOLS_OPTION, false ) ) {
		add_option( RV_TOOLS_OPTION, rv_default_settings() );
	}

	flush_rewrite_rules();
}
register_activation_hook( RV_TOOLS_FILE, 'rv_tools_activate' );

/**
 * Plugin deactivation: flush rewrite rules to remove the CPT's routes.
 */
function rv_tools_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( RV_TOOLS_FILE, 'rv_tools_deactivate' );

/**
 * Bootstrap all plugin modules.
 */
function rv_tools_init() {
	RV_CPT::register();
	RV_Settings::register();
	RV_Simulator::register();
	RV_Validation_Form::register();
	RV_Export::register();
	RV_Case_Review::register();
}
add_action( 'plugins_loaded', 'rv_tools_init' );
