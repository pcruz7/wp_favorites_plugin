<?php

class TestAjaxinteractor extends WP_UnitTestCase
{
  private $interactor;
  private $repository;
  private $user;
  private $userId;

  const LISTNAME = 'favorites-list';

  public function setUp()
  {
    parent::setUp();
    $this->user       = $this->factory->user->create();
    $this->repository = $this->getMock('FavoritesRepository');
    $this->interactor = new FavoritesInteractor($this->repository, self::LISTNAME);
  }

  public function tearDown()
  {
    parent::tearDown();
    unset($this->interactor);
  }

  /**
   * interactor isFavorite()
   */
  public function testIsFavoriteWithFavoritedPost()
  {
    $this->givenUserIsLoggedIn();
    $this->givenPostIsFavorited();
    $response = $this->onIsFavoriteRequest();
    $this->assertTrue($response, 'interactor should return true for favorited post');
  }

  public function testIsFavoriteWithNotFavoritedPost()
  {
    $this->givenUserIsLoggedIn();
    $this->givenPostIsNotFavorited();
    $response = $this->onIsFavoriteRequest();
    $this->assertFalse($response, 'interactor should return false for un-favorited post');
  }

  private function onIsFavoriteRequest()
  {
    $request = array('post_id' => '1');
    return $this->interactor->isFavorite($request);
  }

  /**
   * interactor toggleFavorite()
   */
  public function testToggleFavoriteWithNotFavoritedPost()
  {
    $this->givenUserIsLoggedIn();
    $this->givenPostIsNotFavorited();

    $this->repository->expects($this->once())
                     ->method('saveFavoriteList')
                     ->with(self::LISTNAME, $this->userId, array(1, 4, 5, 6));
    $this->assertEquals($this->requestToToggleFavorite(), 1, 'should return the post id when toggling not favorited post');
  }

  public function testToggleFavoriteWithFavoritedPost()
  {
    $this->givenUserIsLoggedIn();
    $this->givenPostIsFavorited();

    $this->repository->expects($this->once())
                     ->method('saveFavoriteList')
                     ->with(self::LISTNAME, $this->userId, array(1 => 2, 2 => 3));
    $this->assertFalse($this->requestToToggleFavorite(), 'should return false when toggling favorited post');
  }

  private function requestToToggleFavorite()
  {
    $request = array('post_id' => '1');
    return $this->interactor->toggleFavorite($request);
  }

  /**
   * interactor favoriteList()
   */
  public function testFavoriteListWithExistingPosts()
  {
    $this->givenUserIsLoggedIn();
    $posts     = $this->givenPostsExist();
    $favorites = $this->requestToListFavorites();
    $this->assertEquals(count($posts), count($favorites), 'favorites should equal the created posts');
  }

  public function testFavoriteListWithNonExistingPosts()
  {
    $this->givenUserIsLoggedIn();
    $posts     = $this->givenPostsDoNotExist();
    $favorites = $this->requestToListFavorites();
    $this->assertEquals(count($posts), 0, 'favorites should be empty');
  }

  private function requestToListFavorites()
  {
    $request = array(
            'paged'          => 1,
            'posts_per_page' => 5,
            'order'          => 'ASC',
            'orderby'        => 'post__in');
    return $this->interactor->favoriteList($request);
  }

  /**
   * interactor getTotalFavorited()
   */
  public function testGetTotalFavorited()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $total = $this->interactor->getTotalFavorited();
    $this->assertEquals($total, count($posts), 'number of favorites should be ' . count($posts));
  }

  /**
   * interactor getFavoriteById()
   */
  public function testGetFavoriteByIdWithExistingPost()
  {
    $this->givenUserIsLoggedIn();
    $posts = $this->givenPostsExist();
    $post  = $this->requestToGetPostById($posts[0]);
    $this->assertEquals(1, count($post), 'should only return one result');
    $this->assertEquals($posts[0], $post->getId(), 'fetched post should equal the created one');
  }

  public function testGetFavoriteByIdWithNonExistingPost()
  {
    $this->givenUserIsLoggedIn();
    $this->givenPostsDoNotExist();
    $post = $this->requestToGetPostById('1');
    $this->assertFalse($post, 'should return false for non existing post');
  }

  private function requestToGetPostById($id)
  {
    $request = array('post_id' => $id);
    return $this->interactor->getFavoriteById($request);
  }

  private function givenPostsDoNotExist()
  {
    $posts = array();
    $this->stubRepository($posts);
    return $posts;
  }

  private function givenPostsExist()
  {
    $posts = $this->factory->post->create_many(2);
    $this->stubRepository($posts);
    return $posts;
  }

  /**
   * Favorites list state
   */
  private function givenPostIsFavorited()
  {
    $return = array_map('intval', array('1', '2', '3'));
    $this->stubRepository($return);
  }

  private function givenPostIsNotFavorited()
  {
    $return = array_map('intval', array('4', '5', '6'));
    $this->stubRepository($return);
  }

  private function stubRepository($array)
  {
    $this->repository->expects($this->any())
                     ->method('getFavoriteList')
                     ->with(self::LISTNAME, $this->userId)
                     ->will($this->returnValue($array));
  }

  /**
   * User logged state
   */
  private function givenUserIsLoggedIn()
  {
    $this->userLogged($this->user);
  }
  private function givenUserIsLoggedOut()
  {
    $this->userLogged(null);
  }

  private function userLogged($user)
  {
    $this->userId = $user;
    wp_set_current_user($this->userId);
  }
}
