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
 *
 *
 * Interface class of the phpCASSHIB library
 * PHP Version 5
 *
 * @file     CAS/CASSHIB.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Pascal Aubry <pascal.aubry@univ-rennes1.fr>
 * @author   Olivier Berger <olivier.berger@it-sudparis.eu>
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 * @ingroup public
 */


//
// hack by Vangelis Haniotakis to handle the absence of $_SERVER['REQUEST_URI']
// in IIS
//
if (php_sapi_name() != 'cli') {
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
    }
}

// Add a E_USER_DEPRECATED for php versions <= 5.2
if (!defined('E_USER_DEPRECATED')) {
    define('E_USER_DEPRECATED', E_USER_NOTICE);
}


// ########################################################################
//  CONSTANTS
// ########################################################################

// ------------------------------------------------------------------------
//  CASSHIB VERSIONS
// ------------------------------------------------------------------------

/**
 * phpCASSHIB version. accessible for the user by phpCASSHIB::getVersion().
 */
define('PHPCASSHIB_VERSION', '1.3.4');

/**
 * @addtogroup public
 * @{
 */

/**
 * CASSHIB version 1.0
 */
if (!defined('CASSHIB_VERSION_1_0'))
    define("CASSHIB_VERSION_1_0", '1.0');
/*!
 * CASSHIB version 2.0
*/
if (!defined('CASSHIB_VERSION_2_0'))
    define("CASSHIB_VERSION_2_0", '2.0');
/**
 * CASSHIB version 3.0
 */
if (!defined('CASSHIB_VERSION_3_0'))
    define("CASSHIB_VERSION_3_0", '3.0');

// ------------------------------------------------------------------------
//  SAML defines
// ------------------------------------------------------------------------

/**
 * SAML protocol
 */
if (!defined('SAML_VERSION_1_1'))
define("SAML_VERSION_1_1", 'S1');

/**
 * XML header for SAML POST
 */
if (!defined('SAML_XML_HEADER'))
define("SAML_XML_HEADER", '<?xml version="1.0" encoding="UTF-8"?>');

/**
 * SOAP envelope for SAML POST
 */
if (!defined('SAML_SOAP_ENV'))
define("SAML_SOAP_ENV", '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Header/>');

/**
 * SOAP body for SAML POST
 */
if (!defined('SAML_SOAP_BODY'))
define("SAML_SOAP_BODY", '<SOAP-ENV:Body>');

/**
 * SAMLP request
 */
if (!defined('SAMLP_REQUEST')) {
    define("SAMLP_REQUEST", '<samlp:Request xmlns:samlp="urn:oasis:names:tc:SAML:1.0:protocol"  MajorVersion="1" MinorVersion="1" RequestID="_192.168.16.51.1024506224022" IssueInstant="2002-06-19T17:03:44.022Z">');
    define("SAMLP_REQUEST_CLOSE", '</samlp:Request>');
}

/**
 * SAMLP artifact tag (for the ticket)
 */
if (!defined('SAML_ASSERTION_ARTIFACT'))
define("SAML_ASSERTION_ARTIFACT", '<samlp:AssertionArtifact>');

/**
 * SAMLP close
 */
if (!defined('SAML_ASSERTION_ARTIFACT_CLOSE'))
define("SAML_ASSERTION_ARTIFACT_CLOSE", '</samlp:AssertionArtifact>');

/**
 * SOAP body close
 */
if (!defined('SAML_SOAP_BODY_CLOSE'))
define("SAML_SOAP_BODY_CLOSE", '</SOAP-ENV:Body>');

/**
 * SOAP envelope close
 */
if (!defined('SAML_SOAP_ENV_CLOSE'))
define("SAML_SOAP_ENV_CLOSE", '</SOAP-ENV:Envelope>');

/**
 * SAML Attributes
 */
if (!defined('SAML_ATTRIBUTES'))
define("SAML_ATTRIBUTES", 'SAMLATTRIBS');

/**
 * SAML Attributes
 */
if (!defined('DEFAULT_ERROR'))
define("DEFAULT_ERROR", 'Internal script failure');

/** @} */
/**
 * @addtogroup publicPGTStorage
 * @{
 */
// ------------------------------------------------------------------------
//  FILE PGT STORAGE
// ------------------------------------------------------------------------
/**
 * Default path used when storing PGT's to file
 */
if (!defined('CASSHIB_PGT_STORAGE_FILE_DEFAULT_PATH'))
define("CASSHIB_PGT_STORAGE_FILE_DEFAULT_PATH", session_save_path());
/** @} */
// ------------------------------------------------------------------------
// SERVICE ACCESS ERRORS
// ------------------------------------------------------------------------
/**
 * @addtogroup publicServices
 * @{
 */

/**
 * phpCASSHIB::service() error code on success
 */
define("PHPCASSHIB_SERVICE_OK", 0);
/**
 * phpCASSHIB::service() error code when the PT could not retrieve because
 * the CASSHIB server did not respond.
 */
define("PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE", 1);
/**
 * phpCASSHIB::service() error code when the PT could not retrieve because
 * the response of the CASSHIB server was ill-formed.
 */
define("PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE", 2);
/**
 * phpCASSHIB::service() error code when the PT could not retrieve because
 * the CASSHIB server did not want to.
 */
define("PHPCASSHIB_SERVICE_PT_FAILURE", 3);
/**
 * phpCASSHIB::service() error code when the service was not available.
 */
define("PHPCASSHIB_SERVICE_NOT_AVAILABLE", 4);

// ------------------------------------------------------------------------
// SERVICE TYPES
// ------------------------------------------------------------------------
/**
 * phpCASSHIB::getProxiedService() type for HTTP GET
 */
define("PHPCASSHIB_PROXIED_SERVICE_HTTP_GET", 'CASSHIB_ProxiedService_Http_Get');
/**
 * phpCASSHIB::getProxiedService() type for HTTP POST
 */
define("PHPCASSHIB_PROXIED_SERVICE_HTTP_POST", 'CASSHIB_ProxiedService_Http_Post');
/**
 * phpCASSHIB::getProxiedService() type for IMAP
 */
define("PHPCASSHIB_PROXIED_SERVICE_IMAP", 'CASSHIB_ProxiedService_Imap');


/** @} */
// ------------------------------------------------------------------------
//  LANGUAGES
// ------------------------------------------------------------------------
/**
 * @addtogroup publicLang
 * @{
 */

