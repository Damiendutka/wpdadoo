<?php
# ========================================================================================
# Recherche dans les champs personnalisés et la taxonomy

# ----------------------------------------------------------------------------------------
if ( !function_exists('fSelectDistinct') )
{
	function fSelectDistinct( $query )
	{
		global $wp_query, $wpdb;
		if (!empty( $wp_query->query_vars['s']) )
		{
			if (strstr( $where, 'DISTINCT' ))
			{}
			else
			{
				$query = str_replace( 'SELECT', 'SELECT DISTINCT', $query );
			}
		}
		return $query;
	}
	add_filter( 'posts_request', 'fSelectDistinct' );
}

# ----------------------------------------------------------------------------------------

if ( !function_exists('fSearchMetadataJoin') )
{
	function fSearchMetadataJoin( $join)
	{
		global $wp_query, $wpdb;

		if (!empty( $wp_query->query_vars['s']))
		{
			$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
		}
		return $join;
	}
	add_filter( 'posts_join', 'fSearchMetadataJoin' );
}

# ----------------------------------------------------------------------------------------

if ( !function_exists('fSearchMetadataWhere') )
{
	function fSearchMetadataWhere( $search )
	{
		global $wp_query, $wpdb;
		$s = $wp_query->query_vars['s'];
		if ( !empty( $s ) )
		{
			$sentence_term = $wpdb->escape( $s );
			$search = str_replace("($wpdb->posts.post_title LIKE '%{$sentence_term}%')", "($wpdb->posts.post_title LIKE '%$sentence_term%') OR (meta_value LIKE '%{$sentence_term}%') OR (tter.name LIKE '%{$sentence_term}%')", $search);
		}
		return $search;
	}
	add_filter( 'posts_where', 'fSearchMetadataWhere' );
}

# ----------------------------------------------------------------------------------------
if ( !function_exists('fSearchTermJoin') )
{
	function fSearchTermJoin( $join ) 
	{
		global $wp_query, $wpdb;
	// echo 'fSearchTermJoin : ' . $wp_query->query_vars['s'];
		if (!empty( $wp_query->query_vars['s'])) 
		{
			$join .= " LEFT JOIN $wpdb->term_relationships AS trel ON ( $wpdb->posts.ID = trel.object_id)";
			$join .= " LEFT JOIN $wpdb->term_taxonomy AS ttax ON ( (ttax.taxonomy = 'categorie-domaineactivite-fp' OR ttax.taxonomy = 'categorie-theme-fp'  OR ttax.taxonomy = 'tags-fichepedagogique' ) AND trel.term_taxonomy_id = ttax.term_taxonomy_id)";
			$join .= " LEFT JOIN $wpdb->terms AS tter ON (ttax.term_id = tter.term_id) ";
		}
		return $join;
	}
	// add_filter( 'posts_join', 'fSearchTermJoin' );
}
?>