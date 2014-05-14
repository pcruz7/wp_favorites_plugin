<?php

class TestFavoritesPlugin extends WP_UnitTestCase
{
  private $plugin;
  private $userID;

  public function setUp()
  {
    parent::setUp();
    $this->plugin = $GLOBALS[FavoritesPlugin::SLUG];
  }

  function tearDown ()
  {
    parent::tearDown();
    unset($this->plugin);
  }

  /**
   * Plugin init()
   */
	public function testInit()
  {
    $actions = array(
      'wp_ajax_is_favorite',
      'wp_ajax_nopriv_is_favorite',
      'wp_ajax_toggle_favorite',
      'wp_ajax_nopriv_toggle_favorite',
      'wp_ajax_favorite_list',
      'wp_ajax_nopriv_favorite_list',
      'wp_ajax_get_by_id',
      'wp_ajax_nopriv_get_by_id',
      'wp_login'
    );
    FavoritesPlugin::init();
    $this->assertJsIsRegistered();
    $this->assertJsIsEnqueued();
    $this->assertActionsAreAdded($actions);
	}

  private function assertJsIsRegistered()
  {
    $this->assertTrue(wp_script_is('favorites', 'registered'), 'favorites script should be registered');
  }

  private function assertJsIsEnqueued()
  {
    $this->assertTrue(wp_script_is('jquery', 'enqueued'), 'jQuery should be enqueued');
    $this->assertTrue(wp_script_is('favorites', 'enqueued'), 'favorites script should be enqueued');
  }

  private function assertActionsAreAdded($actions)
  {
    foreach ($actions as $action) {
      $this->assertTrue(has_action($action), $action . ' action should be added');
    }
  }

  /**
   * Plugin isFavorite()
   */
  public function testIsFavorite()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $this->givenFavoritedPosts($posts);
    $this->onRequest(array('post_id' => $posts[0]));
    $this->expectResponse(true);
    $this->plugin->isFavorite();
  }

  /**
   * Plugin toggleFavorite()
   */
  public function testToggleFavoriteWhenPostIsFavorited()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $this->givenFavoritedPosts($posts);
    $this->onRequest(array('post_id' => $posts[0]));
    $this->expectResponse(array('post_id' => -1));
    $this->plugin->toggleFavorite();
  }

  /**
   */
  public function testToggleFavoriteWhenPostIsNotFavorited()
  {
    $this->givenUserIsLoggedIn();
    $this->givenFavoritedPosts();
    $this->onRequest(array('post_id' => 1));
    $this->expectResponse(array('post_id' => 1));
    $this->plugin->toggleFavorite();
  }

  /**
   * Plugin favoriteList()
   */
  public function testFavoriteList()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $this->givenFavoritedPosts($posts);
    $this->onRequest(array('paged' => 1, 'posts_per_page' => 5, 'order' => 'ASC', 'orderby' => 'post__in'));
    $this->expectResponse(array(
      array('id' => $posts[0], 'title' => 'Post title 1', 'permalink' => 'http://example.org/?p=' . $posts[0]),
      array('id' => $posts[1], 'title' => 'Post title 2', 'permalink' => 'http://example.org/?p=' . $posts[1])
    ));
    $this->plugin->favoriteList();
  }

  /**
   * Plugin getById()
   */
  public function testGetById()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $this->onRequest(array('post_id' => $posts[0], 'json' => true));
    $this->expectResponse(array('id' => $posts[0], 'title' => 'Post title 1', 'permalink' => 'http://example.org/?p=' . $posts[0]));
    $this->plugin->getById();
  }

  private function expectResponse($response)
  {
    $this->expectOutputString(json_encode($response));
  }

  private function onRequest($array)
  {
    $_POST = $array;
  }

  private function givenPostsExist()
  {
    return $this->factory->post->create_many(2);
  }

  private function givenUserIsLoggedIn()
  {
    $this->userID = $this->factory->user->create();
    wp_set_current_user($this->userID);
  }

  private function givenFavoritedPosts($array = array())
  {
    update_user_meta($this->userID, FavoritesPlugin::LISTNAME, $array);
  }
}