<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication Plugin: CAS Authentication
 *
 * Authentication using CAS (Central Authentication Server).
 *
 * @author Martin Dougiamas
 * @author Jerome GUTIERREZ
 * @author Iñaki Arenaza
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth_casshibshib
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/auth/ldap/auth.php');
require_once($CFG->dirroot.'/auth/casshib/CAS/CASSHIB.php');

/**
 * CAS authentication plugin.
 */
class auth_plugin_casshib extends auth_plugin_ldap {

    /**
     * Constructor.
     */
    function __construct() {
        $this->authtype = 'casshib';
        $this->roleauth = 'auth_casshib';
        $this->errorlogtag = '[AUTH CASSHIB] ';
        $this->init_plugin($this->authtype);
    }

    public function prevent_local_passwords() {
        return true;
    }

    /**
     * Authenticates user against CAS
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    public function user_login ($username, $password) {
        $this->connectCAS();
        return phpCASSHIB::isAuthenticated() && (trim(core_text::strtolower(phpCASSHIB::getUser())) == $username);
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    public function can_change_password() {
        return false;
    }

    /**
     * Authentication choice (CAS or other)
     * Redirection to the CAS form or to login/index.php
     * for other authentication
     */
    public function loginpage_hook() {
        global $frm;
        global $CFG, $DB;
        global $SESSION, $OUTPUT, $PAGE;

        $site = get_site();
        $CASform = get_string('CASform', 'auth_casshib');
        $username = optional_param('username', '', PARAM_RAW);
        $courseid = optional_param('courseid', 0, PARAM_INT);

        if (!empty($username)) {
            if (isset($SESSION->wantsurl) && (strstr($SESSION->wantsurl, 'ticket') ||
                                              strstr($SESSION->wantsurl, 'NOCAS'))) {
                unset($SESSION->wantsurl);
            }
            return;
        }

        // Return if CAS enabled and settings not specified yet
        if (empty($this->config->hostname)) {
            return;
        }

        // CHANGE+ : Allow nocas even when multiauth is OFF.
        $authCAS = optional_param('authCAS', '', PARAM_RAW);
        if ($authCAS == 'NOCAS') {
            return;
        }
        // CHANGE-.

        // If the multi-authentication setting is used, check for the param before connecting to CAS.
        if ($this->config->multiauth) {

            // If there is an authentication error, stay on the default authentication page.
            if (!empty($SESSION->loginerrormsg)) {
                return;
            }

            // Show authentication form for multi-authentication.
            // Test pgtIou parameter for proxy mode (https connection in background from CAS server to the php server).
            if ($authCAS != 'CAS' && !isset($_GET['pgtIou'])) {
                $PAGE->set_url('/login/index.php');
                $PAGE->navbar->add($CASform);
                $PAGE->set_title("$site->fullname: $CASform");
                $PAGE->set_heading($site->fullname);
                echo $OUTPUT->header();
                include($CFG->dirroot.'/auth/casshib/cas_form.html');
                echo $OUTPUT->footer();
                exit();
            }
        }

        // Connection to CAS server.
        if (!$this->connectCAS()) {
            if ($authCAS == 'CAS') {
                // Redirect and signal no connection if CAS was explicitely required.
                $redirect = new moodle_url('/auth/casshib/deadend.php?code=1');
                redirect($redirect);
            }
        }

        if (phpCASSHIB::checkAuthentication()) {
            $frm = new stdClass();
            $frm->username = phpCASSHIB::getUser();
            $frm->password = 'passwdCas';

            if (!$user = $DB->get_record('user', array('username' => $frm->username))) {
                redirect(new moodle_url('/auth/casshib/deadend.php', array('username' => $frm->username, 'code' => 2)));
            }

            complete_user_login($user);

            if (!empty($SESSION->wantsurl)) {
                redirect($SESSION->wantsurl);
            }

            // Redirect to a course if multi-auth is activated, authCAS is set to CAS and the courseid is specified.
            // Redirect to a course if multi-auth is activated, authCAS is set to CAS and the courseid is specified.
            if ($this->config->multiauth && !empty($courseid)) {
                redirect(new moodle_url('/course/view.php', array('id' => $courseid, 'authCAS' => 'CAS')));
            } else {
                redirect(new moodle_url('/', array('id' => SITEID, 'authCAS' => 'CAS')));
            }

            return;
        }

        if (isset($_GET['loginguest']) && ($_GET['loginguest'] == true)) {
            $frm = new stdClass();
            $frm->username = 'guest';
            $frm->password = 'guest';
            return;
        }

        // Force CAS authentication (if needed).
        if (!phpCASSHIB::isAuthenticated()) {
            phpCASSHIB::setLang($this->config->language);
            phpCASSHIB::forceAuthentication();
        }
    }

