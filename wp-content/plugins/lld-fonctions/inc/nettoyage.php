<?php
/*
	Liste des fonctions présentes : 
	=> fRetirerMetaboxesPage
	=> fNettoyageHeader
*/

# -------------------------------------------------------------------------------------------------------------------------------
if ( !function_exists('fRetirerMetaboxesPage') )
{
	function fRetirerMetaboxesPage() {
		remove_meta_box( 'postcustom' , 'page' , 'normal' ); 
		// remove_meta_box( 'commentstatusdiv' , 'page' , 'normal' );
		remove_meta_box( 'slugdiv' , 'page' , 'normal' ); 
	    // remove_meta_box( 'authordiv' , 'page' , 'normal' ); 
		remove_meta_box( 'revisionsdiv' , 'page' , 'normal' );
// 		remove_meta_box( 'postimagediv' , 'page' , 'side' ); 
	}
	add_action( 'do_meta_boxes' , 'fRetirerMetaboxesPage' );
}

# ==============================================================================================================================
# Nettoyage du header html
if ( !function_exists('fNettoyageHeader') )
{
	function fNettoyageHeader()
	{
		remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
		remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
		remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
		remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
	}
	add_action( 'init' , 'fNettoyageHeader' );
}

# ==============================================================================================================================
# XML-RPC Interface

add_filter( 'xmlrpc_enabled', '__return_false' );
/* Den HTTP-Header vom XMLRPC-Eintrag bereinigen */

// -----------------------------------------------------------------------------------------------------------------------------
// Desactivation complete du XMLRPC
function AH_remove_x_pingback( $headers )
{
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'AH_remove_x_pingback' );

// -----------------------------------------------------------------------------------------------------------------------------
// Desactivation complete du pingback
function remove_xmlrpc_pingback_ping( $methods ) 
{
   unset( $methods['pingback.ping'] );
   return $methods;
} ;
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );

# ---------------------------------------------------------------------------------------------------
# Autoptimze : Suppression de l'alerte 'Vider le cache autoptimze' et suppression du cache
function ao_cachechecker_cronjob_6tem9()
{
	$autoptimize_cachesize_notice = get_option( 'autoptimize_cachesize_notice' );
	if ( $autoptimize_cachesize_notice )
	{
		autoptimizeCache::clearall(); // Suppression du cache
		update_option( 'autoptimize_cachesize_notice', false ); // Ne pas afficher la notice
		update_option( 'autoptimize_cache_clean', 0 );
	}
}
add_action( 'ao_cachechecker', 'ao_cachechecker_cronjob_6tem9', 11 );
?>