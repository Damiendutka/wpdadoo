<?php
/*
Plugin Name: LLD Fonctions
Plugin URI: http://www.lalucioledigitale.com
Description: Bibliothèque de fonctions utilitaires.
Version: 1.8.0
Author: La Luciole Digitale
Author URI: http://www.lalucioledigitale.com
*/

# ===============================================================================================================================
# INCLUSIONS 
include( dirname(__FILE__) . '/inc/customUserRoles/functions.php' );
require( dirname(__FILE__) . '/inc/general.php' );
require( dirname(__FILE__) . '/inc/walker.php' );
require( dirname(__FILE__) . '/inc/utilisateur.php' );
require( dirname(__FILE__) . '/inc/nettoyage.php' );
require( dirname(__FILE__) . '/inc/debug.php' );

require( dirname(__FILE__) . '/inc/class/dateTimeFrench.class.php' );
// require( dirname(__FILE__) . '/inc/class/localisation.class.php' ); // Fonctionnalité lié aux départements
require( dirname(__FILE__) . '/inc/Mobile-Detect-master/Mobile_Detect.php' );

# ===============================================================================================================================
# INITIALISATION DU PLUGIN 
function fInitialisation_LLD()
{

    wp_register_style( 'style-lld', plugins_url( 'inc/css/style.css', __FILE__ ), '', '1.2' );
    wp_enqueue_style( 'style-lld' ); 

    // Activation de fonctionnalités WordPress 
    add_theme_support( 'post-thumbnails' ); 

    // Languages
    load_plugin_textdomain( '6tem9Fonction', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    load_plugin_textdomain( '6tem9Countries', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}
add_action( 'plugins_loaded', 'fInitialisation_LLD' );

# ===============================================================================================================================
# STYLES ET SCRIPTS

# -------------------------------------------------------------------------------------------------------------------------------
# Ajout de bilbiotheques Javascript et feuilles de style CSS pour l'adminisatration
function fAdminScriptStyle_LLD() 
{
    wp_register_script( 'fonction-js-lld', plugins_url( 'js/function.js', __FILE__ ), array(), true );
    wp_enqueue_script( 'fonction-js-lld' );
}
add_action( 'admin_enqueue_scripts', 'fAdminScriptStyle_LLD' );
add_action( 'wp_enqueue_scripts', 'fAdminScriptStyle_LLD' );

# -------------------------------------------------------------------------------------------------------------------------------
# Filter the default list of post mime types.
function fAddMimeTypesToListFilter( $post_mime_types ) 
{
    $post_mime_types['application/pdf'] = array( __( 'PDF\'s' ), __( 'Manage PDFs' ), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDF\'s <span class="count">(%s)</span>' ) );
    // $post_mime_types['application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'] = array( __( 'Documents' ), __( 'Manage Documents' ), _n_noop( 'Documents <span class="count">(%s)</span>', 'Documents <span class="count">(%s)</span>' ) );

    return $post_mime_types;
}
add_filter( 'post_mime_types', 'fAddMimeTypesToListFilter' );

?>