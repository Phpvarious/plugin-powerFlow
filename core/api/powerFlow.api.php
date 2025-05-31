<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

header('Content-Type: application/json');

require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
global $jsonrpc;
global $_USER_GLOBAL;
if (!is_object($jsonrpc)) {
	throw new Exception(__('JSONRPC object not defined', __FILE__), -32699);
}

$params = $jsonrpc->getParams();
log::add('powerFlow', 'debug', '┌──────────▶︎ :fg-warning: Appel API powerFlow :/fg: ◀︎───────────');
log::add('powerFlow', 'debug', '| Method > ' . $jsonrpc->getMethod());
$secureApikeyLog = $params;
if (isset($secureApikeyLog['apikey'])) $secureApikeyLog['apikey'] = substr($secureApikeyLog['apikey'], 0, 10) . '...';
log::add('powerFlow', 'debug', '| Paramètres passés > ' . json_encode($secureApikeyLog));
log::add('powerFlow', 'debug', '└───────────────────────────────────────────');

throw new Exception(__('Aucune demande', __FILE__));