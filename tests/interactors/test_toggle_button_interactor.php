<?php

class TestToggleButtonInteractor extends WP_UnitTestCase
{
  const POST_ID = 1;
  const CONTENT = 'A content';
  const BUTTON = '[favorite_button]';
  private $interactor;
  private $repository;

  public function setUp()
  {
    parent::setUp();
    $this->repository = $this->getMock('PostsRepository');
    $this->interactor = new ToggleButtonInteractor($this->repository);
  }

  public function tearDown()
  {
    unset($this->interactor);
    unset($this->repository);
    parent::tearDown();
  }

  /**
   * interactor togglePost()
   */
  public function testTogglePostWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with((self::CONTENT . self::BUTTON), self::POST_ID);
    $this->assertTrue($this->interactor->togglePost(self::POST_ID), 'should return true when toggling was successful');
  }

  public function testTogglePostWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::BUTTON);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT, self::POST_ID);
    $this->assertTrue($this->interactor->togglePost(self::POST_ID), 'should return true when toggling was successful');
  }

  public function testTogglePostWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->togglePost(self::POST_ID), 'should return false when there is no post');
  }

  /**
   * interactor activatePost()
   */
  public function testActivatePostWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT . self::BUTTON, self::POST_ID);
    $this->assertTrue($this->interactor->activatePost(self::POST_ID), 'should return true when activation was successful');
  }

  public function testActivatePostWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::BUTTON);
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->activatePost(self::POST_ID), 'should return false when was already activated');
  }

  public function testActivatePostWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->activatePost(self::POST_ID), 'should return false when there is no post');
  }

  /**
   * interactor deactivatePost()
   */
  public function testDeactivatePostWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::BUTTON);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT, self::POST_ID);
    $this->assertTrue($this->interactor->deactivatePost(self::POST_ID), 'should return true when deactivation was successful');
  }

  public function testDeactivatePostWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->deactivatePost(self::POST_ID), 'should return false when was already deactivated');
  }

  public function testDeactivatePostWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->deactivatePost(self::POST_ID), 'should return false when there is no post');
  }

  private function givenPostExists($content)
  {
    $this->repository->expects($this->once())
                     ->method('getById')
                     ->will($this->returnValue($this->getModel($content)));
  }

  private function givenPostDoesNotExist()
  {
    $this->repository->expects($this->once())
                     ->method('getById')
                     ->will($this->returnValue(null));
  }

  private function assertContentIsNotUpdated()
  {
    $this->repository->expects($this->never())
                     ->method('setContent');
  }

  private function getModel($content)
  {
    return new DataModel(self::POST_ID, 'title', self::POST_ID, $content);
  }
}