<?php

require_once dirname(__FILE__) . '/../models/data_model.php';

if (!class_exists('PostsRepository'))
{
  class PostsRepository
  {
    function __construct() { }

    public function get($options = '')
    {
      $posts   = get_posts($options);
      $results = array();
      foreach ($posts as $post) {
        array_push($results, $this->getModel($post));
      }

      return $results;
    }

    public function getById($id)
    {
      if (!$id) return null;
      return $this->getModel(get_post($id));
    }

    public function setContent($content, $id)
    {
      if (!$id) return;

      wp_update_post(array(
        'ID'           => $id,
        'post_content' => $content
      ));
    }

    private function getModel($post)
    {
      return new DataModel($post->ID, $post->post_title, $post->guid, $post->post_content);
    }
  }
}