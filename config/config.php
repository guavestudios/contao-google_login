<?php

$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('\Guave\GoogleLogin\Oauth', 'checkLogin');

