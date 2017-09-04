<?php
  namespace KuntaAPI\Extensions\Mantyharju\Elokuvat;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'KuntaAPI\Extensions\Mantyharju\Elokuvat\Shortcodes' ) ) {
    
    class Shortcodes {
      
      private $twig;
      
      public function __construct() {
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem( __DIR__ . '/../templates'));
        add_shortcode('kunta_api_mantyharju_elokuva_lista', [$this, 'movieListShortcode']);
        add_shortcode('kunta_api_mantyharju_elokuva_lista_tulevat', [$this, 'upcomingMovieListShortcode']);
        add_action('edit_post', [$this, "onEditPost"]);
      }
      
      public function movieListShortcode($tagAttrs) {
        $attrs = shortcode_atts([
          'order' => "natural"
        ], $tagAttrs);
        
        $movies = $this->getMovieDatas($this->listMovies($attrs['order']), false);
        
        return $this->twig->render("movie-list.twig", [
          movies => $movies  
        ]);
      }
      
      public function upcomingMovieListShortcode() {
        $movies = $this->getMovieDatas($this->listMovies('date'), true);
        return $this->twig->render("upcoming-movie-list.twig", [
          movies => $movies  
        ]);
      }
      
      public function onEditPost($postId) {
        $postType = get_post_type($postId);
        if ($postType == 'mantyharju-elokuva') {
          foreach ($this->getPagesWithShortcodes(['kunta_api_mantyharju_elokuva_lista', 'kunta_api_mantyharju_elokuva_lista_tulevat']) as $page) {
            do_action('edit_post_related', $page->ID, $page);
          }
        }
      }
      
      private function getMovieDatas($movies, $onlyUpcoming) {
        $result = [];
        
        foreach ($movies as $movie) {
          $showtimes = [];
          $ageLimit = get_post_meta($movie->ID, "agelimit", true);
          $runTime = get_post_meta($movie->ID, "runtime", true);
          $price = get_post_meta($movie->ID, "ticketprice", true);
          $trailerUrl = get_post_meta($movie->ID, "trailerurl", true);
          $showtimeCount = get_post_meta($movie->ID, "showtimes", true);
          $director = get_post_meta($movie->ID, "director", true);
          $cast = get_post_meta($movie->ID, "cast", true);
          $imageId = get_post_thumbnail_id($movie->ID);
          $comingShows = false;
          $pastShows = false;
          $now = time();
          
          $oldTimezone = date_default_timezone_get();
          try {
            date_default_timezone_set("Europe/Helsinki");
            for ($i = 0; $i < $showtimeCount; $i++) {
              $showtime = strtotime(get_post_meta($movie->ID, "showtimes_" . $i . "_datetime", true));
              
              if ($showtime > $now) {
                $comingShows = true;
              } else {
                $pastShows = true;
              }
              
              $showtimes[] = date("c", $showtime);
            }
          } finally {
            date_default_timezone_set($oldTimezone);
          }
          
          if ($onlyUpcoming && $pastShows) {
            continue;
          }
          
          $classifications = [];
          $classificationIds = get_post_meta($movie->ID, "classification", true);
          if ($classificationIds) {
            foreach ($classificationIds as $classificationId) {
              $term = get_term(intval($classificationId));
              if ($term) {
                $classifications[] = [
                  name => $term->name,
                  slug => $term->slug
                ];
              }
            }
          }
          
          $result[] = [
            id => $movie->ID,
            imageId => $imageId,
            imageUrl => wp_get_attachment_url($imageId),
            title => $movie->post_title,
            showtimes => $showtimes,
            classifications => $classifications,
            ageLimit => $ageLimit,
            runTime => $runTime,
            price => $price,
            trailerUrl => $trailerUrl,
            description => $movie->post_content,
            director => $director,
            cast => $cast
          ]; 
        }
        
        return $result;
      }
      
      private function listMovies($order) {
        $listOptions = [
          'post_type' => 'mantyharju-elokuva',
          'numberposts' => -1
        ];
        
        switch ($order) {
          case 'title':
            $listOptions['orderby'] = 'title';
          break;
          case 'date':
            $listOptions['orderby'] = 'date';
          break;
        }
        
        return get_posts($listOptions); 
      }
      
      private function getPagesWithShortcodes($shortcodes) {
        $result = [];
        
        foreach ($shortcodes as $shortcode) {
          $pages = $this->getPagesWithShortcode($shortcode);
          $result = array_merge($result, $pages);
        }
        
        return $result;
      }
      
      private function getPagesWithShortcode($shortcode) {
        $result = [];
        
        $query = new \WP_Query([
          'post_type' => 'page',
          's' => "[$shortcode"  
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