<?php

# ------------------------------------------------------------------------------------------------------------------
# Define all hooks on the footer links management page

if ( isset( $_GET['page'] ) && $_GET['page'] == 'footerLinks')
{
	add_action( 'admin_enqueue_scripts', 'fEnqueueScriptStylesFooterLinks', 10, 1 );	
	add_action( 'admin_head', 'fPrintScriptStylesFooterLinks', 10, 1 );	
}

function fEnqueueScriptStylesFooterLinks()
{
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );

    wp_register_script( 'sortableList', plugins_url( '../js/jquery.sortableList.js', __FILE__ ), array( 'jquery' ), '', true );
	wp_enqueue_script( 'sortableList' );

	wp_register_style( 'sortableList', plugins_url( '../css/sortableList.css', __FILE__ ) );
	wp_enqueue_style( 'sortableList' );

	# Localize the script with new data
    $tabTranslation = array(
        'trash' => __('Trash', '6tem9Fonction'),
        'newTab' => __('Open in a new tab', '6tem9Fonction'),
        'addItem' => __('Add the item', '6tem9Fonction'),
        'title' => __('Title', '6tem9Fonction')
    );

    wp_localize_script( 'sortableList', 'translationObject', $tabTranslation );
}

function fPrintScriptStylesFooterLinks()
{
?>
	<script>
		jQuery(document).ready(function(){

			// Options for the sortable list
			var tabOption = new Array(); 
			tabOption['sortable'] = true;
			tabOption['checkbox'] = false;
			tabOption['remove'] = true;
			tabOption['trash'] = false;
			tabOption['link'] = true;
			tabOption['newtab'] = true;

			// Call sortable list
			jQuery(".sortableList ul").sortableList(tabOption);

			// Autocomplete from post title			
			jQuery( "#addValue" ).autocomplete({
		        source: function( request, response ) {
			        jQuery.ajax({
			        	type: "POST",
						url: "/wp-content/plugins/6tem9Fonction/ajax/getPostByTitle.php",
						dataType: "json",
						data: 'value='+request.term,
						success: function( data ) {
							response( data );
				        }
			        });
			    },
			    select: function( event, ui ) {
			        jQuery('#addUrl').attr('value',ui.item.url);
			    }
		    });
		});
	</script>
<?php
}

# ------------------------------------------------------------------------------------------------------------------
# Affichage de la page d'ajout et d'ordonnancement des liens du footer

function fPageFooterLinks6tem9Fonction()
{
    
    # ----------------------------------------------------------------------------------------
    # Affichage des options
?>
    <div class="wrap">
        <h2><?php _e('Footer links', '6tem9Fonction'); ?></h2>
<?php
        if ( $_POST['action'] )
        {
        	# Apply stripslashes on titles
        	foreach( $_POST['sortableField'] as $key => $sortableField )
        	{
        		$sortableField['title'] = htmlentities(stripslashes(strip_tags($sortableField['title'])));
        		$_POST['sortableField'][$key]['title'] = $sortableField['title'];
        	}

	        update_option( 'tabFooterCustomLink', $_POST['sortableField'] );
?>
            <div id="message" class="updated below-h2">
                <p>Les options ont bien &eacute;t&eacute; enregistr&eacute;es</p>
            </div>
<?php
        }

        # Get the options
		$tabFooterCustomLink = get_option( 'tabFooterCustomLink' );
?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=footerLinks" method="post" id="formOption">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Links', '6tem9Fonction' ); ?></th>
                        <td>
															 
							<div class="sortableList">
								<ul>   
<?php
									foreach ( $tabFooterCustomLink as $customLink )
									{
?>
										<li data-url="<?php echo $customLink['url']; ?>" data-newtab="<?php echo $customLink['newtab']; ?>"><?php echo $customLink['title']; ?></li>
<?php								
									}
?>
								</ul>
							</div> 
                        </td>
                    </td>
                </tbody>
            </table>

            <input type="hidden" name="action" value="saveFooterLinks" />
            <p class="submit clear">
                <input type="submit" class="button-primary" value="Enregistrer" name="submit" />
            </p>
        </form>
    </div>
<?php
}    

?>