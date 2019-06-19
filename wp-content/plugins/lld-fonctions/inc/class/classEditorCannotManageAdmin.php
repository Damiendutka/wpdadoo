<?php

# Prevent editor from deleting, editing, or creating an administrator
# Only needed if the editor was given right to edit users
 
class EditorCannotManageAdmin {
 
  # Add our filters
  function EditorCannotManageAdmin()
  {
    add_filter( 'editable_roles', array(&$this, 'editable_roles'));
    // add_filter( 'map_meta_cap', array(&$this, 'map_meta_cap'),10,4);
  }

  # Remove 'Administrator' from the list of roles if the current user is not an admin
  function editable_roles( $roles )
  {
    # Current user roles
    $currentUser = wp_get_current_user();
    $tabCurrentUserRole = $currentUser->roles;

    if ( in_array("editor", $tabCurrentUserRole) )
    {
        unset( $roles['reseller']);
        unset( $roles['subscriber']);
        unset( $roles['contributor']);
        unset( $roles['support6temflex']);
        // unset( $roles['author']);
        unset( $roles['shop_manager']);
        unset( $roles['administrator']);
        unset( $roles['billingCustomer']);
    }
    else if ( in_array("reseller", $tabCurrentUserRole) )
    {
        unset( $roles['subscriber']);
        unset( $roles['contributor']);
        unset( $roles['support6temflex']);
        unset( $roles['shop_manager']);
        unset( $roles['administrator']);
    }
    else if ( in_array("support6temflex", $tabCurrentUserRole) )
    {
        unset( $roles['subscriber']);
        unset( $roles['contributor']);
        unset( $roles['shop_manager']);
        unset( $roles['administrator']);
    }

    return $roles;
  }

  # If someone is trying to edit or delete an
  # admin and that user isn't an admin, don't allow it
  function map_meta_cap( $caps, $cap, $user_id, $args )
  {
    switch( $cap )
    {
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) || $other->has_cap( 'reseller' ) || $other->has_cap( 'support6temflex' ) ){
                if(!current_user_can('administrator') && !current_user_can('reseller') && !current_user_can('support6temflex') ){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) || $other->has_cap( 'reseller' ) || $other->has_cap( 'support6temflex' ) )
            {
                if(!current_user_can('administrator') && !current_user_can('reseller') && !current_user_can('support6temflex') ){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }
 
}
 
?>