define("PHPCASSHIB_LANG_ENGLISH", 'CASSHIB_Languages_English');
define("PHPCASSHIB_LANG_FRENCH", 'CASSHIB_Languages_French');
define("PHPCASSHIB_LANG_GREEK", 'CASSHIB_Languages_Greek');
define("PHPCASSHIB_LANG_GERMAN", 'CASSHIB_Languages_German');
define("PHPCASSHIB_LANG_JAPANESE", 'CASSHIB_Languages_Japanese');
define("PHPCASSHIB_LANG_SPANISH", 'CASSHIB_Languages_Spanish');
define("PHPCASSHIB_LANG_CATALAN", 'CASSHIB_Languages_Catalan');

/** @} */

/**
 * @addtogroup internalLang
 * @{
 */

/**
 * phpCASSHIB default language (when phpCASSHIB::setLang() is not used)
 */
define("PHPCASSHIB_LANG_DEFAULT", PHPCASSHIB_LANG_ENGLISH);

/** @} */
// ------------------------------------------------------------------------
//  DEBUG
// ------------------------------------------------------------------------
/**
 * @addtogroup publicDebug
 * @{
 */

/**
 * The default directory for the debug file under Unix.
 */
function casshib_gettmpdir() {
    if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
    if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
    if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
    return "/tmp";
}
if (!defined('DEFAULT_DEBUG_DIR'))
define('DEFAULT_DEBUG_DIR', casshib_gettmpdir()."/");

/** @} */

// include the class autoloader
require_once dirname(__FILE__) . '/CASSHIB/Autoload.php';

