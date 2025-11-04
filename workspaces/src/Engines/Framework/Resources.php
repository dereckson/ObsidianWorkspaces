<?php

namespace Waystone\Workspaces\Engines\Framework;

use Waystone\Workspaces\Engines\Workspaces\Workspace;

use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use User;
use UserGroup;

use InvalidArgumentException;

class Resources {

    /**
     * @return Option<int>
     */
    public static function resolveID (string $resource_type, string $identifier) : Option {
        //Trivial cases: already an ID, null or void ID
        if (is_numeric($identifier)) {
            return new Some((int)$identifier);
        }

        if (!$identifier) {
            return new None;
        }

        //Searches identifier
        switch ($resource_type) {
            case 'U':
                return User::resolveUserID($identifier);

            case 'G':
                $group = UserGroup::fromCode($identifier);

                return new Some($group->id);

            case 'W':
                $workspace = Workspace::fromCode($identifier);

                return new Some($workspace->id);

            default:
                throw new InvalidArgumentException("Unknown resource type: $resource_type", E_USER_ERROR);
        }
    }
}
