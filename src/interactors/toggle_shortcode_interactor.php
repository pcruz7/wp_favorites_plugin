<?php

if (!class_exists('ToggleShortcodeInteractor'))
{
  class ToggleShortcodeInteractor
  {
    private $repository;
    private $shortcode;
    private $regex;

    function __construct($repository, $shortcode, $regex = '')
    {
      $this->repository = $repository;
      $this->shortcode  = $shortcode;
      $this->regex      = $regex;
    }

    public function toggleShortcode($postId = null)
    {
      $post = $this->getPost($postId);
      if (!$post) {
        return false;
      }

      $this->hasButton($post) ? $this->removeShortcode($post)
                              : $this->addShortcode($post);
      return true;
    }

    public function activateShortcode($postId = null)
    {
      $post = $this->getPost($postId);
      if ($post && !$this->hasButton($post)) {
        $this->addShortcode($post);
        return true;
      } else {
        return false;
      }
    }

    public function deactivateShortcode($postId = null)
    {
      $post = $this->getPost($postId);
      if ($post && $this->hasButton($post)) {
        $this->removeShortcode($post);
        return true;
      } else {
        return false;
      }
    }

    private function getPost($postId)
    {
      if (!isset($postId)) {
        return false;
      }

      $post = $this->repository->getById($postId);
      if(!isset($post)) {
        return false;
      }

      return $post;
    }

    private function hasButton($post)
    {
      return (preg_match($this->regex, $post->getContent(), $match) ? true : false);
    }

    private function removeShortcode($post)
    {
      $content = preg_replace($this->regex, '', $post->getContent());
      $this->repository->setContent($content, $post->getId());
    }

    private function addShortcode($post)
    {
      $content = $post->getContent() . $this->shortcode;
      $this->repository->setContent($content, $post->getId());
    }
  }
}
