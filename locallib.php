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
 * @package     auth_casshib
 * @category    auth
 * @author      Valery Fremaux pour ac-rennes.fr
 * @copyright  2015 onwards Valery Fremaux (http://www.mylearnignfactory.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * tokenizes the given name and make an initials construct
 * so : 
 * Pierre => P.
 * Jean-Pierre => JP.
 */
function initialize($username) {

    $parts = preg_split('/[ -]/', $username);
    foreach($parts as $token) {
        $inits[] = strtoupper(substr($token, 0, 1));
    }
    $init = implode('', $inits).'.';

    return $init;
}

function sortbyidnumber($a, $b) {
    return strcmp($a->idnumber, $b->idnumber);
}

function capture_cas_settings() {
    global $DB, $CFG;

    // Shift all users from cas to casshib.

    $sql = "
        UPDATE
            {user}
        SET
            auth = 'casshib'
        WHERE
            auth = 'cas'
    ";
    $DB->execute($sql);
    mtrace("Cas Users Udated");

    // Copy cas config.

    $casconfig = get_config('auth/cas');
    foreach ($casconfig as $key => $value) {
        if ($key != 'version') {
            set_config($key, $value, 'auth/casshib');
        }
    }
    mtrace("Cas Settings copied");

    // Change cas call into auth stack.

    $authstack = get_config('moodle', 'auth');
    $stack = explode(',', $authstack);

    $newstack = array();
    foreach($stack as $st) {
        if ($st != 'cas') {
            $newstack[] = $st;
        }
    }
    if (!in_array('casshib', $newstack)) $newstack[] = 'casshib';
    $authstack = implode(',', $newstack);

    set_config('auth', $authstack);
    mtrace("Cas Auth enabled with $authstack");

    // Change auth setting in ent_installer.
    set_config('real_used_auth', 'casshib', 'local_ent_installer');
    mtrace("Cas Ent Installer patched");

    // ensure last version.
    $plugin = new StdClass();
    require_once($CFG->dirroot.'/auth/casshib/version.php');
    set_config('version', $plugin->version, 'auth_casshib');

    cache_helper::invalidate_by_definition('core', 'config');
}