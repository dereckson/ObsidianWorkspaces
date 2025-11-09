<?php

namespace Waystone\Workspaces\Engines\Users;

use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Exceptions\UserNotFoundException;
use Waystone\Workspaces\Engines\Framework\Repository;

use Keruald\Database\Exceptions\SqlException;
use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use RuntimeException;

class UserRepository extends Repository {

    ///
    /// Find user in database
    ///

    public function resolveUserID (string $expression) : Option {
        return $this->getUserFromUsername($expression)
            ->orElse(fn() => $this->getUserFromEmail($expression))
            ->map(fn($user) => $user->id);
    }

    /**
     * @return Option<User>
     */
    private function getByProperty (string $property, mixed $value) : Option {
        $value = $this->db->escape($value);
        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE $property = '$value'";
        if (!$result = $this->db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Can't get user", '', __LINE__, __FILE__, $sql);
        }

        $row = $this->db->fetchRow($result);

        if (!$row) {
            return new None;
        }

        $user = User::fromRow($row);
        $this->table[$user->id] = $user;

        return new Some($user);
    }

    /**
     * Gets user from specified e-mail
     *
     * @return Option<User> the user matching the specified e-mail; None, if the mail were not found.
     */
    public function getUserFromEmail (string $mail) : Option {
        return $this->lookupInTable("email", $mail)
            ->orElse(fn () => $this->getByProperty("user_email", $mail));
    }

    /**
     * @return Option<User>
     */
    public function getUserFromUsername (string $username) : Option {
        return $this->lookupInTable("name", $username)
            ->orElse(fn () => $this->getByProperty("username", $username));
    }

    /**
     * Gets user from remote identity provider identifiant
     *
     * @param string $authType The authentication method type
     * @param string $remoteUserId The remote user identifier
     * @return Option<User> the user matching the specified identity provider and identifiant; None if no user were found.
     */
    public function getUserFromRemoteIdentity (string $authType, string $remoteUserId) : Option {
        $authType = $this->db->escape($authType);
        $remoteUserId = $this->db->escape($remoteUserId);
        $sql = "SELECT user_id FROM " . TABLE_USERS_AUTH . "    WHERE "
            . "auth_type = '$authType' AND auth_identity = '$remoteUserId'";

        try {
            $result = $this->db->queryScalar($sql);
        } catch (SqlException $ex) {
            ErrorHandling::messageAndDie(SQL_ERROR, $ex->getMessage(), "Can't get user", __LINE__, __FILE__, $sql);
        }

        return Option::from($result)
            ->map(fn($user_id) => $this->get($user_id));
    }

    ///
    /// Registration facilities
    ///

    /**
     * Checks if a username is still available
     */
    public function isAvailableUsername (string $login) : bool {
        $login = $this->db->escape($login);

        $sql = "SELECT COUNT(*) FROM " . TABLE_USERS
            . " WHERE username = '$login'";

        try {
            $result = $this->db->queryScalar($sql);
        } catch (SqlException $ex) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Can't check if the specified login is available", '', __LINE__, __FILE__, $sql);
        }

        return $result == 0;
    }

    ///
    /// Load object
    ///

    /**
     * Gets an instance of the class from the table or loads it from database.
     *
     * @param int $id the user ID
     *
     * @return User the user instance
     * @throws Exception when the user is not found
     */
    public function get (int $id) : User {
        if ($this->table->has($id)) {
            return $this->table[$id];
        }

        $user = $this->loadFromDatabase($id);
        if ($user->isNone()) {
            throw new UserNotFoundException;
        }

        $user = $user->getValue();
        $this->table[$id] = $user;

        return $user;
    }

    /**
     * Loads the object User (ie fill the properties) from the database
     *
     * @return Option<User> the user instance, or None if not found
     */
    private function loadFromDatabase (int $id) : Option {
        $db = $this->db;

        $sql = "SELECT * FROM " . TABLE_USERS . " WHERE user_id = '" . $id . "'";
        if (!$result = $db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Unable to query users", '', __LINE__, __FILE__, $sql);
        }

        $row = $db->fetchRow($result);
        if (!$row) {
            return new None;
        }

        return new Some(User::fromRow($row));
    }

    ///
    /// Create object
    ///

    /**
     * Initializes a new User instance ready to have its property filled
     *
     * @return User the new user instance
     */
    public function create () : User {
        $id = $this->generateId();

        return User::create($id);
    }

    /**
     * Generates a unique user id
     */
    private function generateId () : int {
        $db = $this->db;

        do {
            $id = mt_rand(2001, 9999);
            $sql = "SELECT COUNT(*) FROM " . TABLE_USERS . " WHERE user_id = $this->id";

            try {
                $result = $db->queryScalar($sql);
            } catch (SqlException) {
                ErrorHandling::messageAndDie(SQL_ERROR, "Can't check if a user id is free", '', __LINE__, __FILE__, $sql);
            }
        } while ($result);

        return $id;
    }

    ///
    /// Save object
    ///

    /**
     * Saves to database
     */
    function saveToDatabase (User $user) : void {
        $db = $this->db;

        $id = $user->id ? "'" . $db->escape($user->id) . "'" : 'NULL';
        $name = $db->escape($user->name);
        $password = $db->escape($user->password);
        $active = $user->active ? 1 : 0;
        $email = $db->escape($user->email);
        $regdate = $user->regdate ? "'" . $db->escape($user->regdate) . "'" : 'NULL';

        //Updates or inserts
        $sql = "REPLACE INTO " . TABLE_USERS . " (`user_id`, `username`, `user_password`, `user_active`, `user_email`, `user_regdate`) VALUES ($id, '$name', '$password', $active, '$email', $regdate)";
        if (!$db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Unable to save user", '', __LINE__, __FILE__, $sql);
        }

        if (!$user->id) {
            //Gets new record id value
            $user->id = $db->nextId();
        }
    }

    //
    // User authentication
    //

    /**
     * Sets user's remote identity provider identifiant
     *
     * @param User $user
     * @param string $authType The authentication method type
     * @param string $remoteUserId The remote user identifier
     * @param array $properties The authentication method properties
     */
    public function setRemoteIdentity (User $user, string $authType, string $remoteUserId, array $properties = []) : void {
        $db = $this->db;

        if ($properties != []) {
            throw new RuntimeException("The remote identity provider properties have not been implemented yet.");
        }

        $authType = $db->escape($authType);
        $remoteUserId = $db->escape($remoteUserId);
        $properties = "NULL";
        $sql = "INSERT INTO " . TABLE_USERS_AUTH . " (auth_type, auth_identity, auth_properties, user_id) "
            . "VALUES ('$authType', '$remoteUserId', $properties, $user->id)";
        if (!$db->query($sql)) {
            ErrorHandling::messageAndDie(SQL_ERROR, "Can't set user remote identity provider information", '', __LINE__, __FILE__, $sql);
        }
    }

}
