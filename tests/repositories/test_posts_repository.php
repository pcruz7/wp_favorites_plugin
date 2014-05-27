<?php

class TestPostsRepository extends WP_UnitTestCase
{
  private $repository;

  public function setUp()
  {
    parent::setUp();
    $this->repository = new PostsRepository();
  }

  public function tearDown()
  {
    parent::tearDown();
    unset($this->repository);
  }

  /**
   * repository get()
   */
  public function testGetPosts()
  {
    $posts   = $this->givenPostsExist(6);
    $results = $this->repository->get(array('orderby' => 'ID', 'order' => 'ASC', 'numberposts' => -1));
    $this->shouldReturnAllPosts($posts, $results);
  }

  private function shouldReturnAllPosts($posts, $results)
  {
    for ($i = 0; $i < count($posts); $i++) {
      $this->postShouldBeEqual($posts[$i], $results[$i], $i+1);
    }
  }

  /**
   * repository getById()
   */
  public function testGetPostById()
  {
    $posts  = $this->givenPostsExist(2);
    $result = $this->repository->getById($posts[0]);
    $this->postShouldBeEqual($posts[0], $result, 1);
  }

  /**
   * repository setContent()
   */
  public function testSetPostContent()
  {
    $posts = $this->givenPostsExist(2);
    $this->repository->setContent('The content', $posts[0]);
    $post  = $this->repository->getById($posts[0]);
    $this->assertEquals('The content', $post->getContent(), 'content should\'ve been modified');
  }

  private function givenPostsExist($how_many)
  {
    return $this->factory->post->create_many($how_many);
  }

  private function postShouldBeEqual($post, $result, $i)
  {
    $this->assertEquals($post                           , $result->getId()       , 'id\'s should be equal');
    $this->assertEquals('Post title ' . $i              , $result->getTitle()    , 'titles should be equal');
    $this->assertEquals('http://example.org/?p=' . $post, $result->getPermalink(), 'link should be equal');
    $this->assertEquals('Post content ' . $i            , $result->getContent()  , 'contents should be equal');
  }
}
