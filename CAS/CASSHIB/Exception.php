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
 * @file     CASSHIB/Exception.php
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 */

/**
 * A root exception interface for all exceptions in phpCASSHIB.
 *
 * All exceptions thrown in phpCASSHIB should implement this interface to allow them
 * to be caught as a category by clients. Each phpCASSHIB exception should extend
 * an appropriate SPL exception class that best fits its type.
 *
 * For example, an InvalidArgumentException in phpCASSHIB should be defined as
 *
 *		class CASSHIB_InvalidArgumentException
 *			extends InvalidArgumentException
 *			implements CASSHIB_Exception
 *		{ }
 *
 * This definition allows the CASSHIB_InvalidArgumentException to be caught as either
 * an InvalidArgumentException or as a CASSHIB_Exception.
 *
 * @class    CASSHIB_Exception
 * @category Authentication
 * @package  PhpCASSHIB
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASSHIBC/phpCASSHIB
 *
 */
interface CASSHIB_Exception
{

}
?>