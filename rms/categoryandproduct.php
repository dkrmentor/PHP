<?php

//get ##################################### WORDPRESS cat id
function wp_category_id($rms_product_group)
{
  $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD, "ck_4b1394e394416dba1fd3bfd84451fcbfc7b2b268:cs_9fc8e825885b5d40340a77aa8a7426ff2d5d3b19");

  $search = $rms_product_group;
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://skytoddler.tk/wp-json/wc/v3/products/categories?search=$search",
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


function create_category()
{
  ##################################### GET [CATEGORY] ----- RMS

  $rms_category_response = rms_category();
  $arr = json_decode($rms_category_response, true);
  // print_r($arr['product_groups'][0]['name']);
  getAllCategories($arr['product_groups']);
}

function getAllCategories($category_list)
{
  for ($i = 0; $i < count($category_list); $i++) {
    $rms_category_name = $category_list[$i]['name'];


    ##################################### POST [CATEGORY] ----- WORDPRESS

    $body = '{
    
  "name": " ' . $rms_category_name . ' "
  
        }';

    wp_category_post($body);
  }
}


function create_product()
{
  ##################################### GET [PRODUCT] ----- RMS


  $arr_block = [];


  $rms_product_response = rms_product(1);
  $arr1 = json_decode($rms_product_response, true);

 // $pagewise = $arr['meta']['total_row_count'];


  $total_items = ceil($arr1['meta']['total_row_count']/20);





  for ($i = 1 ; $i < $total_items ; $i++){
    $rms_product_response = rms_product($i);
  // $rms_product_response = json_decode($rms_product_response, true);
   
  array_push($arr_block,json_decode($rms_product_response, true));
   // $arr_block =json_decode($arr_block[$i]);
  }
//  print_r(json_encode($arr_block[0]));

getAllProducts($arr_block);

}


