<?php

require_once dirname(__FILE__) . '/../models/data_model.php';

if (!class_exists('PagesRepository'))
{
  class PagesRepository
  {
    function __construct() { }

    public function get($options = '')
    {
      $pages   = get_pages($options);
      $results = array();
      foreach ($pages as $page) {
        array_push($results, $this->getModel($page));
      }

      return $results;
    }

    public function getById($id)
    {
      if (!$id) return null;
      return $this->getModel(get_page($id));
    }

    public function setContent($content, $id)
    {
      if (!$id) return;

      wp_update_post(array(
        'ID'           => $id,
        'post_content' => $content
      ));
    }

    private function getModel($page)
    {
      return new DataModel($page->ID, $page->post_title, $page->guid, $page->post_content);
    }
  }
}