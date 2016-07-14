<?php

namespace Guave\GoogleLogin;

use Firebase\JWT\JWT;

class Oauth {

    protected static $instance = null;
    protected static $client = null;
    private static $user = null;

    protected function __construct()
    {

        JWT::$leeway = 1;

        $oauthCredis = self::getOAuthCredentialsFile();
        if(!$oauthCredis) {
            echo 'oauth-credentials.json file missing in system/config';
            exit;
        }

        $redirectUrl = 'http://'.$_SERVER['HTTP_HOST'].'/check-google-login';

        $client = new \Google_Client();
        $client->setAuthConfig($oauthCredis);
        $client->setRedirectUri($redirectUrl);
        $client->addScope('email');

        if($_SESSION['oauth']['access_token']){
            $client->setAccessToken($_SESSION['oauth']['access_token']);
        }

        self::$client = $client;

    }

    protected function __clone()
    {

    }

    /**
     * @return Oauth
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @return bool|string
     */
    public static function getOAuthCredentialsFile()
    {
        // oauth2 creds
        $oauthCredis = TL_ROOT.'/system/config/oauth-credentials.json';

        if (file_exists($oauthCredis)) {
            return $oauthCredis;
        }

        return false;
    }

    public static function getOauthLinkForLogin()
    {

        $client = self::$client;
        return $client->createAuthUrl();

    }

    /**
     * called by hook getPageIdFromUrl
     * registers routing for check-google-login
     */
    public static function checkLogin($arrFragments)
    {

        if($arrFragments[0] == 'check-google-login') {


            $client = self::$client;

            if (isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $client->setAccessToken($token);
                $_SESSION['oauth']['access_token'] = $token;
            }

            if ($client->getAccessToken()) {
                $token_data = $client->verifyIdToken();

                /**
                 * check for user
                 * @var $user BackendUser
                 */
                $user = \BackendUser::getInstance();
                $find = $user->findBy('email', $token_data['email']);
                if(!$find) {
                    \Message::addError('no user with '.$token_data['email'].' found');
                    \Controller::redirect('contao');
                } else {

                    //register hook
                    $GLOBALS['TL_HOOKS']['importUser'][] = array('\Guave\GoogleLogin\Oauth', 'importUser');
                    $GLOBALS['TL_HOOKS']['checkCredentials'][] = array('\Guave\GoogleLogin\Oauth', 'importUser');
                    self::$user = $user;

                    \Input::setPost('username', 'oauthuser');
                    \Input::setPost('password', 'oauthpw');
                    if($user->login()) {
                        \Controller::redirect('contao/main.php');
                    } else {
                        \Controller::redirect('contao');
                    }
                }
            }

        }

    }

    /**
     * import user hook
     * @return bool
     */
    public function importUser($username, $password, $table)
    {

        $user = self::$user;
        if($user) {
            \Input::setPost('username', $user->username);
            return true;
        }

        return false;

    }

}