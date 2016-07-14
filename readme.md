# contao google login

## An contao module to login per oauth over google api
search for googleemail in user db and login as user

##Dependencies
google/apiclient

## Install

composer require google/apiclient:^2.0

create an api key with the google API-Manager
-https://console.developers.google.com/
register http:://yourdomain.com/check-google-login to allowed redirect urls of your app (only public domains are allowed by google)

download the oauth-credentials.json and copy it to /system/config



