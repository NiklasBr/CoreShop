<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;

define('PIMCORE_PROJECT_ROOT', __DIR__ . '/..');
$_SERVER['PIMCORE_ENVIRONMENT'] = $_SERVER['APP_ENV'] = $_SERVER['SYMFONY_ENV'] = 'test';

include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/../behat-bootstrap.php";

$request = Request::createFromGlobals();

// set current request as property on tool as there's no
// request stack available yet
Tool::setCurrentRequest($request);

/** @var \Pimcore\Kernel $kernel */
$kernel = \Pimcore\Bootstrap::kernel();

// reset current request - will be read from request stack from now on
Tool::setCurrentRequest(null);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
