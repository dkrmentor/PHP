<?php
///sessions
add_action('init', 'myStartSession', 1);
    add_action('wp_logout', 'myEndSession');
    add_action('wp_login', 'myEndSession');
    function myStartSession() {
        if(!session_id()) {
            session_start();
        }
    }
    function myEndSession() {
        session_destroy ();
    }


// //logout redirection
add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){

  wp_redirect( home_url() );
  exit();

}


//validations
add_action( 'process_registration', 'validate2', 10, 2 );
 
function validate2( $fields, $errors ){
 
    if ( preg_match( '/[^a-zA-Z]+/', $fields[ 'xoo_el_reg_username' ] )){
        $errors->add( 'validation', 'First or last name can only contain alphabets' );
    
	}
}

//validations
add_action( 'woocommerce_after_checkout_validation', 'validate', 10, 2 );
 
function validate( $fields, $errors ){
 
    if ( preg_match( '/[^a-zA-Z]+/', $fields[ 'billing_first_name' ] ) || preg_match( '/[^a-zA-Z]+/', $fields[ 'billing_last_name' ] ) ){
        $errors->add( 'validation', 'First or last name can only contain alphabets' );
    
	}
	
	  if ( strlen( $_POST['billing_first_name'] ) > 25 || strlen( $_POST['billing_last_name'] ) > 25){
        $errors->add( 'validation', 'First or last name cannot exceeed 25 character limit' );
    }
	
	
		
		  if ( strlen( $_POST['billing_address_1'] ) > 200 || strlen( $_POST['billing_address_2'] ) > 200){
        $errors->add( 'validation', 'Address cannot exceeed 200 character limit' );
    }
			
		  if ( strlen( $_POST['billing_city'] ) > 50 ){
        $errors->add( 'validation', 'City cannot exceeed 50 character limit' );
    }
	
		  if ( strlen( $_POST['billing_postcode'] ) > 5 ){
        $errors->add( 'validation', 'Zip code cannot exceeed 5 character limit' );
    }
	
	   if ( preg_match( '/[^a-zA-Z]+/', $fields[ 'billing_first_name' ]  )){
        $errors->add( 'validation', 'Zip code can only contain number' );
    
	}
		   if ( !preg_match( '/^[0-9\-\(\)\/\+\s]*$/', $fields[ 'billing_phone' ]  )){
        $errors->add( 'validation', 'Number is not valid' );
    
	}
	
	if ( !preg_match( '/^([a-z0-9\_\-]+)(\.[a-z0-9\_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', $fields[ 'billing_email' ]  )){
        $errors->add( 'validation', 'Email is not valid' );
    
	}

}
	


//AED
add_filter( 'woocommerce_currency_symbol', 'wc_change_uae_currency_symbol', 10, 2 );

function wc_change_uae_currency_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'AED':
			$currency_symbol = 'AED ';
		break;
	}

	return $currency_symbol;
}




//change text
add_filter( 'woocommerce_product_add_to_cart_text', function( $text ) {
    if ( 'Read more' == $text ) {
        $text = __( 'Buy Now', 'woocommerce' );
    }

    return $text;
} );



?>

