<?php

class TestPagesRepository extends WP_UnitTestCase
{
  private $repository;

  public function setUp()
  {
    parent::setUp();
    $this->repository = new PagesRepository();
  }

  public function tearDown()
  {
    unset($this->repository);
    parent::tearDown();
  }

 /**
   * repository get()
   */
  public function testGetPages()
  {
    $pages   = $this->givenPagesExist(6);
    $results = $this->repository->get(array('orderby' => 'ID', 'order' => 'ASC', 'numberposts' => -1));
    $this->shouldReturnAllPages($pages, $results);
  }

  private function shouldReturnAllPages($pages, $results)
  {
    for ($i = 0; $i < count($pages); $i++) {
      $this->pageShouldBeEqual($pages[$i], $results[$i], $i+1);
    }
  }

  /**
   * repository getById()
   */
  public function testGetPageById()
  {
    $pages  = $this->givenPagesExist(2);
    $result = $this->repository->getById($pages[0]);
    $this->pageShouldBeEqual($pages[0], $result, 1);
  }

  /**
   * repository setContent()
   */
  public function testSetPageContent()
  {
    $pages = $this->givenPagesExist(2);
    $this->repository->setContent('The content', $pages[0]);
    $page  = $this->repository->getById($pages[0]);
    $this->assertEquals('The content', $page->getContent(), 'content should\'ve been modified');
  }

  private function givenPagesExist($how_many)
  {
    return $this->factory->post->create_many($how_many);
  }

  private function pageShouldBeEqual($page, $result, $i)
  {
    $this->assertEquals($page                           , $result->getId()       , 'id\'s should be equal');
    $this->assertEquals('Post title ' . $i              , $result->getTitle()    , 'titles should be equal');
    $this->assertEquals('http://example.org/?p=' . $page, $result->getPermalink(), 'link should be equal');
    $this->assertEquals('Post content ' . $i            , $result->getContent()  , 'contents should be equal');
  }
}
