<?php

namespace Src\Core;

class Session
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public function hasFlash()
    {
        return isset($_SESSION['flash']);
    }

    public function getFlash()
    {
        if ($this->hasFlash()) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    // Autres méthodes de session...
}
