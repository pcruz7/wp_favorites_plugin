<?php

class TestFavoritesRepository extends WP_UnitTestCase
{
  private $repository;
  private $userId;
  private $cookies;

  private $someList   = array(3, 4);
  private $listCookie = array(1, 2, 3);
  private $listRepo   = array('stuff', 'from', 'cookie');

  const LISTNAME = 'favorites-list';

  public function setUp()
  {
    parent::setUp();

    $this->cookies = $this->getMock('CookieHandler');
    $this->cookies->expects($this->any())
                  ->method('getCookie')
                  ->will($this->returnValue($this->listCookie));

    $this->repository = new FavoritesRepository($this->cookies);
  }

  public function tearDown()
  {
    parent::tearDown();
    unset($this->repository);
  }

  /**
   * Repository getFavoriteList()
   */
  public function testGetFavoriteList()
  {
    $this->whenUserIsLoggedOut();
    $this->assertListIsFetchedFromCookie();

    $this->whenUserIsLoggedIn();
    $this->assertListIsFetchedFromMetadata();
  }

  private function assertListIsFetchedFromCookie()
  {
    $list = $this->repository->getFavoriteList(self::LISTNAME, $this->userId);
    $this->assertEquals(array_map('intval', $this->listCookie), $list);
    $this->assertNotEquals(array_map('intval', $this->listRepo), $list);
  }

  private function assertListIsFetchedFromMetadata()
  {
    update_user_meta($this->userId, self::LISTNAME, $this->listRepo);
    $list = $this->repository->getFavoriteList(self::LISTNAME, $this->userId);
    $this->assertEquals(array_map('intval', $this->listRepo), $list);
    $this->assertNotEquals(array_map('intval', $this->listCookie), $list);
    delete_user_meta($this->userId, self::LISTNAME, $this->listRepo);
  }

  /**
   * Repository setFavoriteList()
   */
  public function testSetFavoriteList()
  {
    $this->whenUserIsLoggedOut();
    $this->assertListIsSavedIntoCookie();

    $this->whenUserIsLoggedIn();
    $this->assertListIsSavedIntoMetadata();
  }

  private function assertListIsSavedIntoCookie()
  {
    $this->cookies->expects($this->once())
                  ->method('setCookie')
                  ->with(self::LISTNAME, array_map('intval', $this->someList));
    $this->repository->saveFavoriteList(self::LISTNAME, $this->userId, $this->someList);
  }

  private function assertListIsSavedIntoMetadata()
  {
    $this->repository->saveFavoriteList(self::LISTNAME, $this->userId, $this->someList);
    $list = get_user_meta($this->userId, self::LISTNAME, true);
    $this->assertEquals(array_map('intval', $this->someList), $list);
  }

  /**
   * Repository initFavoriteList()
   */
  public function testInitFavoriteListWithLoggedInUser()
  {
    $this->whenUserIsLoggedIn();
    $this->repository->saveFavoriteList(self::LISTNAME, $this->userId, $this->someList);
    $this->assertCookieIsErased();
    $this->repository->initFavoriteList(self::LISTNAME, $this->userId);
    $this->assertListIsMerged();
  }

  public function testInitFavoriteListWithLoggedOut()
  {
    $this->whenUserIsLoggedIn();
    $this->repository->saveFavoriteList(self::LISTNAME, $this->userId, $this->someList);
    $user = $this->userId;

    $this->whenUserIsLoggedOut();
    $this->repository->initFavoriteList(self::LISTNAME, $this->userId);
    $this->assertListIsNotMerged($user);
  }

  private function assertListIsNotMerged($user)
  {
    $list = get_user_meta($user, self::LISTNAME, true);
    $this->assertEquals($this->someList, $list, 'list should not be merged');
  }

  private function assertListIsMerged()
  {
    $list   = get_user_meta($this->userId, self::LISTNAME, true);
    $merged = array_merge($this->someList, $this->listCookie);
    $this->assertEquals(array_unique($merged), $list, 'list should be merged');
  }

  private function assertCookieIsErased()
  {
    $this->cookies->expects($this->once())
                  ->method('getCookie')
                  ->with(self::LISTNAME);
  }

  /**
   * User logged state
   */
  private function whenUserIsLoggedOut()
  {
    $this->userId = null;
  }

  private function whenUserIsLoggedIn()
  {
    $this->userId = $this->factory->user->create();
  }
}
