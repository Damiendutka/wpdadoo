<?php

# -----------------------------------------------------------------------------------------------------
# Type de contenu
function fInitFilterCapabilitiesSlugCpt( $slug )
{
    add_filter( 'filterCapabilitiesSlugCpt', function( $tabCapabilitiesSlugCpt ) use ( $slug ) {
        return fAddFilterCapabilitiesSlugCpt( $tabCapabilitiesSlugCpt, $slug );
    } );    
}

# -----------------------------------------------------------------------------------------------------
function fAddFilterCapabilitiesSlugCpt( $tabCapabilitiesSlugCpt, $slug )
{
    $tabCapabilitiesSlugCpt[] = $slug;
    return $tabCapabilitiesSlugCpt;
}

# -----------------------------------------------------------------------------------------------------
# Construct an array with all the options

function fGetTabAllCapabilities()
{
	# -----
	# Array of all customizable capabilities
	$tabAllCapabilities = array();

    # -----
	# Array of custom post type slug
	$tabSlugCpt = apply_filters( 'filterCapabilitiesSlugCpt', $tabSlugCpt );
    $tabSlugCpt[] = 'pages';

    # -----
    # Fetch all custom post types to build the array with the cpt capabilities
    foreach( $tabSlugCpt as $key => $slugCPT )
    {

    	if ( current_user_can( 'edit_' . $slugCPT ) )
    	{

	    	if ( $slugCPT == 'pages' )
	    		$pluralLabel = get_post_type_object('page')->label;
	    	else
	    		$pluralLabel = get_post_type_object($slugCPT)->labels->name;

	    	$tabAllCapabilities[$slugCPT] = array( 
	    		'title' => $pluralLabel,
	    		'condition' => 'edit_' . $slugCPT,
	    	);

	    	# Edit posts
			$tabAllCapabilities[$slugCPT]['cap']['edit_' . $slugCPT] = __( "Write or edit own posts", '6tem9Fonction' );
			
			# See others posts
			// if ( current_user_can( 'show_others_' . $slugCPT ) )
			// 	$tabAllCapabilities[$slugCPT]['cap']['show_others_' . $slugCPT] = sprintf( __( "Voir les %s des autres utilisateurs dans la liste", '6tem9Fonction' ), $slugCPT );
			
			# Edit others posts
			if ( current_user_can( 'edit_others_' . $slugCPT ) )
				$tabAllCapabilities[$slugCPT]['cap']['edit_others_' . $slugCPT] = sprintf( __( "Edit others users posts %s", '6tem9Fonction' ), '<p class="description">' . __('If the user can access posts of others users but don\'t have the rights of publication below, then he can\'t edit the published posts of the others users.', '6tem9Fonction' ) . '</p>');

			# Publish posts
			if ( current_user_can( 'publish_' . $slugCPT ) )
				$tabAllCapabilities[$slugCPT]['cap']['publish_' . $slugCPT] = sprintf( __( "Publish posts %s", '6tem9Fonction' ), '<p class="description">' . __('Give you access to publish posts, edit published posts and remove published posts.', '6tem9Fonction' ) . '</p>' );

			# Delete posts
			if ( current_user_can( 'delete_' . $slugCPT ) )
				$tabAllCapabilities[$slugCPT]['cap']['delete_' . $slugCPT] = __( "Remove own posts", '6tem9Fonction' );

			# Delete others posts
			if ( current_user_can( 'delete_others_' . $slugCPT ) )
				$tabAllCapabilities[$slugCPT]['cap']['delete_others_' . $slugCPT] = __( "Remove others users posts ", '6tem9Fonction' );

			# Manage options
			if ( current_user_can( 'manage_options_' . $slugCPT ) && $slugCPT != 'pages' && $slugCPT != 'privatepage' )
				$tabAllCapabilities[$slugCPT]['cap']['manage_options_' . $slugCPT] = __( "Access options page", '6tem9Fonction' );

			# Manage / delete categories
	    	if ( taxonomy_exists('category-' . $slugCPT) )
	    	{
	    		if ( current_user_can( 'manage_categories_' . $slugCPT ) )
	    			$tabAllCapabilities[$slugCPT]['cap']['manage_categories_' . $slugCPT] = __( "Add or edit categories", '6tem9Fonction' );
	    		
	    		if ( current_user_can( 'delete_categories_' . $slugCPT ) )
	    			$tabAllCapabilities[$slugCPT]['cap']['delete_categories_' . $slugCPT] = __( "Remove categories", '6tem9Fonction' );
			}
		}
    }

    # -----
    # Shop
    if ( fPluginIsActivate('woocommerce/woocommerce.php') )
    {

    	# -----
    	# Shop - General
    	if ( current_user_can( 'manage_woocommerce' ) )
    	{
	    	$tabAllCapabilities['shopGeneral']['title'] = __('Shop', '6tem9Fonction');

	    	if ( current_user_can( 'manage_woocommerce' ) )
		    	$tabAllCapabilities['shopGeneral']['cap']['manage_woocommerce'] = sprintf( __( "Manage the shop settings %s", '6tem9Fonction' ), '<p class="description">' . __('Allow the user to manage all the shop settings, like global settings, billings, coupons codes, etc.. ', '6tem9Fonction') . '</p>' );

	   		if ( fPluginIsActivate('woocommerce-bookings/woocommerce-bookings.php') && current_user_can( 'manage_bookings' ) )
		    	$tabAllCapabilities['shopGeneral']['cap']['manage_bookings'] = __('Manage bookings', '6tem9Fonction');
	    	
	    	if ( current_user_can( 'manage_woocommerce_display_options' ) )
		    	$tabAllCapabilities['shopGeneral']['cap']['manage_woocommerce_display_options'] = __('Manage display settings', '6tem9Fonction');
		}

    	# -----
    	# Shop - Products
		if ( current_user_can( 'edit_products' ) )
		{
	    	$tabAllCapabilities['shopProducts']['title'] = __('Shop - Products', '6tem9Fonction');
	    	$tabAllCapabilities['shopProducts']['condition'] = 'edit_products';

	   		if ( current_user_can( 'edit_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['edit_products'] = __('Write or edit own products', '6tem9Fonction');

	   		if ( current_user_can( 'edit_others_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['edit_others_products'] = __('Edit others users products', '6tem9Fonction');

		    if ( current_user_can( 'publish_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['publish_products'] = __('Publish products', '6tem9Fonction');

		    if ( current_user_can( 'delete_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['delete_products'] = __('Remove own products', '6tem9Fonction');

		    if ( current_user_can( 'delete_others_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['delete_others_products'] = __('Remove others users products', '6tem9Fonction');

		    if ( current_user_can( 'manage_categories_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['manage_categories_products'] = __('Add or edit categories', '6tem9Fonction');
	    	
	    	if ( current_user_can( 'delete_categories_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['delete_categories_products'] = __('Remove categories', '6tem9Fonction');

		    if ( current_user_can( 'manage_keywords_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['manage_keywords_products'] = __('Add or edit keywords', '6tem9Fonction');
	    	
	    	if ( current_user_can( 'delete_keywords_products' ) )
		    	$tabAllCapabilities['shopProducts']['cap']['delete_keywords_products'] = __('Remove keywords', '6tem9Fonction');
		}

	    # -----
    	# Shop - Orders
		if ( current_user_can( 'edit_shop_orders' ) )
		{
	    	$tabAllCapabilities['shopOrders']['title'] = __('Shop - Orders', '6tem9Fonction');
	    	$tabAllCapabilities['shopOrders']['condition'] = 'edit_shop_orders';

	   		if ( current_user_can( 'edit_shop_orders' ) )
		    	$tabAllCapabilities['shopOrders']['cap']['edit_shop_orders'] = __('Write or edit own orders', '6tem9Fonction');

	   		if ( current_user_can( 'edit_others_shop_orders' ) )
		    	$tabAllCapabilities['shopOrders']['cap']['edit_others_shop_orders'] = __('Edit others users orders', '6tem9Fonction');

		    if ( current_user_can( 'publish_shop_orders' ) )
		    	$tabAllCapabilities['shopOrders']['cap']['publish_shop_orders'] = __('Publish orders', '6tem9Fonction');

		    if ( current_user_can( 'delete_shop_orders' ) )
		    	$tabAllCapabilities['shopOrders']['cap']['delete_shop_orders'] = __('Remove own orders', '6tem9Fonction');

		    if ( current_user_can( 'delete_others_shop_orders' ) )
		    	$tabAllCapabilities['shopOrders']['cap']['delete_others_shop_orders'] = __('Remove others users orders', '6tem9Fonction');

		    # -----
	    	# Shop - Coupons
	    	$tabAllCapabilities['shopCoupons']['title'] = __('Shop - Coupons', '6tem9Fonction');
	    	$tabAllCapabilities['shopCoupons']['condition'] = 'edit_shop_coupons';

	   		if ( current_user_can( 'edit_shop_coupons' ) )
		    	$tabAllCapabilities['shopCoupons']['cap']['edit_shop_coupons'] = __('Write or edit own coupons', '6tem9Fonction');

	   		if ( current_user_can( 'edit_others_shop_coupons' ) )
		    	$tabAllCapabilities['shopCoupons']['cap']['edit_others_shop_coupons'] = __('Edit others users coupons', '6tem9Fonction');

		    if ( current_user_can( 'publish_shop_coupons' ) )
		    	$tabAllCapabilities['shopCoupons']['cap']['publish_shop_coupons'] = __('Publish coupons', '6tem9Fonction');

		    if ( current_user_can( 'delete_shop_coupons' ) )
		    	$tabAllCapabilities['shopCoupons']['cap']['delete_shop_coupons'] = __('Remove own coupons', '6tem9Fonction');

		    if ( current_user_can( 'delete_others_shop_coupons' ) )
		    	$tabAllCapabilities['shopCoupons']['cap']['delete_others_shop_coupons'] = __('Remove others users coupons', '6tem9Fonction');
    	}
    }

    # -----
    # Carrousel
    if ( fPluginIsActivate('6tem9Carousel/6tem9Carousel.php') && (current_user_can( '6tem9_carousel' ) || current_user_can( '6tem9_carousel_options' ) ) )
    {
	    $tabAllCapabilities['slide']['title'] = __('Carrousel', '6tem9Fonction');

	    if ( current_user_can( '6tem9_carousel' ) )
	    	$tabAllCapabilities['slide']['cap']['6tem9_carousel'] = __('Manage the carousel', '6tem9Fonction');

	    if ( current_user_can( '6tem9_carousel_options' ) )
	    	$tabAllCapabilities['slide']['cap']['6tem9_carousel_options'] = __('Edit the carousel general options', '6tem9Fonction');
	}

    # -----
    # Site management
	if ( current_user_can( '6tem9_edit_theme_options_manage_customization' ) || current_user_can( '6tem9_edit_theme_options_manage_widgets' ) || current_user_can( '6tem9_edit_theme_options_manage_menu' ) || current_user_can( 'moderate_comments' ) || current_user_can( 'upload_files' ) || (current_user_can( '6tem9_manage_custom_roles' ) && strpos(array_shift(wp_get_current_user()->roles), 'custom') === false) || current_user_can( '6tem9_advanced_options' ) || (current_user_can( '6tem9_create_post_type' ) && get_option('enablePostTypeCreation', false)))
	{
	    $tabAllCapabilities['general']['title'] = __('Site management', '6tem9Fonction');

	    if ( current_user_can( '6tem9_edit_theme_options_manage_customization' ) )
	    	$tabAllCapabilities['general']['cap']['6tem9_edit_theme_options_manage_customization'] = __('Manage site customization', '6tem9Fonction');

	    if ( current_user_can( '6tem9_edit_theme_options_manage_widgets' ) )
	    	$tabAllCapabilities['general']['cap']['6tem9_edit_theme_options_manage_widgets'] = __('Manage widgets', '6tem9Fonction');

	    if ( current_user_can( '6tem9_edit_theme_options_manage_menu' ) )
	    	$tabAllCapabilities['general']['cap']['6tem9_edit_theme_options_manage_menu'] = __('Manage menu', '6tem9Fonction');

	    if ( current_user_can( 'moderate_comments' ) )
	    	$tabAllCapabilities['general']['cap']['moderate_comments'] = __('Manage comments', '6tem9Fonction');

	 	if ( current_user_can( 'upload_files' ) )
	    	$tabAllCapabilities['general']['cap']['upload_files'] = __('Manage medias', '6tem9Fonction');

	    if ( current_user_can( 'manage_box_seo' ) )
	    	$tabAllCapabilities['general']['cap']['manage_box_seo'] = __('Manage SEO metaboxes', '6tem9Fonction');

	    if ( current_user_can( 'manage_google_analytics' ) )
	    	$tabAllCapabilities['general']['cap']['manage_google_analytics'] = __('Manage Google Analytics', '6tem9Fonction');

	 	if ( get_option('enableCustomUsersRoles', false) && current_user_can( '6tem9_manage_custom_roles' ) && strpos(array_shift(wp_get_current_user()->roles), 'custom') === false )
	    	$tabAllCapabilities['general']['cap']['6tem9_manage_custom_roles'] = __('Manage custom roles', '6tem9Fonction');

	 	if ( current_user_can( '6tem9_advanced_options' ) )
	    	$tabAllCapabilities['general']['cap']['6tem9_advanced_options'] = __('Enable / Disable advanced options', '6tem9Fonction');

	 	if ( current_user_can( '6tem9_create_post_type' ) && get_option('enablePostTypeCreation', false) )
	    	$tabAllCapabilities['general']['cap']['6tem9_create_post_type'] = __('Create custom post types', '6tem9Fonction');

	    if ( fPluginIsActivate('6tem9Membership/6tem9Membership.php') && current_user_can( '6tem9_private_area_access' ) )
	    	$tabAllCapabilities['general']['cap']['6tem9_private_area_access'] = __('Access private area', '6tem9Fonction');

	    if ( current_user_can( 'manage_keywords' ) )
	    	$tabAllCapabilities['general']['cap']['manage_keywords'] = sprintf( __( "Add or edit custom post types keywords %s", '6tem9Fonction' ), '<p class="description">' . __('Keywords are shared between all the custom post types.', '6tem9Fonction') . '</p>' );

	    if ( current_user_can( 'delete_keywords' ) )
	    	$tabAllCapabilities['general']['cap']['delete_keywords'] = sprintf( __( "Remove custom post types keywords %s", '6tem9Fonction' ), '<p class="description">' . __('Keywords are shared between all the custom post types.', '6tem9Fonction') . '</p>' );
	}

    # -----
    # Newsletter
    if ( get_option('enableNewsletter', false) && (current_user_can( 'wysija_newsletters' ) || current_user_can( 'wysija_subscribers' ) || current_user_can( 'wysija_stats_dashboard' )) )
    {
	    $tabAllCapabilities['newsletter']['title'] = __('Newsletter', '6tem9Fonction');

	    if ( current_user_can( 'wysija_newsletters' ) )
	    	$tabAllCapabilities['newsletter']['cap']['wysija_newsletters'] = __('Manage newsletters', '6tem9Fonction');

	    if ( current_user_can( 'wysija_subscribers' ) )
	    	$tabAllCapabilities['newsletter']['cap']['wysija_subscribers'] = __('Manage subscribers', '6tem9Fonction');

	    if ( current_user_can( 'wysija_stats_dashboard' ) )
	    	$tabAllCapabilities['newsletter']['cap']['wysija_stats_dashboard'] = __('View statistics', '6tem9Fonction');
	}

    # -----
    # Forms
	if ( fPluginIsActivate('gravityforms/gravityforms.php') && (current_user_can( 'gravityforms_create_form' ) || current_user_can( 'gravityforms_edit_forms' ) || current_user_can( 'gravityforms_delete_forms' ) || current_user_can( 'gravityforms_view_entries' ) || current_user_can( 'gravityforms_edit_entries' ) || current_user_can( 'gravityforms_delete_entries' ) || current_user_can( 'gravityforms_export_entries' ) || current_user_can( 'gravityforms_edit_settings' )) )
	{
	    $tabAllCapabilities['forms']['title'] = __('Forms', '6tem9Fonction');
	    $tabAllCapabilities['forms']['condition'] = 'gravityforms_create_form';

	    if ( current_user_can( 'gravityforms_create_form' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_create_form'] = __('Create forms', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_edit_forms' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_edit_forms'] = __('Edit forms', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_delete_forms' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_delete_forms'] = __('Delete forms', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_view_entries' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_view_entries'] = __('View entries', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_edit_entries' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_edit_entries'] = __('Edit entries', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_delete_entries' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_delete_entries'] = __('Delete entries', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_export_entries' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_export_entries'] = __('Export entries', '6tem9Fonction');

	    if ( current_user_can( 'gravityforms_edit_settings' ) )
	    	$tabAllCapabilities['forms']['cap']['gravityforms_edit_settings'] = __('Edit general settings', '6tem9Fonction');    
	}

    # -----
    # Settings pages
	if ( current_user_can( '6tem9_desc_keywords_seo' ) || current_user_can( '6tem9_sharing_bar' ) || current_user_can( '6tem9_website_manager' ) || current_user_can( '6tem9_manage_footer_links' ) || current_user_can( '6tem9_mentions_cookies_option' ) )
	{

	    $tabAllCapabilities['settings']['title'] = __('Settings pages', '6tem9Fonction');

	    if ( current_user_can( '6tem9_desc_keywords_seo' ) )
	    	$tabAllCapabilities['settings']['cap']['6tem9_desc_keywords_seo'] = __('Referencing', '6tem9Fonction');

	    if ( current_user_can( '6tem9_sharing_bar' ) )
	    	$tabAllCapabilities['settings']['cap']['6tem9_sharing_bar'] = __('Sharing bar', '6tem9Fonction');

	    if ( current_user_can( '6tem9_website_manager' ) )
	    	$tabAllCapabilities['settings']['cap']['6tem9_website_manager'] = __('Website manager', '6tem9Fonction');

	    if ( current_user_can( '6tem9_manage_footer_links' ) )
	    	$tabAllCapabilities['settings']['cap']['6tem9_manage_footer_links'] = __('Footer links', '6tem9Fonction');

	     if ( current_user_can( '6tem9_mentions_cookies_option' ) )
	    	$tabAllCapabilities['settings']['cap']['6tem9_mentions_cookies_option'] = __('Cookies message', '6tem9Fonction');
	}

    # -----
    # Users
	if ( current_user_can( 'create_users' ) || current_user_can( 'manage_network_users' ) || current_user_can( 'edit_users' ) || current_user_can( 'delete_users' ) || current_user_can( 'promote_users' ) )
	{
	    $tabAllCapabilities['users']['title'] = __('Users', '6tem9Fonction');
	    $tabAllCapabilities['users']['condition'] = 'manage_network_users';
	    
	    if ( current_user_can( 'manage_network_users' ) )
	    	$tabAllCapabilities['users']['cap']['manage_network_users'] = __('View and edit users', '6tem9Fonction');	

	    if ( current_user_can( 'edit_users' ) )
	    	$tabAllCapabilities['users']['cap']['edit_users'] = __('View and edit users', '6tem9Fonction');

	    if ( current_user_can( 'create_users' ) )
	    	$tabAllCapabilities['users']['cap']['create_users'] = __('Add new users', '6tem9Fonction');

	    if ( current_user_can( 'delete_users' ) )
	    	$tabAllCapabilities['users']['cap']['delete_users'] = __('Delete users', '6tem9Fonction');

	    if ( current_user_can( 'promote_users' ) )
	    	$tabAllCapabilities['users']['cap']['promote_users'] = __('Change users role', '6tem9Fonction');
	}

    return $tabAllCapabilities;
}

# -----------------------------------------------------------------------------------------------------
# Update all the capabilities of the roles

function fUpdateRolesCapabilities( $tabUserCapabilitiesToSave )
{
	# Affect / remove all the capabilities from the corresponding roles
	foreach ( $tabUserCapabilitiesToSave as $role => $tabUserCap )
	{	
		$roleObject = get_role( $role );

		# if the role exist, we affect the capabilities
		if ( $roleObject != null && $roleObject != '' && is_object($roleObject) )
		{

    		# Only for non locked roles
    		if ( !in_array( $role, $tabLockedRoles) )
    		{  	

    			# Change role name
    			global $wp_roles;
    			$wp_roles->roles[$role]['name'] = $tabUserCap['name'];
				$wp_roles->role_names[$role] = $tabUserCap['name'];   

        		foreach( $tabUserCap['cap'] as $capability => $isActive )
        		{
        			if ( $isActive )
        				$roleObject->add_cap($capability);
        			else
        				$roleObject->remove_cap($capability);
        		}
        		# -----
        		# Special cases

        		# Published pages
        		if ( $tabUserCap['cap']['publish_pages'] )
        			$roleObject->add_cap('edit_published_pages');
        		else
					$roleObject->remove_cap('edit_published_pages');      	

        		# Customization
				if ( $tabUserCap['cap']['6tem9_edit_theme_options_manage_customization'] || $tabUserCap['cap']['6tem9_edit_theme_options_manage_widgets'] || $tabUserCap['cap']['6tem9_edit_theme_options_manage_menu'] )
					$roleObject->add_cap('edit_theme_options');
				else if ( !$tabUserCap['cap']['6tem9_edit_theme_options_manage_customization'] && !$tabUserCap['cap']['6tem9_edit_theme_options_manage_widgets'] && !$tabUserCap['cap']['6tem9_edit_theme_options_manage_menu'] )
					$roleObject->remove_cap('edit_theme_options');      	

				# Users
				if ( $tabUserCap['cap']['manage_network_users'] || $tabUserCap['cap']['edit_users'] )
				{
					$roleObject->add_cap('edit_users');
					$roleObject->add_cap('list_users');
				}
				else
				{
					$roleObject->remove_cap('edit_users');
					$roleObject->remove_cap('list_users');
				}

				if ( $tabUserCap['cap']['delete_users'] )
					$roleObject->add_cap('remove_users');
				else
					$roleObject->remove_cap('remove_users');
        	}
        }
        # Else, we create it
        else
        {
        	$tabBasicCap = array( '6tem9_can_access_wp-admin' => true, 'read' => true, 'delete_posts' => true, 'delete_private_posts' => true, 'delete_published_posts' => true, 'edit_others_posts' => true, 'edit_posts' => true, 'edit_published_posts' => true, 'publish_posts' => true, 'read_private_posts' => true);
        	$tabNewUserCap = array_merge($tabBasicCap, $tabUserCap['cap']);

        	add_role( $role,  $tabUserCap['name'], $tabNewUserCap );
        	update_option( 'countCustomUserRoles', get_option( 'countCustomUserRoles', 0 ) + 1 );	// Update the custom user role count
        }
	}
}
