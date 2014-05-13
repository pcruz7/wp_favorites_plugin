<?php

require_once(sprintf('%s/cookie_handler.php', dirname(__FILE__)));

if (!class_exists('FavoritesRepository'))
{
  class FavoritesRepository
  {
    private $cookieHandler;

    function __construct($cookieHandler = null)
    {
      $this->cookieHandler = $cookieHandler;
    }

    public function getFavoriteList($listname, $userId = null)
    {
      $list = isset($userId) ? get_user_meta($userId, $listname, true)
                             : $this->cookieHandler->getCookie($listname);
      if (!$list) {
        $list = array();
      }

      return array_map('intval', $list);
    }

    public function saveFavoriteList($listname, $userId = null, $list = array())
    {
      $list = array_map('intval', $list);
      isset($userId) ? update_user_meta($userId, $listname, $list)
                     : $this->cookieHandler->setCookie($listname, $list);
    }

    public function initFavoriteList($listname, $userId = null)
    {
      $list = $this->cookieHandler->getCookie($listname);
      if (isset($userId) && $list) {
        $meta   = get_user_meta($userId, $listname, true);
        $merged = array_merge($meta, $list);
        $this->cookieHandler->eraseCookie($listname);
        $this->saveFavoriteList($listname, $userId, array_unique($merged));
      }
    }
  }
}