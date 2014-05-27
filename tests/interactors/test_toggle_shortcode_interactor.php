<?php

class TestToggleShortcodeInteractor extends WP_UnitTestCase
{
  const POST_ID = 1;
  const CONTENT = 'A content';
  const SHORTCODE = '[favorite_button]';
  const REGEX     = '/\[favorite_button\]/';
  private $interactor;
  private $repository;

  public function setUp()
  {
    parent::setUp();
    $this->repository = $this->getMock('PostsRepository');
    $this->interactor = new ToggleShortcodeInteractor($this->repository, self::SHORTCODE, self::REGEX);
  }

  public function tearDown()
  {
    unset($this->interactor);
    unset($this->repository);
    parent::tearDown();
  }

  /**
   * interactor toggleShortcode()
   */
  public function testToggleShortcodeWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with((self::CONTENT . self::SHORTCODE), self::POST_ID);
    $this->assertTrue($this->interactor->toggleShortcode(self::POST_ID), 'should return true when toggling was successful');
  }

  public function testToggleShortcodeWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::SHORTCODE);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT, self::POST_ID);
    $this->assertTrue($this->interactor->toggleShortcode(self::POST_ID), 'should return true when toggling was successful');
  }

  public function testToggleShortcodeWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->toggleShortcode(self::POST_ID), 'should return false when there is no post');
  }

  /**
   * interactor activateShortcode()
   */
  public function testActivateShortcodeWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT . self::SHORTCODE, self::POST_ID);
    $this->assertTrue($this->interactor->activateShortcode(self::POST_ID), 'should return true when activation was successful');
  }

  public function testActivateShortcodeWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::SHORTCODE);
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->activateShortcode(self::POST_ID), 'should return false when was already activated');
  }

  public function testActivateShortcodeWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->activateShortcode(self::POST_ID), 'should return false when there is no post');
  }

  /**
   * interactor deactivateShortcode()
   */
  public function testDeactivateShortcodeWhenPostExistsAndHasFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT . self::SHORTCODE);
    $this->repository->expects($this->once())
                     ->method('setContent')
                     ->with(self::CONTENT, self::POST_ID);
    $this->assertTrue($this->interactor->deactivateShortcode(self::POST_ID), 'should return true when deactivation was successful');
  }

  public function testDeactivateShortcodeWhenPostExistsAndHasNoFavoriteButton()
  {
    $this->givenPostExists(self::CONTENT);
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->deactivateShortcode(self::POST_ID), 'should return false when was already deactivated');
  }

  public function testDeactivateShortcodeWhenPostDoesNotExist()
  {
    $this->givenPostDoesNotExist();
    $this->assertContentIsNotUpdated();
    $this->assertFalse($this->interactor->deactivateShortcode(self::POST_ID), 'should return false when there is no post');
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