function getAllProducts($product_page)
{
 
  for ($p = 0; $p < count($product_page); $p++) {

  for ($i = 0; $i < count($product_page[$p]['products']); $i++) {
  //  print_r($i);
    $rms_id = $product_page[$p]['products'][$i]['id'];
    $rms_product_name = $product_page[$p]['products'][$i]['name'];
    $rms_product_description = $product_page[$p]['products'][$i]['description'];
    $rms_product_short_description = $product_page[$p]['products'][$i]['custom_fields']['short_description'];
    $rms_product_group = $product_page[$p]['products'][$i]['product_group']['name'];

    if (($product_page[$p]['products'][$i]['rental_rate']) != null) {
      $rms_rental_rate_price = $product_page[$p]['products'][$i]['rental_rate']['price'];
    }
    else{
    $rms_rental_rate_price = 0;
    
    }


    if (($product_page[$p]['products'][$i]['icon']) != null) {

      $rms_img_url = $product_page[$p]['products'][$i]['icon']['url'];
 
    }
    else{
   
      $rms_img_url = "https://s3.amazonaws.com/current-rms/45617a00-7a48-0134-0f49-0a9ca217e95b/icons/166/original/Ameda_Hygienikit_Milk_Collection_System.jpg"
      ;
    }
    


    // ##################################### GET [CATEGORY] id ----- WORDPRESS
    $wp_category_response = wp_category_id($rms_product_group);
    $arr = json_decode($wp_category_response, true);
    if ($arr != null){ $wp_category_id = $arr[0]['id'];}

    // ##################################### GET [RATE DEFINITION] TO GET MULTIPLIER & FACTORS ----- RMS
    // $rms_rate_def_response = rms_rateDefinition();
    // $arr = json_decode($rms_rate_def_response, TRUE);
    // $rms_rate_def_multipliers = $arr['rate_definition']['config']['multipliers'];
    // print_r(json_encode($rms_rate_def_multipliers));
    // $rms_rate_def_factors = $arr['rate_definition']['config']['factors'];
    // print_r(json_encode($rms_rate_def_factors));
    // print_r('stop');


    ##################################### POST TO GET [STOCK] TO quantity_held ----- RMS
    $body =
      '{"booking_availability_view_options": {
  "product_id": "' . $rms_id . '",
  "days_period": 1
  
   }
    }';
    $rms_quantity = rms_stock($body);
    $arr = json_decode($rms_quantity, true);
    $rms_stock_quantity = $arr['product_bookings']['quantity_held']['0'];
  //  print_r( $rms_stock_quantity);
    ##################################### POST [PRODUCT] ----- WORDPRESS
    // "sku" : "' . $rms_id . '" ,
    $body =
      '{
      "sku" : "' . $rms_id . '" ,
    "name" : "' . $rms_product_name . '" ,
  "purchasable": true,
    "type" : "ovabrw_car_rental",
   "status": "publish",
   "on_sale": false,
    "description" : "' . $rms_product_description . '" ,
    "short_description" : "' . $rms_product_short_description . '" , 

  "price": "0",
  "regular_price": "0",
    "categories": [
      {
          "id" : ' . $wp_category_id . ',
          "name" : "' . $rms_product_group . '"          
      }
    ],
    "images": [
      {
        
          "src": "' . $rms_img_url . '",          
          "alt": "product_img"
      }],
    "meta_data": [{
      "key": "ovabrw_manage_store",
      "value": "store"
      },{
  
      "key": "ovabrw_price_type",
      "value": "period_time"
     },
       {
        "key": "ovabrw_package_type",
    "value": [
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other",
        "other" ,
        "other"

    ]
        },
  {
        "key": "ovabrw_petime_id",
    "value": [
        "1",
        "2",
        "3",
        "4",
        "5",
        "6",
        "7",
        "8",
        "9",
        "10",
        "11",
        "12",
        "13",
        "14",
        "15",
        "16",
        "17",
        "18",
        "19",
        "20",
        "21",
        "22",
        "23",
        "24",
        "25",
        "26",
        "27",
        "28",
        "29",
        "30",
        "31",
        "32",
        "33"
    ]
 },
  {

        "key": "ovabrw_petime_price",
     "value": [
      "' . $rms_rental_rate_price * 1 . '",
      "' . $rms_rental_rate_price * 1.4 . '",
      "' . $rms_rental_rate_price * 1.5 . '",
      "' . $rms_rental_rate_price * 1.7 . '",
      "' . $rms_rental_rate_price * 1.75 . '",
      "' . $rms_rental_rate_price * 1.8 . '",
      "' . $rms_rental_rate_price * 1.19 . '",
      "' . $rms_rental_rate_price * 1.195 . '",
      "' . $rms_rental_rate_price * 2 . '",
      "' . $rms_rental_rate_price * 2.1 . '",
      "' . $rms_rental_rate_price * 2.15 . '",
      "' . $rms_rental_rate_price * 2.2 . '",
      "' . $rms_rental_rate_price * 2.3 . '",
      "' . $rms_rental_rate_price * 2.35 . '",
      "' . $rms_rental_rate_price * 2.4 . '",
      "' . $rms_rental_rate_price * 2.5 . '",
      "' . $rms_rental_rate_price * 2.6 . '",
      "' . $rms_rental_rate_price * 2.65 . '",
      "' . $rms_rental_rate_price * 2.7 . '",
      "' . $rms_rental_rate_price * 2.75 . '",
      "' . $rms_rental_rate_price * 2.8 . '",
      "' . $rms_rental_rate_price * 2.9 . '",
      "' . $rms_rental_rate_price * 3 . '",
      "' . $rms_rental_rate_price * 3.05 . '",
      "' . $rms_rental_rate_price * 3.1 . '",
      "' . $rms_rental_rate_price * 3.2 . '",
      "' . $rms_rental_rate_price * 3.25 . '",
      "' . $rms_rental_rate_price * 3.3 . '",
      "' . $rms_rental_rate_price * 3.5 . '",
      "' . $rms_rental_rate_price * 4.5 . '",
      "' . $rms_rental_rate_price * 5.5 . '",
      "' . $rms_rental_rate_price * 6.5 . '" ,
      "' . $rms_rental_rate_price * 7.5 . '"

  ]
  },
  {
  
    "key": "ovabrw_petime_days",
    "value": [
      "3",
      "4",
      "5",
      "6",
      "7",
      "8",
      "9",
      "10",
      "11",
      "12",
      "13",
      "14",
      "15",
      "16",
      "17",
      "18",
      "19",
      "19",
      "21",
      "22",
      "23",
      "24",
      "25",
      "26",
      "27",
      "28",
      "29",
      "30",
      "31",
      "90",
      "180",
      "270",
      "365"
  ]
  },
  {
  
    "key": "ovabrw_petime_label",
    "value": [
      "3 days",
      "4 days",
      "5 days",
      "6 days",
      "7 days",
      "8 days",
      "9 days",
      "10 days",
      "11 days",
      "12 days",
      "13 days",
      "14 days",
      "15 days",
      "16 days",
      "17 days",
      "18 days",
      "19 days",
      "20 days",
      "21 days",
      "22 days",
      "23 days",
      "24 days",
      "25 days",
      "26 days",
      "27 days",
      "28 days",
      "29 days",
      "30 days",
      "31 days",
      "3 Month",
      "6 Month",
      "9 Month",
      "1 Year"
  ]
  },
  {
           
            "key": "ovabrw_manage_time_book_start",
            "value": "no"
        }, {
            "id": 222186,
            "key": "ovabrw_manage_time_book_end",
            "value": "no"
        },
        
  {
    "key": "ovabrw_rent_day_min",
    "value": "3"
  },
  {
      "key": "ovabrw_car_count",
      "value": ' . $rms_stock_quantity . '
  }
  ]  
  }';


 wp_product_post($body);
  }}
}

  //  //GET ##################################### RMS
  function rms_category()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.current-rms.com/api/v1/product_groups/',
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
        'Cookie: _cobra_session=ba021367eaabe89b228bef85b736d403'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
  //  //post ##################################### WORDPRESS
  function wp_category_post($body)
  {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD, "ck_4b1394e394416dba1fd3bfd84451fcbfc7b2b268:cs_9fc8e825885b5d40340a77aa8a7426ff2d5d3b19");

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://skytoddler.tk/wp-json/wc/v3/products/categories/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Cookie: PHPSESSID=4c06180e0e24167a13dce0137ca75aa4'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
  }
  //  //GET ##################################### RMS
  function rms_product($page)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.current-rms.com/api/v1/products?per_page=20&page=$page",
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
        'Cookie: _cobra_session=ba021367eaabe89b228bef85b736d403'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
  //  //post ##################################### WORDPRESS

  function wp_product_post($body)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD, "ck_4b1394e394416dba1fd3bfd84451fcbfc7b2b268:cs_9fc8e825885b5d40340a77aa8a7426ff2d5d3b19");
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://skytoddler.tk/wp-json/wc/v3/products/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',

      CURLOPT_POSTFIELDS =>
      $body,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Cookie: PHPSESSID=2f5189ea709e94c987e64c69c64a523c'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
  }
  //  //POST TO GET ##################################### RateDefinition
  function rms_stock($body)
  {


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.current-rms.com/api/v1/availability/product',
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
        'Content-Type: application/json',
        'Cookie: _cobra_session=13029147196f459218048fb9ef28cbb7'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
  }