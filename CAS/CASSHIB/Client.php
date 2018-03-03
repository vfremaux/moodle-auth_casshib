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
 * @file     CASSHIB/Client.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Pascal Aubry <pascal.aubry@univ-rennes1.fr>
 * @author   Olivier Berger <olivier.berger@it-sudparis.eu>
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

/**
 * The CASSHIB_Client class is a client interface that provides CASSHIB authentication
 * to PHP applications.
 *
 * @class    CASSHIB_Client
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Pascal Aubry <pascal.aubry@univ-rennes1.fr>
 * @author   Olivier Berger <olivier.berger@it-sudparis.eu>
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 *
 */

class CASSHIB_Client
{

    // ########################################################################
    //  HTML OUTPUT
    // ########################################################################
    /**
    * @addtogroup internalOutput
    * @{
    */

    private $authconfig;

    /**
     * This method filters a string by replacing special tokens by appropriate values
     * and prints it. The corresponding tokens are taken into account:
     * - __CASSHIB_VERSION__
     * - __PHPCASSHIB_VERSION__
     * - __SERVER_BASE_URL__
     *
     * Used by CASSHIB_Client::PrintHTMLHeader() and CASSHIB_Client::printHTMLFooter().
     *
     * @param string $str the string to filter and output
     *
     * @return void
     */
    private function _htmlFilterOutput($str)
    {
        $str = str_replace('__CASSHIB_VERSION__', $this->getServerVersion(), $str);
        $str = str_replace('__PHPCASSHIB_VERSION__', phpCASSHIB::getVersion(), $str);
        $str = str_replace('__SERVER_BASE_URL__', $this->_getServerBaseURL(), $str);
        echo $str;
    }

    /**
     * A string used to print the header of HTML pages. Written by
     * CASSHIB_Client::setHTMLHeader(), read by CASSHIB_Client::printHTMLHeader().
     *
     * @hideinitializer
     * @see CASSHIB_Client::setHTMLHeader, CASSHIB_Client::printHTMLHeader()
     */
    private $_output_header = '';

    /**
     * This method prints the header of the HTML output (after filtering). If
     * CASSHIB_Client::setHTMLHeader() was not used, a default header is output.
     *
     * @param string $title the title of the page
     *
     * @return void
     * @see _htmlFilterOutput()
     */
    public function printHTMLHeader($title)
    {
        $this->_htmlFilterOutput(
            str_replace(
                '__TITLE__', $title,
                (empty($this->_output_header)
                ? '<html><head><title>__TITLE__</title></head><body><h1>__TITLE__</h1>'
                : $this->_output_header)
            )
        );
    }

    /**
     * A string used to print the footer of HTML pages. Written by
     * CASSHIB_Client::setHTMLFooter(), read by printHTMLFooter().
     *
     * @hideinitializer
     * @see CASSHIB_Client::setHTMLFooter, CASSHIB_Client::printHTMLFooter()
     */
    private $_output_footer = '';

    /**
     * This method prints the footer of the HTML output (after filtering). If
     * CASSHIB_Client::setHTMLFooter() was not used, a default footer is output.
     *
     * @return void
     * @see _htmlFilterOutput()
     */
    public function printHTMLFooter()
    {
        $lang = $this->getLangObj();
        $this->_htmlFilterOutput(
            empty($this->_output_footer)?
            (phpCASSHIB::getVerbose())?
                '<hr><address>phpCASSHIB __PHPCASSHIB_VERSION__ '
                .$lang->getUsingServer()
                .' <a href="__SERVER_BASE_URL__">__SERVER_BASE_URL__</a> (CASSHIB __CASSHIB_VERSION__)</a></address></body></html>'
                :'</body></html>'
            :$this->_output_footer
        );
    }

    /**
     * This method set the HTML header used for all outputs.
     *
     * @param string $header the HTML header.
     *
     * @return void
     */
    public function setHTMLHeader($header)
    {
        // Argument Validation
        if (gettype($header) != 'string')
            throw new CASSHIB_TypeMismatchException($header, '$header', 'string');

        $this->_output_header = $header;
    }

    /**
     * This method set the HTML footer used for all outputs.
     *
     * @param string $footer the HTML footer.
     *
     * @return void
     */
    public function setHTMLFooter($footer)
    {
        // Argument Validation
        if (gettype($footer) != 'string')
            throw new CASSHIB_TypeMismatchException($footer, '$footer', 'string');

        $this->_output_footer = $footer;
    }


    /** @} */


    // ########################################################################
    //  INTERNATIONALIZATION
    // ########################################################################
    /**
    * @addtogroup internalLang
    * @{
    */
    /**
     * A string corresponding to the language used by phpCASSHIB. Written by
     * CASSHIB_Client::setLang(), read by CASSHIB_Client::getLang().

     * @note debugging information is always in english (debug purposes only).
     */
    private $_lang = PHPCASSHIB_LANG_DEFAULT;

    /**
     * This method is used to set the language used by phpCASSHIB.
     *
     * @param string $lang representing the language.
     *
     * @return void
     */
    public function setLang($lang)
    {
        // Argument Validation
        if (gettype($lang) != 'string')
            throw new CASSHIB_TypeMismatchException($lang, '$lang', 'string');

        if (strpos($lang, 'CASSHIB') === false) {
            // Fix classname.
            $firstletter = core_text::strtoupper($lang[0]);
            $lang = 'CASSHIB_Languages_'.$firstletter.substr($lang, 1);
        }

        phpCASSHIB::traceBegin();
        $obj = new $lang();
        if (!($obj instanceof CASSHIB_Languages_LanguageInterface)) {
            throw new CASSHIB_InvalidArgumentException(
                '$className must implement the CASSHIB_Languages_LanguageInterface'
            );
        }
        $this->_lang = $lang;
        phpCASSHIB::traceEnd();
    }
    /**
     * Create the language
     *
     * @return CASSHIB_Languages_LanguageInterface object implementing the class
     */
    public function getLangObj()
    {
        $classname = $this->_lang;
        return new $classname();
    }

    /** @} */
    // ########################################################################
    //  Cas server CONFIG
    // ########################################################################
    /**
    * @addtogroup internalConfig
    * @{
    */

    /**
     * a record to store information about the Cas server.
     * - $_server['version']: the version of the Cas server
     * - $_server['hostname']: the hostname of the Cas server
     * - $_server['port']: the port the Cas server is running on
     * - $_server['uri']: the base URI the Cas server is responding on
     * - $_server['base_url']: the base URL of the Cas server
     * - $_server['login_url']: the login URL of the Cas server
     * - $_server['service_validate_url']: the service validating URL of the
     *   Cas server
     * - $_server['proxy_url']: the proxy URL of the Cas server
     * - $_server['proxy_validate_url']: the proxy validating URL of the Cas server
     * - $_server['logout_url']: the logout URL of the Cas server
     *
     * $_server['version'], $_server['hostname'], $_server['port'] and
     * $_server['uri'] are written by CASSHIB_Client::CASSHIB_Client(), read by
     * CASSHIB_Client::getServerVersion(), CASSHIB_Client::_getServerHostname(),
     * CASSHIB_Client::_getServerPort() and CASSHIB_Client::_getServerURI().
     *
     * The other fields are written and read by CASSHIB_Client::_getServerBaseURL(),
     * CASSHIB_Client::getServerLoginURL(), CASSHIB_Client::getServerServiceValidateURL(),
     * CASSHIB_Client::getServerProxyValidateURL() and CASSHIB_Client::getServerLogoutURL().
     *
     * @hideinitializer
     */
    private $_server = array(
        'version' => -1,
        'hostname' => 'none',
        'port' => -1,
        'uri' => 'none');

    /**
     * This method is used to retrieve the version of the Cas server.
     *
     * @return string the version of the Cas server.
     */
    public function getServerVersion()
    {
        return $this->_server['version'];
    }

    /**
     * This method is used to retrieve the hostname of the Cas server.
     *
     * @return string the hostname of the Cas server.
     */
    private function _getServerHostname()
    {
        return $this->_server['hostname'];
    }

    /**
     * This method is used to retrieve the port of the Cas server.
     *
     * @return string the port of the Cas server.
     */
    private function _getServerPort()
    {
        return $this->_server['port'];
    }

    /**
     * This method is used to retrieve the URI of the Cas server.
     *
     * @return string a URI.
     */
    private function _getServerURI()
    {
        return $this->_server['uri'];
    }

    /**
     * This method is used to retrieve the base URL of the Cas server.
     *
     * @return string a URL.
     */
    private function _getServerBaseURL()
    {
        // the URL is build only when needed
        if ( empty($this->_server['base_url']) ) {
            $this->_server['base_url'] = 'https://' . $this->_getServerHostname();
            if ($this->_getServerPort()!=443) {
                $this->_server['base_url'] .= ':'
                .$this->_getServerPort();
            }
            $this->_server['base_url'] .= $this->_getServerURI();
        }
        return $this->_server['base_url'];
    }

    /**
     * This method is used to retrieve the login URL of the Cas server.
     *
     * @param bool $gateway true to check authentication, false to force it
     * @param bool $renew   true to force the authentication with the Cas server
     *
     * @return a URL.
     * @note It is recommended that CASSHIB implementations ignore the "gateway"
     * parameter if "renew" is set
     */
    public function getServerLoginURL($gateway=false,$renew=false)
    {
        phpCASSHIB::traceBegin();
        // the URL is build only when needed
        if ( empty($this->_server['login_url']) ) {
            $this->_server['login_url'] = $this->_buildQueryUrl($this->_getServerBaseURL().'login','service='.urlencode($this->getURL()));
        }
        $url = $this->_server['login_url'];
        if ($renew) {
            // It is recommended that when the "renew" parameter is set, its
            // value be "true"
            $url = $this->_buildQueryUrl($url, 'renew=true');
        } elseif ($gateway) {
            // It is recommended that when the "gateway" parameter is set, its
            // value be "true"
            $url = $this->_buildQueryUrl($url, 'gateway=true');
        }
        phpCASSHIB::traceEnd($url);
        return $url;
    }

