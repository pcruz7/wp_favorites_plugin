<?php

if (!class_exists('CookieHandler'))
{
  class CookieHandler
  {
    public function getCookie($listname)
    {
      return (array_key_exists($listname, $_COOKIE) ? unserialize($_COOKIE[$listname])
                                                    : array());
    }

    public function setCookie($listname, $list = array())
    {
      setcookie($listname, serialize(array_unique($list)), time()+3600*24*100, COOKIEPATH, COOKIE_DOMAIN, false);
    }

    public function eraseCookie($listname)
    {
      setcookie($listname, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false);
    }
  }
}