/**
 * The phpCASSHIB class is a simple container for the phpCASSHIB library. It provides CASSHIB
 * authentication for web applications written in PHP.
 *
 * @ingroup public
 * @class phpCASSHIB
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

class phpCASSHIB
{

    /**
     * This variable is used by the interface class phpCASSHIB.
     *
     * @var CASSHIB_Client
     * @hideinitializer
     */
    private static $_PHPCASSHIB_CLIENT;

    /**
     * This variable is used to store where the initializer is called from
     * (to print a comprehensive error in case of multiple calls).
     *
     * @hideinitializer
     */
    private static $_PHPCASSHIB_INIT_CALL;

    /**
     * This variable is used to store phpCASSHIB debug mode.
     *
     * @hideinitializer
     */
    private static $_PHPCASSHIB_DEBUG;

    /**
     * This variable is used to enable verbose mode
     * This pevents debug info to be show to the user. Since it's a security
     * feature the default is false
     *
     * @hideinitializer
     */
    private static $_PHPCASSHIB_VERBOSE = false;


    // ########################################################################
    //  INITIALIZATION
    // ########################################################################

    /**
     * @addtogroup publicInit
     * @{
     */

    /**
     * phpCASSHIB client initializer.
     *
     * @param string $server_version  the version of the CASSHIB server
     * @param string $server_hostname the hostname of the CASSHIB server
     * @param string $server_port     the port the CASSHIB server is running on
     * @param string $server_uri      the URI the CASSHIB server is responding on
     * @param bool   $changeSessionID Allow phpCASSHIB to change the session_id (Single
     * Sign Out/handleLogoutRequests is based on that change)
     *
     * @return a newly created CASSHIB_Client object
     * @note Only one of the phpCASSHIB::client() and phpCASSHIB::proxy functions should be
     * called, only once, and before all other methods (except phpCASSHIB::getVersion()
     * and phpCASSHIB::setDebug()).
     */
    public static function client($server_version, $server_hostname,
        $server_port, $server_uri, $changeSessionID = true) {

        $authconfig = get_config('auth/casshib');

        phpCASSHIB :: traceBegin();
        if (is_object(self::$_PHPCASSHIB_CLIENT)) {
            phpCASSHIB :: error(self::$_PHPCASSHIB_INIT_CALL['method'] . '() has already been called (at ' . self::$_PHPCASSHIB_INIT_CALL['file'] . ':' . self::$_PHPCASSHIB_INIT_CALL['line'] . ')');
        }

        // store where the initializer is called from
        $dbg = debug_backtrace();
        self::$_PHPCASSHIB_INIT_CALL = array (
            'done' => true,
            'file' => $dbg[0]['file'],
            'line' => $dbg[0]['line'],
            'method' => __CLASS__ . '::' . __FUNCTION__
        );

        // initialize the object $_PHPCASSHIB_CLIENT
        try {
            self::$_PHPCASSHIB_CLIENT = new CASSHIB_Client(
                $server_version, false, $server_hostname, $server_port, $server_uri,
                $changeSessionID
            );
            // ENT Shibboletisation.
            if (!empty($authconfig->shib_service_validate_url)) {
                self::$_PHPCASSHIB_CLIENT->setServerServiceValidateURL($authconfig->shib_service_validate_url);
            }
            if (!empty($authconfig->shib_proxy_validate_url)) {
                self::$_PHPCASSHIB_CLIENT->setServerProxyValidateURL($authconfig->shib_proxy_validate_url);
            }
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
        phpCASSHIB :: traceEnd();
    }

    /**
     * phpCASSHIB proxy initializer.
     *
     * @param string $server_version  the version of the CASSHIB server
     * @param string $server_hostname the hostname of the CASSHIB server
     * @param string $server_port     the port the CASSHIB server is running on
     * @param string $server_uri      the URI the CASSHIB server is responding on
     * @param bool   $changeSessionID Allow phpCASSHIB to change the session_id (Single
     * Sign Out/handleLogoutRequests is based on that change)
     *
     * @return a newly created CASSHIB_Client object
     * @note Only one of the phpCASSHIB::client() and phpCASSHIB::proxy functions should be
     * called, only once, and before all other methods (except phpCASSHIB::getVersion()
     * and phpCASSHIB::setDebug()).
     */
    public static function proxy($server_version, $server_hostname,
        $server_port, $server_uri, $changeSessionID = true
    ) {
        phpCASSHIB :: traceBegin();
        if (is_object(self::$_PHPCASSHIB_CLIENT)) {
            phpCASSHIB :: error(self::$_PHPCASSHIB_INIT_CALL['method'] . '() has already been called (at ' . self::$_PHPCASSHIB_INIT_CALL['file'] . ':' . self::$_PHPCASSHIB_INIT_CALL['line'] . ')');
        }

        // store where the initialzer is called from
        $dbg = debug_backtrace();
        self::$_PHPCASSHIB_INIT_CALL = array (
            'done' => true,
            'file' => $dbg[0]['file'],
            'line' => $dbg[0]['line'],
            'method' => __CLASS__ . '::' . __FUNCTION__
        );

        // initialize the object $_PHPCASSHIB_CLIENT
        try {
            self::$_PHPCASSHIB_CLIENT = new CASSHIB_Client(
                $server_version, true, $server_hostname, $server_port, $server_uri,
                $changeSessionID
            );
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
        phpCASSHIB :: traceEnd();
    }

    /**
     * Answer whether or not the client or proxy has been initialized
     *
     * @return bool
     */
    public static function isInitialized ()
    {
        return (is_object(self::$_PHPCASSHIB_CLIENT));
    }

    /** @} */
    // ########################################################################
    //  DEBUGGING
    // ########################################################################

    /**
     * @addtogroup publicDebug
     * @{
     */

    /**
     * Set/unset debug mode
     *
     * @param string $filename the name of the file used for logging, or false
     * to stop debugging.
     *
     * @return void
     */
    public static function setDebug($filename = '')
    {
        if ($filename != false && gettype($filename) != 'string') {
            phpCASSHIB :: error('type mismatched for parameter $dbg (should be false or the name of the log file)');
        }
        if ($filename === false) {
            self::$_PHPCASSHIB_DEBUG['filename'] = false;

        } else {
            if (empty ($filename)) {
                if (preg_match('/^Win.*/', getenv('OS'))) {
                    if (isset ($_ENV['TMP'])) {
                        $debugDir = $_ENV['TMP'] . '/';
                    } else {
                        $debugDir = '';
                    }
                } else {
                    $debugDir = DEFAULT_DEBUG_DIR;
                }
                $filename = $debugDir . 'phpCASSHIB.log';
            }

            if (empty (self::$_PHPCASSHIB_DEBUG['unique_id'])) {
                self::$_PHPCASSHIB_DEBUG['unique_id'] = substr(strtoupper(md5(uniqid(''))), 0, 4);
            }

            self::$_PHPCASSHIB_DEBUG['filename'] = $filename;
            self::$_PHPCASSHIB_DEBUG['indent'] = 0;

            phpCASSHIB :: trace('START ('.date("Y-m-d H:i:s").') phpCASSHIB-' . PHPCASSHIB_VERSION . ' ******************');
        }
    }

    /**
     * Enable verbose errors messages in the website output
     * This is a security relevant since internal status info may leak an may
     * help an attacker. Default is therefore false
     *
     * @param bool $verbose enable verbose output
     *
     * @return void
     */
    public static function setVerbose($verbose)
    {
        if ($verbose === true) {
            self::$_PHPCASSHIB_VERBOSE = true;
        } else {
            self::$_PHPCASSHIB_VERBOSE = false;
        }
    }


    /**
     * Show is verbose mode is on
     *
     * @return boot verbose
     */
    public static function getVerbose()
    {
        return self::$_PHPCASSHIB_VERBOSE;
    }

    /**
     * Logs a string in debug mode.
     *
     * @param string $str the string to write
     *
     * @return void
     * @private
     */
    public static function log($str)
    {
        $indent_str = ".";


        if (!empty(self::$_PHPCASSHIB_DEBUG['filename'])) {
            // Check if file exists and modifiy file permissions to be only
            // readable by the webserver
            if (!file_exists(self::$_PHPCASSHIB_DEBUG['filename'])) {
                touch(self::$_PHPCASSHIB_DEBUG['filename']);
                // Chmod will fail on windows
                @chmod(self::$_PHPCASSHIB_DEBUG['filename'], 0600);
            }
            for ($i = 0; $i < self::$_PHPCASSHIB_DEBUG['indent']; $i++) {

                $indent_str .= '|    ';
            }
            // allow for multiline output with proper identing. Usefull for
            // dumping CASSHIB answers etc.
            $str2 = str_replace("\n", "\n" . self::$_PHPCASSHIB_DEBUG['unique_id'] . ' ' . $indent_str, $str);
            error_log(self::$_PHPCASSHIB_DEBUG['unique_id'] . ' ' . $indent_str . $str2 . "\n", 3, self::$_PHPCASSHIB_DEBUG['filename']);
        }

    }

    /**
     * This method is used by interface methods to print an error and where the
     * function was originally called from.
     *
     * @param string $msg the message to print
     *
     * @return void
     * @private
     */
    public static function error($msg)
    {
        phpCASSHIB :: traceBegin();
        $dbg = debug_backtrace();
        $function = '?';
        $file = '?';
        $line = '?';
        if (is_array($dbg)) {
            for ($i = 1; $i < sizeof($dbg); $i++) {
                if (is_array($dbg[$i]) && isset($dbg[$i]['class']) ) {
                    if ($dbg[$i]['class'] == __CLASS__) {
                        $function = $dbg[$i]['function'];
                        $file = $dbg[$i]['file'];
                        $line = $dbg[$i]['line'];
                    }
                }
            }
        }
        if (self::$_PHPCASSHIB_VERBOSE) {
            echo "<br />\n<b>phpCASSHIB error</b>: <font color=\"FF0000\"><b>" . __CLASS__ . "::" . $function . '(): ' . htmlentities($msg) . "</b></font> in <b>" . $file . "</b> on line <b>" . $line . "</b><br />\n";
        } else {
            echo "<br />\n<b>Error</b>: <font color=\"FF0000\"><b>". DEFAULT_ERROR ."</b><br />\n";
        }
        phpCASSHIB :: trace($msg . ' in ' . $file . 'on line ' . $line );
        phpCASSHIB :: traceEnd();

        throw new CASSHIB_GracefullTerminationException(__CLASS__ . "::" . $function . '(): ' . $msg);
    }

    /**
     * This method is used to log something in debug mode.
     *
     * @param string $str string to log
     *
     * @return void
     */
    public static function trace($str)
    {
        $dbg = debug_backtrace();
        phpCASSHIB :: log($str . ' [' . basename($dbg[0]['file']) . ':' . $dbg[0]['line'] . ']');
    }

    /**
     * This method is used to indicate the start of the execution of a function
     * in debug mode.
     *
     * @return void
     */
    public static function traceBegin()
    {
        $dbg = debug_backtrace();
        $str = '=> ';
        if (!empty ($dbg[1]['class'])) {
            $str .= $dbg[1]['class'] . '::';
        }
        $str .= $dbg[1]['function'] . '(';
        if (is_array($dbg[1]['args'])) {
            foreach ($dbg[1]['args'] as $index => $arg) {
                if ($index != 0) {
                    $str .= ', ';
                }
                if (is_object($arg)) {
                    $str .= get_class($arg);
                } else {
                    $str .= str_replace(array("\r\n", "\n", "\r"), "", var_export($arg, true));
                }
            }
        }
        if (isset($dbg[1]['file'])) {
            $file = basename($dbg[1]['file']);
        } else {
            $file = 'unknown_file';
        }
        if (isset($dbg[1]['line'])) {
            $line = $dbg[1]['line'];
        } else {
            $line = 'unknown_line';
        }
        $str .= ') [' . $file . ':' . $line . ']';
        phpCASSHIB :: log($str);
        if (!isset(self::$_PHPCASSHIB_DEBUG['indent'])) {
            self::$_PHPCASSHIB_DEBUG['indent'] = 0;
        } else {
            self::$_PHPCASSHIB_DEBUG['indent']++;
        }
    }

    /**
     * This method is used to indicate the end of the execution of a function in
     * debug mode.
     *
     * @param string $res the result of the function
     *
     * @return void
     */
    public static function traceEnd($res = '')
    {
        if (empty(self::$_PHPCASSHIB_DEBUG['indent'])) {
            self::$_PHPCASSHIB_DEBUG['indent'] = 0;
        } else {
            self::$_PHPCASSHIB_DEBUG['indent']--;
        }
        $dbg = debug_backtrace();
        $str = '';
        if (is_object($res)) {
            $str .= '<= ' . get_class($res);
        } else {
            $str .= '<= ' . str_replace(array("\r\n", "\n", "\r"), "", var_export($res, true));
        }

        phpCASSHIB :: log($str);
    }

    /**
     * This method is used to indicate the end of the execution of the program
     *
     * @return void
     */
    public static function traceExit()
    {
        phpCASSHIB :: log('exit()');
        while (self::$_PHPCASSHIB_DEBUG['indent'] > 0) {
            phpCASSHIB :: log('-');
            self::$_PHPCASSHIB_DEBUG['indent']--;
        }
    }

    /** @} */
    // ########################################################################
    //  INTERNATIONALIZATION
    // ########################################################################
    /**
    * @addtogroup publicLang
    * @{
    */

    /**
     * This method is used to set the language used by phpCASSHIB.
     *
     * @param string $lang string representing the language.
     *
     * @return void
     *
     * @sa PHPCASSHIB_LANG_FRENCH, PHPCASSHIB_LANG_ENGLISH
     * @note Can be called only once.
     */
    public static function setLang($lang)
    {
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setLang($lang);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /** @} */
    // ########################################################################
    //  VERSION
    // ########################################################################
    /**
    * @addtogroup public
    * @{
    */

    /**
     * This method returns the phpCASSHIB version.
     *
     * @return the phpCASSHIB version.
     */
    public static function getVersion()
    {
        return PHPCASSHIB_VERSION;
    }

    /** @} */
    // ########################################################################
    //  HTML OUTPUT
    // ########################################################################
    /**
    * @addtogroup publicOutput
    * @{
    */

    /**
     * This method sets the HTML header used for all outputs.
     *
     * @param string $header the HTML header.
     *
     * @return void
     */
    public static function setHTMLHeader($header)
    {
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setHTMLHeader($header);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * This method sets the HTML footer used for all outputs.
     *
     * @param string $footer the HTML footer.
     *
     * @return void
     */
    public static function setHTMLFooter($footer)
    {
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setHTMLFooter($footer);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /** @} */
    // ########################################################################
    //  PGT STORAGE
    // ########################################################################
    /**
    * @addtogroup publicPGTStorage
    * @{
    */

    /**
     * This method can be used to set a custom PGT storage object.
     *
     * @param CASSHIB_PGTStorage $storage a PGT storage object that inherits from the
     * CASSHIB_PGTStorage class
     *
     * @return void
     */
    public static function setPGTStorage($storage)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setPGTStorage($storage);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to tell phpCASSHIB to store the response of the
     * CASSHIB server to PGT requests in a database.
     *
     * @param string $dsn_or_pdo     a dsn string to use for creating a PDO
     * object or a PDO object
     * @param string $username       the username to use when connecting to the
     * database
     * @param string $password       the password to use when connecting to the
     * database
     * @param string $table          the table to use for storing and retrieving
     * PGT's
     * @param string $driver_options any driver options to use when connecting
     * to the database
     *
     * @return void
     */
    public static function setPGTStorageDb($dsn_or_pdo, $username='',
        $password='', $table='', $driver_options=null
    ) {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setPGTStorageDb($dsn_or_pdo, $username, $password, $table, $driver_options);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to tell phpCASSHIB to store the response of the
     * CASSHIB server to PGT requests onto the filesystem.
     *
     * @param string $path the path where the PGT's should be stored
     *
     * @return void
     */
    public static function setPGTStorageFile($path = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setPGTStorageFile($path);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
        phpCASSHIB :: traceEnd();
    }
    /** @} */
    // ########################################################################
    // ACCESS TO EXTERNAL SERVICES
    // ########################################################################
    /**
    * @addtogroup publicServices
    * @{
    */

    /**
     * Answer a proxy-authenticated service handler.
     *
     * @param string $type The service type. One of
     * PHPCASSHIB_PROXIED_SERVICE_HTTP_GET; PHPCASSHIB_PROXIED_SERVICE_HTTP_POST;
     * PHPCASSHIB_PROXIED_SERVICE_IMAP
     *
     * @return CASSHIB_ProxiedService
     * @throws InvalidArgumentException If the service type is unknown.
     */
    public static function getProxiedService ($type)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            $res = self::$_PHPCASSHIB_CLIENT->getProxiedService($type);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
        return $res;
    }

    /**
     * Initialize a proxied-service handler with the proxy-ticket it should use.
     *
     * @param CASSHIB_ProxiedService $proxiedService Proxied Service Handler
     *
     * @return void
     * @throws CASSHIB_ProxyTicketException If there is a proxy-ticket failure.
     *		The code of the Exception will be one of:
     *			PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE
     *			PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE
     *			PHPCASSHIB_SERVICE_PT_FAILURE
     */
    public static function initializeProxiedService (CASSHIB_ProxiedService $proxiedService)
    {
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->initializeProxiedService($proxiedService);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * This method is used to access an HTTP[S] service.
     *
     * @param string $url       the service to access.
     * @param string &$err_code an error code Possible values are
     * PHPCASSHIB_SERVICE_OK (on success), PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE,
     * PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE, PHPCASSHIB_SERVICE_PT_FAILURE,
     * PHPCASSHIB_SERVICE_NOT_AVAILABLE.
     * @param string &$output   the output of the service (also used to give an
     * error message on failure).
     *
     * @return bool true on success, false otherwise (in this later case,
     * $err_code gives the reason why it failed and $output contains an error
     * message).
     */
    public static function serviceWeb($url, & $err_code, & $output)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            $res = self::$_PHPCASSHIB_CLIENT->serviceWeb($url, $err_code, $output);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd($res);
        return $res;
    }

    /**
     * This method is used to access an IMAP/POP3/NNTP service.
     *
     * @param string $url       a string giving the URL of the service,
     * including the mailing box for IMAP URLs, as accepted by imap_open().
     * @param string $service   a string giving for CASSHIB retrieve Proxy ticket
     * @param string $flags     options given to imap_open().
     * @param string &$err_code an error code Possible values are
     * PHPCASSHIB_SERVICE_OK (on success), PHPCASSHIB_SERVICE_PT_NO_SERVER_RESPONSE,
     * PHPCASSHIB_SERVICE_PT_BAD_SERVER_RESPONSE, PHPCASSHIB_SERVICE_PT_FAILURE,
     * PHPCASSHIB_SERVICE_NOT_AVAILABLE.
     * @param string &$err_msg  an error message on failure
     * @param string &$pt       the Proxy Ticket (PT) retrieved from the CASSHIB
     * server to access the URL on success, false on error).
     *
     * @return object IMAP stream on success, false otherwise (in this later
     * case, $err_code gives the reason why it failed and $err_msg contains an
     * error message).
     */
    public static function serviceMail($url, $service, $flags, & $err_code, & $err_msg, & $pt)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            $res = self::$_PHPCASSHIB_CLIENT->serviceMail($url, $service, $flags, $err_code, $err_msg, $pt);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd($res);
        return $res;
    }

    /** @} */
    // ########################################################################
    //  AUTHENTICATION
    // ########################################################################
    /**
    * @addtogroup publicAuth
    * @{
    */

    /**
     * Set the times authentication will be cached before really accessing the
     * CASSHIB server in gateway mode:
     * - -1: check only once, and then never again (until you pree login)
     * - 0: always check
     * - n: check every "n" time
     *
     * @param int $n an integer.
     *
     * @return void
     */
    public static function setCacheTimesForAuthRecheck($n)
    {
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setCacheTimesForAuthRecheck($n);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Set a callback function to be run when a user authenticates.
     *
     * The callback function will be passed a $logoutTicket as its first
     * parameter, followed by any $additionalArgs you pass. The $logoutTicket
     * parameter is an opaque string that can be used to map the session-id to
     * logout request in order to support single-signout in applications that
     * manage their own sessions (rather than letting phpCASSHIB start the session).
     *
     * phpCASSHIB::forceAuthentication() will always exit and forward client unless
     * they are already authenticated. To perform an action at the moment the user
     * logs in (such as registering an account, performing logging, etc), register
     * a callback function here.
     *
     * @param string $function       Callback function
     * @param array  $additionalArgs optional array of arguments
     *
     * @return void
     */
    public static function setPostAuthenticateCallback ($function, array $additionalArgs = array())
    {
        phpCASSHIB::_validateClientExists();

        self::$_PHPCASSHIB_CLIENT->setPostAuthenticateCallback($function, $additionalArgs);
    }

    /**
     * Set a callback function to be run when a single-signout request is
     * received. The callback function will be passed a $logoutTicket as its
     * first parameter, followed by any $additionalArgs you pass. The
     * $logoutTicket parameter is an opaque string that can be used to map a
     * session-id to the logout request in order to support single-signout in
     * applications that manage their own sessions (rather than letting phpCASSHIB
     * start and destroy the session).
     *
     * @param string $function       Callback function
     * @param array  $additionalArgs optional array of arguments
     *
     * @return void
     */
    public static function setSingleSignoutCallback ($function, array $additionalArgs = array())
    {
        phpCASSHIB::_validateClientExists();

        self::$_PHPCASSHIB_CLIENT->setSingleSignoutCallback($function, $additionalArgs);
    }

    /**
     * This method is called to check if the user is already authenticated
     * locally or has a global CASSHIB session. A already existing CASSHIB session is
     * determined by a CASSHIB gateway call.(CASSHIB login call without any interactive
     * prompt)
     *
     * @return true when the user is authenticated, false when a previous
     * gateway login failed or the function will not return if the user is
     * redirected to the CASSHIB server for a gateway login attempt
     */
    public static function checkAuthentication()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        $auth = self::$_PHPCASSHIB_CLIENT->checkAuthentication();

        // store where the authentication has been checked and the result
        self::$_PHPCASSHIB_CLIENT->markAuthenticationCall($auth);

        phpCASSHIB :: traceEnd($auth);
        return $auth;
    }

    /**
     * This method is called to force authentication if the user was not already
     * authenticated. If the user is not authenticated, halt by redirecting to
     * the CASSHIB server.
     *
     * @return bool Authentication
     */
    public static function forceAuthentication()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();
        $auth = self::$_PHPCASSHIB_CLIENT->forceAuthentication();

        // store where the authentication has been checked and the result
        self::$_PHPCASSHIB_CLIENT->markAuthenticationCall($auth);

        /*      if (!$auth) {
         phpCASSHIB :: trace('user is not authenticated, redirecting to the CASSHIB server');
        self::$_PHPCASSHIB_CLIENT->forceAuthentication();
        } else {
        phpCASSHIB :: trace('no need to authenticate (user `' . phpCASSHIB :: getUser() . '\' is already authenticated)');
        }*/

        phpCASSHIB :: traceEnd();
        return $auth;
    }

    /**
     * This method is called to renew the authentication.
     *
     * @return void
     **/
    public static function renewAuthentication()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        $auth = self::$_PHPCASSHIB_CLIENT->renewAuthentication();

        // store where the authentication has been checked and the result
        self::$_PHPCASSHIB_CLIENT->markAuthenticationCall($auth);

        //self::$_PHPCASSHIB_CLIENT->renewAuthentication();
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is called to check if the user is authenticated (previously or by
     * tickets given in the URL).
     *
     * @return true when the user is authenticated.
     */
    public static function isAuthenticated()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        // call the isAuthenticated method of the $_PHPCASSHIB_CLIENT object
        $auth = self::$_PHPCASSHIB_CLIENT->isAuthenticated();

        // store where the authentication has been checked and the result
        self::$_PHPCASSHIB_CLIENT->markAuthenticationCall($auth);

        phpCASSHIB :: traceEnd($auth);
        return $auth;
    }

    /**
     * Checks whether authenticated based on $_SESSION. Useful to avoid
     * server calls.
     *
     * @return bool true if authenticated, false otherwise.
     * @since 0.4.22 by Brendan Arnold
     */
    public static function isSessionAuthenticated()
    {
        phpCASSHIB::_validateClientExists();

        return (self::$_PHPCASSHIB_CLIENT->isSessionAuthenticated());
    }

    /**
     * This method returns the CASSHIB user's login name.
     *
     * @return string the login name of the authenticated user
     * @warning should only be called after phpCASSHIB::forceAuthentication()
     * or phpCASSHIB::checkAuthentication().
     * */
    public static function getUser()
    {
        phpCASSHIB::_validateClientExists();

        try {
            return self::$_PHPCASSHIB_CLIENT->getUser();
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Answer attributes about the authenticated user.
     *
     * @warning should only be called after phpCASSHIB::forceAuthentication()
     * or phpCASSHIB::checkAuthentication().
     *
     * @return array
     */
    public static function getAttributes()
    {
        phpCASSHIB::_validateClientExists();

        try {
            return self::$_PHPCASSHIB_CLIENT->getAttributes();
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Answer true if there are attributes for the authenticated user.
     *
     * @warning should only be called after phpCASSHIB::forceAuthentication()
     * or phpCASSHIB::checkAuthentication().
     *
     * @return bool
     */
    public static function hasAttributes()
    {
        phpCASSHIB::_validateClientExists();

        try {
            return self::$_PHPCASSHIB_CLIENT->hasAttributes();
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Answer true if an attribute exists for the authenticated user.
     *
     * @param string $key attribute name
     *
     * @return bool
     * @warning should only be called after phpCASSHIB::forceAuthentication()
     * or phpCASSHIB::checkAuthentication().
     */
    public static function hasAttribute($key)
    {
        phpCASSHIB::_validateClientExists();

        try {
            return self::$_PHPCASSHIB_CLIENT->hasAttribute($key);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Answer an attribute for the authenticated user.
     *
     * @param string $key attribute name
     *
     * @return mixed string for a single value or an array if multiple values exist.
     * @warning should only be called after phpCASSHIB::forceAuthentication()
     * or phpCASSHIB::checkAuthentication().
     */
    public static function getAttribute($key)
    {
        phpCASSHIB::_validateClientExists();

        try {
            return self::$_PHPCASSHIB_CLIENT->getAttribute($key);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle logout requests.
     *
     * @param bool  $check_client    additional safety check
     * @param array $allowed_clients array of allowed clients
     *
     * @return void
     */
    public static function handleLogoutRequests($check_client = true, $allowed_clients = false)
    {
        phpCASSHIB::_validateClientExists();

        return (self::$_PHPCASSHIB_CLIENT->handleLogoutRequests($check_client, $allowed_clients));
    }

    /**
     * This method returns the URL to be used to login.
     * or phpCASSHIB::isAuthenticated().
     *
     * @return the login name of the authenticated user
     */
    public static function getServerLoginURL()
    {
        phpCASSHIB::_validateClientExists();

        return self::$_PHPCASSHIB_CLIENT->getServerLoginURL();
    }

    /**
     * Set the login URL of the CASSHIB server.
     *
     * @param string $url the login URL
     *
     * @return void
     * @since 0.4.21 by Wyman Chan
     */
    public static function setServerLoginURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setServerLoginURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Set the serviceValidate URL of the CASSHIB server.
     * Used only in CASSHIB 1.0 validations
     *
     * @param string $url the serviceValidate URL
     *
     * @return void
     */
    public static function setServerServiceValidateURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setServerServiceValidateURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Set the proxyValidate URL of the CASSHIB server.
     * Used for all CASSHIB 2.0 validations
     *
     * @param string $url the proxyValidate URL
     *
     * @return void
     */
    public static function setServerProxyValidateURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setServerProxyValidateURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Set the samlValidate URL of the CASSHIB server.
     *
     * @param string $url the samlValidate URL
     *
     * @return void
     */
    public static function setServerSamlValidateURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setServerSamlValidateURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * This method returns the URL to be used to login.
     * or phpCASSHIB::isAuthenticated().
     *
     * @return the login name of the authenticated user
     */
    public static function getServerLogoutURL()
    {
        phpCASSHIB::_validateClientExists();

        return self::$_PHPCASSHIB_CLIENT->getServerLogoutURL();
    }

    /**
     * Set the logout URL of the CASSHIB server.
     *
     * @param string $url the logout URL
     *
     * @return void
     * @since 0.4.21 by Wyman Chan
     */
    public static function setServerLogoutURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setServerLogoutURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to logout from CASSHIB.
     *
     * @param string $params an array that contains the optional url and
     * service parameters that will be passed to the CASSHIB server
     *
     * @return void
     */
    public static function logout($params = "")
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        $parsedParams = array ();
        if ($params != "") {
            if (is_string($params)) {
                phpCASSHIB :: error('method `phpCASSHIB::logout($url)\' is now deprecated, use `phpCASSHIB::logoutWithUrl($url)\' instead');
            }
            if (!is_array($params)) {
                phpCASSHIB :: error('type mismatched for parameter $params (should be `array\')');
            }
            foreach ($params as $key => $value) {
                if ($key != "service" && $key != "url") {
                    phpCASSHIB :: error('only `url\' and `service\' parameters are allowed for method `phpCASSHIB::logout($params)\'');
                }
                $parsedParams[$key] = $value;
            }
        }
        self::$_PHPCASSHIB_CLIENT->logout($parsedParams);
        // never reached
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to logout from CASSHIB. Halts by redirecting to the CASSHIB
     * server.
     *
     * @param string $service a URL that will be transmitted to the CASSHIB server
     *
     * @return void
     */
    public static function logoutWithRedirectService($service)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        if (!is_string($service)) {
            phpCASSHIB :: error('type mismatched for parameter $service (should be `string\')');
        }
        self::$_PHPCASSHIB_CLIENT->logout(array ( "service" => $service ));
        // never reached
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to logout from CASSHIB. Halts by redirecting to the CASSHIB
     * server.
     *
     * @param string $url a URL that will be transmitted to the CASSHIB server
     *
     * @return void
     * @deprecated The url parameter has been removed from the CASSHIB server as of
     * version 3.3.5.1
     */
    public static function logoutWithUrl($url)
    {
        trigger_error('Function deprecated for CASSHIB servers >= 3.3.5.1', E_USER_DEPRECATED);
        phpCASSHIB :: traceBegin();
        if (!is_object(self::$_PHPCASSHIB_CLIENT)) {
            phpCASSHIB :: error('this method should only be called after ' . __CLASS__ . '::client() or' . __CLASS__ . '::proxy()');
        }
        if (!is_string($url)) {
            phpCASSHIB :: error('type mismatched for parameter $url (should be `string\')');
        }
        self::$_PHPCASSHIB_CLIENT->logout(array ( "url" => $url ));
        // never reached
        phpCASSHIB :: traceEnd();
    }

    /**
     * This method is used to logout from CASSHIB. Halts by redirecting to the CASSHIB
     * server.
     *
     * @param string $service a URL that will be transmitted to the CASSHIB server
     * @param string $url     a URL that will be transmitted to the CASSHIB server
     *
     * @return void
     *
     * @deprecated The url parameter has been removed from the CASSHIB server as of
     * version 3.3.5.1
     */
    public static function logoutWithRedirectServiceAndUrl($service, $url)
    {
        trigger_error('Function deprecated for CASSHIB servers >= 3.3.5.1', E_USER_DEPRECATED);
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        if (!is_string($service)) {
            phpCASSHIB :: error('type mismatched for parameter $service (should be `string\')');
        }
        if (!is_string($url)) {
            phpCASSHIB :: error('type mismatched for parameter $url (should be `string\')');
        }
        self::$_PHPCASSHIB_CLIENT->logout(
            array (
                "service" => $service,
                "url" => $url
            )
        );
        // never reached
        phpCASSHIB :: traceEnd();
    }

    /**
     * Set the fixed URL that will be used by the CASSHIB server to transmit the
     * PGT. When this method is not called, a phpCASSHIB script uses its own URL
     * for the callback.
     *
     * @param string $url the URL
     *
     * @return void
     */
    public static function setFixedCallbackURL($url = '')
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setCallbackURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Set the fixed URL that will be set as the CASSHIB service parameter. When this
     * method is not called, a phpCASSHIB script uses its own URL.
     *
     * @param string $url the URL
     *
     * @return void
     */
    public static function setFixedServiceURL($url)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateProxyExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setURL($url);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Get the URL that is set as the CASSHIB service parameter.
     *
     * @return string Service Url
     */
    public static function getServiceURL()
    {
        phpCASSHIB::_validateProxyExists();
        return (self::$_PHPCASSHIB_CLIENT->getURL());
    }

    /**
     * Retrieve a Proxy Ticket from the CASSHIB server.
     *
     * @param string $target_service Url string of service to proxy
     * @param string &$err_code      error code
     * @param string &$err_msg       error message
     *
     * @return string Proxy Ticket
     */
    public static function retrievePT($target_service, & $err_code, & $err_msg)
    {
        phpCASSHIB::_validateProxyExists();

        try {
            return (self::$_PHPCASSHIB_CLIENT->retrievePT($target_service, $err_code, $err_msg));
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Set the certificate of the CASSHIB server CA and if the CN should be properly
     * verified.
     *
     * @param string $cert        CA certificate file name
     * @param bool   $validate_cn Validate CN in certificate (default true)
     *
     * @return void
     */
    public static function setCasServerCACert($cert, $validate_cn = true)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->setCasServerCACert($cert, $validate_cn);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Set no SSL validation for the CASSHIB server.
     *
     * @return void
     */
    public static function setNoCasServerValidation()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        phpCASSHIB :: trace('You have configured no validation of the legitimacy of the CASSHIB server. This is not recommended for production use.');
        self::$_PHPCASSHIB_CLIENT->setNoCasServerValidation();
        phpCASSHIB :: traceEnd();
    }


    /**
     * Disable the removal of a CAS-Ticket from the URL when authenticating
     * DISABLING POSES A SECURITY RISK:
     * We normally remove the ticket by an additional redirect as a security
     * precaution to prevent a ticket in the HTTP_REFERRER or be carried over in
     * the URL parameter
     *
     * @return void
     */
    public static function setNoClearTicketsFromUrl()
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        self::$_PHPCASSHIB_CLIENT->setNoClearTicketsFromUrl();
        phpCASSHIB :: traceEnd();
    }

    /** @} */

    /**
     * Change CURL options.
     * CURL is used to connect through HTTPS to CASSHIB server
     *
     * @param string $key   the option key
     * @param string $value the value to set
     *
     * @return void
     */
    public static function setExtraCurlOption($key, $value)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        self::$_PHPCASSHIB_CLIENT->setExtraCurlOption($key, $value);
        phpCASSHIB :: traceEnd();
    }

    /**
     * If you want your service to be proxied you have to enable it (default
     * disabled) and define an accepable list of proxies that are allowed to
     * proxy your service.
     *
     * Add each allowed proxy definition object. For the normal CASSHIB_ProxyChain
     * class, the constructor takes an array of proxies to match. The list is in
     * reverse just as seen from the service. Proxies have to be defined in reverse
     * from the service to the user. If a user hits service A and gets proxied via
     * B to service C the list of acceptable on C would be array(B,A). The definition
     * of an individual proxy can be either a string or a regexp (preg_match is used)
     * that will be matched against the proxy list supplied by the CASSHIB server
     * when validating the proxy tickets. The strings are compared starting from
     * the beginning and must fully match with the proxies in the list.
     * Example:
     * 		phpCASSHIB::allowProxyChain(new CASSHIB_ProxyChain(array(
     *				'https://app.example.com/'
     *			)));
     * 		phpCASSHIB::allowProxyChain(new CASSHIB_ProxyChain(array(
     *				'/^https:\/\/app[0-9]\.example\.com\/rest\//',
     *				'http://client.example.com/'
     *			)));
     *
     * For quick testing or in certain production screnarios you might want to
     * allow allow any other valid service to proxy your service. To do so, add
     * the "Any" chain:
     *		phpCASSHIB::allowProxyChain(new CASSHIB_ProxyChain_Any);
     * THIS SETTING IS HOWEVER NOT RECOMMENDED FOR PRODUCTION AND HAS SECURITY
     * IMPLICATIONS: YOU ARE ALLOWING ANY SERVICE TO ACT ON BEHALF OF A USER
     * ON THIS SERVICE.
     *
     * @param CASSHIB_ProxyChain_Interface $proxy_chain A proxy-chain that will be
     * matched against the proxies requesting access
     *
     * @return void
     */
    public static function allowProxyChain(CASSHIB_ProxyChain_Interface $proxy_chain)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        if (self::$_PHPCASSHIB_CLIENT->getServerVersion() !== CASSHIB_VERSION_2_0
            && self::$_PHPCASSHIB_CLIENT->getServerVersion() !== CASSHIB_VERSION_3_0
        ) {
            phpCASSHIB :: error('this method can only be used with the CASSHIB 2.0/3.0 protocols');
        }
        self::$_PHPCASSHIB_CLIENT->getAllowedProxyChains()->allowProxyChain($proxy_chain);
        phpCASSHIB :: traceEnd();
    }

    /**
     * Answer an array of proxies that are sitting in front of this application.
     * This method will only return a non-empty array if we have received and
     * validated a Proxy Ticket.
     *
     * @return array
     * @access public
     * @since 6/25/09
     */
    public static function getProxies ()
    {
        phpCASSHIB::_validateProxyExists();

        return(self::$_PHPCASSHIB_CLIENT->getProxies());
    }

    // ########################################################################
    // PGTIOU/PGTID and logoutRequest rebroadcasting
    // ########################################################################

    /**
     * Add a pgtIou/pgtId and logoutRequest rebroadcast node.
     *
     * @param string $rebroadCASSHIBtNodeUrl The rebroadcast node URL. Can be
     * hostname or IP.
     *
     * @return void
     */
    public static function addRebroadcastNode($rebroadcastNodeUrl)
    {
        phpCASSHIB::traceBegin();
        phpCASSHIB::log('rebroadCastNodeUrl:'.$rebroadcastNodeUrl);
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->addRebroadcastNode($rebroadcastNodeUrl);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB::traceEnd();
    }

    /**
     * This method is used to add header parameters when rebroadcasting
     * pgtIou/pgtId or logoutRequest.
     *
     * @param String $header Header to send when rebroadcasting.
     *
     * @return void
     */
    public static function addRebroadcastHeader($header)
    {
        phpCASSHIB :: traceBegin();
        phpCASSHIB::_validateClientExists();

        try {
            self::$_PHPCASSHIB_CLIENT->addRebroadcastHeader($header);
        } catch (Exception $e) {
            phpCASSHIB :: error(get_class($e) . ': ' . $e->getMessage());
        }

        phpCASSHIB :: traceEnd();
    }

    /**
     * Checks if a client already exists
     *
     * @throws CASSHIB_OutOfSequenceBeforeClientException
     *
     * @return void
     */
    private static function _validateClientExists()
    {
        if (!is_object(self::$_PHPCASSHIB_CLIENT)) {
            throw new CASSHIB_OutOfSequenceBeforeClientException();
        }
    }

    /**
     * Checks of a proxy client aready exists
     *
     * @throws CASSHIB_OutOfSequenceBeforeProxyException
     *
     * @return void
     */
    private static function _validateProxyExists()
    {
        if (!is_object(self::$_PHPCASSHIB_CLIENT)) {
            throw new CASSHIB_OutOfSequenceBeforeProxyException();
        }
    }
}
// ########################################################################
// DOCUMENTATION
// ########################################################################

// ########################################################################
//  MAIN PAGE

/**
 * @mainpage
 *
 * The following pages only show the source documentation.
 *
 */

// ########################################################################
//  MODULES DEFINITION

/** @defgroup public User interface */

/** @defgroup publicInit Initialization
 *  @ingroup public */

/** @defgroup publicAuth Authentication
 *  @ingroup public */

/** @defgroup publicServices Access to external services
 *  @ingroup public */

/** @defgroup publicConfig Configuration
 *  @ingroup public */

/** @defgroup publicLang Internationalization
 *  @ingroup publicConfig */

/** @defgroup publicOutput HTML output
 *  @ingroup publicConfig */

/** @defgroup publicPGTStorage PGT storage
 *  @ingroup publicConfig */

/** @defgroup publicDebug Debugging
 *  @ingroup public */

/** @defgroup internal Implementation */

/** @defgroup internalAuthentication Authentication
 *  @ingroup internal */

/** @defgroup internalBasic CASSHIB Basic client features (CASSHIB 1.0, Service Tickets)
 *  @ingroup internal */

/** @defgroup internalProxy CASSHIB Proxy features (CASSHIB 2.0, Proxy Granting Tickets)
 *  @ingroup internal */

/** @defgroup internalSAML CASSHIB SAML features (SAML 1.1)
 *  @ingroup internal */

/** @defgroup internalPGTStorage PGT storage
 *  @ingroup internalProxy */

/** @defgroup internalPGTStorageDb PGT storage in a database
 *  @ingroup internalPGTStorage */

/** @defgroup internalPGTStorageFile PGT storage on the filesystem
 *  @ingroup internalPGTStorage */

/** @defgroup internalCallback Callback from the CASSHIB server
 *  @ingroup internalProxy */

/** @defgroup internalProxyServices Proxy other services
 *  @ingroup internalProxy */

/** @defgroup internalService CASSHIB client features (CASSHIB 2.0, Proxied service)
 *  @ingroup internal */

/** @defgroup internalConfig Configuration
 *  @ingroup internal */

/** @defgroup internalBehave Internal behaviour of phpCASSHIB
 *  @ingroup internalConfig */

/** @defgroup internalOutput HTML output
 *  @ingroup internalConfig */

/** @defgroup internalLang Internationalization
 *  @ingroup internalConfig
 *
 * To add a new language:
 * - 1. define a new constant PHPCASSHIB_LANG_XXXXXX in CASSHIB/CASSHIB.php
 * - 2. copy any file from CASSHIB/languages to CASSHIB/languages/XXXXXX.php
 * - 3. Make the translations
 */

/** @defgroup internalDebug Debugging
 *  @ingroup internal */

/** @defgroup internalMisc Miscellaneous
 *  @ingroup internal */

// ########################################################################
//  EXAMPLES

/**
 * @example example_simple.php
 */
/**
 * @example example_service.php
 */
/**
 * @example example_service_that_proxies.php
 */
/**
 * @example example_service_POST.php
 */
/**
 * @example example_proxy_serviceWeb.php
 */
/**
 * @example example_proxy_serviceWeb_chaining.php
 */
/**
 * @example example_proxy_POST.php
 */
/**
 * @example example_proxy_GET.php
 */
/**
 * @example example_lang.php
 */
/**
 * @example example_html.php
 */
/**
 * @example example_pgt_storage_file.php
 */
/**
 * @example example_pgt_storage_db.php
 */
/**
 * @example example_gateway.php
 */
/**
 * @example example_logout.php
 */
/**
 * @example example_rebroadCASSHIBt.php
 */
/**
 * @example example_custom_urls.php
 */
/**
 * @example example_advanced_saml11.php
 */
?>
