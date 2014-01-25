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

if ($_POST['LogIn']) {
    //User have submitted login form
    $username = $db->sql_escape($_POST['username']);
    $sql = "SELECT user_password, user_id FROM " . TABLE_USERS . " WHERE username = '$username'";
    if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Can't get user information", '', __LINE__, __FILE__, $sql);

    if ($row = $db->sql_fetchrow($result)) {
        if (!$row['user_password']) {
            //No password set
            $LoginError = "This account exists but hasn't a password defined. Contact the site administrator.";
        } elseif ($row['user_password'] != md5($_POST['password'])) {
            //The password doesn't match
            $LoginError = "Incorrect password.";
        } else {
            //Login successful
            Session::load()->user_login($row['user_id']);
            $LoginSuccessful = true;
        }
    } else {
        $LoginError = "Username not found.";
    }
} elseif ($_POST['LogOut'] || $_GET['action'] == "user.logout") {
    //User have submitted logout form or clicked a logout link
    Session::load()->user_logout();
}
