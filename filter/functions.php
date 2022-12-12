<?php

function dropdown_filter_shortcode(){
    ob_start();
        include(bloginfo('stylesheet_directory').'filter.php');
    return ob_get_clean();
}
add_shortcode('dropdown-filter', 'dropdown_filter_shortcode');
?>