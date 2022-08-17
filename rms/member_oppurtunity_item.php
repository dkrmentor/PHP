<?php

function create_order($order_id)
{
  ##################################### GET [ORDER] ----- WP

  $wp_response = wp_order($order_id);
 //print($wp_response);
  $arr = json_decode($wp_response, true);
  // print_r($arr);
  // print_r($arr['id']);    
  getOrder($arr);
}

function getOrder($order)
{
  // print_r($order);

  //member
  $member = $order['billing'];
  //single item
  $item = $order['line_items'];
 // print_r($item);

  ##################################### POST [ORDER] ----- RMS
  //MEMBER / OPPoRTUNITY / ITEM

  //member keys
$full_name = $member['first_name'] . " " . $member['last_name'];

  $address = $member['address_1'] . " " . $member['address_2'];
  $city = $member['city'];
  $country = $member['country'];
  $cc = $city. " " . $country;
  $postcode = $member['postcode'];
 $email = $member['email'];

  $phone = $member['phone'];


 // GET ##################################### RMS MEMBER

 $member_getresponse = rms_getmember($email);
 $arr = json_decode($member_getresponse, true);

// $arr['members'] == email
 if(count($arr['members']) > 0){

    // GET ID ##################################### RMS MEMBER
    $member_id = $arr['members'][0]['id'];
    // print_r($member_id);
 }

 else{
  // POST ##################################### RMS MEMBER
  $body = '{
    "member": {
      "name": "' . $full_name . '",  
      "active": true,   
      "membership_type": "Organisation", 
      "membership": {
          "owned_by": 1
      },
      "primary_address": {
        "street": "' . $address . '",
        "postcode": "' . $postcode . '",
        "city": "' . $cc . '",    
        "country_id": 242     
      },
      "emails": [
        {
          "address": "' . $email . '",
          "type_id": 4001,
          "email_type_name": "Work"
  
        }
      ],
      "phones": [
        {
          "number": "' . $phone . '",
          "type_id": 6001,
          "phone_type_name": "Work"
     
        }
      ]
        }
      
    
  }';
    $member_response = rms_member($body);
    // print_r($member_response);
    $arr = json_decode($member_response, true);
    $rms_member = $arr['member'];
    $member_id = $rms_member['id'];
   //  print_r($member_id);
 }


  // POST ##################################### RMS OPPURTUNITY



  for ($i = 0; $i < count($item); $i++) {
    // print_r($i);

  $sku = $item[$i]['sku'];  //must be integer

   
  $price = $item[$i]['price'];  
  $pickup = $item[$i]['meta_data']['0']['value'];  
  $dropoff = $item[$i]['meta_data']['1']['value'];  
  $quantity = $item[$i]['meta_data']['4']['value']; 


print_r($quantity );



  $body = '{
    "opportunity": {   
          "member_id": ' . $member_id . ',
          "subject": "'.$full_name . "-" .$i.'",
          "owned_by": 1,
          "state": 3,  
          "charge_starts_at": "'.$pickup.'",
          "charge_ends_at": "'.$dropoff.'",
          "destination": {
              "source_type": "Opportunity",
              "address": {       
                "name": "' . $full_name . '",  
                "street": "' . $address . '",
                "postcode": "' . $postcode . '",
                "city": "' . $cc . '",
                "country_id": 242     
              }
          }
    }
  }';
  $opportunity_response = rms_opportunity($body);
  // print_r($opportunity_response);
  $arr = json_decode($opportunity_response, true);
  $rms_opportunity = $arr['opportunity'];
  $opportunity_id = $rms_opportunity['id'];
  // print_r($opportunity_id);


 


  // POST ##################################### RMS  ITEM
    
  $body = '{
    "opportunity_item":
              {
                  "opportunity_id": ' . $opportunity_id . ',
                  "item_id":  '.$sku.',
                  "item_type": "Product",
                  "quantity": "'.$quantity.'",              
                  "price": "'.$price.'"
                    
              
               
              }
  }';
  rms_item($body, $opportunity_id);
  
}
}


//get ##################################### WORDPRESS 
function wp_order($order_id)
{
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_USERPWD, "ck_4b1394e394416dba1fd3bfd84451fcbfc7b2b268:cs_9fc8e825885b5d40340a77aa8a7426ff2d5d3b19");

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://skytoddler.tk/wp-json/wc/v3/orders/$order_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Cookie: PHPSESSID=4c06180e0e24167a13dce0137ca75aa4'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;
}










// GET ##################################### RMS Member
function rms_getmember($email)
{
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.current-rms.com/api/v1/members?q[work_email_address_eq]=$email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'X-SUBDOMAIN: skytots',
      'X-AUTH-TOKEN: N5L6Nbb_zsgZhNkoo6_Q',
      'Cookie: _cobra_session=f0d7ec5ce3929d68d90a5d67d3c0eaff'
    ),
  ));
  

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;
  //    echo $response;
}


// POST ##################################### RMS Member
function rms_member($body)
{
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.current-rms.com/api/v1/members?',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => array(
      'X-SUBDOMAIN: skytots',
      'X-AUTH-TOKEN: N5L6Nbb_zsgZhNkoo6_Q',
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;
  //    echo $response;
}
// POST ##################################### RMS Opportunity
function rms_opportunity($body)
{
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.current-rms.com/api/v1/opportunities',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => array(
      'X-SUBDOMAIN: skytots',
      'X-AUTH-TOKEN: N5L6Nbb_zsgZhNkoo6_Q',
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;
  //    echo $response;
}
// POST ##################################### RMS Item
function rms_item($body, $opportunity_id)
{
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.current-rms.com/api/v1/opportunities/$opportunity_id'/opportunity_items/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => array(
      'X-SUBDOMAIN: skytots',
      'X-AUTH-TOKEN: N5L6Nbb_zsgZhNkoo6_Q',
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;
  //    echo $response;
}

?>