    /**
     * This method sets the login URL of the Cas server.
     *
     * @param string $url the login URL
     *
     * @return string login url
     */
    public function setServerLoginURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_server['login_url'] = $url;
    }


    /**
     * This method sets the serviceValidate URL of the Cas server.
     *
     * @param string $url the serviceValidate URL
     *
     * @return string serviceValidate URL
     */
    public function setServerServiceValidateURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_server['service_validate_url'] = $url;
    }


    /**
     * This method sets the proxyValidate URL of the Cas server.
     *
     * @param string $url the proxyValidate URL
     *
     * @return string proxyValidate URL
     */
    public function setServerProxyValidateURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_server['proxy_validate_url'] = $url;
    }


    /**
     * This method sets the samlValidate URL of the Cas server.
     *
     * @param string $url the samlValidate URL
     *
     * @return string samlValidate URL
     */
    public function setServerSamlValidateURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_server['saml_validate_url'] = $url;
    }


    /**
     * This method is used to retrieve the service validating URL of the Cas server.
     *
     * @return string serviceValidate URL.
     */
    public function getServerServiceValidateURL()
    {
        phpCASSHIB::traceBegin();
        // the URL is build only when needed
        if ( empty($this->_server['service_validate_url']) ) {
            switch ($this->getServerVersion()) {
            case CASSHIB_VERSION_1_0:
                $this->_server['service_validate_url'] = $this->_getServerBaseURL()
                .'validate';
                break;
            case CASSHIB_VERSION_2_0:
                $this->_server['service_validate_url'] = $this->_getServerBaseURL()
                .'serviceValidate';
                break;
            case CASSHIB_VERSION_3_0:
                $this->_server['service_validate_url'] = $this->_getServerBaseURL()
                .'p3/serviceValidate';
                break;
            }
        }
        $url = $this->_buildQueryUrl(
            $this->_server['service_validate_url'],
            'service='.urlencode($this->getURL())
        );
        phpCASSHIB::traceEnd($url);
        return $url;
    }
    /**
     * This method is used to retrieve the SAML validating URL of the Cas server.
     *
     * @return string samlValidate URL.
     */
    public function getServerSamlValidateURL()
    {
        phpCASSHIB::traceBegin();
        // the URL is build only when needed
        if ( empty($this->_server['saml_validate_url']) ) {
            switch ($this->getServerVersion()) {
            case SAML_VERSION_1_1:
                $this->_server['saml_validate_url'] = $this->_getServerBaseURL().'samlValidate';
                break;
            }
        }

        $url = $this->_buildQueryUrl(
            $this->_server['saml_validate_url'],
            'TARGET='.urlencode($this->getURL())
        );
        phpCASSHIB::traceEnd($url);
        return $url;
    }

    /**
     * This method is used to retrieve the proxy validating URL of the Cas server.
     *
     * @return string proxyValidate URL.
     */
    public function getServerProxyValidateURL()
    {
        phpCASSHIB::traceBegin();
        // the URL is build only when needed
        if ( empty($this->_server['proxy_validate_url']) ) {
            switch ($this->getServerVersion()) {
            case CASSHIB_VERSION_1_0:
                $this->_server['proxy_validate_url'] = '';
                break;
            case CASSHIB_VERSION_2_0:
                $this->_server['proxy_validate_url'] = $this->_getServerBaseURL().'proxyValidate';
                break;
            case CASSHIB_VERSION_3_0:
                $this->_server['proxy_validate_url'] = $this->_getServerBaseURL().'p3/proxyValidate';
                break;
            }
        }
        $url = $this->_buildQueryUrl(
            $this->_server['proxy_validate_url'],
            'service='.urlencode($this->getURL())
        );
        phpCASSHIB::traceEnd($url);
        return $url;
    }


    /**
     * This method is used to retrieve the proxy URL of the Cas server.
     *
     * @return  string proxy URL.
     */
    public function getServerProxyURL()
    {
        // the URL is build only when needed
        if ( empty($this->_server['proxy_url']) ) {
            switch ($this->getServerVersion()) {
                case CASSHIB_VERSION_1_0:
                    $this->_server['proxy_url'] = '';
                    break;
                case CASSHIB_VERSION_2_0:
                case CASSHIB_VERSION_3_0:
                    $this->_server['proxy_url'] = $this->_getServerBaseURL().'proxy';
                    break;
            }
        }
        return $this->_server['proxy_url'];
    }

    /**
     * This method is used to retrieve the logout URL of the Cas server.
     *
     * @return string logout URL.
     */
    public function getServerLogoutURL()
    {
        // the URL is build only when needed
        if ( empty($this->_server['logout_url']) ) {
            $this->_server['logout_url'] = $this->_getServerBaseURL().'logout';
        }
        return $this->_server['logout_url'];
    }

    /**
     * This method sets the logout URL of the Cas server.
     *
     * @param string $url the logout URL
     *
     * @return string logout url
     */
    public function setServerLogoutURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_server['logout_url'] = $url;
    }

    /**
     * An array to store extra curl options.
     */
    private $_curl_options = array();

    /**
     * This method is used to set additional user curl options.
     *
     * @param string $key   name of the curl option
     * @param string $value value of the curl option
     *
     * @return void
     */
    public function setExtraCurlOption($key, $value)
    {
        $this->_curl_options[$key] = $value;
    }

    /** @} */

    // ########################################################################
    //  Change the internal behaviour of phpCASSHIB
    // ########################################################################

    /**
     * @addtogroup internalBehave
     * @{
     */

    /**
     * The class to instantiate for making web requests in readUrl().
     * The class specified must implement the CASSHIB_Request_RequestInterface.
     * By default CASSHIB_Request_CurlRequest is used, but this may be overridden to
     * supply alternate request mechanisms for testing.
     */
    private $_requestImplementation = 'CASSHIB_Request_CurlRequest';

    /**
     * Override the default implementation used to make web requests in readUrl().
     * This class must implement the CASSHIB_Request_RequestInterface.
     *
     * @param string $className name of the RequestImplementation class
     *
     * @return void
     */
    public function setRequestImplementation ($className)
    {
        $obj = new $className;
        if (!($obj instanceof CASSHIB_Request_RequestInterface)) {
            throw new CASSHIB_InvalidArgumentException(
                '$className must implement the CASSHIB_Request_RequestInterface'
            );
        }
        $this->_requestImplementation = $className;
    }

    /**
     * @var boolean $_clearTicketsFromUrl; If true, phpCASSHIB will clear session
     * tickets from the URL after a successful authentication.
     */
    private $_clearTicketsFromUrl = true;

    /**
     * Configure the client to not send redirect headers and call exit() on
     * authentication success. The normal redirect is used to remove the service
     * ticket from the client's URL, but for running unit tests we need to
     * continue without exiting.
     *
     * Needed for testing authentication
     *
     * @return void
     */
    public function setNoClearTicketsFromUrl ()
    {
        $this->_clearTicketsFromUrl = false;
    }

    /**
     * @var callback $_postAuthenticateCallbackFunction;
     */
    private $_postAuthenticateCallbackFunction = null;

    /**
     * @var array $_postAuthenticateCallbackArgs;
     */
    private $_postAuthenticateCallbackArgs = array();

    /**
     * Set a callback function to be run when a user authenticates.
     *
     * The callback function will be passed a $logoutTicket as its first parameter,
     * followed by any $additionalArgs you pass. The $logoutTicket parameter is an
     * opaque string that can be used to map a session-id to the logout request
     * in order to support single-signout in applications that manage their own
     * sessions (rather than letting phpCASSHIB start the session).
     *
     * phpCASSHIB::forceAuthentication() will always exit and forward client unless
     * they are already authenticated. To perform an action at the moment the user
     * logs in (such as registering an account, performing logging, etc), register
     * a callback function here.
     *
     * @param string $function       callback function to call
     * @param array  $additionalArgs optional array of arguments
     *
     * @return void
     */
    public function setPostAuthenticateCallback ($function, array $additionalArgs = array())
    {
        $this->_postAuthenticateCallbackFunction = $function;
        $this->_postAuthenticateCallbackArgs = $additionalArgs;
    }

    /**
     * @var callback $_signoutCallbackFunction;
     */
    private $_signoutCallbackFunction = null;

    /**
     * @var array $_signoutCallbackArgs;
     */
    private $_signoutCallbackArgs = array();

    /**
     * Set a callback function to be run when a single-signout request is received.
     *
     * The callback function will be passed a $logoutTicket as its first parameter,
     * followed by any $additionalArgs you pass. The $logoutTicket parameter is an
     * opaque string that can be used to map a session-id to the logout request in
     * order to support single-signout in applications that manage their own sessions
     * (rather than letting phpCASSHIB start and destroy the session).
     *
     * @param string $function       callback function to call
     * @param array  $additionalArgs optional array of arguments
     *
     * @return void
     */
    public function setSingleSignoutCallback ($function, array $additionalArgs = array())
    {
        $this->_signoutCallbackFunction = $function;
        $this->_signoutCallbackArgs = $additionalArgs;
    }

    // ########################################################################
    //  Methods for supplying code-flow feedback to integrators.
    // ########################################################################

    /**
     * Ensure that this is actually a proxy object or fail with an exception
     *
     * @throws CASSHIB_OutOfSequenceBeforeProxyException
     *
     * @return void
     */
    public function ensureIsProxy()
    {
        if (!$this->isProxy()) {
            throw new CASSHIB_OutOfSequenceBeforeProxyException();
        }
    }

    /**
     * Mark the caller of authentication. This will help client integraters determine
     * problems with their code flow if they call a function such as getUser() before
     * authentication has occurred.
     *
     * @param bool $auth True if authentication was successful, false otherwise.
     *
     * @return null
     */
    public function markAuthenticationCall ($auth)
    {
        // store where the authentication has been checked and the result
        $dbg = debug_backtrace();
        $this->_authentication_caller = array (
            'file' => $dbg[1]['file'],
            'line' => $dbg[1]['line'],
            'method' => $dbg[1]['class'] . '::' . $dbg[1]['function'],
            'result' => (boolean)$auth
        );
    }
    private $_authentication_caller;

    /**
     * Answer true if authentication has been checked.
     *
     * @return bool
     */
    public function wasAuthenticationCalled ()
    {
        return !empty($this->_authentication_caller);
    }

    /**
     * Ensure that authentication was checked. Terminate with exception if no
     * authentication was performed
     *
     * @throws CASSHIB_OutOfSequenceBeforeAuthenticationCallException
     *
     * @return void
     */
    private function _ensureAuthenticationCalled()
    {
        if (!$this->wasAuthenticationCalled()) {
            throw new CASSHIB_OutOfSequenceBeforeAuthenticationCallException();
        }
    }

    /**
     * Answer the result of the authentication call.
     *
     * Throws a CASSHIB_OutOfSequenceException if wasAuthenticationCalled() is false
     * and markAuthenticationCall() didn't happen.
     *
     * @return bool
     */
    public function wasAuthenticationCallSuccessful ()
    {
        $this->_ensureAuthenticationCalled();
        return $this->_authentication_caller['result'];
    }


    /**
     * Ensure that authentication was checked. Terminate with exception if no
     * authentication was performed
     *
     * @throws CASSHIB_OutOfSequenceBeforeAuthenticationCallException
     *
     * @return void
     */
    public function ensureAuthenticationCallSuccessful()
    {
        $this->_ensureAuthenticationCalled();
        if (!$this->_authentication_caller['result']) {
            throw new CASSHIB_OutOfSequenceException(
                'authentication was checked (by '
                . $this->getAuthenticationCallerMethod()
                . '() at ' . $this->getAuthenticationCallerFile()
                . ':' . $this->getAuthenticationCallerLine()
                . ') but the method returned false'
            );
        }
    }

    /**
     * Answer information about the authentication caller.
     *
     * Throws a CASSHIB_OutOfSequenceException if wasAuthenticationCalled() is false
     * and markAuthenticationCall() didn't happen.
     *
     * @return array Keys are 'file', 'line', and 'method'
     */
    public function getAuthenticationCallerFile ()
    {
        $this->_ensureAuthenticationCalled();
        return $this->_authentication_caller['file'];
    }

    /**
     * Answer information about the authentication caller.
     *
     * Throws a CASSHIB_OutOfSequenceException if wasAuthenticationCalled() is false
     * and markAuthenticationCall() didn't happen.
     *
     * @return array Keys are 'file', 'line', and 'method'
     */
    public function getAuthenticationCallerLine ()
    {
        $this->_ensureAuthenticationCalled();
        return $this->_authentication_caller['line'];
    }

    /**
     * Answer information about the authentication caller.
     *
     * Throws a CASSHIB_OutOfSequenceException if wasAuthenticationCalled() is false
     * and markAuthenticationCall() didn't happen.
     *
     * @return array Keys are 'file', 'line', and 'method'
     */
    public function getAuthenticationCallerMethod ()
    {
        $this->_ensureAuthenticationCalled();
        return $this->_authentication_caller['method'];
    }

    /** @} */

    // ########################################################################
    //  CONSTRUCTOR
    // ########################################################################
    /**
    * @addtogroup internalConfig
    * @{
    */

    /**
     * CASSHIB_Client constructor.
     *
     * @param string $server_version  the version of the Cas server
     * @param bool   $proxy           true if the CASSHIB client is a CASSHIB proxy
     * @param string $server_hostname the hostname of the Cas server
     * @param int    $server_port     the port the Cas server is running on
     * @param string $server_uri      the URI the Cas server is responding on
     * @param bool   $changeSessionID Allow phpCASSHIB to change the session_id
     *                                (Single Sign Out/handleLogoutRequests
     *                                is based on that change)
     *
     * @return a newly created CASSHIB_Client object
     */
    public function __construct(
        $server_version,
        $proxy,
        $server_hostname,
        $server_port,
        $server_uri,
        $changeSessionID = true
    ) {
        // Argument validation
        if (gettype($server_version) != 'string')
            throw new CASSHIB_TypeMismatchException($server_version, '$server_version', 'string');
        if (gettype($proxy) != 'boolean')
            throw new CASSHIB_TypeMismatchException($proxy, '$proxy', 'boolean');
        if (gettype($server_hostname) != 'string')
            throw new CASSHIB_TypeMismatchException($server_hostname, '$server_hostname', 'string');
        if (gettype($server_port) != 'integer')
            throw new CASSHIB_TypeMismatchException($server_port, '$server_port', 'integer');
        if (gettype($server_uri) != 'string')
            throw new CASSHIB_TypeMismatchException($server_uri, '$server_uri', 'string');
        if (gettype($changeSessionID) != 'boolean')
            throw new CASSHIB_TypeMismatchException($changeSessionID, '$changeSessionID', 'boolean');

        phpCASSHIB::traceBegin();
        // true : allow to change the session_id(), false session_id won't be
        // change and logout won't be handle because of that
        $this->_setChangeSessionID($changeSessionID);

        // skip Session Handling for logout requests and if don't want it'
        if (session_id() == "" && !$this->_isLogoutRequest()) {
            session_start();
            phpCASSHIB :: trace("Starting a new session " . session_id());
        }

        // are we in proxy mode ?
        $this->_proxy = $proxy;

        // Make cookie handling available.
        if ($this->isProxy()) {
            if (!isset($_SESSION['phpCASSHIB'])) {
                $_SESSION['phpCASSHIB'] = array();
            }
            if (!isset($_SESSION['phpCASSHIB']['service_cookies'])) {
                $_SESSION['phpCASSHIB']['service_cookies'] = array();
            }
            $this->_serviceCookieJar = new CASSHIB_CookieJar(
                $_SESSION['phpCASSHIB']['service_cookies']
            );
        }

        //check version
        switch ($server_version) {
            case CASSHIB_VERSION_1_0:
                if ( $this->isProxy() ) {
                    phpCASSHIB::error(
                        'CASSHIB proxies are not supported in CASSHIB '.$server_version
                    );
                }
                break;
            case CASSHIB_VERSION_2_0:
            case CASSHIB_VERSION_3_0:
                break;
            case SAML_VERSION_1_1:
                break;
            default:
                phpCASSHIB::error(
                    'this version of CASSHIB (`'.$server_version
                    .'\') is not supported by phpCASSHIB '.phpCASSHIB::getVersion()
                );
        }
        $this->_server['version'] = $server_version;

        // check hostname
        if ( empty($server_hostname)
            || !preg_match('/[\.\d\-abcdefghijklmnopqrstuvwxyz]*/', $server_hostname)
        ) {
            phpCASSHIB::error('bad Cas server hostname (`'.$server_hostname.'\')');
        }
        $this->_server['hostname'] = $server_hostname;

        // check port
        if ( $server_port == 0
            || !is_int($server_port)
        ) {
            phpCASSHIB::error('bad Cas server port (`'.$server_hostname.'\')');
        }
        $this->_server['port'] = $server_port;

        // check URI
        if ( !preg_match('/[\.\d\-_abcdefghijklmnopqrstuvwxyz\/]*/', $server_uri) ) {
            phpCASSHIB::error('bad Cas server URI (`'.$server_uri.'\')');
        }
        // add leading and trailing `/' and remove doubles
        if (strstr($server_uri, '?') === false) $server_uri .= '/';
        $server_uri = preg_replace('/\/\//', '/', '/'.$server_uri);
        $this->_server['uri'] = $server_uri;

        // set to callback mode if PgtIou and PgtId CGI GET parameters are provided
        if ( $this->isProxy() ) {
            $this->_setCallbackMode(!empty($_GET['pgtIou'])&&!empty($_GET['pgtId']));
        }

        if ( $this->_isCallbackMode() ) {
            //callback mode: check that phpCASSHIB is secured
            if ( !$this->_isHttps() ) {
                phpCASSHIB::error(
                    'CASSHIB proxies must be secured to use phpCASSHIB; PGT\'s will not be received from the Cas server'
                );
            }
        } else {
            //normal mode: get ticket and remove it from CGI parameters for
            // developers
            $ticket = (isset($_GET['ticket']) ? $_GET['ticket'] : null);
            if (preg_match('/^[SP]T-/', $ticket) ) {
                phpCASSHIB::trace('Ticket \''.$ticket.'\' found');
                $this->setTicket($ticket);
                unset($_GET['ticket']);
            } else if ( !empty($ticket) ) {
                //ill-formed ticket, halt
                phpCASSHIB::error(
                    'ill-formed ticket found in the URL (ticket=`'
                    .htmlentities($ticket).'\')'
                );
            }

        }
        phpCASSHIB::traceEnd();
    }

    /** @} */

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                           Session Handling                         XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    /**
     * @addtogroup internalConfig
     * @{
     */


    /**
     * A variable to whether phpCASSHIB will use its own session handling. Default = true
     * @hideinitializer
     */
    private $_change_session_id = true;

    /**
     * Set a parameter whether to allow phpCASSHIB to change session_id
     *
     * @param bool $allowed allow phpCASSHIB to change session_id
     *
     * @return void
     */
    private function _setChangeSessionID($allowed)
    {
        $this->_change_session_id = $allowed;
    }

    /**
     * Get whether phpCASSHIB is allowed to change session_id
     *
     * @return bool
     */
    public function getChangeSessionID()
    {
        return $this->_change_session_id;
    }

    /** @} */

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                           AUTHENTICATION                           XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    /**
     * @addtogroup internalAuthentication
     * @{
     */

    /**
     * The Authenticated user. Written by CASSHIB_Client::_setUser(), read by
     * CASSHIB_Client::getUser().
     *
     * @hideinitializer
     */
    private $_user = '';

    /**
     * This method sets the CASSHIB user's login name.
     *
     * @param string $user the login name of the authenticated user.
     *
     * @return void
     */
    private function _setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * This method returns the CASSHIB user's login name.
     *
     * @return string the login name of the authenticated user
     *
     * @warning should be called only after CASSHIB_Client::forceAuthentication() or
     * CASSHIB_Client::isAuthenticated(), otherwise halt with an error.
     */
    public function getUser()
    {
        // Sequence validation
        $this->ensureAuthenticationCallSuccessful();

        return $this->_getUser();
    }

    /**
     * This method returns the CASSHIB user's login name.
     *
     * @return string the login name of the authenticated user
     *
     * @warning should be called only after CASSHIB_Client::forceAuthentication() or
     * CASSHIB_Client::isAuthenticated(), otherwise halt with an error.
     */
    private function _getUser()
    {
        // This is likely a duplicate check that could be removed....
        if ( empty($this->_user) ) {
            phpCASSHIB::error(
                'this method should be used only after '.__CLASS__
                .'::forceAuthentication() or '.__CLASS__.'::isAuthenticated()'
            );
        }
        return $this->_user;
    }

    /**
     * The Authenticated users attributes. Written by
     * CASSHIB_Client::setAttributes(), read by CASSHIB_Client::getAttributes().
     * @attention client applications should use phpCASSHIB::getAttributes().
     *
     * @hideinitializer
     */
    private $_attributes = array();

    /**
     * Set an array of attributes
     *
     * @param array $attributes a key value array of attributes
     *
     * @return void
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * Get an key values arry of attributes
     *
     * @return arry of attributes
     */
    public function getAttributes()
    {
        // Sequence validation
        $this->ensureAuthenticationCallSuccessful();
        // This is likely a duplicate check that could be removed....
        if ( empty($this->_user) ) {
            // if no user is set, there shouldn't be any attributes also...
            phpCASSHIB::error(
                'this method should be used only after '.__CLASS__
                .'::forceAuthentication() or '.__CLASS__.'::isAuthenticated()'
            );
        }
        return $this->_attributes;
    }

    /**
     * Check whether attributes are available
     *
     * @return bool attributes available
     */
    public function hasAttributes()
    {
        // Sequence validation
        $this->ensureAuthenticationCallSuccessful();

        return !empty($this->_attributes);
    }
    /**
     * Check whether a specific attribute with a name is available
     *
     * @param string $key name of attribute
     *
     * @return bool is attribute available
     */
    public function hasAttribute($key)
    {
        // Sequence validation
        $this->ensureAuthenticationCallSuccessful();

        return $this->_hasAttribute($key);
    }

    /**
     * Check whether a specific attribute with a name is available
     *
     * @param string $key name of attribute
     *
     * @return bool is attribute available
     */
    private function _hasAttribute($key)
    {
        return (is_array($this->_attributes)
            && array_key_exists($key, $this->_attributes));
    }

    /**
     * Get a specific attribute by name
     *
     * @param string $key name of attribute
     *
     * @return string attribute values
     */
    public function getAttribute($key)
    {
        // Sequence validation
        $this->ensureAuthenticationCallSuccessful();

        if ($this->_hasAttribute($key)) {
            return $this->_attributes[$key];
        }
    }

    /**
     * This method is called to renew the authentication of the user
     * If the user is authenticated, renew the connection
     * If not, redirect to CASSHIB
     *
     * @return  true when the user is authenticated; otherwise halt.
     */
    public function renewAuthentication()
    {
        phpCASSHIB::traceBegin();
        // Either way, the user is authenticated by CASSHIB
        if (isset( $_SESSION['phpCASSHIB']['auth_checked'])) {
            unset($_SESSION['phpCASSHIB']['auth_checked']);
        }
        if ( $this->isAuthenticated(true) ) {
            phpCASSHIB::trace('user already authenticated');
            $res = true;
        } else {
            $this->redirectToCas(false, true);
            // never reached
            $res = false;
        }
        phpCASSHIB::traceEnd();
        return $res;
    }

    /**
     * This method is called to be sure that the user is authenticated. When not
     * authenticated, halt by redirecting to the Cas server; otherwise return true.
     *
     * @return true when the user is authenticated; otherwise halt.
     */
    public function forceAuthentication()
    {
        phpCASSHIB::traceBegin();

        if ( $this->isAuthenticated() ) {
            // the user is authenticated, nothing to be done.
            phpCASSHIB::trace('no need to authenticate');
            $res = true;
        } else {
            // the user is not authenticated, redirect to the Cas server
            if (isset($_SESSION['phpCASSHIB']['auth_checked'])) {
                unset($_SESSION['phpCASSHIB']['auth_checked']);
            }
            $this->redirectToCas(false/* no gateway */);
            // never reached
            $res = false;
        }
        phpCASSHIB::traceEnd($res);
        return $res;
    }

    /**
     * An integer that gives the number of times authentication will be cached
     * before rechecked.
     *
     * @hideinitializer
     */
    private $_cache_times_for_auth_recheck = 0;

    /**
     * Set the number of times authentication will be cached before rechecked.
     *
     * @param int $n number of times to wait for a recheck
     *
     * @return void
     */
    public function setCacheTimesForAuthRecheck($n)
    {
        if (gettype($n) != 'integer')
            throw new CASSHIB_TypeMismatchException($n, '$n', 'string');

        $this->_cache_times_for_auth_recheck = $n;
    }

    /**
     * This method is called to check whether the user is authenticated or not.
     *
     * @return true when the user is authenticated, false when a previous
     * gateway login failed or  the function will not return if the user is
     * redirected to the Cas server for a gateway login attempt
     */
    public function checkAuthentication()
    {
        phpCASSHIB::traceBegin();
        $res = false;
        if ( $this->isAuthenticated() ) {
            phpCASSHIB::trace('user is authenticated');
            /* The 'auth_checked' variable is removed just in case it's set. */
            unset($_SESSION['phpCASSHIB']['auth_checked']);
            $res = true;
        } else if (isset($_SESSION['phpCASSHIB']['auth_checked'])) {
            // the previous request has redirected the client to the Cas server
            // with gateway=true
            unset($_SESSION['phpCASSHIB']['auth_checked']);
            $res = false;
        } else {
            // avoid a check against CASSHIB on every request
            if (!isset($_SESSION['phpCASSHIB']['unauth_count'])) {
                $_SESSION['phpCASSHIB']['unauth_count'] = -2; // uninitialized
            }

            if (($_SESSION['phpCASSHIB']['unauth_count'] != -2
                && $this->_cache_times_for_auth_recheck == -1)
                || ($_SESSION['phpCASSHIB']['unauth_count'] >= 0
                && $_SESSION['phpCASSHIB']['unauth_count'] < $this->_cache_times_for_auth_recheck)
            ) {
                $res = false;

                if ($this->_cache_times_for_auth_recheck != -1) {
                    $_SESSION['phpCASSHIB']['unauth_count']++;
                    phpCASSHIB::trace(
                        'user is not authenticated (cached for '
                        .$_SESSION['phpCASSHIB']['unauth_count'].' times of '
                        .$this->_cache_times_for_auth_recheck.')'
                    );
                } else {
                    phpCASSHIB::trace(
                        'user is not authenticated (cached for until login pressed)'
                    );
                }
            } else {
                $_SESSION['phpCASSHIB']['unauth_count'] = 0;
                $_SESSION['phpCASSHIB']['auth_checked'] = true;
                phpCASSHIB::trace('user is not authenticated (cache reset)');
                // CHANGE+ ENT Shibboletisatkion. Original is true.
                $this->redirectToCas(false/* gateway */);
                // CHANGE-.
                // never reached
                $res = false;
            }
        }
        phpCASSHIB::traceEnd($res);
        return $res;
    }

    /**
     * This method is called to check if the user is authenticated (previously or by
     * tickets given in the URL).
     *
     * @param bool $renew true to force the authentication with the Cas server
     *
     * @return true when the user is authenticated. Also may redirect to the
     * same URL without the ticket.
     */
    public function isAuthenticated($renew=false)
    {
        phpCASSHIB::traceBegin();
        $res = false;
        $validate_url = '';
        if ( $this->_wasPreviouslyAuthenticated() ) {
            if ($this->hasTicket()) {
                // User has a additional ticket but was already authenticated
                phpCASSHIB::trace(
                    'ticket was present and will be discarded, use renewAuthenticate()'
                );
                if ($this->_clearTicketsFromUrl) {
                    phpCASSHIB::trace("Prepare redirect to : ".$this->getURL());
                    session_write_close();
                    header('Location: '.$this->getURL());
                    flush();
                    phpCASSHIB::traceExit();
                    throw new CASSHIB_GracefullTerminationException();
                } else {
                    phpCASSHIB::trace(
                        'Already authenticated, but skipping ticket clearing since setNoClearTicketsFromUrl() was used.'
                    );
                    $res = true;
                }
            } else {
                // the user has already (previously during the session) been
                // authenticated, nothing to be done.
                phpCASSHIB::trace(
                    'user was already authenticated, no need to look for tickets'
                );
                $res = true;
            }

            // Mark the auth-check as complete to allow post-authentication
            // callbacks to make use of phpCASSHIB::getUser() and similar methods
            $this->markAuthenticationCall($res);
        } else {
            if ($this->hasTicket()) {
                switch ($this->getServerVersion()) {
                case CASSHIB_VERSION_1_0:
                    // if a Service Ticket was given, validate it
                    phpCASSHIB::trace(
                        'CASSHIB 1.0 ticket `'.$this->getTicket().'\' is present'
                    );
                    $this->validateCas10(
                        $validate_url, $text_response, $tree_response, $renew
                    ); // if it fails, it halts
                    phpCASSHIB::trace(
                        'CASSHIB 1.0 ticket `'.$this->getTicket().'\' was validated'
                    );
                    $_SESSION['phpCASSHIB']['user'] = $this->_getUser();
                    $res = true;
                    $logoutTicket = $this->getTicket();
                    break;
                case CASSHIB_VERSION_2_0:
                case CASSHIB_VERSION_3_0:
                    // if a Proxy Ticket was given, validate it
                    phpCASSHIB::trace(
                        'CASSHIB '.$this->getServerVersion().' ticket `'.$this->getTicket().'\' is present'
                    );
                    $this->validateCas20(
                        $validate_url, $text_response, $tree_response, $renew
                    ); // note: if it fails, it halts
                    phpCASSHIB::trace(
                        'CASSHIB '.$this->getServerVersion().' ticket `'.$this->getTicket().'\' was validated'
                    );
                    if ( $this->isProxy() ) {
                        $this->_validatePGT(
                            $validate_url, $text_response, $tree_response
                        ); // idem
                        phpCASSHIB::trace('PGT `'.$this->_getPGT().'\' was validated');
                        $_SESSION['phpCASSHIB']['pgt'] = $this->_getPGT();
                    }
                    $_SESSION['phpCASSHIB']['user'] = $this->_getUser();
                    if (!empty($this->_attributes)) {
                        $_SESSION['phpCASSHIB']['attributes'] = $this->_attributes;
                    }
                    $proxies = $this->getProxies();
                    if (!empty($proxies)) {
                        $_SESSION['phpCASSHIB']['proxies'] = $this->getProxies();
                    }
                    $res = true;
                    $logoutTicket = $this->getTicket();
                    break;
                case SAML_VERSION_1_1:
                    // if we have a SAML ticket, validate it.
                    phpCASSHIB::trace(
                        'SAML 1.1 ticket `'.$this->getTicket().'\' is present'
                    );
                    $this->validateSA(
                        $validate_url, $text_response, $tree_response, $renew
                    ); // if it fails, it halts
                    phpCASSHIB::trace(
                        'SAML 1.1 ticket `'.$this->getTicket().'\' was validated'
                    );
                    $_SESSION['phpCASSHIB']['user'] = $this->_getUser();
                    $_SESSION['phpCASSHIB']['attributes'] = $this->_attributes;
                    $res = true;
                    $logoutTicket = $this->getTicket();
                    break;
                default:
                    phpCASSHIB::trace('Protocoll error');
                    break;
                }
            } else {
                // no ticket given, not authenticated
                phpCASSHIB::trace('no ticket found');
            }

            // Mark the auth-check as complete to allow post-authentication
            // callbacks to make use of phpCASSHIB::getUser() and similar methods
            $this->markAuthenticationCall($res);

            if ($res) {
                // call the post-authenticate callback if registered.
                if ($this->_postAuthenticateCallbackFunction) {
                    $args = $this->_postAuthenticateCallbackArgs;
                    array_unshift($args, $logoutTicket);
                    call_user_func_array(
                        $this->_postAuthenticateCallbackFunction, $args
                    );
                }

                // if called with a ticket parameter, we need to redirect to the
                // app without the ticket so that CASSHIB-ification is transparent
                // to the browser (for later POSTS) most of the checks and
                // errors should have been made now, so we're safe for redirect
                // without masking error messages. remove the ticket as a
                // security precaution to prevent a ticket in the HTTP_REFERRER
                if ($this->_clearTicketsFromUrl) {
                    phpCASSHIB::trace("Prepare redirect to : ".$this->getURL());
                    session_write_close();
                    header('Location: '.$this->getURL());
                    flush();
                    phpCASSHIB::traceExit();
                    throw new CASSHIB_GracefullTerminationException();
                }
            }
        }
        phpCASSHIB::traceEnd($res);
        return $res;
    }

    /**
     * This method tells if the current session is authenticated.
     *
     * @return true if authenticated based soley on $_SESSION variable
     */
    public function isSessionAuthenticated ()
    {
        return !empty($_SESSION['phpCASSHIB']['user']);
    }

    /**
     * This method tells if the user has already been (previously) authenticated
     * by looking into the session variables.
     *
     * @note This function switches to callback mode when needed.
     *
     * @return true when the user has already been authenticated; false otherwise.
     */
    private function _wasPreviouslyAuthenticated()
    {
        phpCASSHIB::traceBegin();

        if ( $this->_isCallbackMode() ) {
            // Rebroadcast the pgtIou and pgtId to all nodes
            if ($this->_rebroadcast&&!isset($_POST['rebroadcast'])) {
                $this->_rebroadcast(self::PGTIOU);
            }
            $this->_callback();
        }

        $auth = false;

        if ( $this->isProxy() ) {
            // CASSHIB proxy: username and PGT must be present
            if ( $this->isSessionAuthenticated()
                && !empty($_SESSION['phpCASSHIB']['pgt'])
            ) {
                // authentication already done
                $this->_setUser($_SESSION['phpCASSHIB']['user']);
                if (isset($_SESSION['phpCASSHIB']['attributes'])) {
                    $this->setAttributes($_SESSION['phpCASSHIB']['attributes']);
                }
                $this->_setPGT($_SESSION['phpCASSHIB']['pgt']);
                phpCASSHIB::trace(
                    'user = `'.$_SESSION['phpCASSHIB']['user'].'\', PGT = `'
                    .$_SESSION['phpCASSHIB']['pgt'].'\''
                );

                // Include the list of proxies
                if (isset($_SESSION['phpCASSHIB']['proxies'])) {
                    $this->_setProxies($_SESSION['phpCASSHIB']['proxies']);
                    phpCASSHIB::trace(
                        'proxies = "'
                        .implode('", "', $_SESSION['phpCASSHIB']['proxies']).'"'
                    );
                }

                $auth = true;
            } elseif ( $this->isSessionAuthenticated()
                && empty($_SESSION['phpCASSHIB']['pgt'])
            ) {
                // these two variables should be empty or not empty at the same time
                phpCASSHIB::trace(
                    'username found (`'.$_SESSION['phpCASSHIB']['user']
                    .'\') but PGT is empty'
                );
                // unset all tickets to enforce authentication
                unset($_SESSION['phpCASSHIB']);
                $this->setTicket('');
            } elseif ( !$this->isSessionAuthenticated()
                && !empty($_SESSION['phpCASSHIB']['pgt'])
            ) {
                // these two variables should be empty or not empty at the same time
                phpCASSHIB::trace(
                    'PGT found (`'.$_SESSION['phpCASSHIB']['pgt']
                    .'\') but username is empty'
                );
                // unset all tickets to enforce authentication
                unset($_SESSION['phpCASSHIB']);
                $this->setTicket('');
            } else {
                phpCASSHIB::trace('neither user nor PGT found');
            }
        } else {
            // `simple' CASSHIB client (not a proxy): username must be present
            if ( $this->isSessionAuthenticated() ) {
                // authentication already done
                $this->_setUser($_SESSION['phpCASSHIB']['user']);
                if (isset($_SESSION['phpCASSHIB']['attributes'])) {
                    $this->setAttributes($_SESSION['phpCASSHIB']['attributes']);
                }
                phpCASSHIB::trace('user = `'.$_SESSION['phpCASSHIB']['user'].'\'');

                // Include the list of proxies
                if (isset($_SESSION['phpCASSHIB']['proxies'])) {
                    $this->_setProxies($_SESSION['phpCASSHIB']['proxies']);
                    phpCASSHIB::trace(
                        'proxies = "'
                        .implode('", "', $_SESSION['phpCASSHIB']['proxies']).'"'
                    );
                }

                $auth = true;
            } else {
                phpCASSHIB::trace('no user found');
            }
        }

        phpCASSHIB::traceEnd($auth);
        return $auth;
    }

    /**
     * This method is used to redirect the client to the Cas server.
     * It is used by CASSHIB_Client::forceAuthentication() and
     * CASSHIB_Client::checkAuthentication().
     *
     * @param bool $gateway true to check authentication, false to force it
     * @param bool $renew   true to force the authentication with the Cas server
     *
     * @return void
     */
    public function redirectToCas($gateway=false,$renew=false)
    {
        phpCASSHIB::traceBegin();
        $CASSHIB_url = $this->getServerLoginURL($gateway, $renew);
        session_write_close();
        if (php_sapi_name() === 'cli') {
            @header('Location: '.$CASSHIB_url);
        } else {
            header('Location: '.$CASSHIB_url);
        }
        phpCASSHIB::trace("Redirect to : ".$CASSHIB_url);
        $lang = $this->getLangObj();
        $this->printHTMLHeader($lang->getAuthenticationWanted());
        printf('<p>'. $lang->getShouldHaveBeenRedirected(). '</p>', $CASSHIB_url);
        $this->printHTMLFooter();
        phpCASSHIB::traceExit();
        throw new CASSHIB_GracefullTerminationException();
    }


    /**
     * This method is used to logout from CASSHIB.
     *
     * @param array $params an array that contains the optional url and service
     * parameters that will be passed to the Cas server
     *
     * @return void
     */
    public function logout($params)
    {
        phpCASSHIB::traceBegin();
        $CASSHIB_url = $this->getServerLogoutURL();
        $paramSeparator = '?';
        if (isset($params['url'])) {
            $CASSHIB_url = $CASSHIB_url . $paramSeparator . "url="
                . urlencode($params['url']);
            $paramSeparator = '&';
        }
        if (isset($params['service'])) {
            $CASSHIB_url = $CASSHIB_url . $paramSeparator . "service="
                . urlencode($params['service']);
        }
        header('Location: '.$CASSHIB_url);
        phpCASSHIB::trace("Prepare redirect to : ".$CASSHIB_url);

        session_unset();
        session_destroy();
        $lang = $this->getLangObj();
        $this->printHTMLHeader($lang->getLogout());
        printf('<p>'.$lang->getShouldHaveBeenRedirected(). '</p>', $CASSHIB_url);
        $this->printHTMLFooter();
        phpCASSHIB::traceExit();
        throw new CASSHIB_GracefullTerminationException();
    }

    /**
     * Check of the current request is a logout request
     *
     * @return bool is logout request.
     */
    private function _isLogoutRequest()
    {
        return !empty($_POST['logoutRequest']);
    }

    /**
     * This method handles logout requests.
     *
     * @param bool $check_client    true to check the client bofore handling
     * the request, false not to perform any access control. True by default.
     * @param bool $allowed_clients an array of host names allowed to send
     * logout requests.
     *
     * @return void
     */
    public function handleLogoutRequests($check_client=true, $allowed_clients=false)
    {
        phpCASSHIB::traceBegin();
        if (!$this->_isLogoutRequest()) {
            phpCASSHIB::trace("Not a logout request");
            phpCASSHIB::traceEnd();
            return;
        }
        if (!$this->getChangeSessionID()
            && is_null($this->_signoutCallbackFunction)
        ) {
            phpCASSHIB::trace(
                "phpCASSHIB can't handle logout requests if it is not allowed to change session_id."
            );
        }
        phpCASSHIB::trace("Logout requested");
        $decoded_logout_rq = urldecode($_POST['logoutRequest']);
        phpCASSHIB::trace("SAML REQUEST: ".$decoded_logout_rq);
        $allowed = false;
        if ($check_client) {
            if (!$allowed_clients) {
                $allowed_clients = array( $this->_getServerHostname() );
            }
            $client_ip = $_SERVER['REMOTE_ADDR'];
            $client = gethostbyaddr($client_ip);
            phpCASSHIB::trace("Client: ".$client."/".$client_ip);
            foreach ($allowed_clients as $allowed_client) {
                if (($client == $allowed_client)
                    || ($client_ip == $allowed_client)
                ) {
                    phpCASSHIB::trace(
                        "Allowed client '".$allowed_client
                        ."' matches, logout request is allowed"
                    );
                    $allowed = true;
                    break;
                } else {
                    phpCASSHIB::trace(
                        "Allowed client '".$allowed_client."' does not match"
                    );
                }
            }
        } else {
            phpCASSHIB::trace("No access control set");
            $allowed = true;
        }
        // If Logout command is permitted proceed with the logout
        if ($allowed) {
            phpCASSHIB::trace("Logout command allowed");
            // Rebroadcast the logout request
            if ($this->_rebroadcast && !isset($_POST['rebroadcast'])) {
                $this->_rebroadcast(self::LOGOUT);
            }
            // Extract the ticket from the SAML Request
            preg_match(
                "|<samlp:SessionIndex>(.*)</samlp:SessionIndex>|",
                $decoded_logout_rq, $tick, PREG_OFFSET_CAPTURE, 3
            );
            $wrappedSamlSessionIndex = preg_replace(
                '|<samlp:SessionIndex>|', '', $tick[0][0]
            );
            $ticket2logout = preg_replace(
                '|</samlp:SessionIndex>|', '', $wrappedSamlSessionIndex
            );
            phpCASSHIB::trace("Ticket to logout: ".$ticket2logout);

            // call the post-authenticate callback if registered.
            if ($this->_signoutCallbackFunction) {
                $args = $this->_signoutCallbackArgs;
                array_unshift($args, $ticket2logout);
                call_user_func_array($this->_signoutCallbackFunction, $args);
            }

            // If phpCASSHIB is managing the session_id, destroy session thanks to
            // session_id.
            if ($this->getChangeSessionID()) {
                $session_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $ticket2logout);
                phpCASSHIB::trace("Session id: ".$session_id);

                // destroy a possible application session created before phpCASSHIB
                if (session_id() !== "") {
                    session_unset();
                    session_destroy();
                }
                // fix session ID
                session_id($session_id);
                $_COOKIE[session_name()]=$session_id;
                $_GET[session_name()]=$session_id;

                // Overwrite session
                session_start();
                session_unset();
                session_destroy();
                phpCASSHIB::trace("Session ". $session_id . " destroyed");
            }
        } else {
            phpCASSHIB::error("Unauthorized logout request from client '".$client."'");
            phpCASSHIB::trace("Unauthorized logout request from client '".$client."'");
        }
        flush();
        phpCASSHIB::traceExit();
        throw new CASSHIB_GracefullTerminationException();

    }

    /** @} */

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                  BASIC CLIENT FEATURES (CASSHIB 1.0)                   XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    // ########################################################################
    //  ST
    // ########################################################################
    /**
    * @addtogroup internalBasic
    * @{
    */

    /**
     * The Ticket provided in the URL of the request if present
     * (empty otherwise). Written by CASSHIB_Client::CASSHIB_Client(), read by
     * CASSHIB_Client::getTicket() and CASSHIB_Client::_hasPGT().
     *
     * @hideinitializer
     */
    private $_ticket = '';

    /**
     * This method returns the Service Ticket provided in the URL of the request.
     *
     * @return string service ticket.
     */
    public  function getTicket()
    {
        return $this->_ticket;
    }

    /**
     * This method stores the Service Ticket.
     *
     * @param string $st The Service Ticket.
     *
     * @return void
     */
    public function setTicket($st)
    {
        $this->_ticket = $st;
    }

    /**
     * This method tells if a Service Ticket was stored.
     *
     * @return bool if a Service Ticket has been stored.
     */
    public function hasTicket()
    {
        return !empty($this->_ticket);
    }

    /** @} */

    // ########################################################################
    //  ST VALIDATION
    // ########################################################################
    /**
    * @addtogroup internalBasic
    * @{
    */

    /**
     * the certificate of the Cas server CA.
     *
     * @hideinitializer
     */
    private $_cas_server_ca_cert = null;


    /**

     * validate CN of the Cas server certificate

     *

     * @hideinitializer

     */

    private $_cas_server_cn_validate = true;

    /**
     * Set to true not to validate the Cas server.
     *
     * @hideinitializer
     */
    private $_no_cas_server_validation = false;


    /**
     * Set the CA certificate of the Cas server.
     *
     * @param string $cert        the PEM certificate file name of the CA that emited
     * the cert of the server
     * @param bool   $validate_cn valiate CN of the Cas server certificate
     *
     * @return void
     */
    public function setCasServerCACert($cert, $validate_cn)
    {
        // Argument validation
        if (gettype($cert) != 'string')
            throw new CASSHIB_TypeMismatchException($cert, '$cert', 'string');
        if (gettype($validate_cn) != 'boolean')
            throw new CASSHIB_TypeMismatchException($validate_cn, '$validate_cn', 'boolean');

        $this->_cas_server_ca_cert = $cert;
        $this->_cas_server_cn_validate = $validate_cn;
    }

    /**
     * Set no SSL validation for the Cas server.
     *
     * @return void
     */
    public function setNoCasServerValidation()
    {
        $this->_no_cas_server_validation = true;
    }

    /**
     * This method is used to validate a Cas 1,0 ticket; halt on failure, and
     * sets $validate_url, $text_reponse and $tree_response on success.
     *
     * @param string &$validate_url  reference to the the URL of the request to
     * the Cas server.
     * @param string &$text_response reference to the response of the CASSHIB
     * server, as is (XML text).
     * @param string &$tree_response reference to the response of the CASSHIB
     * server, as a DOM XML tree.
     * @param bool   $renew          true to force the authentication with the Cas server
     *
     * @return bool true when successfull and issue a CASSHIB_AuthenticationException
     * and false on an error
     */
    public function validateCas10(&$validate_url,&$text_response,&$tree_response,$renew=false)
    {
        phpCASSHIB::traceBegin();
        $result = false;
        // build the URL to validate the ticket
        $validate_url = $this->getServerServiceValidateURL()
            .'&ticket='.urlencode($this->getTicket());

        if ( $renew ) {
            // pass the renew
            $validate_url .= '&renew=true';
        }

        // open and read the URL
        if ( !$this->_readURL($validate_url, $headers, $text_response, $err_msg) ) {
            phpCASSHIB::trace(
                'could not open URL \''.$validate_url.'\' to validate ('.$err_msg.')'
            );
            throw new CASSHIB_AuthenticationException(
                $this, 'CASSHIB 1.0 ticket not validated', $validate_url,
                true/*$no_response*/
            );
            $result = false;
        }

        if (preg_match('/^no\n/', $text_response)) {
            phpCASSHIB::trace('Ticket has not been validated');
            throw new CASSHIB_AuthenticationException(
                $this, 'ST not validated', $validate_url, false/*$no_response*/,
                false/*$bad_response*/, $text_response
            );
            $result = false;
        } else if (!preg_match('/^yes\n/', $text_response)) {
            phpCASSHIB::trace('ill-formed response');
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, true/*$bad_response*/, $text_response
            );
            $result = false;
        }
        // ticket has been validated, extract the user name
        $arr = preg_split('/\n/', $text_response);
        $this->_setUser(trim($arr[1]));
        $result = true;

        if ($result) {
            $this->_renameSession($this->getTicket());
        }
        // at this step, ticket has been validated and $this->_user has been set,
        phpCASSHIB::traceEnd(true);
        return true;
    }

    /** @} */


    // ########################################################################
    //  SAML VALIDATION
    // ########################################################################
    /**
    * @addtogroup internalSAML
    * @{
    */

    /**
     * This method is used to validate a SAML TICKET; halt on failure, and sets
     * $validate_url, $text_reponse and $tree_response on success. These
     * parameters are used later by CASSHIB_Client::_validatePGT() for CASSHIB proxies.
     *
     * @param string &$validate_url  reference to the the URL of the request to
     * the Cas server.
     * @param string &$text_response reference to the response of the CASSHIB
     * server, as is (XML text).
     * @param string &$tree_response reference to the response of the CASSHIB
     * server, as a DOM XML tree.
     * @param bool   $renew          true to force the authentication with the Cas server
     *
     * @return bool true when successfull and issue a CASSHIB_AuthenticationException
     * and false on an error
     */
    public function validateSA(&$validate_url,&$text_response,&$tree_response,$renew=false)
    {
        phpCASSHIB::traceBegin();
        $result = false;
        // build the URL to validate the ticket
        $validate_url = $this->getServerSamlValidateURL();

        if ( $renew ) {
            // pass the renew
            $validate_url .= '&renew=true';
        }

        // open and read the URL
        if ( !$this->_readURL($validate_url, $headers, $text_response, $err_msg) ) {
            phpCASSHIB::trace(
                'could not open URL \''.$validate_url.'\' to validate ('.$err_msg.')'
            );
            throw new CASSHIB_AuthenticationException(
                $this, 'SA not validated', $validate_url, true/*$no_response*/
            );
        }

        phpCASSHIB::trace('server version: '.$this->getServerVersion());

        // analyze the result depending on the version
        switch ($this->getServerVersion()) {
        case SAML_VERSION_1_1:
            // create new DOMDocument Object
            $dom = new DOMDocument();
            // Fix possible whitspace problems
            $dom->preserveWhiteSpace = false;
            // read the response of the Cas server into a DOM object
            if (!($dom->loadXML($text_response))) {
                phpCASSHIB::trace('dom->loadXML() failed');
                throw new CASSHIB_AuthenticationException(
                    $this, 'SA not validated', $validate_url,
                    false/*$no_response*/, true/*$bad_response*/,
                    $text_response
                );
                $result = false;
            }
            // read the root node of the XML tree
            if (!($tree_response = $dom->documentElement)) {
                phpCASSHIB::trace('documentElement() failed');
                throw new CASSHIB_AuthenticationException(
                    $this, 'SA not validated', $validate_url,
                    false/*$no_response*/, true/*$bad_response*/,
                    $text_response
                );
                $result = false;
            } else if ( $tree_response->localName != 'Envelope' ) {
                // insure that tag name is 'Envelope'
                phpCASSHIB::trace(
                    'bad XML root node (should be `Envelope\' instead of `'
                    .$tree_response->localName.'\''
                );
                throw new CASSHIB_AuthenticationException(
                    $this, 'SA not validated', $validate_url,
                    false/*$no_response*/, true/*$bad_response*/,
                    $text_response
                );
                $result = false;
            } else if ($tree_response->getElementsByTagName("NameIdentifier")->length != 0) {
                // check for the NameIdentifier tag in the SAML response
                $success_elements = $tree_response->getElementsByTagName("NameIdentifier");
                phpCASSHIB::trace('NameIdentifier found');
                $user = trim($success_elements->item(0)->nodeValue);
                phpCASSHIB::trace('user = `'.$user.'`');
                $this->_setUser($user);
                $this->_setSessionAttributes($text_response);
                $result = true;
            } else {
                phpCASSHIB::trace('no <NameIdentifier> tag found in SAML payload');
                throw new CASSHIB_AuthenticationException(
                    $this, 'SA not validated', $validate_url,
                    false/*$no_response*/, true/*$bad_response*/,
                    $text_response
                );
                $result = false;
            }
        }
        if ($result) {
            $this->_renameSession($this->getTicket());
        }
        // at this step, ST has been validated and $this->_user has been set,
        phpCASSHIB::traceEnd($result);
        return $result;
    }

    /**
     * This method will parse the DOM and pull out the attributes from the SAML
     * payload and put them into an array, then put the array into the session.
     *
     * @param string $text_response the SAML payload.
     *
     * @return bool true when successfull and false if no attributes a found
     */
    private function _setSessionAttributes($text_response)
    {
        phpCASSHIB::traceBegin();

        $result = false;

        $attr_array = array();

        // create new DOMDocument Object
        $dom = new DOMDocument();
        // Fix possible whitspace problems
        $dom->preserveWhiteSpace = false;
        if (($dom->loadXML($text_response))) {
            $xPath = new DOMXpath($dom);
            $xPath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:1.0:protocol');
            $xPath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:1.0:assertion');
            $nodelist = $xPath->query("//saml:Attribute");

            if ($nodelist) {
                foreach ($nodelist as $node) {
                    $xres = $xPath->query("saml:AttributeValue", $node);
                    $name = $node->getAttribute("AttributeName");
                    $value_array = array();
                    foreach ($xres as $node2) {
                        $value_array[] = $node2->nodeValue;
                    }
                    $attr_array[$name] = $value_array;
                }
                // UGent addition...
                foreach ($attr_array as $attr_key => $attr_value) {
                    if (count($attr_value) > 1) {
                        $this->_attributes[$attr_key] = $attr_value;
                        phpCASSHIB::trace("* " . $attr_key . "=" . print_r($attr_value, true));
                    } else {
                        $this->_attributes[$attr_key] = $attr_value[0];
                        phpCASSHIB::trace("* " . $attr_key . "=" . $attr_value[0]);
                    }
                }
                $result = true;
            } else {
                phpCASSHIB::trace("SAML Attributes are empty");
                $result = false;
            }
        }
        phpCASSHIB::traceEnd($result);
        return $result;
    }

    /** @} */

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                     PROXY FEATURES (CASSHIB 2.0)                       XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    // ########################################################################
    //  PROXYING
    // ########################################################################
    /**
    * @addtogroup internalProxy
    * @{
    */

    /**
     * A boolean telling if the client is a CASSHIB proxy or not. Written by
     * CASSHIB_Client::CASSHIB_Client(), read by CASSHIB_Client::isProxy().
     */
    private $_proxy;

    /**
     * Handler for managing service cookies.
     */
    private $_serviceCookieJar;

    /**
     * Tells if a CASSHIB client is a CASSHIB proxy or not
     *
     * @return true when the CASSHIB client is a CASSHIB proxy, false otherwise
     */
    public function isProxy()
    {
        return $this->_proxy;
    }


    /** @} */
    // ########################################################################
    //  PGT
    // ########################################################################
    /**
    * @addtogroup internalProxy
    * @{
    */

    /**
     * the Proxy Grnting Ticket given by the Cas server (empty otherwise).
     * Written by CASSHIB_Client::_setPGT(), read by CASSHIB_Client::_getPGT() and
     * CASSHIB_Client::_hasPGT().
     *
     * @hideinitializer
     */
    private $_pgt = '';

    /**
     * This method returns the Proxy Granting Ticket given by the Cas server.
     *
     * @return string the Proxy Granting Ticket.
     */
    private function _getPGT()
    {
        return $this->_pgt;
    }

    /**
     * This method stores the Proxy Granting Ticket.
     *
     * @param string $pgt The Proxy Granting Ticket.
     *
     * @return void
     */
    private function _setPGT($pgt)
    {
        $this->_pgt = $pgt;
    }

    /**
     * This method tells if a Proxy Granting Ticket was stored.
     *
     * @return true if a Proxy Granting Ticket has been stored.
     */
    private function _hasPGT()
    {
        return !empty($this->_pgt);
    }

    /** @} */

    // ########################################################################
    //  CALLBACK MODE
    // ########################################################################
    /**
    * @addtogroup internalCallback
    * @{
    */
    /**
     * each PHP script using phpCASSHIB in proxy mode is its own callback to get the
     * PGT back from the Cas server. callback_mode is detected by the constructor
     * thanks to the GET parameters.
     */

    /**
     * a boolean to know if the CASSHIB client is running in callback mode. Written by
     * CASSHIB_Client::setCallBackMode(), read by CASSHIB_Client::_isCallbackMode().
     *
     * @hideinitializer
     */
    private $_callback_mode = false;

    /**
     * This method sets/unsets callback mode.
     *
     * @param bool $callback_mode true to set callback mode, false otherwise.
     *
     * @return void
     */
    private function _setCallbackMode($callback_mode)
    {
        $this->_callback_mode = $callback_mode;
    }

    /**
     * This method returns true when the CASSHIB client is running i callback mode,
     * false otherwise.
     *
     * @return A boolean.
     */
    private function _isCallbackMode()
    {
        return $this->_callback_mode;
    }

    /**
     * the URL that should be used for the PGT callback (in fact the URL of the
     * current request without any CGI parameter). Written and read by
     * CASSHIB_Client::_getCallbackURL().
     *
     * @hideinitializer
     */
    private $_callback_url = '';

    /**
     * This method returns the URL that should be used for the PGT callback (in
     * fact the URL of the current request without any CGI parameter, except if
     * phpCASSHIB::setFixedCallbackURL() was used).
     *
     * @return The callback URL
     */
    private function _getCallbackURL()
    {
        // the URL is built when needed only
        if ( empty($this->_callback_url) ) {
            $final_uri = '';
            // remove the ticket if present in the URL
            $final_uri = 'https://';
            $final_uri .= $this->_getClientUrl();
            $request_uri = $_SERVER['REQUEST_URI'];
            $request_uri = preg_replace('/\?.*$/', '', $request_uri);
            $final_uri .= $request_uri;
            $this->_callback_url = $final_uri;
        }
        return $this->_callback_url;
    }

    /**
     * This method sets the callback url.
     *
     * @param string $url url to set callback
     *
     * @return void
     */
    public function setCallbackURL($url)
    {
        // Sequence validation
        $this->ensureIsProxy();
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        return $this->_callback_url = $url;
    }

    /**
     * This method is called by CASSHIB_Client::CASSHIB_Client() when running in callback
     * mode. It stores the PGT and its PGT Iou, prints its output and halts.
     *
     * @return void
     */
    private function _callback()
    {
        phpCASSHIB::traceBegin();
        if (preg_match('/PGTIOU-[\.\-\w]/', $_GET['pgtIou'])) {
            if (preg_match('/[PT]GT-[\.\-\w]/', $_GET['pgtId'])) {
                $this->printHTMLHeader('phpCASSHIB callback');
                $pgt_iou = $_GET['pgtIou'];
                $pgt = $_GET['pgtId'];
                phpCASSHIB::trace('Storing PGT `'.$pgt.'\' (id=`'.$pgt_iou.'\')');
                echo '<p>Storing PGT `'.$pgt.'\' (id=`'.$pgt_iou.'\').</p>';
                $this->_storePGT($pgt, $pgt_iou);
                $this->printHTMLFooter();
                phpCASSHIB::traceExit("Successfull Callback");
            } else {
                phpCASSHIB::error('PGT format invalid' . $_GET['pgtId']);
                phpCASSHIB::traceExit('PGT format invalid' . $_GET['pgtId']);
            }
        } else {
            phpCASSHIB::error('PGTiou format invalid' . $_GET['pgtIou']);
            phpCASSHIB::traceExit('PGTiou format invalid' . $_GET['pgtIou']);
        }

        // Flush the buffer to prevent from sending anything other then a 200
        // Success Status back to the Cas server. The Exception would normally
        // report as a 500 error.
        flush();
        throw new CASSHIB_GracefullTerminationException();
    }


    /** @} */

    // ########################################################################
    //  PGT STORAGE
    // ########################################################################
    /**
    * @addtogroup internalPGTStorage
    * @{
    */

    /**
     * an instance of a class inheriting of PGTStorage, used to deal with PGT
     * storage. Created by CASSHIB_Client::setPGTStorageFile(), used
     * by CASSHIB_Client::setPGTStorageFile() and CASSHIB_Client::_initPGTStorage().
     *
     * @hideinitializer
     */
    private $_pgt_storage = null;

    /**
     * This method is used to initialize the storage of PGT's.
     * Halts on error.
     *
     * @return void
     */
    private function _initPGTStorage()
    {
        // if no SetPGTStorageXxx() has been used, default to file
        if ( !is_object($this->_pgt_storage) ) {
            $this->setPGTStorageFile();
        }

        // initializes the storage
        $this->_pgt_storage->init();
    }

    /**
     * This method stores a PGT. Halts on error.
     *
     * @param string $pgt     the PGT to store
     * @param string $pgt_iou its corresponding Iou
     *
     * @return void
     */
    private function _storePGT($pgt,$pgt_iou)
    {
        // ensure that storage is initialized
        $this->_initPGTStorage();
        // writes the PGT
        $this->_pgt_storage->write($pgt, $pgt_iou);
    }

    /**
     * This method reads a PGT from its Iou and deletes the corresponding
     * storage entry.
     *
     * @param string $pgt_iou the PGT Iou
     *
     * @return mul The PGT corresponding to the Iou, false when not found.
     */
    private function _loadPGT($pgt_iou)
    {
        // ensure that storage is initialized
        $this->_initPGTStorage();
        // read the PGT
        return $this->_pgt_storage->read($pgt_iou);
    }

    /**
     * This method can be used to set a custom PGT storage object.
     *
     * @param CASSHIB_PGTStorage_AbstractStorage $storage a PGT storage object that
     * inherits from the CASSHIB_PGTStorage_AbstractStorage class
     *
     * @return void
     */
    public function setPGTStorage($storage)
    {
        // Sequence validation
        $this->ensureIsProxy();

        // check that the storage has not already been set
        if ( is_object($this->_pgt_storage) ) {
            phpCASSHIB::error('PGT storage already defined');
        }

        // check to make sure a valid storage object was specified
        if ( !($storage instanceof CASSHIB_PGTStorage_AbstractStorage) )
            throw new CASSHIB_TypeMismatchException($storage, '$storage', 'CASSHIB_PGTStorage_AbstractStorage object');

        // store the PGTStorage object
        $this->_pgt_storage = $storage;
    }

    /**
     * This method is used to tell phpCASSHIB to store the response of the
     * Cas server to PGT requests in a database.
     *
     * @param string $dsn_or_pdo     a dsn string to use for creating a PDO
     * object or a PDO object
     * @param string $username       the username to use when connecting to the
     * database
     * @param string $password       the password to use when connecting to the
     * database
     * @param string $table          the table to use for storing and retrieving
     * PGTs
     * @param string $driver_options any driver options to use when connecting
     * to the database
     *
     * @return void
     */
    public function setPGTStorageDb(
        $dsn_or_pdo, $username='', $password='', $table='', $driver_options=null
    ) {
        // Sequence validation
        $this->ensureIsProxy();

        // Argument validation
        if ((is_object($dsn_or_pdo) && !($dsn_or_pdo instanceof PDO)) || gettype($dsn_or_pdo) != 'string')
            throw new CASSHIB_TypeMismatchException($dsn_or_pdo, '$dsn_or_pdo', 'string or PDO object');
        if (gettype($username) != 'string')
            throw new CASSHIB_TypeMismatchException($username, '$username', 'string');
        if (gettype($password) != 'string')
            throw new CASSHIB_TypeMismatchException($password, '$password', 'string');
        if (gettype($table) != 'string')
            throw new CASSHIB_TypeMismatchException($table, '$password', 'string');

        // create the storage object
        $this->setPGTStorage(
            new CASSHIB_PGTStorage_Db(
                $this, $dsn_or_pdo, $username, $password, $table, $driver_options
            )
        );
    }

    /**
     * This method is used to tell phpCASSHIB to store the response of the
     * Cas server to PGT requests onto the filesystem.
     *
     * @param string $path the path where the PGT's should be stored
     *
     * @return void
     */
    public function setPGTStorageFile($path='')
    {
        // Sequence validation
        $this->ensureIsProxy();

        // Argument validation
        if (gettype($path) != 'string')
            throw new CASSHIB_TypeMismatchException($path, '$path', 'string');

        // create the storage object
        $this->setPGTStorage(new CASSHIB_PGTStorage_File($this, $path));
    }


    // ########################################################################
    //  PGT VALIDATION
    // ########################################################################
    /**
    * This method is used to validate a PGT; halt on failure.
    *
    * @param string &$validate_url the URL of the request to the Cas server.
    * @param string $text_response the response of the Cas server, as is
    *                              (XML text); result of
    *                              CASSHIB_Client::validateCas10() or
    *                              CASSHIB_Client::validateCas20().
    * @param string $tree_response the response of the Cas server, as a DOM XML
    * tree; result of CASSHIB_Client::validateCas10() or CASSHIB_Client::validateCas20().
    *
    * @return bool true when successfull and issue a CASSHIB_AuthenticationException
    * and false on an error
    */
    private function _validatePGT(&$validate_url,$text_response,$tree_response)
    {
        phpCASSHIB::traceBegin();
        if ( $tree_response->getElementsByTagName("proxyGrantingTicket")->length == 0) {
            phpCASSHIB::trace('<proxyGrantingTicket> not found');
            // authentication succeded, but no PGT Iou was transmitted
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket validated but no PGT Iou transmitted',
                $validate_url, false/*$no_response*/, false/*$bad_response*/,
                $text_response
            );
        } else {
            // PGT Iou transmitted, extract it
            $pgt_iou = trim(
                $tree_response->getElementsByTagName("proxyGrantingTicket")->item(0)->nodeValue
            );
            if (preg_match('/PGTIOU-[\.\-\w]/', $pgt_iou)) {
                $pgt = $this->_loadPGT($pgt_iou);
                if ( $pgt == false ) {
                    phpCASSHIB::trace('could not load PGT');
                    throw new CASSHIB_AuthenticationException(
                        $this,
                        'PGT Iou was transmitted but PGT could not be retrieved',
                        $validate_url, false/*$no_response*/,
                        false/*$bad_response*/, $text_response
                    );
                }
                $this->_setPGT($pgt);
            } else {
                phpCASSHIB::trace('PGTiou format error');
                throw new CASSHIB_AuthenticationException(
                    $this, 'PGT Iou was transmitted but has wrong format',
                    $validate_url, false/*$no_response*/, false/*$bad_response*/,
                    $text_response
                );
            }
        }
        phpCASSHIB::traceEnd(true);
        return true;
    }

    // ########################################################################
    //  PGT VALIDATION
    // ########################################################################

    /**
     * This method is used to retrieve PT's from the Cas server thanks to a PGT.
     *
     * @param string $target_service the service to ask for with the PT.
     * @param string &$err_code      an error code (PHPCASSHIB_SERVICE_OK on success).
     * @param string &$err_msg       an error message (empty on success).
     *
     * @return a Proxy Ticket, or false on error.
     */
    public function retrievePT($target_service,&$err_code,&$err_msg)
    {
        // Argument validation
        if (gettype($target_service) != 'string')
            throw new CASSHIB_TypeMismatchException($target_service, '$target_service', 'string');

        phpCASSHIB::traceBegin();

        // by default, $err_msg is set empty and $pt to true. On error, $pt is
        // set to false and $err_msg to an error message. At the end, if $pt is false
        // and $error_msg is still empty, it is set to 'invalid response' (the most
        // commonly encountered error).
        $err_msg = '';

        // build the URL to retrieve the PT
        $CASSHIB_url = $this->getServerProxyURL().'?targetService='
            .urlencode($target_service).'&pgt='.$this->_getPGT();

        // open and read the URL
        if ( !$this->_readURL($CASSHIB_url, $headers, $CASSHIB_response, $err_msg) ) {
            phpCASSHIB::trace(
                'could not open URL \''.$CASSHIB_url.'\' to validate ('.$err_msg.')'
            );
            $err_code = PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE;
            $err_msg = 'could not retrieve PT (no response from the Cas server)';
            phpCASSHIB::traceEnd(false);
            return false;
        }

        $bad_response = false;

        if ( !$bad_response ) {
            // create new DOMDocument object
            $dom = new DOMDocument();
            // Fix possible whitspace problems
            $dom->preserveWhiteSpace = false;
            // read the response of the Cas server into a DOM object
            if ( !($dom->loadXML($CASSHIB_response))) {
                phpCASSHIB::trace('dom->loadXML() failed');
                // read failed
                $bad_response = true;
            }
        }

        if ( !$bad_response ) {
            // read the root node of the XML tree
            if ( !($root = $dom->documentElement) ) {
                phpCASSHIB::trace('documentElement failed');
                // read failed
                $bad_response = true;
            }
        }

        if ( !$bad_response ) {
            // insure that tag name is 'serviceResponse'
            if ( $root->localName != 'serviceResponse' ) {
                phpCASSHIB::trace('localName failed');
                // bad root node
                $bad_response = true;
            }
        }

        if ( !$bad_response ) {
            // look for a proxySuccess tag
            if ( $root->getElementsByTagName("proxySuccess")->length != 0) {
                $proxy_success_list = $root->getElementsByTagName("proxySuccess");

                // authentication succeded, look for a proxyTicket tag
                if ( $proxy_success_list->item(0)->getElementsByTagName("proxyTicket")->length != 0) {
                    $err_code = PHPCASSHIB_SERVICE_OK;
                    $err_msg = '';
                    $pt = trim(
                        $proxy_success_list->item(0)->getElementsByTagName("proxyTicket")->item(0)->nodeValue
                    );
                    phpCASSHIB::trace('original PT: '.trim($pt));
                    phpCASSHIB::traceEnd($pt);
                    return $pt;
                } else {
                    phpCASSHIB::trace('<proxySuccess> was found, but not <proxyTicket>');
                }
            } else if ($root->getElementsByTagName("proxyFailure")->length != 0) {
                // look for a proxyFailure tag
                $proxy_failure_list = $root->getElementsByTagName("proxyFailure");

                // authentication failed, extract the error
                $err_code = PHPCASSHIB_SERVICE_PT_FAILURE;
                $err_msg = 'PT retrieving failed (code=`'
                .$proxy_failure_list->item(0)->getAttribute('code')
                .'\', message=`'
                .trim($proxy_failure_list->item(0)->nodeValue)
                .'\')';
                phpCASSHIB::traceEnd(false);
                return false;
            } else {
                phpCASSHIB::trace('neither <proxySuccess> nor <proxyFailure> found');
            }
        }

        // at this step, we are sure that the response of the Cas server was
        // illformed
        $err_code = PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE;
        $err_msg = 'Invalid response from the Cas server (response=`'
            .$CASSHIB_response.'\')';

        phpCASSHIB::traceEnd(false);
        return false;
    }

    /** @} */

    // ########################################################################
    // READ Cas server ANSWERS
    // ########################################################################

    /**
     * @addtogroup internalMisc
     * @{
     */

    /**
     * This method is used to acces a remote URL.
     *
     * @param string $url      the URL to access.
     * @param string &$headers an array containing the HTTP header lines of the
     * response (an empty array on failure).
     * @param string &$body    the body of the response, as a string (empty on
     * failure).
     * @param string &$err_msg an error message, filled on failure.
     *
     * @return true on success, false otherwise (in this later case, $err_msg
     * contains an error message).
     */
    private function _readURL($url, &$headers, &$body, &$err_msg)
    {
        phpCASSHIB::traceBegin();
        $className = $this->_requestImplementation;
        $request = new $className();

        if (count($this->_curl_options)) {
            $request->setCurlOptions($this->_curl_options);
        }

        $request->setUrl($url);

        if (empty($this->_cas_server_ca_cert) && !$this->_no_cas_server_validation) {
            phpCASSHIB::error(
                'one of the methods phpCASSHIB::setCASSHIBServerCACert() or phpCASSHIB::setNoCASSHIBServerValidation() must be called.'
            );
        }
        if ($this->_cas_server_ca_cert != '') {
            $request->setSslCaCert(
                $this->_cas_server_ca_cert, $this->_cas_server_cn_validate
            );
        }

        // add extra stuff if SAML
        if ($this->getServerVersion() == SAML_VERSION_1_1) {
            $request->addHeader("soapaction: http://www.oasis-open.org/committees/security");
            $request->addHeader("cache-control: no-cache");
            $request->addHeader("pragma: no-cache");
            $request->addHeader("accept: text/xml");
            $request->addHeader("connection: keep-alive");
            $request->addHeader("content-type: text/xml");
            $request->makePost();
            $request->setPostBody($this->_buildSAMLPayload());
        }

        if ($request->send()) {
            $headers = $request->getResponseHeaders();
            $body = $request->getResponseBody();
            $err_msg = '';
            phpCASSHIB::traceEnd(true);
            return true;
        } else {
            $headers = '';
            $body = '';
            $err_msg = $request->getErrorMessage();
            phpCASSHIB::traceEnd(false);
            return false;
        }
    }

    /**
     * This method is used to build the SAML POST body sent to /samlValidate URL.
     *
     * @return the SOAP-encased SAMLP artifact (the ticket).
     */
    private function _buildSAMLPayload()
    {
        phpCASSHIB::traceBegin();

        //get the ticket
        $sa = urlencode($this->getTicket());

        $body = SAML_SOAP_ENV.SAML_SOAP_BODY.SAMLP_REQUEST
            .SAML_ASSERTION_ARTIFACT.$sa.SAML_ASSERTION_ARTIFACT_CLOSE
            .SAMLP_REQUEST_CLOSE.SAML_SOAP_BODY_CLOSE.SAML_SOAP_ENV_CLOSE;

        phpCASSHIB::traceEnd($body);
        return ($body);
    }

    /** @} **/

    // ########################################################################
    // ACCESS TO EXTERNAL SERVICES
    // ########################################################################

    /**
     * @addtogroup internalProxyServices
     * @{
     */


    /**
     * Answer a proxy-authenticated service handler.
     *
     * @param string $type The service type. One of:
     * PHPCASSHIB_PROXIED_SERVICE_HTTP_GET, PHPCASSHIB_PROXIED_SERVICE_HTTP_POST,
     * PHPCASSHIB_PROXIED_SERVICE_IMAP
     *
     * @return CASSHIB_ProxiedService
     * @throws InvalidArgumentException If the service type is unknown.
     */
    public function getProxiedService ($type)
    {
        // Sequence validation
        $this->ensureIsProxy();
        $this->ensureAuthenticationCallSuccessful();

        // Argument validation
        if (gettype($type) != 'string')
            throw new CASSHIB_TypeMismatchException($type, '$type', 'string');

        switch ($type) {
        case PHPCASSHIB_PROXIED_SERVICE_HTTP_GET:
        case PHPCASSHIB_PROXIED_SERVICE_HTTP_POST:
            $requestClass = $this->_requestImplementation;
            $request = new $requestClass();
            if (count($this->_curl_options)) {
                $request->setCurlOptions($this->_curl_options);
            }
            $proxiedService = new $type($request, $this->_serviceCookieJar);
            if ($proxiedService instanceof CASSHIB_ProxiedService_Testable) {
                $proxiedService->setCASSHIBClient($this);
            }
            return $proxiedService;
        case PHPCASSHIB_PROXIED_SERVICE_IMAP;
            $proxiedService = new CASSHIB_ProxiedService_Imap($this->_getUser());
            if ($proxiedService instanceof CASSHIB_ProxiedService_Testable) {
                $proxiedService->setCASSHIBClient($this);
            }
            return $proxiedService;
        default:
            throw new CASSHIB_InvalidArgumentException(
                "Unknown proxied-service type, $type."
            );
        }
    }

    /**
     * Initialize a proxied-service handler with the proxy-ticket it should use.
     *
     * @param CASSHIB_ProxiedService $proxiedService service handler
     *
     * @return void
     *
     * @throws CASSHIB_ProxyTicketException If there is a proxy-ticket failure.
     *        The code of the Exception will be one of:
     *            PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE
     *            PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE
     *            PHPCASSHIB_SERVICE_PT_FAILURE
     * @throws CASSHIB_ProxiedService_Exception If there is a failure getting the
     * url from the proxied service.
     */
    public function initializeProxiedService (CASSHIB_ProxiedService $proxiedService)
    {
        // Sequence validation
        $this->ensureIsProxy();
        $this->ensureAuthenticationCallSuccessful();

        $url = $proxiedService->getServiceUrl();
        if (!is_string($url)) {
            throw new CASSHIB_ProxiedService_Exception(
                "Proxied Service ".get_class($proxiedService)
                ."->getServiceUrl() should have returned a string, returned a "
                .gettype($url)." instead."
            );
        }
        $pt = $this->retrievePT($url, $err_code, $err_msg);
        if (!$pt) {
            throw new CASSHIB_ProxyTicketException($err_msg, $err_code);
        }
        $proxiedService->setProxyTicket($pt);
    }

    /**
     * This method is used to access an HTTP[S] service.
     *
     * @param string $url       the service to access.
     * @param int    &$err_code an error code Possible values are
     * PHPCASSHIB_SERVICE_OK (on success), PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE,
     * PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE, PHPCASSHIB_SERVICE_PT_FAILURE,
     * PHPCASSHIB_SERVICE_NOT_AVAILABLE.
     * @param string &$output   the output of the service (also used to give an error
     * message on failure).
     *
     * @return true on success, false otherwise (in this later case, $err_code
     * gives the reason why it failed and $output contains an error message).
     */
    public function serviceWeb($url,&$err_code,&$output)
    {
        // Sequence validation
        $this->ensureIsProxy();
        $this->ensureAuthenticationCallSuccessful();

        // Argument validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        try {
            $service = $this->getProxiedService(PHPCASSHIB_PROXIED_SERVICE_HTTP_GET);
            $service->setUrl($url);
            $service->send();
            $output = $service->getResponseBody();
            $err_code = PHPCASSHIB_SERVICE_OK;
            return true;
        } catch (CASSHIB_ProxyTicketException $e) {
            $err_code = $e->getCode();
            $output = $e->getMessage();
            return false;
        } catch (CASSHIB_ProxiedService_Exception $e) {
            $lang = $this->getLangObj();
            $output = sprintf(
                $lang->getServiceUnavailable(), $url, $e->getMessage()
            );
            $err_code = PHPCASSHIB_SERVICE_NOT_AVAILABLE;
            return false;
        }
    }

    /**
     * This method is used to access an IMAP/POP3/NNTP service.
     *
     * @param string $url        a string giving the URL of the service, including
     * the mailing box for IMAP URLs, as accepted by imap_open().
     * @param string $serviceUrl a string giving for CASSHIB retrieve Proxy ticket
     * @param string $flags      options given to imap_open().
     * @param int    &$err_code  an error code Possible values are
     * PHPCASSHIB_SERVICE_OK (on success), PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE,
     * PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE, PHPCASSHIB_SERVICE_PT_FAILURE,
     *  PHPCASSHIB_SERVICE_NOT_AVAILABLE.
     * @param string &$err_msg   an error message on failure
     * @param string &$pt        the Proxy Ticket (PT) retrieved from the CASSHIB
     * server to access the URL on success, false on error).
     *
     * @return object an IMAP stream on success, false otherwise (in this later
     *  case, $err_code gives the reason why it failed and $err_msg contains an
     *  error message).
     */
    public function serviceMail($url,$serviceUrl,$flags,&$err_code,&$err_msg,&$pt)
    {
        // Sequence validation
        $this->ensureIsProxy();
        $this->ensureAuthenticationCallSuccessful();

        // Argument validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');
        if (gettype($serviceUrl) != 'string')
            throw new CASSHIB_TypeMismatchException($serviceUrl, '$serviceUrl', 'string');
        if (gettype($flags) != 'integer')
            throw new CASSHIB_TypeMismatchException($flags, '$flags', 'string');

        try {
            $service = $this->getProxiedService(PHPCASSHIB_PROXIED_SERVICE_IMAP);
            $service->setServiceUrl($serviceUrl);
            $service->setMailbox($url);
            $service->setOptions($flags);

            $stream = $service->open();
            $err_code = PHPCASSHIB_SERVICE_OK;
            $pt = $service->getImapProxyTicket();
            return $stream;
        } catch (CASSHIB_ProxyTicketException $e) {
            $err_msg = $e->getMessage();
            $err_code = $e->getCode();
            $pt = false;
            return false;
        } catch (CASSHIB_ProxiedService_Exception $e) {
            $lang = $this->getLangObj();
            $err_msg = sprintf(
                $lang->getServiceUnavailable(),
                $url,
                $e->getMessage()
            );
            $err_code = PHPCASSHIB_SERVICE_NOT_AVAILABLE;
            $pt = false;
            return false;
        }
    }

    /** @} **/

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                  PROXIED CLIENT FEATURES (CASSHIB 2.0)                 XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    // ########################################################################
    //  PT
    // ########################################################################
    /**
    * @addtogroup internalService
    * @{
    */

    /**
     * This array will store a list of proxies in front of this application. This
     * property will only be populated if this script is being proxied rather than
     * accessed directly.
     *
     * It is set in CASSHIB_Client::validateCas20() and can be read by
     * CASSHIB_Client::getProxies()
     *
     * @access private
     */
    private $_proxies = array();

    /**
     * Answer an array of proxies that are sitting in front of this application.
     *
     * This method will only return a non-empty array if we have received and
     * validated a Proxy Ticket.
     *
     * @return array
     * @access public
     */
    public function getProxies()
    {
        return $this->_proxies;
    }

    /**
     * Set the Proxy array, probably from persistant storage.
     *
     * @param array $proxies An array of proxies
     *
     * @return void
     * @access private
     */
    private function _setProxies($proxies)
    {
        $this->_proxies = $proxies;
        if (!empty($proxies)) {
            // For proxy-authenticated requests people are not viewing the URL
            // directly since the client is another application making a
            // web-service call.
            // Because of this, stripping the ticket from the URL is unnecessary
            // and causes another web-service request to be performed. Additionally,
            // if session handling on either the client or the server malfunctions
            // then the subsequent request will not complete successfully.
            $this->setNoClearTicketsFromUrl();
        }
    }

    /**
     * A container of patterns to be allowed as proxies in front of the CASSHIB client.
     *
     * @var CASSHIB_ProxyChain_AllowedList
     */
    private $_allowed_proxy_chains;

    /**
     * Answer the CASSHIB_ProxyChain_AllowedList object for this client.
     *
     * @return CASSHIB_ProxyChain_AllowedList
     */
    public function getAllowedProxyChains ()
    {
        if (empty($this->_allowed_proxy_chains)) {
            $this->_allowed_proxy_chains = new CASSHIB_ProxyChain_AllowedList();
        }
        return $this->_allowed_proxy_chains;
    }

    /** @} */
    // ########################################################################
    //  PT VALIDATION
    // ########################################################################
    /**
    * @addtogroup internalProxied
    * @{
    */

    /**
     * This method is used to validate a Cas 2.0 ST or PT; halt on failure
     * Used for all Cas 2.0 validations
     *
     * @param string &$validate_url  the url of the reponse
     * @param string &$text_response the text of the repsones
     * @param string &$tree_response the domxml tree of the respones
     * @param bool   $renew          true to force the authentication with the Cas server
     *
     * @return bool true when successfull and issue a CASSHIB_AuthenticationException
     * and false on an error
     */
    public function validateCas20(&$validate_url,&$text_response,&$tree_response, $renew=false)
    {
        phpCASSHIB::traceBegin();
        phpCASSHIB::trace($text_response);
        $result = false;
        // build the URL to validate the ticket
        if ($this->getAllowedProxyChains()->isProxyingAllowed()) {
            $validate_url = $this->getServerProxyValidateURL().'&ticket='
                .urlencode($this->getTicket());
        } else {
            $validate_url = $this->getServerServiceValidateURL().'&ticket='
                .urlencode($this->getTicket());
        }

        if ( $this->isProxy() ) {
            // pass the callback url for CASSHIB proxies
            $validate_url .= '&pgtUrl='.urlencode($this->_getCallbackURL());
        }

        if ( $renew ) {
            // pass the renew
            $validate_url .= '&renew=true';
        }

        // open and read the URL
        if ( !$this->_readURL($validate_url, $headers, $text_response, $err_msg) ) {
            phpCASSHIB::trace(
                'could not open URL \''.$validate_url.'\' to validate ('.$err_msg.')'
            );
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                true/*$no_response*/
            );
            $result = false;
        }

        // create new DOMDocument object
        $dom = new DOMDocument();
        // Fix possible whitspace problems
        $dom->preserveWhiteSpace = false;
        // Cas servers should only return data in utf-8
        $dom->encoding = "utf-8";
        // read the response of the Cas server into a DOMDocument object
        if ( !($dom->loadXML($text_response))) {
            // read failed
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, true/*$bad_response*/, $text_response
            );
            $result = false;
        } else if ( !($tree_response = $dom->documentElement) ) {
            // read the root node of the XML tree
            // read failed
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, true/*$bad_response*/, $text_response
            );
            $result = false;
        } else if ($tree_response->localName != 'serviceResponse') {
            // insure that tag name is 'serviceResponse'
            // bad root node
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, true/*$bad_response*/, $text_response
            );
            $result = false;
        } else if ($tree_response->getElementsByTagName("authenticationSuccess")->length != 0) {
            // authentication succeded, extract the user name
            $success_elements = $tree_response
                ->getElementsByTagName("authenticationSuccess");
            if ( $success_elements->item(0)->getElementsByTagName("user")->length == 0) {
                // no user specified => error
                throw new CASSHIB_AuthenticationException(
                    $this, 'Ticket not validated', $validate_url,
                    false/*$no_response*/, true/*$bad_response*/, $text_response
                );
                $result = false;
            } else {
                $this->_setUser(
                    trim(
                        $success_elements->item(0)->getElementsByTagName("user")->item(0)->nodeValue
                    )
                );
                $this->_readExtraAttributesCas20($success_elements);
                // Store the proxies we are sitting behind for authorization checking
                $proxyList = array();
                if ( sizeof($arr = $success_elements->item(0)->getElementsByTagName("proxy")) > 0) {
                    foreach ($arr as $proxyElem) {
                        phpCASSHIB::trace("Found Proxy: ".$proxyElem->nodeValue);
                        $proxyList[] = trim($proxyElem->nodeValue);
                    }
                    $this->_setProxies($proxyList);
                    phpCASSHIB::trace("Storing Proxy List");
                }
                // Check if the proxies in front of us are allowed
                if (!$this->getAllowedProxyChains()->isProxyListAllowed($proxyList)) {
                    throw new CASSHIB_AuthenticationException(
                        $this, 'Proxy not allowed', $validate_url,
                        false/*$no_response*/, true/*$bad_response*/,
                        $text_response
                    );
                    $result = false;
                } else {
                    $result = true;
                }
            }
        } else if ( $tree_response->getElementsByTagName("authenticationFailure")->length != 0) {
            // authentication succeded, extract the error code and message
            $auth_fail_list = $tree_response
                ->getElementsByTagName("authenticationFailure");
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, false/*$bad_response*/,
                $text_response,
                $auth_fail_list->item(0)->getAttribute('code')/*$err_code*/,
                trim($auth_fail_list->item(0)->nodeValue)/*$err_msg*/
            );
            $result = false;
        } else {
            throw new CASSHIB_AuthenticationException(
                $this, 'Ticket not validated', $validate_url,
                false/*$no_response*/, true/*$bad_response*/,
                $text_response
            );
            $result = false;
        }
        if ($result) {
            $this->_renameSession($this->getTicket());
        }
        // at this step, Ticket has been validated and $this->_user has been set,

        phpCASSHIB::traceEnd($result);
        return $result;
    }


    /**
     * This method will parse the DOM and pull out the attributes from the XML
     * payload and put them into an array, then put the array into the session.
     *
     * @param string $success_elements payload of the response
     *
     * @return bool true when successfull, halt otherwise by calling
     * CASSHIB_Client::_authError().
     */
    private function _readExtraAttributesCas20($success_elements)
    {
        phpCASSHIB::traceBegin();

        $extra_attributes = array();

        // "Jasig Style" Attributes:
        //
        //     <Cas:serviceResponse xmlns:Cas='http://www.yale.edu/tp/Cas'>
        //         <Cas:authenticationSuccess>
        //             <Cas:user>jsmith</Cas:user>
        //             <Cas:attributes>
        //                 <Cas:attraStyle>RubyCas</Cas:attraStyle>
        //                 <Cas:surname>Smith</Cas:surname>
        //                 <Cas:givenName>John</Cas:givenName>
        //                 <Cas:memberOf>CN=Staff,OU=Groups,DC=example,DC=edu</Cas:memberOf>
        //                 <Cas:memberOf>CN=Spanish Department,OU=Departments,OU=Groups,DC=example,DC=edu</Cas:memberOf>
        //             </Cas:attributes>
        //             <Cas:proxyGrantingTicket>PGTIOU-84678-8a9d2sfa23Casd</Cas:proxyGrantingTicket>
        //         </Cas:authenticationSuccess>
        //     </Cas:serviceResponse>
        //
        if ( $success_elements->item(0)->getElementsByTagName("attributes")->length != 0) {
            $attr_nodes = $success_elements->item(0)
                ->getElementsByTagName("attributes");
            phpCASSHIB :: trace("Found nested jasig style attributes");
            if ($attr_nodes->item(0)->hasChildNodes()) {
                // Nested Attributes
                foreach ($attr_nodes->item(0)->childNodes as $attr_child) {
                    phpCASSHIB :: trace(
                        "Attribute [".$attr_child->localName."] = "
                        .$attr_child->nodeValue
                    );
                    $this->_addAttributeToArray(
                        $extra_attributes, $attr_child->localName,
                        $attr_child->nodeValue
                    );
                }
            }
        } else {
            // "RubyCas Style" attributes
            //
            //     <Cas:serviceResponse xmlns:Cas='http://www.yale.edu/tp/Cas'>
            //         <Cas:authenticationSuccess>
            //             <Cas:user>jsmith</Cas:user>
            //
            //             <Cas:attraStyle>RubyCas</Cas:attraStyle>
            //             <Cas:surname>Smith</Cas:surname>
            //             <Cas:givenName>John</Cas:givenName>
            //             <Cas:memberOf>CN=Staff,OU=Groups,DC=example,DC=edu</Cas:memberOf>
            //             <Cas:memberOf>CN=Spanish Department,OU=Departments,OU=Groups,DC=example,DC=edu</Cas:memberOf>
            //
            //             <Cas:proxyGrantingTicket>PGTIOU-84678-8a9d2sfa23Casd</Cas:proxyGrantingTicket>
            //         </Cas:authenticationSuccess>
            //     </Cas:serviceResponse>
            //
            phpCASSHIB :: trace("Testing for rubyCas style attributes");
            $childnodes = $success_elements->item(0)->childNodes;
            foreach ($childnodes as $attr_node) {
                switch ($attr_node->localName) {
                case 'user':
                case 'proxies':
                case 'proxyGrantingTicket':
                    continue;
                default:
                    if (strlen(trim($attr_node->nodeValue))) {
                        phpCASSHIB :: trace(
                            "Attribute [".$attr_node->localName."] = ".$attr_node->nodeValue
                        );
                        $this->_addAttributeToArray(
                            $extra_attributes, $attr_node->localName,
                            $attr_node->nodeValue
                        );
                    }
                }
            }
        }

        // "Name-Value" attributes.
        //
        // Attribute format from these mailing list thread:
        // http://jasig.275507.n4.nabble.com/CASSHIB-attributes-and-how-they-appear-in-the-CASSHIB-response-td264272.html
        // Note: This is a less widely used format, but in use by at least two institutions.
        //
        //     <Cas:serviceResponse xmlns:Cas='http://www.yale.edu/tp/Cas'>
        //         <Cas:authenticationSuccess>
        //             <Cas:user>jsmith</Cas:user>
        //
        //             <Cas:attribute name='attraStyle' value='Name-Value' />
        //             <Cas:attribute name='surname' value='Smith' />
        //             <Cas:attribute name='givenName' value='John' />
        //             <Cas:attribute name='memberOf' value='CN=Staff,OU=Groups,DC=example,DC=edu' />
        //             <Cas:attribute name='memberOf' value='CN=Spanish Department,OU=Departments,OU=Groups,DC=example,DC=edu' />
        //
        //             <Cas:proxyGrantingTicket>PGTIOU-84678-8a9d2sfa23Casd</Cas:proxyGrantingTicket>
        //         </Cas:authenticationSuccess>
        //     </Cas:serviceResponse>
        //
        if (!count($extra_attributes)
            && $success_elements->item(0)->getElementsByTagName("attribute")->length != 0
        ) {
            $attr_nodes = $success_elements->item(0)
                ->getElementsByTagName("attribute");
            $firstAttr = $attr_nodes->item(0);
            if (!$firstAttr->hasChildNodes()
                && $firstAttr->hasAttribute('name')
                && $firstAttr->hasAttribute('value')
            ) {
                phpCASSHIB :: trace("Found Name-Value style attributes");
                // Nested Attributes
                foreach ($attr_nodes as $attr_node) {
                    if ($attr_node->hasAttribute('name')
                        && $attr_node->hasAttribute('value')
                    ) {
                        phpCASSHIB :: trace(
                            "Attribute [".$attr_node->getAttribute('name')
                            ."] = ".$attr_node->getAttribute('value')
                        );
                        $this->_addAttributeToArray(
                            $extra_attributes, $attr_node->getAttribute('name'),
                            $attr_node->getAttribute('value')
                        );
                    }
                }
            }
        }

        $this->setAttributes($extra_attributes);
        phpCASSHIB::traceEnd();
        return true;
    }

    /**
     * Add an attribute value to an array of attributes.
     *
     * @param array  &$attributeArray reference to array
     * @param string $name            name of attribute
     * @param string $value           value of attribute
     *
     * @return void
     */
    private function _addAttributeToArray(array &$attributeArray, $name, $value)
    {
        // If multiple attributes exist, add as an array value
        if (isset($attributeArray[$name])) {
            // Initialize the array with the existing value
            if (!is_array($attributeArray[$name])) {
                $existingValue = $attributeArray[$name];
                $attributeArray[$name] = array($existingValue);
            }

            $attributeArray[$name][] = trim($value);
        } else {
            $attributeArray[$name] = trim($value);
        }
    }

    /** @} */

    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    // XX                                                                    XX
    // XX                               MISC                                 XX
    // XX                                                                    XX
    // XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

    /**
     * @addtogroup internalMisc
     * @{
     */

    // ########################################################################
    //  URL
    // ########################################################################
    /**
    * the URL of the current request (without any ticket CGI parameter). Written
    * and read by CASSHIB_Client::getURL().
    *
    * @hideinitializer
    */
    private $_url = '';


    /**
     * This method sets the URL of the current request
     *
     * @param string $url url to set for service
     *
     * @return void
     */
    public function setURL($url)
    {
        // Argument Validation
        if (gettype($url) != 'string')
            throw new CASSHIB_TypeMismatchException($url, '$url', 'string');

        $this->_url = $url;
    }

    /**
     * This method returns the URL of the current request (without any ticket
     * CGI parameter).
     *
     * @return The URL
     */
    public function getURL()
    {
        phpCASSHIB::traceBegin();
        // the URL is built when needed only
        if ( empty($this->_url) ) {
            $final_uri = '';
            // remove the ticket if present in the URL
            $final_uri = ($this->_isHttps()) ? 'https' : 'http';
            $final_uri .= '://';

            $final_uri .= $this->_getClientUrl();
            $request_uri    = explode('?', $_SERVER['REQUEST_URI'], 2);
            $final_uri        .= $request_uri[0];

            if (isset($request_uri[1]) && $request_uri[1]) {
                $query_string= $this->_removeParameterFromQueryString('ticket', $request_uri[1]);

                // If the query string still has anything left,
                // append it to the final URI
                if ($query_string !== '') {
                    $final_uri    .= "?$query_string";
                }
            }

            phpCASSHIB::trace("Final URI: $final_uri");
            $this->setURL($final_uri);
        }
        phpCASSHIB::traceEnd($this->_url);
        return $this->_url;
    }


    /**
     * Try to figure out the phpCASSHIB client URL with possible Proxys / Ports etc.
     *
     * @return string Server URL with domain:port
     */
    private function _getClientUrl()
    {
        $server_url = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            // explode the host list separated by comma and use the first host
            $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
            // see rfc7239#5.3 and rfc7230#2.7.1: port is in HTTP_X_FORWARDED_HOST if non default
            return $hosts[0];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
            $server_url = $_SERVER['HTTP_X_FORWARDED_SERVER'];
        } else {
            if (empty($_SERVER['SERVER_NAME'])) {
                $server_url = $_SERVER['HTTP_HOST'];
            } else {
                $server_url = $_SERVER['SERVER_NAME'];
            }
        }
        if (!strpos($server_url, ':')) {
            if (empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                $server_port = $_SERVER['SERVER_PORT'];
            } else {
                $ports = explode(',', $_SERVER['HTTP_X_FORWARDED_PORT']);
                $server_port = $ports[0];
            }

            if ( ($this->_isHttps() && $server_port!=443)
                || (!$this->_isHttps() && $server_port!=80)
            ) {
                $server_url .= ':';
                $server_url .= $server_port;
            }
        }
        return $server_url;
    }

    /**
     * This method checks to see if the request is secured via HTTPS
     *
     * @return bool true if https, false otherwise
     */
    private function _isHttps()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        }
        if ( isset($_SERVER['HTTPS'])
            && !empty($_SERVER['HTTPS'])
            && strcasecmp($_SERVER['HTTPS'], 'off') !== 0
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Removes a parameter from a query string
     *
     * @param string $parameterName name of parameter
     * @param string $queryString   query string
     *
     * @return string new query string
     *
     * @link http://stackoverflow.com/questions/1842681/regular-expression-to-remove-one-parameter-from-query-string
     */
    private function _removeParameterFromQueryString($parameterName, $queryString)
    {
        $parameterName    = preg_quote($parameterName);
        return preg_replace(
            "/&$parameterName(=[^&]*)?|^$parameterName(=[^&]*)?&?/",
            '', $queryString
        );
    }

    /**
     * This method is used to append query parameters to an url. Since the url
     * might already contain parameter it has to be detected and to build a proper
     * URL
     *
     * @param string $url   base url to add the query params to
     * @param string $query params in query form with & separated
     *
     * @return url with query params
     */
    private function _buildQueryUrl($url, $query)
    {
        $url .= (strstr($url, '?') === false) ? '?' : '&';
        $url .= $query;
        return $url;
    }

    /**
     * Renaming the session
     *
     * @param string $ticket name of the ticket
     *
     * @return void
     */
    private function _renameSession($ticket)
    {
        phpCASSHIB::traceBegin();
        if ($this->getChangeSessionID()) {
            if (!empty($this->_user)) {
                $old_session = $_SESSION;
                phpCASSHIB :: trace("Killing session: ". session_id());
                session_destroy();
                // set up a new session, of name based on the ticket
                $session_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $ticket);
                phpCASSHIB :: trace("Starting session: ". $session_id);
                session_id($session_id);
                session_start();
                phpCASSHIB :: trace("Restoring old session vars");
                $_SESSION = $old_session;
            } else {
                phpCASSHIB :: trace (
                    'Session should only be renamed after successfull authentication'
                );
            }
        } else {
            phpCASSHIB :: trace(
                "Skipping session rename since phpCASSHIB is not handling the session."
            );
        }
        phpCASSHIB::traceEnd();
    }


    // ########################################################################
    //  AUTHENTICATION ERROR HANDLING
    // ########################################################################
    /**
    * This method is used to print the HTML output when the user was not
    * authenticated.
    *
    * @param string $failure      the failure that occured
    * @param string $CASSHIB_url      the URL the Cas server was asked for
    * @param bool   $no_response  the response from the Cas server (other
    * parameters are ignored if true)
    * @param bool   $bad_response bad response from the Cas server ($err_code
    * and $err_msg ignored if true)
    * @param string $CASSHIB_response the response of the Cas server
    * @param int    $err_code     the error code given by the Cas server
    * @param string $err_msg      the error message given by the Cas server
    *
    * @return void
    */
    private function _authError(
        $failure,
        $CASSHIB_url,
        $no_response,
        $bad_response='',
        $CASSHIB_response='',
        $err_code='',
        $err_msg=''
    ) {
        phpCASSHIB::traceBegin();
        $lang = $this->getLangObj();
        $this->printHTMLHeader($lang->getAuthenticationFailed());
        printf(
            $lang->getYouWereNotAuthenticated(), htmlentities($this->getURL()),
            isset($_SERVER['SERVER_ADMIN']) ? $_SERVER['SERVER_ADMIN']:''
        );
        phpCASSHIB::trace('CASSHIB URL: '.$CASSHIB_url);
        phpCASSHIB::trace('Authentication failure: '.$failure);
        if ( $no_response ) {
            phpCASSHIB::trace('Reason: no response from the Cas server');
        } else {
            if ( $bad_response ) {
                phpCASSHIB::trace('Reason: bad response from the Cas server');
            } else {
                switch ($this->getServerVersion()) {
                case CASSHIB_VERSION_1_0:
                    phpCASSHIB::trace('Reason: CASSHIB error');
                    break;
                case CASSHIB_VERSION_2_0:
                case CASSHIB_VERSION_3_0:
                    if ( empty($err_code) ) {
                        phpCASSHIB::trace('Reason: no CASSHIB error');
                    } else {
                        phpCASSHIB::trace(
                            'Reason: ['.$err_code.'] CASSHIB error: '.$err_msg
                        );
                    }
                    break;
                }
            }
            phpCASSHIB::trace('CASSHIB response: '.$CASSHIB_response);
        }
        $this->printHTMLFooter();
        phpCASSHIB::traceExit();
        throw new CASSHIB_GracefullTerminationException();
    }

    // ########################################################################
    //  PGTIOU/PGTID and logoutRequest rebroadcasting
    // ########################################################################

    /**
     * Boolean of whether to rebroadcast pgtIou/pgtId and logoutRequest, and
     * array of the nodes.
     */
    private $_rebroadcast = false;
    private $_rebroadcast_nodes = array();

    /**
     * Constants used for determining rebroadcast node type.
     */
    const HOSTNAME = 0;
    const IP = 1;

    /**
     * Determine the node type from the URL.
     *
     * @param String $nodeURL The node URL.
     *
     * @return string hostname
     *
     */
    private function _getNodeType($nodeURL)
    {
        phpCASSHIB::traceBegin();
        if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $nodeURL)) {
            phpCASSHIB::traceEnd(self::IP);
            return self::IP;
        } else {
            phpCASSHIB::traceEnd(self::HOSTNAME);
            return self::HOSTNAME;
        }
    }

    /**
     * Store the rebroadcast node for pgtIou/pgtId and logout requests.
     *
     * @param string $rebroadcastNodeUrl The rebroadcast node URL.
     *
     * @return void
     */
    public function addRebroadcastNode($rebroadcastNodeUrl)
    {
        // Argument validation
        if ( !(bool)preg_match("/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $rebroadcastNodeUrl))
            throw new CASSHIB_TypeMismatchException($rebroadcastNodeUrl, '$rebroadcastNodeUrl', 'url');

        // Store the rebroadcast node and set flag
        $this->_rebroadcast = true;
        $this->_rebroadcast_nodes[] = $rebroadcastNodeUrl;
    }

    /**
     * An array to store extra rebroadcast curl options.
     */
    private $_rebroadcast_headers = array();

    /**
     * This method is used to add header parameters when rebroadcasting
     * pgtIou/pgtId or logoutRequest.
     *
     * @param string $header Header to send when rebroadcasting.
     *
     * @return void
     */
    public function addRebroadcastHeader($header)
    {
        if (gettype($header) != 'string')
            throw new CASSHIB_TypeMismatchException($header, '$header', 'string');

        $this->_rebroadcast_headers[] = $header;
    }

    /**
     * Constants used for determining rebroadcast type (logout or pgtIou/pgtId).
     */
    const LOGOUT = 0;
    const PGTIOU = 1;

    /**
     * This method rebroadcasts logout/pgtIou requests. Can be LOGOUT,PGTIOU
     *
     * @param int $type type of rebroadcasting.
     *
     * @return void
     */
    private function _rebroadcast($type)
    {
        phpCASSHIB::traceBegin();

        $rebroadcast_curl_options = array(
        CURLOPT_FAILONERROR => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT => 4);

        // Try to determine the IP address of the server
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } else if (!empty($_SERVER['LOCAL_ADDR'])) {
            // IIS 7
            $ip = $_SERVER['LOCAL_ADDR'];
        }
        // Try to determine the DNS name of the server
        if (!empty($ip)) {
            $dns = gethostbyaddr($ip);
        }
        $multiClassName = 'CASSHIB_Request_CurlMultiRequest';
        $multiRequest = new $multiClassName();

        for ($i = 0; $i < sizeof($this->_rebroadcast_nodes); $i++) {
            if ((($this->_getNodeType($this->_rebroadcast_nodes[$i]) == self::HOSTNAME) && !empty($dns) && (stripos($this->_rebroadcast_nodes[$i], $dns) === false))
                || (($this->_getNodeType($this->_rebroadcast_nodes[$i]) == self::IP) && !empty($ip) && (stripos($this->_rebroadcast_nodes[$i], $ip) === false))
            ) {
                phpCASSHIB::trace(
                    'Rebroadcast target URL: '.$this->_rebroadcast_nodes[$i]
                    .$_SERVER['REQUEST_URI']
                );
                $className = $this->_requestImplementation;
                $request = new $className();

                $url = $this->_rebroadcast_nodes[$i].$_SERVER['REQUEST_URI'];
                $request->setUrl($url);

                if (count($this->_rebroadcast_headers)) {
                    $request->addHeaders($this->_rebroadcast_headers);
                }

                $request->makePost();
                if ($type == self::LOGOUT) {
                    // Logout request
                    $request->setPostBody(
                        'rebroadcast=false&logoutRequest='.$_POST['logoutRequest']
                    );
                } else if ($type == self::PGTIOU) {
                    // pgtIou/pgtId rebroadcast
                    $request->setPostBody('rebroadcast=false');
                }

                $request->setCurlOptions($rebroadcast_curl_options);

                $multiRequest->addRequest($request);
            } else {
                phpCASSHIB::trace(
                    'Rebroadcast not sent to self: '
                    .$this->_rebroadcast_nodes[$i].' == '.(!empty($ip)?$ip:'')
                    .'/'.(!empty($dns)?$dns:'')
                );
            }
        }
        // We need at least 1 request
        if ($multiRequest->getNumRequests() > 0) {
            $multiRequest->send();
        }
        phpCASSHIB::traceEnd();
    }

    /** @} */
}