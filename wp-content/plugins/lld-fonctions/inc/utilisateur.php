<?php

/*
Liste des fonctions :
	- fScriptStyleAdminProfile
	- fAjouterCap
	- fSupprimerCap
	- fAjoutCapabitiesCPT
	- fSuppressionCapabitiesCPT
	- fEditorCanManageUsers
	- fSuppressionCapabitiesCPT
	- fEditorCanManageUsers
	- fAllowEditorEditUser
	- fChangeRolesNames
	- fRemovePersonnalOptions
	- fRemoveAddUserOptions 
	- fProfileSubjectStart
	- fAddUserSubjectStart
	- fProfileSubjectEnd
	- fConnexionWordpress
	- fHideRoleFromUserList
	- fUserListNumberByRole
	- fNotifyAdminForPendingPost
	- fRedirectFromDashboard
 */

# -----------------------------------------------------------------------------------------------------------------------------------------
# Ajout
function fScriptStyleAdminProfile()
{
    echo '<style type="text/css">';
	if ( !current_user_can('6tem9_add_existing_user') )
	{
		echo '
		#visibility.misc-pub-visibility, 
		form#adduser,
		#add-existing-user, 
		#add-existing-user ~ p, 
		h3#create-new-user { display: none !important; }';

		global $current_user;
		if ( in_array('billingCustomer', $current_user->roles) )
			echo '#phoneFields, #addressFields, #shippingAddressFields { display: none !important; }';
	}

	echo '.user-rich-editing-wrap, .user-comment-shortcuts-wrap { display: none; }';

	echo '</style>';
}

// add_action( 'admin_head', 'fScriptStyleAdminProfile' );

