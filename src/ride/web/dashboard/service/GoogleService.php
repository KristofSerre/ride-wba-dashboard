<?php
/*
PHP implementation of Google Cloud Print
Author, Yasir Siddiqui

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

namespace ride\web\dashboard\service;

use ride\application\orm\entry\OrderEntry;
use ride\library\config\Config;
use ride\library\http\client\Client;
use ride\library\http\Request;
use ride\library\orm\OrmManager;
use ride\library\system\file\browser\FileBrowser;
use Google_Client;
use Google_Auth_AssertionCredentials;
use Carbon\Carbon;

class GoogleService {

    const ANALYTICS_URL = "https://www.googleapis.com/auth/analytics.readonly";
    const ANALYTICS_GET = "https://www.googleapis.com/analytics/v3/data/ga";



    private $config;
    private $google;
    private $httpClient;
    private $clientId;
    private $accountName;
    private $key;
    private $fileBrowser;

    /**
     * Function __construct
     * Set private members varials to blank
     */
    public function __construct(Config $config, OrmManager $orm, Google_Client $googleClient, FileBrowser $fileBrowser, Request $request, Client $client) {
        $this->config = $config;
        $this->clientId = $config->get('google.service.client_id');
        $this->accountName = $config->get('google.service.account_name');
        $this->key = $config->get('google.service.key');
        $this->fileBrowser = $fileBrowser;
        $this->request = $request;
        $this->httpClient = $client;

        $this->google = $googleClient;
        $this->google->setApplicationName("Analytics");
        $this->google->setDeveloperKey($config->get('google.api.key'));
    }

    public function getAccessToken()
    {
        if ($this->request->hasSession()) {
            $session = $this->request->getSession();
            $token = $session->get('gsa_token', false);
            $key = $this->fileBrowser ->getApplicationDirectory()->getChild($this->key)->read();
            $cred = new Google_Auth_AssertionCredentials($this->accountName, array($this::ANALYTICS_URL), $key);

            if($token)
            {
                $this->google->setAccessToken($token);
            }

            if ($this->google->getAuth()->isAccessTokenExpired()) {
                $this->google->setAssertionCredentials($cred);
                $this->google->getAuth()->refreshTokenWithAssertion($cred);
            }

            $session->set('gsa_token', $this->google->getAccessToken());

            $token = json_decode($this->google->getAccessToken(), true);
            return $token['access_token'];
        }

        else
        {
            $key = $this->fileBrowser ->getApplicationDirectory()->getChild($this->key)->read();
            $cred = new Google_Auth_AssertionCredentials($this->accountName, array($this::ANALYTICS_URL), $key);
            $this->google->setAssertionCredentials($cred);
            $this->google->getAuth()->refreshTokenWithAssertion($cred);

            $token = json_decode($this->google->getAccessToken(), true);
            return $token['access_token'];
        }

        return false;
    }

    /**
     * Function getAnalytics
     *
     */
    public function getAnalytics($analyticId) {
        $response = $this->httpClient->get(self::ANALYTICS_GET . '?ids=ga:' . $analyticId . '&start-date=2016-02-24&end-date=2016-02-27&metrics=ga:sessions,ga:bounces' , array(
            "Authorization" => "Bearer " . $this->getAccessToken()
        ));

        $responseData = json_decode($response->getBody(), true);
kd($responseData);
        return isset($responseData) ? $responseData : null;
    }
}