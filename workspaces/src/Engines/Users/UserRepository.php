<?php

namespace Waystone\Workspaces\Engines\Users;

use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Framework\Repository;

use Keruald\Database\Exceptions\SqlException;
use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use User;

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

        $user = new User(null, $this->db);
        $user->load_from_row($row);
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
     * @return User the user instance
     */
    public function get (int $id) : User {
        if ($this->table->has($id)) {
            return $this->table[$id];
        }

        $user = new User($id, $this->db);
        $this->table[$id] = $user;

        return $user;
    }

}
