<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Authentication method class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Auth
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

 /**
  * Authentication method class
  *
  * This class has to be extended to implement custom authentication methods.
  */

abstract class AuthenticationMethod implements ObjectDeserializable {
    /**
     * @var User The local user matching the authentication
     */
    public $localUser;

    /**
     * @var string The username
     */
    public $name;

    /**
     * @var string The e-mail address
     */
    public $email;

    /**
     * @var string The authentication method identifiant
     */
    public $id;

    /**
     * @var string The remote identity provider user identifiant
     */
    public $remoteUserId;

    /**
     * @var Message The localized authentication login message
     */
    public $loginMessage;

    /**
     * @var boolean Determines if the authentication method could  be used to register new users
     */
    public $canCreateUser = false;

    /**
     * @var Array Actions to execute if an user is created, eacth instance a member of UserAction
     */
    public $createUserActions = [];

    /**
     * @var Context The site context
     */
    public $context;

    /**
     * @var Message The localized authentication error message
     */
    public $loginError;

    /**
     * Gets authentication link for this method
     */
    public abstract function getAuthenticationLink();

    /**
     * Handles request
     */
    public abstract function handleRequest();

    /**
     * Runs actions planned on user create
     */
    protected function runCreateUserActions () {
        foreach ($this->createUserActions as $action) {
            $action->targetUser = $this->localUser;
            $action->run();
        }
    }

    /**
     * Finds user from available data
     *
     * @return User the user if an user has been found; otherwise, false.
     */
    private function findUser () {
        if ($this->remoteUserId != '') {
            $user = User::getUserFromRemoteIdentity(
                $this->id, $this->remoteUserId
            );

            if ($user !== null) return $user;
        }

        if ($this->email != '') {
            $user = User::get_user_from_email($this->email);
            if ($user !== null) return $user;
        }

        return null;
    }

    /**
     * Signs in or creates a new user
     *
     * @return boolean true if user has been successfully logged in; otherwise, false.
     */
    public function signInOrCreateUser () {
        // At this stage, if we don't already have an user instance,
        // we're fetching it by remote user id or mail.
        //
        // If no result is returned, we're creating a new user if needed.
        //
        // Finally, we proceed to log in.

        if ($this->localUser === null) {
            $this->localUser = $this->findUser();
        }

        if ($this->localUser === null) {
            if (!$this->canCreateUser) {
                $this->loginError = Language::get("ExternalLoginCantCreateAccount");
                return false;
            } else {
                $this->createUser();
                if ($this->localUser === null) {
                    throw new Exception("Can't sign in: after correct remote authentication, an error occured creating locally a new user.");
                }
            }
        }

        $this->signIn($this->localUser);
        return true;
    }

    /**
     * Signs in the specified user
     *
     * @param User The user to log in
     */
    public function signIn(User $user) {
        $this->context->session->user_login($user->id);
    }

    /**
     * Creates a new user based on the authentication provisionning information
     *
     * @return User The user created
     */
    public function createUser () {
        if (!$this->canCreateUser) {
            throw new Exception("Can't create user: the canCreateUser property is set at false.");
        }

        $user = User::create();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->save_to_database();

        $user->setRemoteIdentity(
            $this->id, $this->remoteUserId
        );

        $this->localUser = $user;

        $this->runCreateUserActions();
    }

    /**
     * Gets authentication method from ID
     *
     * @param string $id The authentication method id
     * @param Context $context The site context
     * @return AuthenticationMethod The authentication method matching the id
     */
    public static function getFromId ($id, $context) {
        if ($context->workspace != null) {
            foreach ($context->workspace->configuration->authenticationMethods as $authenticationMethod) {
                if ($authenticationMethod->id == $id) {
                    return $authenticationMethod;
                }
            }
        }
        return null;
    }

    /**
     * Loads a AuthenticationMethod instance from a generic object. Typically used to deserialize a JSON document.
     *
     * @param object $data The object to deserialize
     * @return AuthenticationMethod The deserialized instance
     */
    public static function loadFromObject ($data) {
        $instance = new static;

        if (!property_exists($data, 'id')) {
            throw new InvalidArgumentException("Authentication method id is required.");
        }
        $instance->id = $data->id;

        if (property_exists($data, 'loginMessage')) {
            $instance->loginMessage = new Message($data->loginMessage);
        } else {
            $instance->loginMessage = new Message(Language::get("SignIn"));
        }

        if (property_exists($data, 'createUser')) {
            if (property_exists($data->createUser, 'enabled')) {
                $instance->canCreateUser = ($data->createUser->enabled == true);
            }

            if (property_exists($data->createUser, 'addToGroups')) {
                foreach ($data->createUser->addToGroups as $actionData) {
                    $instance->createUserActions[] = AddToGroupUserAction::loadFromObject($actionData);
                }
            }

            if (property_exists($data->createUser, 'givePermissions')) {
                foreach ($data->createUser->givePermissions as $actionData) {
                    $instance->createUserActions[] = GivePermissionUserAction::loadFromObject($actionData);
                }
            }
        }

        return $instance;
    }
}
