<?php

      include __DIR__ . '/categoryandproduct.php';

      function cron_func()
      {
     create_category();
       create_product();
      }
      add_action('cronjob', 'cron_func');
//       //  cron_function();




      include __DIR__ . '/member_oppurtunity_item.php';

      add_action('woocommerce_before_thankyou', 'order');
      function order($order_id)
      {
         create_order($order_id);
      }
//       //create_order(20352);



?>