# -----------------------------------------------------------------------------------------------------------------------------------------
# Ajout d'une capabilitie a un role
# Exemple : fAjouterCap('subscriber','read_private_pages');
if ( !function_exists('fAjouterCap') )
{
	function fAjouterCap( $role, $cap ) 
	{
		$role_obj = get_role($role); 
		if (is_object($role_obj))
			$role_obj->add_cap($cap); 
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
# Ajout de plusieurs capabilities à un rôle
# Exemple : fAjouterCap('subscriber',array('read_private_pages', 'edit_post') );
if ( !function_exists('fAjouterMultiCap') )
{
	function fAjouterMultiCap( $role, $tabCap ) 
	{
		$role_obj = get_role($role); 
		if ( is_object($role_obj) && !empty($tabCap) )
		{
			foreach ( $tabCap as $cap )
				$role_obj->add_cap($cap); 
		}
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
# Ajout d'une capabiliy à plusieurs rôles
# Exemple : fAjouterCapMultiRole('edit_post',array('administrator', 'editor') );
if ( !function_exists('fAjouterCapMultiRole') )
{
	function fAjouterMultiCapRole( $tabRole, $tabCap ) 
	{
		if ( !empty($tabRole) && !empty($tabCap) )
		{
			foreach ( $tabRole as $role )
			{
				$role_obj = get_role($role);
				if ( is_object($role_obj) )
				{
					foreach( $tabCap as $cap )
						$role_obj->add_cap($cap); 
				}
			}
		}
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
# Suppression d'une capabilitie d'un role
# Exemple : fSupprimerCap('editor','manage_categories'); 
if ( !function_exists('fSupprimerCap') )
{
	function fSupprimerCap( $role, $cap ) 
	{
		$role_obj = get_role($role); 
		$role_obj->remove_cap($cap);
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
# Suppression de plusieurs capabilities d'un rôle
# Exemple : fSupprimerMultiCap('subscriber',array('read_private_pages', 'edit_post') );
if ( !function_exists('fSupprimerMultiCap') )
{
	function fSupprimerMultiCap( $role, $tabCap ) 
	{
		$role_obj = get_role($role); 
		if ( is_object($role_obj) && !empty($tabCap) )
		{
			foreach ( $tabCap as $cap )
				$role_obj->remove_cap($cap); 
		}
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
if ( !function_exists('fAjoutCapabitiesCPT') )
{
	function fAjoutCapabitiesCPT( $slugCPT, $role='administrator' )
	{
		# Ajout de capps des custom post type
		fAjouterCap( $role, 'edit_' . $slugCPT . '' );
		fAjouterCap( $role, 'read_' . $slugCPT . '' );
		fAjouterCap( $role, 'delete_' . $slugCPT . '' );
		fAjouterCap( $role, 'edit_' . $slugCPT . 's' );
		fAjouterCap( $role, 'edit_others_' . $slugCPT . 's' );
		fAjouterCap( $role, 'publish_' . $slugCPT . 's' );
		fAjouterCap( $role, 'read_private_' . $slugCPT . 's' );
		fAjouterCap( $role, 'edit_private_' . $slugCPT . 's' );
		fAjouterCap( $role, 'edit_published_' . $slugCPT . 's' );
		fAjouterCap( $role, 'delete_' . $slugCPT . 's' );
		fAjouterCap( $role, 'delete_others_' . $slugCPT . 's' );
		fAjouterCap( $role, 'delete_published_' . $slugCPT . 's' );
		fAjouterCap( $role, 'delete_private_' . $slugCPT . 's' );
		
		fAjouterCap( 'editor', 'create_users');
		fAjouterCap( 'editor', 'unfiltered_html');
		
	}
}

# -----------------------------------------------------------------------------------------------------------------------------------------
if ( !function_exists('fSuppressionCapabitiesCPT') )
{
	function fSuppressionCapabitiesCPT( $slugCPT, $role='administrator' )
	{
		fSupprimerCap( $role, 'edit_' . $slugCPT . '' );
		fSupprimerCap( $role, 'read_' . $slugCPT . '' );
		fSupprimerCap( $role, 'delete_' . $slugCPT . '' );
		fSupprimerCap( $role, 'edit_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'edit_others_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'publish_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'read_private_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'edit_private_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'edit_published_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'delete_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'delete_others_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'delete_published_' . $slugCPT . 's' );
		fSupprimerCap( $role, 'delete_private_' . $slugCPT . 's' );
		
        fSupprimerCap( $role, 'edit_' . $slugCPT );
        fSupprimerCap( $role, 'edit_others_' . $slugCPT );
        fSupprimerCap( $role, 'delete_' . $slugCPT );
        fSupprimerCap( $role, 'delete_others_' . $slugCPT ); 
        fSupprimerCap( $role, 'publish_' . $slugCPT );
        fSupprimerCap( $role, 'manage_options_' . $slugCPT );
        fSupprimerCap( $role, 'manage_categories_' . $slugCPT );
        fSupprimerCap( $role, 'delete_categories_' . $slugCPT );
        fSupprimerCap( $role, 'manage_keywords' . $slugCPT );
        fSupprimerCap( $role, 'manage_keywords' . $slugCPT );
        fSupprimerCap( $role, 'manage_categories_' . $slugCPT );
        fSupprimerCap( $role, 'edit_' . $slugCPT );
	}
}

# ==========================================================================================================================================
# Editors - Capabilities on users

// if ( !function_exists('fEditorCanManageUsers') )
// {
// 	function fEditorCanManageUsers() 
// 	{

// 	    $editor = get_role('editor');
// 	    // $editor->add_cap('promote_users');
// 	    // $editor->add_cap('remove_users');
// 	    // $editor->add_cap('list_users');
// 	    // $editor->add_cap('delete_users');
// 	    // $editor->add_cap('create_users');
// 	    // $editor->add_cap('add_users');
	    	    
// 	    # Disallow editor to edit the administrator role or to change another user role to administrator
// 		// $editorCannotManageAdmin = new EditorCannotManageAdmin();

// 	}

// 	add_action( 'admin_init', 'fEditorCanManageUsers');
// }

// # -----------------------------------------------------------------------------------------------------------------------------------------
// if ( !function_exists('fAllowEditorEditUser') )
// {
// 	function fAllowEditorEditUser( $caps, $cap, $user_id, $args )
// 	{
	 
// 	    foreach( $caps as $key => $capability )
// 	    {
	 
// 	        if( $capability != 'do_not_allow' )
// 	            continue;
	 
// 	        switch( $cap ) {
// 	            case 'edit_user':
// 	            case 'edit_users':
// 	                $caps[$key] = 'edit_users';
// 	                break;
// 	            case 'delete_user':
// 	            case 'delete_users':
// 	                $caps[$key] = 'delete_users';
// 	                break;
// 	            case 'create_users':
// 	                $caps[$key] = $cap;
// 	                break;
// 	        }
// 	    }
	 
// 	    return $caps;
// 	}
// 	add_filter( 'map_meta_cap', 'fAllowEditorEditUser', 10, 4 );
// }

# ==========================================================================================================================================
# If the user can't edit a post (and the corresponding capability is true), we hide the posts

function fHidePostsUserCantEdit( $query )
{
	global $pagenow, $typenow;

	$postType = $typenow == '' ? $_GET['post_type'] : '';

	if( 'edit.php' != $pagenow || !$query->is_admin )
	    return $query;

	// if ( !current_user_can( 'edit_others_partenaire' ) && $_GET['post_type'] == 'partenaire' )
	// {
		global $user_ID;
		$query->set('author', get_current_user_id() );
	// }
	
	return $query;
}

// add_filter('pre_get_posts', 'fHidePostsUserCantEdit', 9);

# ==========================================================================================================================================
# Change roles names

function fChangeRolesNames() 
{
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

    // $wp_roles->roles['editor']['name'] = 'Editeur';
    // $wp_roles->role_names['editor'] = 'Editeur';

    // $wp_roles->roles['reseller']['name'] = __('Reseller', '6tem9Fonction');
    // $wp_roles->role_names['reseller'] = __('Reseller', '6tem9Fonction');

    // $wp_roles->roles['author']['name'] = __('Author', '6tem9Fonction');
    // $wp_roles->role_names['author'] = __('Author', '6tem9Fonction');
}

// add_action('init', 'fChangeRolesNames');

# ==========================================================================================================================================
# Removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.

global $wpseo_admin;
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
remove_action( 'show_user_profile', array( $wpseo_admin, 'user_profile' ) );
remove_action( 'edit_user_profile', array( $wpseo_admin, 'user_profile' ) );

# -----------------------------------------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'fRemovePersonnalOptions' ) ) 
{

  function fRemovePersonnalOptions( $subject ) {
    // $subject = preg_replace( '#<h2>Options personnelles</h2>.+?/table>#s', '', $subject, 1 );
    $subject = preg_replace( '#<h3 id="wordpress-seo">.+?/table>#s', '', $subject, 1 );
    $subject = preg_replace( '#<h3>User Role Editor</h3>.+?/table>#s', '', $subject, 1 );
    $subject = preg_replace( '#<h3>Additional Capabilities</h3>.+?/table>#s', '', $subject, 1 );
    $subject = preg_replace( "#À propos de l.*utilisateur</h3>.+?/table>#s", "", $subject, 1 );
    $subject = preg_replace( "#À propos de vous</h2>.+?/table>#s", "", $subject, 1 );
    $subject = preg_replace( '#<h3>Informations de contact</h3>#s', '', $subject, 1 );

    # The page is even simpler for billingCustomer
    if ( current_user_can( 'billingCustomer' ) )
    {
    	$subject = preg_replace( '#<h3>Options de réseau</h3>#s', '', $subject, 1 );    	
    	$subject = preg_replace( '#<h3>Informations supplémentaires</h3>#s', '', $subject, 1 );
    	$subject = preg_replace( '#<h3>Adresse</h3>#s', '', $subject, 1 );
    	$subject = preg_replace( '#<h2>Gestion de compte</h2>#s', '', $subject, 1 );
    	$subject = preg_replace( '#<h3>Adresse de livraison</h3>#s', '', $subject, 1 );
    }

    return $subject;
  }

  function fRemoveAddUserOptions( $subject ) {
	$subject = preg_replace( '#<th scope="row">Autres rôles</th>.+?/table>#s', '', $subject, 1 );
	$subject = preg_replace( '#<th scope="row">Autres rôles</th>.+?/table>#s', '', $subject, 1 );

    return $subject;
  }

  function fProfileSubjectStart() {
    echo '<style>';
    echo 'tr.user-url-wrap{ display: none; }';

    # Remove toolbar option if user can't access admin
    $idUserToCheck = isset($_GET['user_id']) ? $_GET['user_id'] : 1;

    if ( !user_can( $idUserToCheck, '6tem9_can_access_wp-admin' ) )
    	echo 'tr.show-admin-bar{ display: none; }';

    echo '</style>';
    ob_start( 'fRemovePersonnalOptions' );
  }

  function fAddUserSubjectStart() {
    ob_start( 'fRemoveAddUserOptions' );
  }

  function fProfileSubjectEnd() {
    ob_end_flush();
  }
}
// add_action( 'admin_head-user-edit.php', 'fProfileSubjectStart' );
// add_action( 'admin_footer-user-edit.php', 'fProfileSubjectEnd' );
// add_action( 'admin_head-profile.php', 'fProfileSubjectStart' );
// add_action( 'admin_footer-profile.php', 'fProfileSubjectEnd' );

// add_action( 'admin_head-user-new.php', 'fAddUserSubjectStart' );
// add_action( 'admin_footer-user-new.php', 'fProfileSubjectEnd' );


# ==========================================================================================================================================
# Remove social fields for the user edit page
function fRemoveUserSocialFields($profileFields) 
{
	unset($profileFields['twitter']);
	unset($profileFields['facebook']);
	unset($profileFields['googleplus']);
	return $profileFields;
}

add_filter('user_contactmethods', 'fRemoveUserSocialFields', 11);

# -----------------------------------------------------------------------------------------------------------------------------------------
# Connexion a WordPress
# Gere la connexion avec un e-mail ou un identifiant
# Compatible Multisite
# Si la connexion est etablie, l'utilisateur devient l'utilisateur courant WordPress
if ( !function_exists('fConnexionWordpress') )
{	
	function fConnexionWordpress( $login, $password, $redirect, $rememberMe=false )
	{
		# ------------
		# Get the login parameters

		$tabUserLoginData = array(); // Contains all the user login informations
		$returnTab = array(); // Returned table, with codes for error or success messages
		
		// If the login is an email address
		if ( filter_var($login, FILTER_VALIDATE_EMAIL) )
		{
			// We get the user login linked to the address
			$user = get_user_by( 'email', $login );
			$tabUserLoginData['user_login'] = $user->user_login;
		} 
		else if ( $login != "" ) 
			$tabUserLoginData['user_login'] = $login;
		
		$tabUserLoginData['user_password'] = $password;
		$tabUserLoginData['remember'] = $rememberMe;

		$user = wp_signon( $tabUserLoginData );
		
		if ( is_wp_error($user) )
		{
	   		$returnTab['code'] = "error";
	   		$returnTab['errorMessage'] = __('Incorrect username or password.');
		}
	   	else
		{  
			if ( MULTISITE==1 && !is_user_member_of_blog($user->ID) )
			{
				// If the current site belong to a network, we update the user to this site
				$tabUserInfo = array('user_id' => $user->ID);
				add_existing_user_to_blog($tabUserInfo);
			}	
			
			$returnTab['code'] = "success";
			$returnTab['redirect'] = $redirect;

	 		wp_set_current_user($user->ID);
		}

		return $returnTab;
	}
}

# ---------------------------------------------------------------------------------------------------------------------------------------
# Hide admin from user list
if ( !function_exists( 'fHideRoleFromUserList' )  )
{
	function fHideRoleFromUserList( $user_search ) 
	{
		if ( function_exists( 'wp_get_current_user') )
		{
			$user = wp_get_current_user();

			if ( !current_user_can('administrator') )
			{
				if ( !current_user_can('support6temflex') ) 
			
				{
					global $wpdb;

					# Condition to hide reseller
					if ( !current_user_can('reseller') )
					{
						$resellerCondition = " AND {$wpdb->usermeta}.meta_value NOT LIKE '%reseller%'";

						# Condition to hide billingCustomer
						if ( !current_user_can('billingCustomer') )
							$billingCustomerCondition = " AND {$wpdb->usermeta}.meta_value NOT LIKE '%billingCustomer%'";
						else
							$billingCustomerCondition = '';
					}
					else
						$resellerCondition = '';

					# Condition to hide support6temflex
					if ( !current_user_can('support6temflex') )
						$support6temflexCondition = " AND {$wpdb->usermeta}.meta_value NOT LIKE '%support6temflex%'";
					else
						$support6temflexCondition = '';

					# Condition to hide user '6tem9editeur'
					if ( $user->data->user_login != '6tem9editeur' )
						$user6tem9EditeurCondition = " AND {$wpdb->users}.user_login NOT LIKE '%6tem9editeur%'";

					$user_search->query_where = 
					    str_replace('WHERE 1=1', 
					        "WHERE 1=1 AND {$wpdb->users}.ID IN (
					             SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
					                WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
					                AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%'".$resellerCondition.$support6temflexCondition.$user6tem9EditeurCondition.$billingCustomerCondition.")", 
					        $user_search->query_where
					    );
				}
			}
		}
	}
}

// add_action( 'pre_user_query', 'fHideRoleFromUserList' );

# -----------------------------------------------------------------------------------------------------------------------------------------
# Number of users for each role in user list
function fUserListNumberByRole( $views ) 
{
	# ------------
	# Count users for roles "administrator", "editor", "reseller" and "support6temflex"

	$user = wp_get_current_user();
	$result = count_users();
	$realTotalUserCount = $result['total_users'];
	$show = array('administrator', 'editor', 'support6temflex', 'reseller', 'billingCustomer' );

	foreach($result['avail_roles'] as $role => $count)
	{
		if ( in_array($role, $show) )
		{
			if ( $role == 'administrator' )
				$nbAdministrator = $count;
			else if ( $role == 'support6temflex' )
				$nbSupport = $count;
			else if ( $role == 'reseller' )
				$nbReseller = $count;
			else if ( $role == 'editor' )
			{				
				$nbEditor = $count;

				if ( $user->data->user_login != '6tem9editeur' && !current_user_can('administrator') ) # We retire "1" because of 6tem9editeur user
					$nbEditor = $nbEditor - 1;
			}
			else if ( $role == 'billingCustomer' )
				$nbBillingCustomer = $count;
		}
	}

	# ------------
	# Get "All" role count depending on current user role

	global $wp_roles;


	if ( current_user_can( 'administrator' ) )
		$nbTotalCount = $realTotalUserCount;
	else if ( current_user_can( 'support6temflex' ) )
	{
		$nbTotalCount = $realTotalUserCount - $nbAdministrator - 1; # We retire "1" because of 6tem9editeur user
		unset( $views['administrator'] );
	}
	else if ( current_user_can( 'reseller' ) )
	{
		$nbTotalCount = $realTotalUserCount - $nbAdministrator - $nbSupport - 1; # We retire "1" because of 6tem9editeur user
		unset( $views['administrator'] );
		unset( $views['support6temflex'] );
	}
	else
	{
		$nbTotalCount = $realTotalUserCount - $nbAdministrator - $nbSupport - $nbReseller - $nbBillingCustomer;

		if ( $user->data->user_login != '6tem9editeur' ) # We retire "1" because of 6tem9editeur user
			$nbTotalCount = $nbTotalCount - 1;

		unset( $views['administrator'] );
		unset( $views['support6temflex'] );
		unset( $views['reseller'] );
		unset( $views['billingCustomer'] );
	}

	$activeClassAll = !isset($_GET['role']) || $_GET['role'] == ''?'class="current"':'';
	$views['all'] = '<a href="users.php" ' . $activeClassAll . '>Tous <span class="count">('.$nbTotalCount.')</span></a>';

	$activeClassEditor = isset($_GET['role']) && $_GET['role'] == 'editor'?'class="current"':'';
	$views['editor'] = '<a href="users.php?role=editor" ' . $activeClassEditor . '>' . translate_user_role( $wp_roles->roles[ 'editor' ]['name'] ) . ' <span class="count">('.$nbEditor.')</span></a>';


	return $views;
}

// add_filter( 'views_users', 'fUserListNumberByRole' ); 



?>