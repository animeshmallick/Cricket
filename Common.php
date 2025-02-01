<?php

class Common
{
    public function is_user_an_agent(): bool
    {
        return $this->get_cookie('user_type') == "agent";
    }
    public function is_user_an_admin(): bool
    {
        return $this->get_cookie('user_type') == "admin";
    }
    public function get_cookie(string $name): string
    {
        return $_COOKIE[$name] ?? "";
    }
    public function is_user_logged_in(): bool
    {
        return $this->get_cookie('ref_id') != "";
    }

    public function logout()
    {
    }
    public function redirect_to(string $url)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . $url;
        echo $url;

        header("Location: $url");
    }
}