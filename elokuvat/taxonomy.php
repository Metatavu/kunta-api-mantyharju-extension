<?php
  namespace KuntaAPI\Extensions\Mantyharju\Elokuvat;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  add_action('init', function () {
  	register_taxonomy('mantyharju-elokuva-categories', 'mantyharju-elokuva', [
  	  'label' => 'Kategoriat',
  	  'rewrite' => array( 'slug' => 'mantyharju-elokuva-categories' ),
  	  'show_ui' => true,
  	  'show_in_menu' => true,
  	  'show_in_nav_menus' => false,
  	  'show_in_rest' => true,
  	  'show_in_quick_edit' => false,
  	  'meta_box_cb' => false
  	]);
  	
  });
  
?>
