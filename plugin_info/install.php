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
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function powerFlow_install()
{
	/* function launched when activating the plugin or when installing the plugin 
	log::add('powerFlow', 'debug', '┌────────── :fg-warning: Launch function powerFlow_install() :/fg: ──────────');
	$eqLogic = powerFlow::byLogicalId('powerFlow', 'powerFlow'); // logicalID, pluginID
	if (!is_object($eqLogic)) {
		$eqLogic = new powerFlow();
		$eqLogic->setLogicalId('powerFlow');
		$eqLogic->setName(__('powerFlow', __FILE__));
		$eqLogic->setEqType_name('powerFlow');
		$eqLogic->setIsVisible(1);
		$eqLogic->setIsEnable(1);
		$eqLogic->save();
		message::add('powerFlow', __('Votre Equipement powerFlow a été créé avec succès', __FILE__), '<a href="index.php?v=d&p=plugin&id=powerFlow">' . __('Configuration', __FILE__) . '</a>');
	}
	foreach (eqLogic::byType('powerFlow') as $powerFlow) {
		$powerFlow->save();
	}
	log::add('powerFlow', 'debug', '└───────────────────────────────────────────');
	*/
}

function powerFlow_update()
{
	/* function launched when updating plugin 
	log::add('powerFlow', 'debug', '┌────────── :fg-warning: Launch function powerFlow_update() :/fg: ──────────');
	$eqLogic = powerFlow::byLogicalId('powerFlow', 'powerFlow'); // logicalID, pluginID
	if (!is_object($eqLogic)) {
		$eqLogic = new powerFlow();
		$eqLogic->setLogicalId('powerFlow');
		$eqLogic->setName(__('powerFlow', __FILE__));
		$eqLogic->setEqType_name('powerFlow');
		$eqLogic->setIsVisible(1);
		$eqLogic->setIsEnable(1);
		$eqLogic->save();
		message::add('powerFlow', __('Votre Equipement powerFlow a été créé avec succès', __FILE__), '<a href="index.php?v=d&p=plugin&id=powerFlow">' . __('Configuration', __FILE__) . '</a>');
	}
	foreach (eqLogic::byType('powerFlow') as $powerFlow) {
		$powerFlow->save();
	}
	log::add('powerFlow', 'debug', '└───────────────────────────────────────────');
	*/
}
