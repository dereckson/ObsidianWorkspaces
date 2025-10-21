<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Login and logout code
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

use Waystone\Workspaces\Engines\Errors\ErrorHandling;

global $context;
$db = $context->db;

$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';

if (array_key_exists('LogIn', $_POST)) {
    //User have submitted login form
    $username = $db->escape($_POST['username']);
    $sql = "SELECT user_password, user_id FROM " . TABLE_USERS . " WHERE username = '$username'";
    if ( !($result = $db->query($sql)) ) ErrorHandling::messageAndDie(SQL_ERROR, "Can't get user information", '', __LINE__, __FILE__, $sql);

    if ($row = $db->fetchRow($result)) {
        if (!$row['user_password']) {
            //No password set
            $LoginError = "This account exists but hasn't a password defined. Contact the site administrator.";
        } elseif ($row['user_password'] != md5($_POST['password'])) {
            //The password doesn't match
            $LoginError = "Incorrect password.";
        } else {
            //Login successful
            $context->session->user_login($row['user_id']);
            $LoginSuccessful = true;
        }
    } else {
        $LoginError = "Username not found.";
    }
} elseif (array_key_exists('LogOut', $_POST) || $action == "user.logout") {
    //User have submitted logout form or clicked a logout link
    $context->session->user_logout();
} elseif (array_key_exists('authenticationMethodId', $_GET)) {
    //Call authentication method for more processing
    $auth = AuthenticationMethod::getFromId($_GET['authenticationMethodId'], $context);
    if ($auth) {
        $auth->handleRequest();
    }
}