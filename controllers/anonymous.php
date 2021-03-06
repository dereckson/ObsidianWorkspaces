<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller to handle the pages for not logged in users.
 *
 * It recognizes the following URLs:
 *      /invite     the page to claim the invites.
 *
 * In all other cases, it prints the login form.
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

//
// Prepares the page
//

$urlFragment = count($context->url) ? $context->url[0] : '';

switch ($urlFragment) {
    case 'invite':
        echo "You have been invited to use Obsidian. This feature is currently disabled. Please ask the person who invited you to contact our support desk to create your account.";
        /* Code from Zed
        //Invite form
        if ($_POST['form'] == 'account.create') {
            //User tries to claim its invite to create an account
            require_once('includes/objects/invite.php');
            require_once('includes/objects/user.php');

            //Gets invite
            $invite = new Invite($_POST['invite_code']);
            if ($invite->lastError != '') {
                //Not existant invite.
                $smarty->assign('NOTIFY', Language::get("IncorrectInviteCode"));
            } elseif ($invite->is_claimed()) {
                //The invitation have already claimed by someone else.
                $smarty->assign('NOTIFY', Language::get("InviteCodeAlreadyClaimed"));
            } else {
                //Checks if the given information is correct
                //We ignore bad mails. All we really need is a login and a pass.
                //We fill our array $errors with all the errors
                $errors = array();
                if (!$_POST['username']) {
                    $errors[] = Language::get('MissingUsername');
                } elseif (!User::is_available_login($_POST['username'])) {
                    $errors[] =  Language::get('LoginUnavailable');
                }

                if (User::get_username_from_email($_POST['email']) !== false) {
                    $errors[] = "There is already an account with this e-mail.";
                }

                if (!$_POST['passwd']) {
                    $errors[] = Language::get('MissingPassword');
                }

                if (count($errors)) {
                    $smarty->assign('WAP', join('<br />', $errors));
                } else {
                    //Creates account
                    $user = new User();
                    $user->regdate = time();
                    $user->generate_id();
                    $user->name = $_POST['username'];
                    $user->active = 1;
                    $user->email = $_POST['email'];
                    $user->set_password($_POST['passwd']);
                    $user->save_to_database();

                    //Updates invite
                    $invite->to_user_id = $user->id;
                    $invite->save_to_database();

                    //Notifies inviter
                    require_once('includes/objects/message.php');
                    $message = new Message();
                    $message->from = 0;
                    $message->to = $invite->from_perso_id;
                    $message->text =  sprintf(Language::get('InviteHaveBeenClaimed'), $invite->code);
                    $message->send();

                    //Logs in user
                    login($user->id, $user->name);

                    //Prints confirm message
                    $smarty->assign('WAP', Language::get("AccountCreated"));

                    //Redirects users to homepage
                    header('refresh: 5; url=' . get_url());

                    //Calls void controller
                    $smarty->assign('screen', 'user.create');
                    define('NO_FOOTER_EXTRA', true);
                    include("void.php");

                    exit;
                }
            }

            //Keeps username, email, invite code printed on account create form
            $smarty->assign('username', $_POST['username']);
            $smarty->assign('invite_code', $_POST['invite_code']);
            $smarty->assign('email', $_POST['email']);
        }

        //If the invite code is specified, checks format
        if ($url[1]) {
            if (preg_match("/^([A-Z]){3}([0-9]){3}$/i", $url[1])) {
                $smarty->assign('invite_code', strtoupper($url[1]));
            } else {
                $smarty->assign('NOTIFY', Language::get("IncorrectInviteCode"));
            }
        }

        $template = 'account_create.tpl';
        */
        break;

    default:
        $smarty = $context->templateEngine;
        //Login
        if ($context->workspace == null) {
            $useInternalLogin = true;
        } else {
            $useInternalLogin = $context->workspace->configuration->allowInternalAuthentication;

            $authenticationMethodsTemplateInformation = [];
            $authenticationMethodsLoginErrors = [];
            $authenticationMethodsNav = [];
            foreach ($context->workspace->configuration->authenticationMethods as $method) {
                $authenticationMethodsNav[] = [
                    'text' => (string)$method->loginMessage,
                    'href' => $method->getAuthenticationLink()
                ];
                if ($method->loginError) {
                    $authenticationMethodsLoginErrors[] = (string)$method->loginError;
                }
            }

            if (count($authenticationMethodsNav)) {
                $smarty->assign('ExternalAuthenticationMethodsNav', $authenticationMethodsNav);
            }
            $smarty->assign('ExternalLoginErrors', $authenticationMethodsLoginErrors);
            $smarty->assign('WorkspaceName', $context->workspace->name);
        }

        //Internal login form
        if ($useInternalLogin) {
            if (array_key_exists('LastUsername', $_COOKIE)) {
                $smarty->assign('username', $_COOKIE['LastUsername']);
            }
            if (array_key_exists('LastOpenID', $_COOKIE)) {
                $smarty->assign('OpenID', $_COOKIE['LastOpenID']);
            }

            $action  = $context->workspace ? get_url($context->workspace->code) . '/' : get_url();
            $action .= implode('/', $context->url);

            if (isset($LoginError)) {
                $smarty->assign('LoginError', $LoginError);
            }
            $smarty->assign('PostURL', $action);
            $smarty->assign('PrintInternalLogin', true);
        } else {
            $smarty->assign('PrintInternalLogin', false);
        }
        $template = 'login.tpl';
        break;
}

//
// HTML output
//

if ($template) {
    $smarty->display($template);
}
