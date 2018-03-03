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
 * @file     CASSHIB/AuthenticationException.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

/**
 * This interface defines methods that allow proxy-authenticated service handlers
 * to interact with phpCASSHIB.
 *
 * Proxy service handlers must implement this interface as well as call
 * phpCASSHIB::initializeProxiedService($this) at some point in their implementation.
 *
 * While not required, proxy-authenticated service handlers are encouraged to
 * implement the CASSHIB_ProxiedService_Testable interface to facilitate unit testing.
 *
 * @class    CASSHIB_AuthenticationException
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

class CASSHIB_AuthenticationException
extends RuntimeException
implements CASSHIB_Exception
{

    /**
     * This method is used to print the HTML output when the user was not
     * authenticated.
     *
     * @param CASSHIB_Client $client       phpCASSHIB client
     * @param string     $failure      the failure that occured
     * @param string     $CASSHIB_url      the URL the CASSHIB server was asked for
     * @param bool       $no_response  the response from the CASSHIB server (other
     * parameters are ignored if TRUE)
     * @param bool       $bad_response bad response from the CASSHIB server ($err_code
     * and $err_msg ignored if TRUE)
     * @param string     $CASSHIB_response the response of the CASSHIB server
     * @param int        $err_code     the error code given by the CASSHIB server
     * @param string     $err_msg      the error message given by the CASSHIB server
     */
    public function __construct($client,$failure,$CASSHIB_url,$no_response,
        $bad_response='',$CASSHIB_response='',$err_code='',$err_msg=''
    ) {
        phpCASSHIB::traceBegin();
        $lang = $client->getLangObj();
        $client->printHTMLHeader($lang->getAuthenticationFailed());
        printf(
            $lang->getYouWereNotAuthenticated(),
            htmlentities($client->getURL()),
            isset($_SERVER['SERVER_ADMIN']) ? $_SERVER['SERVER_ADMIN']:''
        );
        phpCASSHIB::trace('CASSHIB URL: '.$CASSHIB_url);
        phpCASSHIB::trace('Authentication failure: '.$failure);
        if ( $no_response ) {
            phpCASSHIB::trace('Reason: no response from the CASSHIB server');
        } else {
            if ( $bad_response ) {
                phpCASSHIB::trace('Reason: bad response from the CASSHIB server');
            } else {
                switch ($client->getServerVersion()) {
                case CASSHIB_VERSION_1_0:
                    phpCASSHIB::trace('Reason: CASSHIB error');
                    break;
                case CASSHIB_VERSION_2_0:
                case CASSHIB_VERSION_3_0:
                    if ( empty($err_code) ) {
                        phpCASSHIB::trace('Reason: no CASSHIB error');
                    } else {
                        phpCASSHIB::trace('Reason: ['.$err_code.'] CASSHIB error: '.$err_msg);
                    }
                    break;
                }
            }
            phpCASSHIB::trace('CASSHIB response: '.$CASSHIB_response);
        }
        $client->printHTMLFooter();
        phpCASSHIB::traceExit();
    }

}
?>
