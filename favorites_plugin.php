<?php
/*
Plugin Name: Favorites Plugin
Description: Use this plugin to save your favorite posts
Author: Pedro
Co-Author: Log
Version: 1.0
License: GPL2
*/

define( 'VERSION', '1.0' );

if (!class_exists('FavoritesPlugin'))
{
  require_once(sprintf('%s/src/repositories/favorites_repository.php', dirname(__FILE__)));
  require_once(sprintf('%s/src/interactors/favorites_interactor.php', dirname(__FILE__)));

  class FavoritesPlugin
  {
    const SLUG     = 'wp-favorites-plugin';
    const LISTNAME = 'favorites-list';

    private $interactor;
    private static $instance = null;

    function __construct($interactor)
    {
      $this->interactor = $interactor;
    }

    public static function activate() { }

    public static function init()
    {
      if (!self::$instance) {
        $repository     = new FavoritesRepository(new CookieHandler());
        $interactor     = new FavoritesInteractor($repository, FavoritesPlugin::LISTNAME);
        self::$instance = new self($interactor);
      }
      self::registerStyles();
      self::registerScripts();
      self::addActions();

      return self::$instance;
    }

    public function isFavorite()
    {
      $output = $this->interactor->isFavorite($_POST);
      $this->response($output);
    }

    public function toggleFavorite()
    {
      $output   = $this->interactor->toggleFavorite($_POST);
      $response = ($output ? array('post_id' => $output)
                           : array('post_id' => -1));
      $this->response($response);
    }

    public function favoriteList()
    {
      $posts   = $this->interactor->favoriteList($_POST);
      $results = array();
      foreach ($posts as $post) {
        array_push($results, $post->toJson());
      }
      $total = $this->interactor->getTotalFavorited();
      $response = array(
        'results'  => $results,
        'page'     => (int) $_POST['paged'],
        'per_page' => (int) $_POST['posts_per_page'],
        'has_more' => (((int) $_POST['paged']) * ((int) $_POST['posts_per_page']) < $total),
        'total'    => $total
      );
      $this->response($response);
    }

    public function getById()
    {
      $favorite = $this->interactor->getFavoriteById($_POST);
      $favorite ? $this->response($favorite->toJson())
                : $this->response($favorite);
    }

    public function initList($user_login, $user)
    {
      $this->interactor->initList($user);
    }

    private static function registerStyles()
    {
      wp_register_style('favorites', plugins_url('src/css/styles.css', __FILE__), false, VERSION, 'screen');
      wp_enqueue_style('favorites');
    }

    private static function registerScripts()
    {
      wp_register_script('handlebars', plugins_url('src/js/handlebars.js', __FILE__), null, VERSION, false);
      wp_register_script('handlebars-helpers', plugins_url('src/js/handlebars_helpers.js', __FILE__), array('handlebars'), VERSION, false);
      wp_register_script('favorites', plugins_url('src/js/favorites.js', __FILE__), array('jquery', 'handlebars-helpers'), VERSION, false);

      wp_localize_script('favorites', 'FavoritesAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

      wp_enqueue_script('handlebars');
      wp_enqueue_script('handlebars-helpers');
      wp_enqueue_script('jquery');
      wp_enqueue_script('favorites');
    }

    private static function addActions()
    {
      add_action('wp_ajax_is_favorite'           , array(self::$instance, 'isFavorite'));
      add_action('wp_ajax_nopriv_is_favorite'    , array(self::$instance, 'isFavorite'));
      add_action('wp_ajax_toggle_favorite'       , array(self::$instance, 'toggleFavorite'));
      add_action('wp_ajax_nopriv_toggle_favorite', array(self::$instance, 'toggleFavorite'));
      add_action('wp_ajax_favorite_list'         , array(self::$instance, 'favoriteList'));
      add_action('wp_ajax_nopriv_favorite_list'  , array(self::$instance, 'favoriteList'));
      add_action('wp_ajax_get_by_id'             , array(self::$instance, 'getById'));
      add_action('wp_ajax_nopriv_get_by_id'      , array(self::$instance, 'getById'));
      add_action('wp_login'                      , array(self::$instance, 'initList'), 20, 2);
    }

    private function response($output)
    {
      header('Content-Type: application/json');
      echo json_encode($output);
      exit();
    }
  }

  $repository = new FavoritesRepository(new CookieHandler());
  $interactor = new FavoritesInteractor($repository, FavoritesPlugin::LISTNAME);
  $GLOBALS[FavoritesPlugin::SLUG] = new FavoritesPlugin($interactor);
}

if (class_exists('FavoritesPlugin'))
{
  register_activation_hook(__FILE__, array('FavoritesPlugin', 'activate'));
  add_action('init', array('FavoritesPlugin', 'init'));

  require_once(plugin_dir_path(__FILE__) . 'src/shortcodes/favorite_list_shortcode.php');
  require_once(plugin_dir_path(__FILE__) . 'src/shortcodes/favorite_toggle_button_shortcode.php');
  require_once(plugin_dir_path(__FILE__) . 'src/widgets/favorites_widget.php');
}
