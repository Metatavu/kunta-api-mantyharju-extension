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
        add_action('edit_post', [$this, "onEditPost"]);
      }
      
      public function movieListShortcode($tagAttrs) {
        $oldTimezone = date_default_timezone_get();
        try {
          date_default_timezone_set("Europe/Helsinki");
        
          $attrs = shortcode_atts([
            'order' => "natural",
            'without-showtimes' => "false",
            'first-showtime-after' => null,
            'last-showtime-after' => null,
            'template' => 'movie-list'
          ], $tagAttrs);

          $movies = $this->getMovieDatas($this->listMovies($attrs['order']), 
            $attrs['without-showtimes'] == "true", 
            $attrs['first-showtime-after'], 
            $attrs['last-showtime-after']
          );

          $template = $attrs['template'];

          return $this->twig->render("$template.twig", [
            movies => $movies  
          ]);
        } finally {
          date_default_timezone_set($oldTimezone);
        }
      }
      
      public function onEditPost($postId) {
        $postType = get_post_type($postId);
        if ($postType == 'mantyharju-elokuva') {
          foreach ($this->getPagesWithShortcodes(['kunta_api_mantyharju_elokuva_lista']) as $page) {
            do_action('edit_post_related', $page->ID, $page);
          }
        }
      }
      
      private function getMovieDatas($movies, $withoutShowtimes, $firstShowtimeAfter, $lastShowtimeAfter) {
        $result = [];
        
        $firstShowtimeAfterTime = $firstShowtimeAfter ? strtotime($firstShowtimeAfter) : null;
        $lastShowtimeAfterTime = $lastShowtimeAfter ? strtotime($lastShowtimeAfter) : null;
        
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
          $firstShowtime = null;
          $lastShowtime = null;
          
          $oldTimezone = date_default_timezone_get();
          try {
            date_default_timezone_set("Europe/Helsinki");
            for ($i = 0; $i < $showtimeCount; $i++) {
              $showtime = strtotime(get_post_meta($movie->ID, "showtimes_" . $i . "_datetime", true));
              $firstShowtime = $firstShowtime == null ? $showtime : min($firstShowtime, $showtime);
              $lastShowtime = $lastShowtime == null ? $showtime : max($lastShowtime, $showtime);
              $showtimes[] = date("c", $showtime);
            }
          } finally {
            date_default_timezone_set($oldTimezone);
          }
          
          if ($this->isFilteredByShowtimes($withoutShowtimes, $firstShowtimeAfterTime, $lastShowtimeAfterTime, $firstShowtime, $lastShowtime)) {
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
   
      private function isFilteredByShowtimes($withoutShowtimes, $firstShowtimeAfterTime, $lastShowtimeAfterTime, $firstShowtime, $lastShowtime) {
        if ($withoutShowtimes && $firstShowtime == null && $lastShowtime == null) {
          // Allow without showtimes
          return false;
        }
        
        if (!$withoutShowtimes && $firstShowtime == null && $lastShowtime == null) {
          // Not allowed without showtimes
          return true;
        }

        if (($lastShowtimeAfterTime != null) && ((!$lastShowtime) || ($lastShowtimeAfterTime > $lastShowtime))) {
          return true;
        }

        if ($firstShowtimeAfterTime != null && ((!$firstShowtime) || ($firstShowtimeAfterTime > $firstShowtime))) {
          return true;
        }
        
        return false;
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