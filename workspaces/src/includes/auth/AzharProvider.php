<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Azhàr provider authentication method class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

 /**
  * Azhàr provider authentication method class
  *
  * Azhàr sends a document providing authentication and registration of new users.
  * It's signed by a shared secret key.
  */
class AzharProvider extends AuthenticationMethod {
    /**
     * @var string Shared secret key
     */
    public $secretKey;

    /**
     * @var string Client key, to identify the consumer application.
     */
    public $clientKey;

    /**
     * @var string The Azhàr identity provider login URL
     */
    public $url;

    /**
     * Handles user login request
     */
    public function handleRequest () {
        $action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
        $sessionKey = array_key_exists('sessionKey', $_GET) ? $_GET['sessionKey'] : '';

        if ($action == "user.login.azhar.initialize") {
            //Redirects user to Azhàr SSO service
            $callbackUrl = get_server_url() . get_url($this->context->workspace->code)
                         . '?action=user.login.azhar.success&authenticationMethodId=' . $this->id;
            $url = $this->url . '?mode=provider&key=' . $this->clientKey
                   . '&sessionKey=' . $this->getSessionKey()
                   . '&url=' . urlencode($callbackUrl);
            header('Location: ' . $url);
            exit;
        } elseif ($action == "user.login.azhar.success") {
            //User claims to have logged in, we can get authentication information
            $reply = $this->fetchInformation();

            if (!$this->isDocumentLegit($reply)) {
                $this ->loginError = Language::get('ExternalLoginNotLegitReply');
                return;
            }

            if ($reply->status == "SUCCESS") {
                //Creates user or login
                $this->name = $reply->username;
                $this->email = $reply->email;
                $this->remoteUserId = $reply->localUserId;
                $this->signInOrCreateUser();
                return;
            } elseif ($reply->status == "ERROR_USER_SIDE") {
                switch ($reply->code) {
                    case 'NO_USER_VISIT':
                    case 'NOT_LOGGED_IN':
                        $this ->loginError = Language::get('ExternalLoginNotRemotelyLoggedIn');
                        return;
                }
            } elseif ($reply->status == "ERROR_BETWEEN_US") {
                switch ($reply->code) {
                    case 'SESSION_BADSECRET':
                        $this->loginError = sprintf(Language::get('ExternalLoginTechnicalDifficulty'), $reply->code);
                        return;
                }
            }

            $this->loginError = '<p>An unknown error has been received:</p><pre>' . print_r($reply, true) . '</pre><p>Please notify technical support about this new error message, so we can handle it in the future.</p>';
        } else {
            $this ->loginError = '<p>Unknown action: $action</p>';
        }
    }

    /**
     * Gets Azhàr provider session key
     *
     * This key allows us as consumer to fetch information, and Azhàr as provider to store it.
     *
     * @return string the session key
     */
    public function getSessionKey () {
        $hash = md5($this->id);
        if (!isset($_SESSION['Auth-$hash']['SessionKey'])) {
            $url = $this->url . '?mode=provider.announce&key=' . $this->clientKey
                   . '&url=n/a';
            $reply = self::query($url);
            $this->setSessionSecret($reply->sessionSecret);
            $_SESSION['Auth-$hash']['SessionKey'] = $reply->sessionKey;
        }
        return $_SESSION['Auth-$hash']['SessionKey'];
    }

    /**
     * Gets Azhàr provider session secret
     *
     * @return string the session secret
     */
    private function getSessionSecret () {
        $hash = md5($this->id);
        return $_SESSION['Auth-$hash']['SessionSecret'];
    }

    /**
     * Sets Azhàr provider session secret
     *
     * @param string $secret the session secret
     */
    private function setSessionSecret ($secret) {
        $hash = md5($this->id);
        $_SESSION['Auth-$hash']['SessionSecret'] = $secret;
    }

    /**
     * Gets Azhàr external authentication link
     *
     * @retrun string the login link
     */
    public function getAuthenticationLink() {
        $url = get_server_url() . get_url($this->context->workspace->code)
             . '?action=user.login.azhar.initialize&authenticationMethodId=' . $this->id;
        return $url;
    }

    /**
     * Determines if the document received has been signed by the correct shared secret key.
     *
     * @return boolean true if the document is legit; otherwise, false.
     */
    function isDocumentLegit ($document) {
        $hash = '';
        $claimedHash = NULL;
        foreach ($document as $key => $value) {
            if ($key == 'hash') {
                $claimedHash = $value;
                continue;
            }

            $hash .= md5($key . $value);
        }

        $salt = '$2y$10$' . substr($this->secretKey, 0, 22);
        $computedHash = crypt($hash, $salt);

        return $claimedHash === $computedHash;
    }

    /**
     * Fetches information document
     *
     * @return stdClass The Azhàr identity provider information about the current login operation
     */
    function fetchInformation () {
        $url = $this->url . '?mode=provider.fetch&key=' . $this->clientKey
               . '&sessionSecret=' . $this->getSessionSecret()
               . '&sessionKey=' . $this->getSessionKey()
               . '&url=n/a';
        return self::query($url);
    }

    /**
     * Gets the contents of the specified URL and decode the JSON reply
     *
     * @param string $url The URL to the JSON document to query.
     * @return stdClass The reply
     */
    public static function query ($url) {
        $data = file_get_contents($url);
        return json_decode($data);
    }

    /**
     * Loads an AzharProvider instance from a generic array.
     * Typically used to deserialize a configuration.
     *
     * @param array $data The associative array to deserialize
     * @return AzharProvider The deserialized instance
     */
    public static function loadFromArray (array $data) : self {
        $instance = parent::loadFromArray($data);

        $instance->url = $data["url"];
        $instance->secretKey = $data["secretKey"];
        $instance->clientKey = $data["clientKey"];

        return $instance;
    }
}
