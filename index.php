<?php
require 'simple_html_dom.php';


class SimpleParser
{
    public $login;
    public $password;
    public $url;
    public $file_cookie = 'cookie.txt';
    public $url_auth;
    public $useragent;
    function __construct($url, $login, $password)
    {
        $this->url = $url;
        $this->url_auth = $this->url . '/auth/credentials';
        $this->login = $login;
        $this->password = $password;
        $this->useragent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';
    }

    public function login()
    {
        $ch = curl_init();
        if (strtolower((substr($this->url_auth, 0, 5)) == 'https')) { // если соединяемся с https
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        curl_setopt($ch, CURLOPT_URL, $this->url_auth);
        // откуда пришли на эту страницу
        curl_setopt($ch, CURLOPT_REFERER, $this->url_auth);
        // cURL будет выводить подробные сообщения о всех производимых действиях
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $sumbit = '';
        curl_setopt($ch, CURLOPT_POSTFIELDS, "Continue=/home&UserName=".$this->login."&Password=".$this->password."&:submit:=".$sumbit);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //сохранять полученные COOKIE в файл
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->file_cookie);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        $result = curl_exec($ch);
        $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $last_url = explode('/', $last_url)[3];
        if ($last_url === "home")
            return true ;
        else return false;


    }
    public function  readPage($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$url);
        // откуда пришли на эту страницу
        curl_setopt($ch, CURLOPT_REFERER, $this->url.$url);
        //запрещаем делать запрос с помощью POST и соответственно разрешаем с помощью GET
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //отсылаем серверу COOKIE полученные от него при авторизации
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->file_cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function run()
    {
        $login = $this->login();  # Start login method

        if ($login) {
            #$user = Read('https://xrpg.ru/user');
            $home = $this->readPage('/tower');
            echo $home;
            if(preg_match('/>&#x421;&#x43E;&#x431;&#x440;&#x430;&#x442;&#x44C;/', $home)){
                $this->readPage('/tower/gatherajax?format=json');
            }
            #$html = str_get_html($home);
            #$gold = $html->find('.c_gold',0);
            #echo($gold->plaintext);
        } else {
            print_r('Sign in unsuccessfully');

        }

    }
}



$user = new SimpleParser('url', 'login', 'passworf');
$user->run();
