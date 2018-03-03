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

require('../../config.php');
require_once($CFG->dirroot.'/auth/casshib/locallib.php');

$context = context_system::instance();
$url = new moodle_url('/auth/casent/quickconfig.php');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('locallogout', 'auth_casent'));
$PAGE->set_heading(get_string('locallogout', 'auth_casent'));

echo $OUTPUT->header();

require_login();
require_capability('moodle/site:config', context_system::instance());

echo '<pre>';
// Convert all cas users.

capture_cas_settings();

echo '</pre>';

echo $OUTPUT->continue_button(new moodle_url('/admin/auth_config.php', array('auth' => 'casshib')));

echo $OUTPUT->footer();
