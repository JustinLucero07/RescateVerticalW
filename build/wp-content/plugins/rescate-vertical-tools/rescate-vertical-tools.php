<?php
/**
 * Plugin Name: Rescate Vertical — Herramientas
 * Plugin URI: https://rescatevertical.local
 * Description: Simulador de factor de caída, formulario de validación por expertos y ajustes de umbrales de riesgo para el sitio educativo "Rescate vertical en emergencias médicas".
 * Version: 1.5.0
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

define( 'RV_TOOLS_VERSION', '1.5.0' );
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
require_once RV_TOOLS_PATH . 'includes/class-rv-anchor.php';
require_once RV_TOOLS_PATH . 'includes/class-rv-cases.php';

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
		'anchor_rating'       => '22',
		'msg_anchor_safe'     => 'Reparto eficiente: con el ángulo por debajo de {angle_warn}° cada anclaje soporta menos que la carga total.',
		'msg_anchor_warn'     => 'El ángulo ya penaliza el reparto: cada anclaje asume una fracción alta de la carga. Acerca las ramas o alarga la cinta.',
		'msg_anchor_critical' => 'Ángulo crítico: por encima de {angle_critical}° cada anclaje soporta tanto o más que la carga total. Reduce el ángulo antes de cargar el sistema.',
		'gemini_api_key'     => '',
		'gemini_model'       => 'gemini-2.0-flash',
		'gemini_prompt'      => rv_default_prompt(),
	);
}

/**
 * Prompt de sistema por defecto.
 *
 * Pide una estructura fija y acotada: sin un techo explícito el modelo se
 * extiende tanto que agota el límite de tokens y la respuesta llega cortada.
 */
function rv_default_prompt() {
	return "Eres un instructor certificado de rescate vertical y emergencias médicas. Un estudiante te describirá un caso. Responde SIEMPRE en español con retroalimentación educativa y accionable, usando exactamente esta estructura:\n\n"
		. "## 1. Riesgos técnicos\nMáximo 4 viñetas (anclajes, factor de caída, ángulos, rozamiento).\n\n"
		. "## 2. Consideraciones médicas\nMáximo 3 viñetas, citando PHTLS, TECC o TCCC cuando aplique.\n\n"
		. "## 3. Qué confirmarías antes de actuar\nMáximo 3 puntos numerados.\n\n"
		. "Reglas estrictas: no repitas el enunciado del caso; no uses viñetas anidadas ni sub-listas; máximo 30 palabras por viñeta; no superes las 400 palabras en total. Cierra con una sola línea recordando que es retroalimentación educativa y no sustituye la supervisión de un instructor certificado en campo.";
}

/**
 * Prompt de la versión 1.1 y anteriores. Se conserva solo para reconocerlo
 * durante la migración y no pisar un texto que el usuario haya personalizado.
 */
function rv_legacy_prompt() {
	return "Eres un instructor certificado de rescate vertical y emergencias médicas. Un estudiante te va a describir un caso o escenario de rescate. Da una retroalimentación breve y educativa en español, organizada en: 1) riesgos técnicos (anclajes, factor de caída, ángulos), 2) consideraciones médicas relevantes (PHTLS/TECC/TCCC), 3) qué harías distinto o qué confirmarías antes de actuar. Sé concreto y no repitas el caso. Cierra siempre recordando que es una retroalimentación educativa y no sustituye la supervisión de un instructor certificado en campo real.";
}

/**
 * Actualiza ajustes guardados por versiones anteriores del plugin.
 *
 * Solo reemplaza el prompt si sigue siendo exactamente el que traía el
 * plugin: si el usuario lo editó, se respeta su texto.
 */
function rv_maybe_upgrade_settings() {
	$stored = get_option( RV_TOOLS_OPTION, array() );
	if ( ! is_array( $stored ) ) {
		return;
	}

	$prompt = isset( $stored['gemini_prompt'] ) ? trim( $stored['gemini_prompt'] ) : '';

	if ( '' === $prompt || trim( rv_legacy_prompt() ) === $prompt ) {
		$stored['gemini_prompt'] = rv_default_prompt();
		update_option( RV_TOOLS_OPTION, $stored );
	}
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
	} else {
		rv_maybe_upgrade_settings();
	}

	update_option( 'rv_tools_version', RV_TOOLS_VERSION );
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
	/*
	 * Al sustituir el zip de un plugin ya activo WordPress no siempre vuelve a
	 * lanzar el hook de activación, así que la migración se comprueba aquí
	 * comparando la versión guardada. Solo escribe cuando hay cambio de versión.
	 */
	if ( get_option( 'rv_tools_version' ) !== RV_TOOLS_VERSION ) {
		rv_maybe_upgrade_settings();
		update_option( 'rv_tools_version', RV_TOOLS_VERSION );
		add_action( 'init', array( 'RV_Cases', 'seed' ), 20 );
	}

	RV_CPT::register();
	RV_Settings::register();
	RV_Simulator::register();
	RV_Validation_Form::register();
	RV_Export::register();
	RV_Case_Review::register();
	RV_Anchor::register();
	RV_Cases::register();
}
add_action( 'plugins_loaded', 'rv_tools_init' );
