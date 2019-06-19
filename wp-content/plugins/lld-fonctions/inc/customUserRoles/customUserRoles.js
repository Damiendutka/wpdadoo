jQuery(document).ready(function()
{
	// ------------------------------------------------------------------------------------------------
	// Click on capability

	// Click on the <td> containing an option to check it
	jQuery(document).on('click', '#tableCustomUserRoles tbody td.option', function(event) {

	    if (jQuery(event.target).is('input:checkbox'))
	        return;

	    var checkbox = jQuery(this).find("input[type='checkbox']");
	    checkbox.click();
	});

	// Click on an option (capability)
	jQuery(document).on('change', '#tableCustomUserRoles input:checkbox', function() {
		jQuery(this).parent().toggleClass( "success" );

		// Update the option that will be saved at the end
		if ( jQuery(this).is(':checked') )
			jQuery(this).parent().find("input[type='hidden']").attr('value', 1);			
		else
			jQuery(this).parent().find("input[type='hidden']").attr('value', 0);			

		// If this input is required for the input of the same section (and is checked), we remove the "disable" attribute of the others inputs
		var inputToChange = jQuery(this).parent().parent().parent().find("input[type='checkbox'][data-toggle='" + jQuery(this).data('trigger') + "'][data-role='" + jQuery(this).data('role') + "']");

		if ( jQuery(this).is(':checked') && jQuery(this).data('trigger') != '' )
		{
			inputToChange.removeAttr('disabled');
			inputToChange.parent().removeClass('disabled');
		}
		else if ( !jQuery(this).is(':checked') && jQuery(this).data('trigger') != '' )
		{
			inputToChange.attr('disabled', 'disabled');
			inputToChange.parent().addClass('disabled');
		}
	});

	// If the "trigger" option is disabled, all the "toggle" options are disabled too
	fLockTriggerToggleOptions();

	// ------------------------------------------------------------------------------------------------
	// Dropdown section

	jQuery('.section').on('click', function() {
		var slugSection = jQuery(this).data('name');

		if ( jQuery(this).hasClass('open') )
		{
			jQuery('tr[data-section="' + slugSection + '"]' ).hide();
			jQuery(this).removeClass('open');
			jQuery(this).find('.dropdownArrow').html('<i class="fa fa-angle-right"></i>');
		}
		else
		{
			jQuery('tr[data-section="' + slugSection + '"]' ).show();
			jQuery(this).addClass('open');
			jQuery(this).find('.dropdownArrow').html('<i class="fa fa-angle-down"></i>');
		}

	});

	// Prevent from submitting the form when pressing "Enter"
	jQuery(document).on("keypress", "#addNewRole", function(event) { 
	    return event.keyCode != 13;
	});

	// ------------------------------------------------------------------------------------------------
	// Create new role

	// Add a new role
	jQuery('#createCol').on('click', function() {

		// Add the new column
		var roleTitle = jQuery('#roleTitle').val();
		var nbCustomRole = jQuery('#nbCustomRole').val();

		if ( jQuery("#addNewRole").valid() )
		{
			fTableRepeaterNewCol(jQuery('#tableCustomUserRoles'), roleTitle, fGetNewRoleSlugID(parseInt(nbCustomRole, 10) + 1));

			// Update list of editable roles
			jQuery('#listRoles').val(jQuery('#listRoles').val() + ',' + slug(roleTitle));
			
			// Update number of custom role count left
			jQuery('#nbRoleLeft').val(jQuery('#nbRoleLeft').val() - 1);

			// Update number of custom role count used
			jQuery('#nbCustomRole').val(parseInt(jQuery('#nbCustomRole').val(), 10) + 1);

			// Empty the field
			jQuery('#roleTitle').val('');

			// If the "trigger" option is disabled, all the "toggle" options are disabled too
			fLockTriggerToggleOptions();
		}

	});

	// Recursive function to get a unique role slug ID
	function fGetNewRoleSlugID( nbCustomRole )
	{
		var tabRoles = jQuery('#listRoles').val().split(',');

		// If role with this ID exist, we increment the ID and check again
		if ( jQuery.inArray( 'custom' + nbCustomRole, tabRoles ) != -1 )
			return fGetNewRoleSlugID( parseInt(nbCustomRole, 10) + 1 );
		else
			return nbCustomRole;
	}

	// Validation
	jQuery("#addNewRole").validate({
        errorElement: "span",
        errorClass: "text-danger-error",
        onfocusout: function(element){
            if ( !this.checkable(element) ) 
                this.element(element);
        },
        rules: {
            roleTitle: {
                required: true,
                existingRole: true,
                limitUserCount: true
            },
        },
        messages: {
			roleTitle: {
				existingRole: translationObject.messageAlreadyExists,
				limitUserCount: translationObject.messageMaxRoleReached
			}
        },
        highlight: function(element){
            jQuery(element).addClass('has-error');
        },
        success: function(element){
            // L'element est le span de l'erreur
            jQuery(element).closest('.postbox').removeClass('has-error');
            jQuery(element).prev('.has-error').removeClass('has-error');            
        }
    });

    // Rule - Check if user role already exists
    jQuery.validator.addMethod(
        "existingRole",
        function(value, element) {

        	var canCreateRole = false;

        	tabCustomRoles = jQuery('#listRoles').val().split(',');

        	if ( jQuery.inArray(slug(value), tabCustomRoles) == -1 )
        		canCreateRole = true;

            return canCreateRole;
        }
    );

    // Rule - Check if user role already exists
    jQuery.validator.addMethod(
        "limitUserCount",
        function(value, element) {
            return jQuery('#nbRoleLeft').val() > 0;
        }
    );

    // ------------------------------------------------------------------------------------------------
	// Remove a role

	jQuery(document).on('click', '#tableCustomUserRoles .removeCol', function(event) {

		var role = jQuery(this).parent().data('role');

		jQuery('td[data-role="' + role + '"], th[data-role="' + role + '"]').remove();

		// Update list of editable roles
		jQuery('#listRoles').val(jQuery('#listRoles').val().replace(',' + slug(role), ''));
		
		// Update number of custom role count left
		jQuery('#nbRoleLeft').val(parseInt(jQuery('#nbRoleLeft').val(), 10) + 1);

		// Update number of custom role count used
		jQuery('#nbCustomRole').val(parseInt(jQuery('#nbCustomRole').val(), 10) - 1);

		// Update list of roles to remove
		var tabRolesToRemove = jQuery('#rolesToRemove').val().split(',');
		if ( jQuery.inArray( role, tabRolesToRemove ) == -1 )
			jQuery('#rolesToRemove').val(jQuery('#rolesToRemove').val() + role + ',');

		// Redirect to the roles affectation page
		// If a role is removed, we need to chose for the users linked to that role, what will be their new role.
		var countUsersOfRole = jQuery(this).parent().data('linkedusers');

		if ( countUsersOfRole > 0 )
			jQuery('input[name="saveCustomRoles"').attr('name', 'pageAffectUsersOfDeletedRoles');

	});

	// ------------------------------------------------------------------------------------------------
	// Edit a role name

	// Click on role name edit button
	jQuery(document).on('click', '#tableCustomUserRoles .editCol', function(event) {

		var roleTitle = jQuery(this).parent().find('.title').text();

		// Hide current title
		jQuery(this).parent().find('.title').hide();
		jQuery(this).parent().find('.editCol').hide();

		// Show an input field instead
		jQuery(this).parent().append('<input class="editCustomRoleTitle" value="' + roleTitle + '" />');
		jQuery(this).parent().append('&nbsp;<span class="button validateCustomRoleTitle">OK</span>');

	});

	// ------------------------------------------------------------------------------------------------
	// Validate role name edition

	// On input lose focus
	jQuery(document).on('blur', '.editCustomRoleTitle', function(event) {
	   	fTableChangeColumnTitle(jQuery(this));
	});
	
	// On button validation click
	jQuery(document).on('click', '.validateCustomRoleTitle', function(event) {
		fTableChangeColumnTitle(jQuery(this));
	});

	// ------------------------------------------------------------------------------------------------
	// Action to perform after affecting roles

	jQuery('#actionForCustomRoles').on('change', function() {

		var actionToDo = jQuery(this).find('option:selected').val();

		if ( actionToDo == 'saveCustomRoles' )
		{
			jQuery('#actionToDo').attr('name', 'saveCustomRoles');
			jQuery('#rolesToRemove').removeAttr('disabled');	
		}
		else if ( actionToDo == 'saveCustomRolesCancelRemoval' )
		{
			jQuery('#actionToDo').attr('name', 'saveCustomRoles');
			jQuery('#rolesToRemove').attr('disabled', 'disabled');
		}
		else if ( actionToDo == 'cancel' )
		{
			jQuery('#actionToDo').attr('name', 'cancel');
			jQuery('#rolesToRemove').attr('disabled', 'disabled');
		}

	}).change();

    // ------------------------------------------------------------------------------------------------
	// Functions

	// If the "trigger" option is disabled, all the "toggle" options are disabled too
	function fLockTriggerToggleOptions()
	{
		jQuery.each(jQuery("input[type='checkbox'][data-trigger]"), function() {
			if ( !jQuery(this).is(':checked') )
			{
				var inputToChange = jQuery(this).parent().parent().parent().find("input[type='checkbox'][data-toggle='" + jQuery(this).data('trigger') + "'][data-role='" + jQuery(this).data('role') + "']");

				inputToChange.attr('disabled', 'disabled');
				inputToChange.parent().addClass('disabled');
			}
		});
	}

	// Edit the title of a column
	function fTableChangeColumnTitle( item )
	{

		// Get new title
		var newTitle =  item.parent().find('.editCustomRoleTitle').val();

	    // Show new title with edit button
	    item.parent().find('.title').html(newTitle).show();
		item.parent().find('.editCol').show();
		item.parent().find(jQuery('input[type="hidden"]')).val(newTitle);

		// Remove edition fields
	    item.parent().find('.editCustomRoleTitle, .validateCustomRoleTitle').remove();	
	}

	// Add a new column to a table
	function fTableRepeaterNewCol( tableRepeater, roleTitle, nbCustomRole )
	{
		// Add the role title
		if ( roleTitle != '' )
		{
			// console.dir(nbCustomRole);
			jQuery(tableRepeater).find('thead tr.actions').append( '<th class="colAction" data-role="custom' + nbCustomRole + '"><span class="removeCol"><i class="fa fa-close"></i></span></th>' );
			jQuery(tableRepeater).find('thead tr.titles').append( '<th class="colTitle" data-role="custom' + nbCustomRole + '"><span class="title">' + roleTitle + '</span><input name="tabUserCapabilities[custom' + nbCustomRole + '][name]" type="hidden" value="' + roleTitle + '">&nbsp;<span class="editCol"><i class="fa fa-pencil" aria-hidden="true"></i></span></th>' );
		}						

		// Add all capabilities for the new role
		jQuery.each( jQuery(tableRepeater).find('tbody tr'), function( index, item ) {

			// Trigger / toggle for options dependencies
			var triggerOption = '';
			if ( jQuery(item).data('triggerable') == '1' )
				triggerOption = ' data-trigger="' + jQuery(item).data('cap') + '"';

			var toggleOption = '';
			if ( jQuery(item).data('toggleable') == '1' )
				toggleOption = ' data-toggle="' + jQuery(item).data('cap') + '"';
			
			// Add the option				
			if ( jQuery(item).data('cap') != undefined && jQuery(item).data('cap') != '' )
				jQuery(item).append('<td class="option" data-role="custom' + nbCustomRole + '"><input name="tabUserCapabilities[custom' + nbCustomRole + '][cap][' + jQuery(item).data('cap') + ']" type="checkbox" value="1" ' + triggerOption + toggleOption + ' data-role="custom' + nbCustomRole + '"><input name="tabUserCapabilities[custom' + nbCustomRole + '][cap][' + jQuery(item).data('cap') + ']" type="hidden" value="0"></td>');
			// Extend the section
			else if ( jQuery(item).find('td').hasClass('section') )
				jQuery(item).find('td').attr('colspan', jQuery(item).find('td').attr('colspan') + 1 );

		});

	}

});