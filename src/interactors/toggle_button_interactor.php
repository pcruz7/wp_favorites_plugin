<?php

if (!class_exists('ToggleButtonInteractor'))
{
  class ToggleButtonInteractor
  {
    const REGEX  = '/\[favorite_button\]/';
    const BUTTON = '[favorite_button]';

    private $repository;

    function __construct($repository)
    {
      $this->repository = $repository;
    }

    public function togglePost($postId = null)
    {
      $post = $this->getPost($postId);
      if (!$post) {
        return false;
      }

      $this->hasButton($post) ? $this->removeFavoriteButton($post)
                              : $this->addFavoriteButton($post);
      return true;
    }

    public function activatePost($postId = null)
    {
      $post = $this->getPost($postId);
      if ($post && !$this->hasButton($post)) {
        $this->addFavoriteButton($post);
        return true;
      } else {
        return false;
      }
    }

    public function deactivatePost($postId = null)
    {
      $post = $this->getPost($postId);
      if ($post && $this->hasButton($post)) {
        $this->removeFavoriteButton($post);
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
      return (preg_match(self::REGEX, $post->getContent(), $match) ? true : false);
    }

    private function removeFavoriteButton($post)
    {
      $content = preg_replace(self::REGEX, '', $post->getContent());
      $this->repository->setContent($content, $post->getId());
    }

    private function addFavoriteButton($post)
    {
      $content = $post->getContent() . self::BUTTON;
      $this->repository->setContent($content, $post->getId());
    }
  }
}
