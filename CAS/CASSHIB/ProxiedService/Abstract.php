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
 * @file     CASSHIB/ProxiedService/Abstract.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

/**
 * This class implements common methods for ProxiedService implementations included
 * with phpCASSHIB.
 *
 * @class    CASSHIB_ProxiedService_Abstract
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */
abstract class CASSHIB_ProxiedService_Abstract
implements CASSHIB_ProxiedService, CASSHIB_ProxiedService_Testable
{

    /**
     * The proxy ticket that can be used when making service requests.
     * @var string $_proxyTicket;
     */
    private $_proxyTicket;

    /**
     * Register a proxy ticket with the Proxy that it can use when making requests.
     *
     * @param string $proxyTicket proxy ticket
     *
     * @return void
     * @throws InvalidArgumentException If the $proxyTicket is invalid.
     * @throws CASSHIB_OutOfSequenceException If called after a proxy ticket has
     *         already been initialized/set.
     */
    public function setProxyTicket ($proxyTicket)
    {
        if (empty($proxyTicket)) {
            throw new CASSHIB_InvalidArgumentException(
                'Trying to initialize with an empty proxy ticket.'
            );
        }
        if (!empty($this->_proxyTicket)) {
            throw new CASSHIB_OutOfSequenceException(
                'Already initialized, cannot change the proxy ticket.'
            );
        }
        $this->_proxyTicket = $proxyTicket;
    }

    /**
     * Answer the proxy ticket to be used when making requests.
     *
     * @return string
     * @throws CASSHIB_OutOfSequenceException If called before a proxy ticket has
     * already been initialized/set.
     */
    protected function getProxyTicket ()
    {
        if (empty($this->_proxyTicket)) {
            throw new CASSHIB_OutOfSequenceException(
                'No proxy ticket yet. Call $this->initializeProxyTicket() to aquire the proxy ticket.'
            );
        }

        return $this->_proxyTicket;
    }

    /**
     * @var CASSHIB_Client $_CASSHIBClient;
     */
    private $_CASSHIBClient;

    /**
     * Use a particular CASSHIB_Client->initializeProxiedService() rather than the
     * static phpCASSHIB::initializeProxiedService().
     *
     * This method should not be called in standard operation, but is needed for unit
     * testing.
     *
     * @param CASSHIB_Client $CASSHIBClient CASSHIB client
     *
     * @return void
     * @throws CASSHIB_OutOfSequenceException If called after a proxy ticket has
     * already been initialized/set.
     */
    public function setCASSHIBClient (CASSHIB_Client $CASSHIBClient)
    {
        if (!empty($this->_proxyTicket)) {
            throw new CASSHIB_OutOfSequenceException(
                'Already initialized, cannot change the CASSHIB_Client.'
            );
        }

        $this->_CASSHIBClient = $CASSHIBClient;
    }

    /**
     * Fetch our proxy ticket.
     *
     * Descendent classes should call this method once their service URL is available
     * to initialize their proxy ticket.
     *
     * @return void
     * @throws CASSHIB_OutOfSequenceException If called after a proxy ticket has
     * already been initialized.
     */
    protected function initializeProxyTicket()
    {
        if (!empty($this->_proxyTicket)) {
            throw new CASSHIB_OutOfSequenceException(
                'Already initialized, cannot initialize again.'
            );
        }
        // Allow usage of a particular CASSHIB_Client for unit testing.
        if (empty($this->_CASSHIBClient)) {
            phpCASSHIB::initializeProxiedService($this);
        } else {
            $this->_CASSHIBClient->initializeProxiedService($this);
        }
    }

}
?>
