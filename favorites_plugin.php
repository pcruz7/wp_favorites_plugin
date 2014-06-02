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
  require_once(dirname(__FILE__) . '/src/repositories/favorites_repository.php');
  require_once(dirname(__FILE__) . '/src/repositories/posts_repository.php');
  require_once(dirname(__FILE__) . '/src/repositories/pages_repository.php');
  require_once(dirname(__FILE__) . '/src/interactors/favorites_interactor.php');
  require_once(dirname(__FILE__) . '/src/interactors/toggle_shortcode_interactor.php');

  class FavoritesPlugin
  {
    const SLUG     = 'wp-favorites-plugin';
    const LISTNAME = 'favorites-list';
    const FAVORITE_BUTTON = '[favorite_button]';
    const FAVORITE_REGEX  = '/\[favorite_button\]/';
    const LIST_REGEX  = '/\[favorite_list.*\]/';

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
      self::addAjaxActions();
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

    public function togglePost()
    {
      $this->toggleIt(new PostsRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function activatePost()
    {
      $this->activateIt(new PostsRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function deactivatePost()
    {
      $this->deactivateIt(new PostsRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function togglePageButton()
    {
      $this->toggleIt(new PagesRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function activatePageButton()
    {
      $this->activateIt(new PagesRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function deactivatePageButton()
    {
      $this->deactivateIt(new PagesRepository(), self::FAVORITE_BUTTON, self::FAVORITE_REGEX);
    }

    public function togglePageList()
    {
      $this->toggleIt(new PagesRepository(), $this->genListShortcode(), self::LIST_REGEX);
    }

    public function activatePageList()
    {
      $this->activateIt(new PagesRepository(), $this->genListShortcode(), self::LIST_REGEX);
    }

    public function deactivatePageList()
    {
      $this->deactivateIt(new PagesRepository(), $this->genListShortcode(), self::LIST_REGEX);
    }

    public function themeSettingsInit()
    {
      register_setting('theme_settings', 'theme_settings');
    }

    public function addSettingsPage()
    {
      add_menu_page(__('Favorites Panel'), __('Favorites Panel'), 'manage_options', 'settings', array($this, 'postsPanel'));
      add_submenu_page('settings', __('Posts Panel'), __('Posts Panel'), 'manage_options', 'posts', array($this, 'postsPanel'));
      add_submenu_page('settings', __('Pages Panel'), __('Pages Panel'), 'manage_options', 'pages', array($this, 'pagesPanel'));
    }

    public function postsPanel()
    {
      $repository = new PostsRepository();
      $results = $repository->get(array('numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
      $include_listing = false;
      include(plugin_dir_path(__FILE__) . '/src/views/plugin_settings_view.php');
    }

    public function pagesPanel()
    {
      $repository = new PagesRepository();
      $results = $repository->get(array('numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
      $include_listing = true;
      include(plugin_dir_path(__FILE__) . '/src/views/plugin_settings_view.php');
    }

    private function toggleIt($repository, $shortcode, $regex)
    {
      $id = $_POST['ids'];
      $interactor = new ToggleShortcodeInteractor($repository, $shortcode, $regex);
      $response = $interactor->toggleShortcode($id) ? $id : -1;
      $this->response(array('toggled_id' => $response));
    }

    private function activateIt($repository, $shortcode, $regex)
    {
      $response = $this->withShortcodeInteractor(function ($interactor, $id) {
        return $interactor->activateShortcode($id) ? $id : -1;
      }, $repository, $shortcode, $regex);
      $this->response(array('activated_id' => $response));
    }

    private function deactivateIt($repository, $shortcode, $regex)
    {
      $response = $this->withShortcodeInteractor(function ($interactor, $id) {
        return $interactor->deactivateShortcode($id) ? $id : -1;
      }, $repository, $shortcode, $regex);
      $this->response(array('deactivated_id' => $response));
    }

    private function withShortcodeInteractor($do_action, $repository, $shortcode, $regex)
    {
      $toggler  = new ToggleShortcodeInteractor($repository, $shortcode, $regex);
      $response = array();
      foreach ($_POST['ids'] as $id) {
        $id = $do_action($toggler, (int) $id);
        array_push($response, $id);
      }

      return $response;

    public function initAdmin()
    {
      if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
        add_filter('mce_buttons'         , array($this, 'mceButtons'));
        add_filter('mce_external_plugins', array($this, 'mcePlugins'));
      }
    }

    public function mceButtons ($buttons)
    {
      array_push($buttons, 'separator', 'favorite_button_key');
      array_push($buttons, 'separator', 'favorite_list_button_key');
      return $buttons;
    }

    public function mcePlugins ($plugins)
    {
      $plugins['favorites_plugin'] = plugins_url('src/js/plugins/favorites_plugin.js', __FILE__);
      return $plugins;
    }

    private function genListShortcode()
    {
      $widget    = new FavoritesWidget();
      $settings  = $widget->get_settings();
      $num_posts = $settings[2]['number'];
      return '[favorite_list posts_per_page=' . $num_posts . ' order="ASC"]';
    }

    private static function registerStyles()
    {
      wp_register_style('favorites', plugins_url('src/css/styles.css', __FILE__), false, VERSION, 'screen');
      wp_enqueue_style('favorites');
    }

    private static function registerScripts()
    {
      wp_register_script('handlebars', plugins_url('src/js/libs/handlebars.js', __FILE__), null, VERSION, false);
      wp_register_script('handlebars-helpers', plugins_url('src/js/handlebars_helpers/handlebars_helpers.js', __FILE__), array('handlebars'), VERSION, false);
      wp_register_script('favorites', plugins_url('src/js/favorites.js', __FILE__), array('jquery', 'handlebars-helpers'), VERSION, false);
      wp_register_script('toggle', plugins_url('src/js/toggle.js', __FILE__), array('jquery'), VERSION, false);

      wp_localize_script('favorites', 'FavoritesAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
      wp_localize_script('toggle', 'ToggleAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

      wp_enqueue_script('handlebars');
      wp_enqueue_script('handlebars-helpers');
      wp_enqueue_script('jquery');
      wp_enqueue_script('favorites');
      wp_enqueue_script('toggle');
    }

    private static function addActions()
    {
      add_action('wp_login'  , array(self::$instance, 'initList'), 20, 2);
      add_action('admin_init', array(self::$instance, 'themeSettingsInit'));
      add_action('admin_menu', array(self::$instance, 'addSettingsPage'));
    }

    private static function addAjaxActions()
    {
      # user's favorite list
      add_action('wp_ajax_is_favorite'           , array(self::$instance, 'isFavorite'));
      add_action('wp_ajax_nopriv_is_favorite'    , array(self::$instance, 'isFavorite'));
      add_action('wp_ajax_toggle_favorite'       , array(self::$instance, 'toggleFavorite'));
      add_action('wp_ajax_nopriv_toggle_favorite', array(self::$instance, 'toggleFavorite'));
      add_action('wp_ajax_favorite_list'         , array(self::$instance, 'favoriteList'));
      add_action('wp_ajax_nopriv_favorite_list'  , array(self::$instance, 'favoriteList'));
      add_action('wp_ajax_get_by_id'             , array(self::$instance, 'getById'));
      add_action('wp_ajax_nopriv_get_by_id'      , array(self::$instance, 'getById'));
      add_action('wp_login'                      , array(self::$instance, 'initList'), 20, 2);
      add_action('admin_init'                    , array(self::$instance, 'initAdmin'));

      # admin post control and favorite button toggle
      add_action('wp_ajax_toggle_post'           , array(self::$instance, 'togglePost'));
      add_action('wp_ajax_activate_post'         , array(self::$instance, 'activatePost'));
      add_action('wp_ajax_deactivate_post'       , array(self::$instance, 'deactivatePost'));

      # admin page control and favorite button toggle
      add_action('wp_ajax_toggle_page_button'    , array(self::$instance, 'togglePageButton'));
      add_action('wp_ajax_activate_page_button'  , array(self::$instance, 'activatePageButton'));
      add_action('wp_ajax_deactivate_page_button', array(self::$instance, 'deactivatePageButton'));
      add_action('wp_ajax_toggle_page_list'      , array(self::$instance, 'togglePageList'));
      add_action('wp_ajax_activate_page_list'    , array(self::$instance, 'activatePageList'));
      add_action('wp_ajax_deactivate_page_list'  , array(self::$instance, 'deactivatePageList'));
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
