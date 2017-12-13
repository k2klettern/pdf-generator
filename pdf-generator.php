<?php
/*
Plugin Name: Pdf Generator
Description: PDF Generator for WP, library loaded for use
Version: 1.0
Author: Eric Zeidan
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 *  We include the main class
 */
require_once( plugin_dir_path( __FILE__ ) . 'inc/class-pdf-generator.php');

/*
*   =================================================================================================
*   CLASSES
*   Include all the Classes you need in the 'inc/' folder and add class-yourname.php
*   automatically.
*   =================================================================================================
*/
foreach (glob(__DIR__ . "/inc/classes/*.php") as $filename)
    require_once($filename);

/**
 * We create the instance
 */

add_action( 'plugins_loaded', array( 'PdfPlugin', 'get_instance' ) );

/**
 * Functions for redirect on activation and include action on activation of plugin
 */
register_activation_hook(__FILE__, array("PdfPlugin", "pdf_activate"));
