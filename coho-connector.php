<?php
/**
 * Plugin Name:       COHO Connector
 * Description:       Connect WordPress to your COHO account to sync property information and check availability in real time.
 * Version:           4.0.0
 * Author:            Atomic Smash
 * Author URI:        https://www.atomicsmash.co.uk/
 * Text Domain:       coho-connector
 */

namespace AtomicSmash\CohoConnector;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin name
define( 'ASCOHO_NAME', 'COHO Connector' );

// Plugin version
define( 'COHO_CONNECTOR_VERSION', '4.0.0' );
define( 'ASCOHO_VERSION', COHO_CONNECTOR_VERSION );

// Plugin Root File
define( 'ASCOHO_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'ASCOHO_PLUGIN_BASE', plugin_basename( ASCOHO_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'ASCOHO_PLUGIN_DIR', plugin_dir_path( ASCOHO_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'ASCOHO_PLUGIN_URL', plugin_dir_url( ASCOHO_PLUGIN_FILE ) );

/**
 * Load the COHO Connector class
 */
require_once ASCOHO_PLUGIN_DIR . 'lib/class-coho-query.php';
