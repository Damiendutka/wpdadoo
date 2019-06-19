<?php
# ================================================================================================================================
/**
 * Replace les checkbox par des boutons radio pour la liste des categories d'une taxonomie d'un type de poste
 * Use radio inputs instead of checkboxes for term checklists in specified taxonomies.
 *
 * @param   array   $args
 * @return  array
 */
function fTermRadioChecklist( $args ) {

    global $post;
    $tabScreenTaxonomy = array();
    $tabScreenTaxonomy = apply_filters( 'hook_button_radio_taxonomy_list_term', $tabScreenTaxonomy );  
    
    if ( !empty($tabScreenTaxonomy) && isset($tabScreenTaxonomy[$post->post_type]) ) 
    {
        if ( ! empty( $args['taxonomy'] ) && in_array($args['taxonomy'], $tabScreenTaxonomy[$post->post_type])  ) 
        {
            if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) 
            { 
                // Don't override 3rd party walkers.
                if ( ! class_exists( 'WPSE_139269_Walker_Category_Radio_Checklist' ) ) 
                {
                    /**
                     * Custom walker for switching checkbox inputs to radio.
                     *
                     * @see Walker_Category_Checklist
                     */

                    class WPSE_139269_Walker_Category_Radio_Checklist extends Walker_Category_Checklist 
                    {
                        function walk( $elements, $max_depth, $args = array() ) 
                        {
                            $output = parent::walk( $elements, $max_depth, $args );
                            $output = str_replace(
                                array( 'type="checkbox"', "type='checkbox'" ),
                                array( 'type="radio"', "type='radio'" ),
                                $output
                            );

                            return $output;
                        }
                    }
                }

                $args['walker'] = new WPSE_139269_Walker_Category_Radio_Checklist;
            }
        }
    }

    return $args;
}

add_filter( 'wp_terms_checklist_args', 'fTermRadioChecklist' );
?>