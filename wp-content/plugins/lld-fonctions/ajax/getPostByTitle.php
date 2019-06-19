<?php
	require_once('../../../../wp-load.php');

	# Inititalisation 
	global $wpdb, $post;
	$tabReturn = array();

	# ----------------------------------------------------------------------------------
	# If we get values in POST

	if($_POST)
	{     

		# Search all posts whrre title is liek the one researched
		$titleToSearch = $_POST['value'];
		$searchQuery = 'SELECT ID FROM wp_' . get_current_blog_id() . '_posts WHERE post_title LIKE %s';

		$like = '%'.$titleToSearch.'%';

		$results = $wpdb->get_results($wpdb->prepare($searchQuery, $like), ARRAY_N);

		foreach( $results as $array => $key )
		{
			if ( !get_post_meta( $key[0], 'hiddenPage', true ) )
		     	$quoteIds[] = $key[0];
		}

		$tabAllPostTypeSlug = get_post_types(array('public' => true, 'show_ui' => true));
		unset($tabAllPostTypeSlug['post']);
		unset($tabAllPostTypeSlug['slide']);
		unset($tabAllPostTypeSlug['attachment']);

		# Get all posts matching the previous request

		$query = new WP_Query( array( 'post_type'=> $tabAllPostTypeSlug, 'post__in' => $quoteIds ) );

		foreach ( $query->posts as $post )
		{
			$tabReturn[] = array(
								'label' => $post->post_title,
			 					'url' => get_permalink($post->ID)
			 				);
		}

		echo json_encode($tabReturn);
	}
?>