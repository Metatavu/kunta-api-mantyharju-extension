<?php
  namespace KuntaAPI\Extensions\Mantyharju\Elokuvat;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'KuntaAPI\Extensions\Mantyharju\Elokuvat\Shortcodes' ) ) {
    
    class Shortcodes {
      
      public function __construct() {
        add_shortcode('kunta_api_mantyharju_elokuva_lista', [$this, 'movieListShortcode']);
        add_action('edit_post', [$this, "onEditPost"]);
      }
      
      public function movieListShortcode($tagAttrs) {
        $attrs = shortcode_atts([
          'order' => "natural"
        ], $tagAttrs);
        
        $listOptions = [
          'post_type' => 'mantyharju-elokuva'
        ];
        
        switch ($attrs['order']) {
          case 'title':
            $listOptions['orderby'] = 'title';
          break;
          case 'date':
            $listOptions['orderby'] = 'date';
          break;
        }
        
        $posts = get_posts($listOptions); 
        
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem( __DIR__ . '/../templates'));
        
        $movies = [];
        
        foreach ($posts as $post) {
          $showtimes = [];
          $ageLimit = get_post_meta($post->ID, "agelimit", true);
          $runTime = get_post_meta($post->ID, "runtime", true);
          $price = get_post_meta($post->ID, "ticketprice", true);
          $trailerUrl = get_post_meta($post->ID, "trailerurl", true);
          $showtimeCount = get_post_meta($post->ID, "showtimes", true);
          $imageId = get_post_thumbnail_id($post->ID);
          
          $oldTimezone = date_default_timezone_get();
          try {
            date_default_timezone_set("Europe/Helsinki");
            for ($i = 0; $i < $showtimeCount; $i++) {
              $showtime = get_post_meta($post->ID, "showtimes_" . $i . "_datetime", true);
              $showtimes[] = date("c", strtotime($showtime));
            }
          } finally {
            date_default_timezone_set($oldTimezone);
          }
          
          $classifications = [];
          $classificationIds = get_post_meta($post->ID, "classification", true);
          
          foreach ($classificationIds as $classificationId) {
            $term = get_term(intval($classificationId));
            if ($term) {
              $classifications[] = [
                name => $term->name,
                slug => $term->slug
              ];
            }
          }
          
          $movies[] = [
            id => $post->ID,
            imageId => $imageId,
            imageUrl => wp_get_attachment_url($imageId),
            title => $post->post_title,
            showtimes => $showtimes,
            classifications => $classifications,
            ageLimit => $ageLimit,
            runTime => $runTime,
            price => $price,
            trailerUrl => $trailerUrl,
            description => $post->post_content
          ];
          
        }
        
        return $twig->render("movie-list.twig", [
          movies => $movies  
        ]);
      }
      
      public function onEditPost($postId) {
        $postType = get_post_type($postId);
        if ($postType == 'mantyharju-elokuva') {
          foreach ($this->getPagesWithShortcode() as $page) {
            do_action('edit_post_related', $page->ID, $page);
          }
        }
      }
      
      private function getPagesWithShortcode() {
        $result = [];
        
        $query = new \WP_Query([
          'post_type' => 'page',
          's' => "[kunta_api_mantyharju_elokuva_lista"  
        ]);

        while ($query->have_posts()) {
          $post = $query->next_post();
          $result[] = $post;
        }
        
        return $result;
      }
      
    }
  
  }
  
  add_action('kunta_api_init', function () {  
    new Shortcodes();
  });
  
?>