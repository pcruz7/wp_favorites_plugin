<?php

if (!class_exists('FavoritesInteractor'))
{
  class FavoritesInteractor
  {
    private $repository;
    private $listname;

    function __construct($repository, $listname)
    {
      $this->repository = $repository;
      $this->listname   = $listname;
    }

    public function isFavorite($request)
    {
      $userId    = $this->currentUser();
      $favorites = $this->repository->getFavoriteList($this->listname, $userId );
      $index     = array_search((int) $request['post_id'], $favorites);
      return $this->indexExists($index);
    }

    public function toggleFavorite($request)
    {
      $userId    = $this->currentUser();
      $favorites = $this->repository->getFavoriteList($this->listname, $userId );
      $index     = array_search((int) $request['post_id'], $favorites);
      $this->indexExists($index) ? $this->removeFromFavorites($userId , $favorites, $index)
                                 : $this->addToFavorites($userId , $favorites, (int) $request['post_id']);
      return ($this->indexExists($index) ? false : (int) $request['post_id']);
    }

    public function favoriteList($request)
    {
      $userId    = $this->currentUser();
      $favorites = $this->repository->getFavoriteList($this->listname, $userId);
      return (empty($favorites) ? array()
                                : $this->getFavorites($favorites, $request));
    }

    public function getFavoriteById($request)
    {
      $args = array(
        'p'           => $request['post_id'],
        'post_type'   => get_post_types(array('public' => true)),
        'post_status' => 'publish');
      $query = new WP_Query($args);
      return $query->have_posts() ? $this->getPost($query)
                                  : false;
    }

    public function getTotalFavorited()
    {
      $userId = $this->currentUser();
      return count($this->repository->getFavoriteList($this->listname, $userId));
    }

    public function initList(WP_User $user = null)
    {
      $this->repository->initFavoriteList($this->listname, $user->ID);
    }

    private function currentUser()
    {
      $user = wp_get_current_user();
      return is_user_logged_in() ? $user->ID : null;
    }

    private function indexExists($index)
    {
      return !($index === false or !isset($index));
    }

    private function getPost($query)
    {
      $query->the_post();
      return new PostData(get_the_id(), get_the_title(), get_the_permalink());
    }

    private function getFavorites($favorites, $request)
    {
      extract($request);
      $paged          = (int) $paged;
      $posts_per_page = (int) $posts_per_page;
      $q = array(
        'post_type'      => get_post_types(array('public' => true)),
        'post__in'       => $favorites,
        'paged'          => $paged,
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'order'          => $order,
        'orderby'        => $orderby
      );

      $query   = new WP_Query($q);
      $results = array();
      while ($query->have_posts()) {
        $query->the_post();
        array_push($results, new PostData(get_the_ID(), get_the_title(), get_the_permalink()));
      }

      return $results;
    }

    private function removeFromFavorites($user, $favorites, $key)
    {
      unset($favorites[$key]);
      $this->repository->saveFavoriteList($this->listname, $user, $favorites);
    }

    private function addToFavorites($user, $favorites, $postID)
    {
      array_unshift($favorites, $postID);
      $favorites = array_unique($favorites);
      $this->repository->saveFavoriteList($this->listname, $user, $favorites);
    }
  }
}

if (!class_exists('PostData'))
{
  class PostData
  {
    private $title;
    private $permalink;
    private $id;

    function __construct($id, $title = '', $permalink = '')
    {
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

    public function toJson()
    {
      return array('id' => $this->id, 'title' => $this->title, 'permalink' => $this->permalink);
    }
  }
}