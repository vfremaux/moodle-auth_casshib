<?php

/**
 * Licensed to Jasig under one or more contributor license
 * agreements. See the NOTICE file distributed with this work for
 * additional information regarding copyright ownership.
 *
 * Jasig licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP Version 5
 *
 * @file     CASSHIB/ProxiedService/Testabel.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

/**
 * This interface defines methods that allow proxy-authenticated service handlers
 * to be tested in unit tests.
 *
 * Classes implementing this interface SHOULD store the CASSHIB_Client passed and
 * initialize themselves with that client rather than via the static phpCASSHIB
 * method. For example:
 *
 *		/ **
 *		 * Fetch our proxy ticket.
 *		 * /
 *		protected function initializeProxyTicket() {
 *			// Allow usage of a particular CASSHIB_Client for unit testing.
 *			if (is_null($this->CASSHIBClient))
 *				phpCASSHIB::initializeProxiedService($this);
 *			else
 *				$this->CASSHIBClient->initializeProxiedService($this);
 *		}
 *
 * @class    CASSHIB_ProxiedService_Testabel
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */
interface CASSHIB_ProxiedService_Testable
{

    /**
     * Use a particular CASSHIB_Client->initializeProxiedService() rather than the
     * static phpCASSHIB::initializeProxiedService().
     *
     * This method should not be called in standard operation, but is needed for unit
     * testing.
     *
     * @param CASSHIB_Client $CASSHIBClient CASSHIB client object
     *
     * @return void
     * @throws CASSHIB_OutOfSequenceException If called after a proxy ticket has
     *         already been initialized/set.
     */
    public function setCASSHIBClient (CASSHIB_Client $CASSHIBClient);

}
?>
