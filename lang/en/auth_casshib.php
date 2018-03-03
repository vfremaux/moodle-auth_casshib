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
 * Strings for component 'auth_cas', language 'en'.
 *
 * @package   auth_casshib
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['CASform'] = 'Authentication choice';
$string['accesCAS'] = 'CAS users';
$string['accesNOCAS'] = 'other users';
$string['auth_casshib_auth_user_create'] = 'Create users externally';
$string['auth_casshib_baseuri'] = 'URI of the server (nothing if no baseUri)<br />For example, if the CAS server responds to host.domaine.fr/CAS/ then<br />cas_baseuri = CAS/';
$string['auth_casshib_baseuri_key'] = 'Base URI';
$string['auth_casshib_broken_password'] = 'You cannot proceed without changing your password, however there is no available page for changing it. Please contact your Moodle Administrator.';
$string['auth_casshib_cantconnect'] = 'LDAP part of CAS-module cannot connect to server: {$a}';
$string['auth_casshib_casversion'] = 'CAS protocol version';
$string['auth_casshib_certificate_check'] = 'Select \'yes\' if you want to validate the server certificate';
$string['auth_casshib_certificate_check_key'] = 'Server validation';
$string['auth_casshib_certificate_path'] = 'Path of the CA chain file (PEM Format) to validate the server certificate';
$string['auth_casshib_certificate_path_empty'] = 'If you turn on Server validation, you need to specify a certificate path';
$string['auth_casshib_certificate_path_key'] = 'Certificate path';
$string['auth_casshib_changepasswordurl'] = 'Password-change URL';
$string['auth_casshib_create_user'] = 'Turn this on if you want to insert CAS-authenticated users in Moodle database. If not then only users who already exist in the Moodle database can log in.';
$string['auth_casshib_create_user_key'] = 'Create user';
$string['auth_casshib_enabled'] = 'Turn this on if you want to use CAS authentication.';
$string['auth_casshib_hostname'] = 'Hostname of the CAS server <br />eg: host.domain.fr';
$string['auth_casshib_hostname_key'] = 'Hostname';
$string['auth_casshib_invalidcaslogin'] = 'Sorry, your login has failed - you could not be authorised';
$string['auth_casshib_language'] = 'Select language for authentication pages';
$string['auth_casshib_language_key'] = 'Language';
$string['auth_casshib_logincas'] = 'Secure connection access';
$string['auth_casshib_logout_return_url'] = 'Provide the URL that CAS users shall be redirected to after logging out.<br />If left empty, users will be redirected to the location that moodle will redirect users to';
$string['auth_casshib_logout_return_url_key'] = 'Alternative logout return URL';
$string['auth_casshib_logoutcas'] = 'Select \'yes\' if you want to logout from CAS when you disconnect from Moodle';
$string['auth_casshib_logoutcas_key'] = 'CAS logout option';
$string['auth_casshib_multiauth'] = 'Select \'yes\' if you want to have multi-authentication (CAS + other authentication)';
$string['auth_casshib_multiauth_key'] = 'Multi-authentication';
$string['auth_casshib_notify'] = 'Notify administrators on failures';
$string['auth_casshib_notify_desc'] = 'Notify admin on CAS auth errors';
$string['auth_casshib_port'] = 'Port of the CAS server';
$string['auth_casshib_port_key'] = 'Port';
$string['auth_casshib_proxycas'] = 'Select \'yes\' if you use CAS in proxy-mode';
$string['auth_casshib_proxycas_key'] = 'Proxy mode';
$string['auth_casshib_redirectonfailure'] = 'Redirect on failure';
$string['auth_casshib_server_settings'] = 'CAS server configuration';
$string['auth_casshib_shib_proxy_validate_url'] = 'Provide the URL that CAS layer shall be redirected to to validate in shibboleth mode the service as a proxy';
$string['auth_casshib_shib_proxy_validate_url_key'] = 'Shibboleth proxy validate URL';
$string['auth_casshib_shib_service_validate_url'] = 'Provide the URL that CAS layer shall be redirected to to validate in shibboleth mode the service';
$string['auth_casshib_shib_service_validate_url_key'] = 'Shibboleth service validate URL';
$string['auth_casshib_shib_settings'] = 'Additional Shib settings';
$string['auth_casshib_text'] = 'Secure connection';
$string['auth_casshib_use_cas'] = 'Use CAS';
$string['auth_casshib_version'] = 'CAS protocol version to use';
$string['auth_casshibdescription'] = 'This method uses a CAS server (Central Authentication Service) to authenticate users in a Single Sign On environment (SSO). You can also use a simple LDAP authentication. If the given username and password are valid according to CAS, Moodle creates a new user entry in its database, taking user attributes from LDAP if required. On following logins only the username and password are checked.';
$string['auth_casshibnotinstalled'] = 'Cannot use CAS authentication. The PHP LDAP module is not installed.';
$string['capture_cas_settings'] = 'Capture all cas settings and users and activate';
$string['invisible'] = 'Direct invisible redirection';
$string['locallogout'] = 'Local logout';
$string['locallogoutmessage'] = 'You have disconnected local session. to use the LMS again, use your portal link. You can close this window now.';
$string['locallogoutmessage2'] = 'Note that your master portal session is still alive. For more security, you should close entirely your browser when closing your working session.';
$string['locallogouttitle'] = 'Local logout';
$string['nocasconnect'] = 'No CAS connection available';
$string['nocasconnectmailbody_tpl'] = 'CAS could not be reached at %%CASURL%% or does not answer at %%SITE%%';
$string['nocasconnectmailtitle_tpl'] = '[%%SITE%%] CAS connexion failure';
$string['nocasconnectmessage'] = 'The authentification server is down or not reachable at the moment.';
$string['noldapserver'] = 'No LDAP server configured for CAS! Syncing disabled.';
$string['pluginname'] = 'CAS server (SSO) Shibboleth addition';
$string['quick_config'] = 'Quick config';
$string['remotelogoutmessage'] = 'Welcome to Moodle.<br/<br/>to use this service please connect on the central ATRIUM postal. Use <a href="/auth/casent/redirecttocas.php">this link</a>';
$string['unknownincomingmailbody_tpl'] = '%%USERNAME%% was authentified by CAS but is not kown as user on %%SITE%%';
$string['unknownincomingmailtitle_tpl'] = '[%%SITE%%] CAS Unknown incomming user';
$string['unknownincominguser'] = 'Authentified user is not known locally.';
$string['visible'] = 'Redirection after a message panel being shown';
// $string['remotelogoutmessage'] = 'You have disconnected your master session. to use the LMS again, use your portal link. You can close this window now.';

$string['unknownincomingmessage'] = '<p>You seem being successfully authentified to the CAS server we are using, but we do not know you locally.
This may be due to a failure of our user account synchronisation, or your account may not yet be propagated to this site.
Administrators have been informed of your attempt. You may also mistaked the destination you want to reach.
</p>
<p>In that case, please login again in <a href="{$a}">your access portal</a></p>';
