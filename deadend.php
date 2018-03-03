<?php

require_once('../../config.php');

$deadcode = optional_param('code', 0, PARAM_INT);
$context = context_system::instance();
$url = new moodle_url('/auth/casent/deadend.php');

$authplugin = get_auth_plugin('casshib');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('locallogout', 'auth_casshib'));
$PAGE->set_heading(get_string('locallogout', 'auth_casshib'));

if ($deadcode == 1) {
    $output = $OUTPUT->heading(get_string('nocasconnect', 'auth_casshib'));

    $output .= '<div class="local-logout-message">';
    $output .= '<div class="logout-message">';
    $output .= get_string('nocasconnectmessage', 'auth_casshib');
    $output .= '</div>';

    // Notifying admin.
    if (!empty($authplugin->config->notify)) {
        $adminuser = $DB->get_record('user', array('username' => 'admin', 'mnethostid' => $CFG->mnet_localhost_id));
        $title = get_string('nocasconnectmailtitle_tpl', 'auth_casshib');
        $body = get_string('nocasconnectmailbody_tpl', 'auth_casshib');

        $title = str_replace('%%SITE%%', $SITE->shortname, $title);
        $body = str_replace('%%SITE%%', $SITE->shortname, $body);

        email_to_user($adminuser, $adminuser, $title, $body);
    }
} else if ($deadcode == 2) {
    if ($authplugin->config->redirectonfailure == 'invisible') {
        redirect('https://'.$authplugin->config->hostname);
    }

    $output = $OUTPUT->heading(get_string('unknownincominguser', 'auth_casshib'));

    $output .= '<div class="local-logout-message">';
    $output .= '<div class="logout-message">';
    $output .= get_string('unknownincomingmessage', 'auth_casshib', 'https://'.$authplugin->config->hostname);
    $output .= '</div>';

    // Notifying admin
    if (!empty($authplugin->config->notify)) {
        $adminuser = $DB->get_record('user', array('username' => 'admin', 'mnethostid' => $CFG->mnet_localhost_id));
        $title = get_string('unknownincomingmailtitle_tpl', 'auth_casshib');
        $body = get_string('unknownincomingmailbody_tpl', 'auth_casshib');

        $username = optional_param('username', '', PARAM_TEXT);

        $title = str_replace('%%SITE%%', $SITE->shortname, $title);
        $body = str_replace('%%SITE%%', $SITE->shortname, $body);
        $body = str_replace('%%USERNAME%%', $SITE->shortname, $username);

        email_to_user($adminuser, $adminuser, $title, $body);
    }
} else {
    if ($authplugin->config->redirectonfailure == 'invisible') {
        redirect('https://'.$authplugin->config->hostname);
    }

    $output = $OUTPUT->heading(get_string('locallogouttitle', 'auth_casshib'));

    $output .= '<div class="local-logout-message">';
    $output .= '<div class="logout-message">';
    $output .= get_string('locallogoutmessage', 'auth_casshib');
    $output .= '</div>';

    $output .= '<div class="logout-notice">';
    $output .= get_string('locallogoutmessage2', 'auth_casshib');
    $output .= '</div>';
    $output .= '</div>';
}

echo $OUTPUT->header();
echo $output;
echo $OUTPUT->footer();