    /**
     * Logout from the CAS
     *
     */
    public function prelogout_hook() {
        global $CFG, $USER, $DB;

        if (!empty($this->config->logoutcas) && $USER->auth == $this->authtype) {
            $backurl = !empty($this->config->logout_return_url) ? $this->config->logout_return_url : $CFG->wwwroot;
            $this->connectCAS();
            // Note: Hack to stable versions to trigger the event before it redirect to CAS logout.
            $sid = session_id();
            $event = \core\event\user_loggedout::create(
                array(
                    'userid' => $USER->id,
                    'objectid' => $USER->id,
                    'other' => array('sessionid' => $sid),
                )
            );
            if ($session = $DB->get_record('sessions', array('sid' => $sid))) {
                $event->add_record_snapshot('sessions', $session);
            }
            \core\session\manager::terminate_current();
            $event->trigger();

            phpCASSHIB::logoutWithRedirectService($backurl);
        }
    }

    /**
     * Connect to the CAS (clientcas connection or proxycas connection)
     *
     */
    public function connectCAS() {
        global $CFG;
        static $connected = false;

        if (!$connected) {
            // Make sure phpCASSHIB doesn't try to start a new PHP session when connecting to the CAS server.
            if ($this->config->proxycas) {
                phpCASSHIB::proxy($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            } else {
                phpCASSHIB::client($this->config->casversion, $this->config->hostname, (int) $this->config->port, $this->config->baseuri, false);
            }
            $connected = true;
        }

        // If Moodle is configured to use a proxy, phpCASSHIB needs some curl options set.
        if (!empty($CFG->proxyhost) && !is_proxybypass($this->config->hostname)) {
            phpCASSHIB::setExtraCurlOption(CURLOPT_PROXY, $CFG->proxyhost);
            if (!empty($CFG->proxyport)) {
                phpCASSHIB::setExtraCurlOption(CURLOPT_PROXYPORT, $CFG->proxyport);
            }
            if (!empty($CFG->proxytype)) {
                // Only set CURLOPT_PROXYTYPE if it's something other than the curl-default http
                if ($CFG->proxytype == 'SOCKS5') {
                    phpCASSHIB::setExtraCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                }
            }
            if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                phpCASSHIB::setExtraCurlOption(CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
                if (defined('CURLOPT_PROXYAUTH')) {
                    // any proxy authentication if PHP 5.1
                    phpCASSHIB::setExtraCurlOption(CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
                }
            }
        }

        if ($this->config->certificate_check && $this->config->certificate_path){
            phpCASSHIB::setCasServerCACert($this->config->certificate_path);
        } else {
            // Don't try to validate the server SSL credentials
            phpCASSHIB::setNoCasServerValidation();
        }

        return $connected;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    public function config_form($config, $err, $user_fields) {
        global $CFG, $OUTPUT;

        if (!function_exists('ldap_connect')) { // Is php-ldap really there?
            echo $OUTPUT->notification(get_string('auth_ldap_noextension', 'auth_ldap'));

            // Don't return here, like we do in auth/ldap. We cas use CAS without LDAP.
            // So just warn the user (done above) and define the LDAP constants we use
            // in config.html, to silence the warnings.
            if (!defined('LDAP_DEREF_NEVER')) {
                define ('LDAP_DEREF_NEVER', 0);
            }
            if (!defined('LDAP_DEREF_ALWAYS')) {
                define ('LDAP_DEREF_ALWAYS', 3);
            }
        }

        include($CFG->dirroot.'/auth/casshib/config.html');
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages
     */
    public function validate_form($form, &$err) {
        $certificate_path = trim($form->certificate_path);
        if ($form->certificate_check && empty($certificate_path)) {
            $err['certificate_path'] = get_string('auth_casshib_certificate_path_empty', 'auth_casshib');
        }
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    public function process_config($config) {

        // CAS settings
        if (!isset($config->hostname)) {
            $config->hostname = '';
        }
        if (!isset($config->port)) {
            $config->port = '';
        }
        if (!isset($config->casversion)) {
            $config->casversion = '';
        }
        if (!isset($config->baseuri)) {
            $config->baseuri = '';
        }
        if (!isset($config->language)) {
            $config->language = '';
        }
        if (!isset($config->proxycas)) {
            $config->proxycas = '';
        }
        if (!isset($config->logoutcas)) {
            $config->logoutcas = '';
        }
        if (!isset($config->multiauth)) {
            $config->multiauth = '';
        }
        if (!isset($config->certificate_check)) {
            $config->certificate_check = '';
        }
        if (!isset($config->certificate_path)) {
            $config->certificate_path = '';
        }
        if (!isset($config->logout_return_url)) {
            $config->logout_return_url = '';
        }

        // LDAP settings
        if (!isset($config->host_url)) {
            $config->host_url = '';
        }
        if (!isset($config->start_tls)) {
             $config->start_tls = false;
        }
        if (empty($config->ldapencoding)) {
            $config->ldapencoding = 'utf-8';
        }
        if (!isset($config->pagesize)) {
            $config->pagesize = LDAP_DEFAULT_PAGESIZE;
        }
        if (!isset($config->contexts)) {
            $config->contexts = '';
        }
        if (!isset($config->user_type)) {
            $config->user_type = 'default';
        }
        if (!isset($config->user_attribute)) {
            $config->user_attribute = '';
        }
        if (!isset($config->search_sub)) {
            $config->search_sub = '';
        }
        if (!isset($config->opt_deref)) {
            $config->opt_deref = LDAP_DEREF_NEVER;
        }
        if (!isset($config->bind_dn)) {
            $config->bind_dn = '';
        }
        if (!isset($config->bind_pw)) {
            $config->bind_pw = '';
        }
        if (!isset($config->ldap_version)) {
            $config->ldap_version = '3';
        }
        if (!isset($config->objectclass)) {
            $config->objectclass = '';
        }
        if (!isset($config->memberattribute)) {
            $config->memberattribute = '';
        }

        if (!isset($config->memberattribute_isdn)) {
            $config->memberattribute_isdn = '';
        }
        if (!isset($config->attrcreators)) {
            $config->attrcreators = '';
        }
        if (!isset($config->groupecreators)) {
            $config->groupecreators = '';
        }
        if (!isset($config->removeuser)) {
            $config->removeuser = AUTH_REMOVEUSER_KEEP;
        }

        // save CAS settings
        set_config('hostname', trim($config->hostname), $this->pluginconfig);
        set_config('port', trim($config->port), $this->pluginconfig);
        set_config('casversion', $config->casversion, $this->pluginconfig);
        set_config('baseuri', trim($config->baseuri), $this->pluginconfig);
        set_config('language', $config->language, $this->pluginconfig);
        set_config('proxycas', $config->proxycas, $this->pluginconfig);
        set_config('logoutcas', $config->logoutcas, $this->pluginconfig);
        set_config('multiauth', $config->multiauth, $this->pluginconfig);
        set_config('certificate_check', $config->certificate_check, $this->pluginconfig);
        set_config('certificate_path', $config->certificate_path, $this->pluginconfig);
        set_config('logout_return_url', $config->logout_return_url, $this->pluginconfig);

        // save LDAP settings
        set_config('host_url', trim($config->host_url), $this->pluginconfig);
        set_config('start_tls', $config->start_tls, $this->pluginconfig);
        set_config('ldapencoding', trim($config->ldapencoding), $this->pluginconfig);
        set_config('pagesize', (int)trim($config->pagesize), $this->pluginconfig);
        set_config('contexts', trim($config->contexts), $this->pluginconfig);
        set_config('user_type', core_text::strtolower(trim($config->user_type)), $this->pluginconfig);
        set_config('user_attribute', core_text::strtolower(trim($config->user_attribute)), $this->pluginconfig);
        set_config('search_sub', $config->search_sub, $this->pluginconfig);
        set_config('opt_deref', $config->opt_deref, $this->pluginconfig);
        set_config('bind_dn', trim($config->bind_dn), $this->pluginconfig);
        set_config('bind_pw', $config->bind_pw, $this->pluginconfig);
        set_config('ldap_version', $config->ldap_version, $this->pluginconfig);
        set_config('objectclass', trim($config->objectclass), $this->pluginconfig);
        set_config('memberattribute', core_text::strtolower(trim($config->memberattribute)), $this->pluginconfig);
        set_config('memberattribute_isdn', $config->memberattribute_isdn, $this->pluginconfig);
        set_config('attrcreators', trim($config->attrcreators), $this->pluginconfig);
        set_config('groupecreators', trim($config->groupecreators), $this->pluginconfig);
        set_config('removeuser', $config->removeuser, $this->pluginconfig);

        return true;
    }

    /**
     * Returns true if user should be coursecreator.
     *
     * @param mixed $username    username (without system magic quotes)
     * @return boolean result
     */
    public function iscreator($username) {
        if (empty($this->config->host_url) or (empty($this->config->attrcreators) && empty($this->config->groupecreators)) or empty($this->config->memberattribute)) {
            return false;
        }

        $extusername = core_text::convert($username, 'utf-8', $this->config->ldapencoding);

        // Test for group creator
        if (!empty($this->config->groupecreators)) {
            $ldapconnection = $this->ldap_connect();
            if ($this->config->memberattribute_isdn) {
                if(!($userid = $this->ldap_find_userdn($ldapconnection, $extusername))) {
                    return false;
                }
            } else {
                $userid = $extusername;
            }

            $group_dns = explode(';', $this->config->groupecreators);
            if (ldap_isgroupmember($ldapconnection, $userid, $group_dns, $this->config->memberattribute)) {
                return true;
            }
        }

        // Build filter for attrcreator
        if (!empty($this->config->attrcreators)) {
            $attrs = explode(';', $this->config->attrcreators);
            $filter = '(& ('.$this->config->user_attribute."=$username)(|";
            foreach ($attrs as $attr){
                if(strpos($attr, '=')) {
                    $filter .= "($attr)";
                } else {
                    $filter .= '('.$this->config->memberattribute."=$attr)";
                }
            }
            $filter .= '))';

            // Search
            $result = $this->ldap_get_userlist($filter);
            if (count($result) != 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reads user information from LDAP and returns it as array()
     *
     * If no LDAP servers are configured, user information has to be
     * provided via other methods (CSV file, manually, etc.). Return
     * an empty array so existing user info is not lost. Otherwise,
     * calls parent class method to get user info.
     *
     * @param string $username username
     * @return mixed array with no magic quotes or false on error
     */
    public function get_userinfo($username) {
        if (empty($this->config->host_url)) {
            return array();
        }
        return parent::get_userinfo($username);
    }

    /**
     * Syncronizes users from LDAP server to moodle user table.
     *
     * If no LDAP servers are configured, simply return. Otherwise,
     * call parent class method to do the work.
     *
     * @param bool $do_updates will do pull in data updates from LDAP if relevant
     * @return nothing
     */
    public function sync_users($do_updates=true) {
        if (empty($this->config->host_url)) {
            error_log('[AUTH CAS] '.get_string('noldapserver', 'auth_casshib'));
            return;
        }
        parent::sync_users($do_updates);
    }

    /**
    * Hook for logout page
    */
    public function logoutpage_hook() {
        global $USER, $redirect;

        // Only do this if the user is actually logged in via CAS
        if ($USER->auth === $this->authtype) {
            // Check if there is an alternative logout return url defined
            if (isset($this->config->logout_return_url) && !empty($this->config->logout_return_url)) {
                // Set redirect to alternative return url
                $redirect = $this->config->logout_return_url;
            }
        }
    }

    // Additional special synchronisation methods
    public function sync_groups() {
    }

    public function build_teacher_category(&$user) {
        global $DB;

        $config = get_config('auth_casshib');
        $teacherrootcat = $config->teacherrootcategory;

        $teachercatname = $this->make_teacher_catname($user);

        if (!$DB->get_record('course_categories', array('name' => $teachercatname, 'parent' => $teacherrootcat))) {
            $cat = new StdClass;
            $cat->parent = $teacherrootcat;
            $cat->idnumber = strtoupper($user->lastname).'_'.substr(strtoupper($user->firstname), 0, 1); // This is later used for reordering.
            $cat->name = $teachercatname;
        }
    }

    public function make_teacher_catname($user, $fullldapinfo) {
        $str = get_string('coursesof', 'auth_casshib');
        $str .= ' '.$fullldapinfo->civ.' '.initialize($user->firstname).' '.strtoupper($user->name);
        return $str;
    }

    public function process_dean_attributes($user) {
    }

    public function process_documentarist_attributes($user) {
    }

    public function filter_user_attributes(&$user) {
    }

    public function process_user_sdet_info(&$user) {
    }

    /**
     * Reorders teachers category based on teacher name stored in idnumber
     */
    public function reorder_teacher_categories() {
        $config = get_config('auth_casshib');

        $teacherrootcat = $config->teacherrootcategory;
        $allcats = $DB->get_records('course_categories', array('parent' => $teacherrootcat), 'sortorder', 'id,idnumber,sortorder');
        if ($allcats) {
            uasort($allcats, 'sortbyidnumber');
            $order = 10;
            foreach ($allcats as $cat) {
                $cat->sortorder = $order;
                $order += 10;
                $DB->update_record('course_categories', $cat);
            }
        }
    }
}
