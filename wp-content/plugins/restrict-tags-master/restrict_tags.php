<?php
/*
Plugin Name: Restringir criação de tags
Plugin URI: http://participe.wp-brasil.org/
Description: Evitar que usuários abaixo de Editor possam criar tags
Version: 1.0
Author: Ricardo Moraleida
*/

add_filter('pre_insert_term','p2wpbr_restrict_tags', 2);

function p2wpbr_restrict_tags( $term, $taxonomy = false ) {

    // Strips non-existing tags if user isn't an Editor
    if(term_exists( $term, 'post_tag' )) {
        return $term;
    } else if(current_user_can( 'edit_pages' )) {
        return $term;
    } else {
        return false;
    }
            
}
?>