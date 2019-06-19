<?php

# ===========================================================================================================
# Scripts and styles for the custom user roles page

function fEnqueueScriptStylesCustomUserRoles()
{
	wp_register_style( 'customUserRoles', plugins_url( '../customUserRoles/customUserRoles.css', __FILE__ ) );
	wp_enqueue_style( 'customUserRoles' );

	wp_register_script( 'customUserRoles', plugins_url( '../customUserRoles/customUserRoles.js', __FILE__ ), array( 'jquery' ), '', true );
	wp_enqueue_script( 'customUserRoles' );

	wp_enqueue_script( 'jquery-ui-tooltip' );

	# Localize the script with new data
    $tabTranslation = array(
        'messageAlreadyExists' => __('This role already exists.', '6tem9Fonction'),
        'messageMaxRoleReached' => __('You reach the maximum count of custom user roles.', '6tem9Fonction')
    );

    wp_localize_script( 'customUserRoles', 'translationObject', $tabTranslation );

	wp_register_script( 'jquery-validate', plugins_url( '../../js/jqueryValidate/jquery.validate.min.js', __FILE__ ), array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-validate' );
	
	if ( explode('_', get_locale())[0]!='en' )
	{
    	wp_register_script( 'jquery-validate-lang',plugins_url( '../../js/jqueryValidate/localization/messages_' . explode('_', get_locale())[0] . '.min.js', __FILE__ ), array( 'jquery', 'jquery-validate' ), '', true );
		wp_enqueue_script( 'jquery-validate-lang' );
	}
}

if ( isset( $_GET['page'] ) && $_GET['page'] == 'customUserRoles')
{
	add_action( 'admin_enqueue_scripts', 'fEnqueueScriptStylesCustomUserRoles', 10, 1 );	
}

# ===========================================================================================================
# Content of the custom user roles page

function fPageCustomUserRoles6tem9Fonction()
{
	global $wp_roles;

	$tabAllCapabilities = fGetTabAllCapabilities();
?>
	<div class="wrap">
<?php
		# ----------------------------------------------------------------------------------
		# Save capabilities

        if ( $_POST['saveCustomRoles'] )
        {
        	# Get the saved capabilities (if saved from this page)
        	if ( isset($_POST['tabUserCapabilities']) )
        		$tabUserCapabilitiesToSave = $_POST['tabUserCapabilities'];
        	# Get the saved capabilities (if saved from the "affect users role" page )
        	else if ( get_user_meta( get_current_user_id(), 'tabUserCapabilities', true ) != '' )
        	{
        		$tabUserCapabilitiesToSave = get_user_meta( get_current_user_id(), 'tabUserCapabilities', true );
        		delete_user_meta( get_current_user_id(), 'tabUserCapabilities' );
        	}

        	# Save the capabilities
        	fUpdateRolesCapabilities( $tabUserCapabilitiesToSave );       	
        
	        # ----------------------------------------------------------------------------------
			# Remove roles

        	if ( isset($_POST['rolesToRemove']) && $_POST['rolesToRemove'] != '' )
        	{
        		$tabRolesToRemove = explode(',', $_POST['rolesToRemove']);

        		foreach( $tabRolesToRemove as $roleToRemove )
        		{
        			if ( $roleToRemove != '')
        			{
        				# ---------------
        				# We switch the users of the old role to the new one
        				$tabUserOfOldRole = get_users( 'role=' . $roleToRemove );

        				$tabSwitchUserRoles = $_POST['tabSwitchUserRoles'];

        				if ( sizeof($tabUserOfOldRole) > 0 && isset($_POST['tabSwitchUserRoles']) )
        				{
	        				foreach( $tabUserOfOldRole as $userToSwitchRole )
	        				{
	        					$userToSwitchRole->remove_role( $roleToRemove );
	        					$userToSwitchRole->add_role($tabSwitchUserRoles[$roleToRemove]);
	        				}
	        			}

	        			# ---------------
        				# Finally, we remove definitively the role
        				remove_role( $roleToRemove );

        				# ---------------
        				# And update the count of custom users roles
        				update_option( 'countCustomUserRoles', get_option( 'countCustomUserRoles', 0 ) - 1 );	// Update the custom user role count
        			}
        		}
        	}
?>
        	<div id="message" class="updated below-h2">
            	<p>Les options ont bien &eacute;t&eacute; enregistr&eacute;es</p>
        	</div>
<?php              	
        }

        # ----------------------------------------------------------------------------------
        # Initialisation

        # -----------------
		# Tab of roles the user can't modify, add or delete (depending on the current user role)
		$tabLockedRoles = array( 'administrator', 'support6temflex' );

		# Tab of roles the user can only modify
		$tabLimitedRoles = array( 'author', 'contributor', 'editor', 'reseller' );

		# -----------------
    	# Array of editable roles

		$tabAllEditableRolesUnordored = get_editable_roles();
		$tabAllEditableRoles = array();
		$tabAllEditableRoles['reseller'] = $tabAllEditableRolesUnordored['reseller']; // We order the roles
		$tabAllEditableRoles['editor'] = $tabAllEditableRolesUnordored['editor']; // We order the roles
		$tabAllEditableRoles['author'] = $tabAllEditableRolesUnordored['author']; // We order the roles
		$tabAllEditableRoles['contributor'] = $tabAllEditableRolesUnordored['contributor'];// We order the roles
		unset($tabAllEditableRolesUnordored['administrator']); // Remove admin
		unset($tabAllEditableRolesUnordored['support6temflex']); // Remove support
		unset($tabAllEditableRolesUnordored['reseller']); // Remove admin
		unset($tabAllEditableRolesUnordored['editor']); // Remove admin
		unset($tabAllEditableRolesUnordored['author']); // Remove author (already in the new array)
		unset($tabAllEditableRolesUnordored['contributor']); // Remove contributor (already in the new array)
		$tabAllEditableRoles = array_merge($tabAllEditableRoles, $tabAllEditableRolesUnordored);

		// $currentUserRole = array_shift(wp_get_current_user()->roles);
		$objectCurrentUser = new WP_User( get_current_user_id() );		
		$currentUserRole = array_shift($objectCurrentUser->roles);

		if ( $currentUserRole == 'editor' )
			unset($tabAllEditableRoles['reseller']); // Remove reseller
		else if ( $currentUserRole == 'author' )
		{
			unset($tabAllEditableRoles['reseller']); // Remove reseller
			unset($tabAllEditableRoles['editor']); // Remove editor
			$tabLockedRoles[] = 'editor';
			$tabLockedRoles[] = 'reseller';
		}
		else if ( $currentUserRole == 'contributor' || strpos($currentUserRole, 'custom') !== false )
		{
			unset($tabAllEditableRoles['reseller']); // Remove reseller
			unset($tabAllEditableRoles['editor']); // Remove editor
			unset($tabAllEditableRoles['author']); // Remove editor
			$tabLockedRoles[] = 'editor';
			$tabLockedRoles[] = 'author';
			$tabLockedRoles[] = 'reseller';
		}

		# We add the current user role to the locked roles, if not already present		
		if ( !in_array($currentUserRole, $tabLockedRoles) || in_array($currentUserRole, $tabLimitedRoles) )
			$tabLockedRoles[] = $currentUserRole;

		$nbCustomRole = get_option( 'countCustomUserRoles', 0 );

        # ----------------------------------------------------------------------------------
		# Page to delete roles and affect users of the deleted role with another

        if ( $_POST['pageAffectUsersOfDeletedRoles'] )
        {

        	global $wp_roles;

        	# We save all the capabilities of the previous page in a temporary variable, to apply them if needed (and delete the temporary variable)
        	update_user_meta( get_current_user_id(), 'tabUserCapabilities', $_POST['tabUserCapabilities'], true );

        	$tabRolesToRemove = explode(',', $_POST['rolesToRemove']);
?>        	
        	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=customUserRoles" method="post">
				<div class="wrap">
					<h2><?php _e('Remove custom roles', '6tem9Fonction'); ?></h2>
					<p><?php _e('You chose for the roles listed below to be deleted. You need to affect the users linked to these deleted roles to other roles.', '6tem9Fonction'); ?></p>

					<ul>
<?php
					# Option for changing the users role
					foreach( $tabRolesToRemove as $slugRoleToRemove )
					{
						if ($slugRoleToRemove != '' && isset($wp_roles->roles[$slugRoleToRemove]) )
						{
							# New roles the user can change to
							$optionsNewRole = '<select name="tabSwitchUserRoles[' . $slugRoleToRemove . ']">';

							foreach ( $tabAllEditableRoles as $slugRole => $role )
							{
						 		# If role can acces admin and is not locked (and is bot the same as the roles to remove)
								if ( $tabAllEditableRoles[$slugRole]['capabilities']['6tem9_can_access_wp-admin'] && !in_array($slugRole, $tabLockedRoles) && !in_array($slugRole, $tabRolesToRemove) )
									$optionsNewRole .= '<option value="' . $slugRole . '">' . $wp_roles->roles[$slugRole]['name'] . '</option>';
							}

							$optionsNewRole .= '</select>';

							$countUsersRole = sizeof(get_users( 'role=' . $slugRoleToRemove ));
?>
							<li>
<?php
								if ( $countUsersRole > 0 )
								{
									$messageCountUsers = sprintf( _n('Linked to %s user', 'Linked to %s users', $countUsersRole, '6tem9Fonction'), $countUsersRole);
									echo sprintf( __("Users with the role %s will now have the role %s (%s)", "6tem9Fonction"), '<strong>' . $wp_roles->roles[$slugRoleToRemove]['name'] . '</strong>', '<strong>' . $optionsNewRole . '</strong>', $messageCountUsers);
								}
								else
									echo sprintf( __("The role %s doesn't have any users and will be simply removed.", "6tem9Fonction"), '<strong>' . $wp_roles->roles[$slugRoleToRemove]['name'] . '</strong>');
?>	
							</li>
<?php
						}
					}
?>
					</ul>
						
					<p class="submit">
						<select id="actionForCustomRoles">
							<option value="saveCustomRoles"><?php _e('Remove these roles and save all the options', '6tem9Fonction'); ?></option>
							<option value="saveCustomRolesCancelRemoval"><?php _e('Cancel roles removal but save the options', '6tem9Fonction'); ?></option>					
							<option value="cancel"><?php _e('Cancel everything', '6tem9Fonction'); ?></option>
						</select>

	            		<input type="hidden" name="saveCustomRoles" id="actionToDo" value="true" /> <?php // Action to perform after affecting roles : save, save and delete or cancel. We do this in javascript ?>
						<input type="hidden" id="rolesToRemove" name="rolesToRemove" value="<?php echo isset($_POST['rolesToRemove']) && $_POST['rolesToRemove'] != '' ? $_POST['rolesToRemove'] : ''; ?>" />

						<input type="submit" class="button button-primary" value="Apply">
					</p>
				</div>
			</form>
<?php			
        }

    	# ----------------------------------------------------------------------------------
        # Table of options

        else
        {

			# List of all existing roles
			$listRoles = implode(array_keys($wp_roles->roles), ',');

			# Max custom roles count
			$maxCustomRoles = 6 - $nbCustomRole;

			# Columns count : size of all roles + one col for the capabilities names
			$colCount = sizeof($tabAllEditableRoles) + 1;

			# ----------------------------------------------------------------------------------
			# Display the options
?>        
        	<h2><?php _e('Custom user roles', '6tem9Fonction'); ?></h2>
			<h3><?php _e('Add a new role', '6tem9Fonction'); ?></h3>

			<form id="addNewRole" action="#">
				<input type="text" id="roleTitle" name="roleTitle" placeholder="<?php _e('Role name', '6tem9Fonction'); ?>" />
				<span class="button" id="createCol"><?php _e('Create', '6tem9Fonction'); ?></span>
				<input type="hidden" id="listRoles" value="<?php echo $listRoles; ?>" />
				<input type="hidden" id="nbRoleLeft" value="<?php echo $maxCustomRoles; ?>" />
				<input type="hidden" id="nbCustomRole" value="<?php echo $nbCustomRole; ?>" />
			</form>

			<h3><?php _e('Edit roles', '6tem9Fonction'); ?></h3>

			<div id="tableCustomUserRoles" class="main table-responsive"> 
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=customUserRoles" method="post" id="formOption">
			        <table class="table table-bordered">
<?php
						# -----------------------------------------------------------------------------------------------------------
						# Roles
?>
			        	<thead>
			        		<tr class="actions">
			        			<th class="colAction"></th>
<?php
								foreach ( $tabAllEditableRoles as $slugRole => $role )
								{
									# If role can acces admin
									if ( $tabAllEditableRoles[$slugRole]['capabilities']['6tem9_can_access_wp-admin'] )
									{

										# If role allowed for beeing deleted
										if ( !in_array($slugRole, $tabLockedRoles) && !in_array($slugRole, $tabLimitedRoles) )
										{
?>              		
	              							<th class="colAction" data-role="<?php echo $slugRole; ?>" data-linkedUsers="<?php echo sizeof(get_users( 'role=' . $slugRole )); ?>"><span class="removeCol"><i class="fa fa-close"></i></span></th>
<?php
										}
										else
										{
?>              		
	              							<th class="colAction" data-role="<?php echo $slugRole; ?>" data-linkedUsers="<?php echo sizeof(get_users( 'role=' . $slugRole )); ?>"></th>
<?php
										}
									}
								}
?>
			            	</tr>
			            	<tr class="titles">
			              		<th class="colTitle colFirst"></th>
<?php
								foreach ( $tabAllEditableRoles as $slugRole => $role )
								{
									# If role can acces admin
									if ( $tabAllEditableRoles[$slugRole]['capabilities']['6tem9_can_access_wp-admin'] )
									{
?>              		
		              					<th class="colTitle" data-role="<?php echo $slugRole; ?>">
		              						<span class="title"><?php echo $role['name']; ?></span>
											<input name="tabUserCapabilities[<?php echo $slugRole; ?>][name]" type="hidden" value="<?php echo $role['name']; ?>">
<?php
											if ( !in_array($slugRole, $tabLockedRoles) && !in_array($slugRole, $tabLimitedRoles) )
											{
?>              						
	              								<span class="editCol"><i class="fa fa-pencil" aria-hidden="true"></i></span>
<?php
											}
?>              							
	              						</th>
<?php
									}
								}
?>
			            	</tr>
			         	</thead>
<?php
						# -----------------------------------------------------------------------------------------------------------
						# Capabilities
?>
	          			<tbody>
<?php
							foreach ( $tabAllCapabilities as $slugSection => $section )
							{
?>          	             
					            <tr>  
					              	<td colspan="<?php echo $colCount; ?>" data-name="<?php echo $slugSection; ?>" class="section open">
					                	<div class="edit dashed">
					                  		<?php echo $section['title']; ?>
					                  		<span class="dropdownArrow"><i class="fa fa-angle-down"></i></span>                    
					                  	</div>
					              	</td>
					            </tr>

<?php
								foreach( $section['cap'] as $capSlug => $capTitle )
								{
									$isTrigger = !empty($section['condition']) && $section['condition'] == $capSlug;
									$isToggle = !empty($section['condition']) && $section['condition'] != $capSlug;
?>	            
					                <tr data-cap="<?php echo $capSlug; ?>" data-section="<?php echo $slugSection; ?>" <?php echo $isTrigger ? 'data-triggerable="1"' : ''; ?> <?php echo $isToggle ? 'data-toggleable="1"' : ''; ?>>
					                	<td class="type"><?php echo $capTitle; ?></td>
<?php
									foreach ( $tabAllEditableRoles as $slugRole => $role )
									{										
										if ( $role['capabilities']['6tem9_can_access_wp-admin'] )
										{
											# If the others options need this one to be checked, we need to check this one
											$triggerOption =  $isTrigger ? 'data-trigger="' . $section['condition'] . '" data-role="' . $slugRole . '"' : ''; 

											# If the others options need a toggle option to be enable, we check this option
											$toggleOption =  $isToggle && in_array( $capSlug, $role['capabilities'] ) ? 'data-toggle="' . $section['condition'] . '" data-role="' . $slugRole . '"' : '';

											$capActive = $role['capabilities'][$capSlug];
											
											$roleAllowedForEdit = !in_array($slugRole, $tabLockedRoles);
											$specialConditionForCustomRoles = (strpos($slugRole, 'custom') !== false || $slugRole == 'author' || $slugRole == 'contributor') && $capSlug == '6tem9_manage_custom_roles';
?>
											<td class="option<?php echo $capActive ? ' success' : ''; ?><?php echo $roleAllowedForEdit && !$specialConditionForCustomRoles ? '' : ' disabled'; ?>" data-role="<?php echo $slugRole; ?>">
												<input name="tabUserCapabilities[<?php echo $slugRole; ?>][cap][<?php echo $capSlug; ?>]" 
												type="checkbox" value="1" 
												<?php echo $capActive ? 'checked="checked"' : ''; ?> <?php echo $roleAllowedForEdit && !$specialConditionForCustomRoles ? '' : 'disabled="disabled"'; ?> 
												<?php echo $triggerOption != '' ? $triggerOption : ''; ?> <?php echo $toggleOption != '' ? $toggleOption : ''; ?>>
												<input name="tabUserCapabilities[<?php echo $slugRole; ?>][cap][<?php echo $capSlug; ?>]" type="hidden" value="<?php echo $capActive ? 1 : 0; ?>">
											</td>
<?php
										}
									}
?>	                
		                			</tr>
<?php
								}
							}
?>
	              		</tbody>
	            	</table>

	            	<input type="hidden" name="saveCustomRoles" value="true" />
					<input type="hidden" id="rolesToRemove" name="rolesToRemove" value="" />

		            <p class="submit clear">
		                <input type="submit" class="button-primary" value="Enregistrer" name="submit" />
		            </p>
	            </form>
	        </div>
<?php
		}
?>	        
	    </div>
<?php
}