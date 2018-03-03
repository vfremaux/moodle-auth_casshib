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

require_once('../../config.php');

// require_login();

$authplugin = get_auth_plugin('casshib');

redirect('https://'.$authplugin->config->hostname);
