<?php
  namespace KuntaAPI\Extensions\Mantyharju\Elokuvat;

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

  add_action ('init', function () {
    register_post_type ( 'mantyharju-elokuva', [
      'labels' => array (
        'name'               => 'Elokuvat',
        'singular_name'      => 'Elokuva',
        'add_new'            => 'Lisää elokuva',
        'add_new_item'       => 'Lisää uusi elokuva',
        'edit_item'          => 'Muokkaa elokuvaa',
        'new_item'           => 'Uusi elokuva',
        'view_item'          => 'Näytä elokuva',
        'search_items'       => 'Näytä elokuva',
        'not_found'          => 'Elokuvia ei löytynyt',
        'not_found_in_trash' => 'Elokuvia ei löytynyt roskakorista',
        'menu_name'          => 'Elokuvat',
        'all_items'          => 'Elokuvat'
      ),
      'menu_icon' => 'dashicons-format-video',
      'public' => true,
      'has_archive' => true,
      'show_in_rest' => true,
      'supports' => array (
        'title',
        'editor',
        'thumbnail'
       )
    ]);
  });

?>