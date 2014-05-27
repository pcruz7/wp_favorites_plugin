<?php

if (!class_exists('DataModel'))
{
  class DataModel
  {
    private $content;
    private $title;
    private $permalink;
    private $id;

    function __construct($id, $title = '', $permalink = '', $content='')
    {
      $this->content   = $content;
      $this->title     = $title;
      $this->permalink = $permalink;
      $this->id        = $id;
    }

    public function getId()
    {
      return $this->id;
    }

    public function getTitle()
    {
      return $this->title;
    }

    public function getPermalink()
    {
      return $this->permalink;
    }

    public function getContent()
    {
      return $this->content;
    }

    public function toJson()
    {
      return array(
        'id'        => $this->id,
        'title'     => $this->title,
        'permalink' => $this->permalink,
        'content'   => $this->content
      );
    }
  }
}