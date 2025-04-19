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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

class powerFlow extends eqLogic
{

	/*     * *************************Attributs****************************** */


	/*     * ***********************Methode static*************************** */

	/**
	 * Core callback to provide additional information for a new Community post
	 * @return string
	 */
	public static function getConfigForCommunity()
	{
		$hw = jeedom::getHardwareName();
		if ($hw == 'diy') $hw = trim(shell_exec('systemd-detect-virt'));
		if ($hw == 'none') $hw = 'diy';
		$distrib = trim(shell_exec('. /etc/*-release && echo $ID $VERSION_ID'));
		$res = 'OS: ' . $distrib . ' on ' . $hw;
		$res .= ' ; PHP: ' . phpversion() . '<br/>';
		return $res;
	}

	/**
	 * Core callback for the plugin cron every five minutes
	 
	public static function cron()
	{
		log::add(__CLASS__, 'debug', '┌──:fg-success: cron5() :/fg:──');
		log::add(__CLASS__, 'debug', '└───────────────────────');
	}
*/
	/**
	 * Convert UTC Date to DateTimeZone Europe/Paris
	 * @return string
	
	public static function convertDateUTC($_date)
	{
		$date_convert = new \DateTime($_date, new \DateTimeZone('UTC'));
		$date_convert->setTimezone(new \DateTimeZone('Europe/Paris'));
		return $date_convert->format('Y-m-d H:i:s');
	}
 */
	/*     * *********************Méthodes d'instance************************* */

	/**
	 * Call by core after save into bdd
	 */
	public function postSave()
	{
		log::add(__CLASS__, 'debug', '┌──:fg-success: postSave() :/fg:──');
		/* Refresh */
		$logicalId = 'refresh';
		$cmd = $this->getCmd(null, $logicalId);
		if (!is_object($cmd)) {
			$cmd = new powerFlowCmd();
			$cmd->setIsVisible(1);
			$cmd->setName(__('Rafraichir', __FILE__));
			$cmd->setLogicalId($logicalId);
			$cmd->setOrder($i);
		}
		$cmd->setEqLogic_id($this->getId());
		$cmd->setType('action');
		$cmd->setSubType('other');
		if ($cmd->getChanged() === true) $cmd->save();
		$i++;

		log::add(__CLASS__, 'debug', '└────────────────────');
	}
}

class powerFlowCmd extends cmd
{
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	/**
	 * This method is called when a command is executed
	 */
	public function execute($_options = array())
	{
		if ($this->getType() != 'action') {
			return;
		}
		$eqLogic = $this->getEqLogic();
		if($this->getLogicalId() == 'refresh'){
			//$eqLogic->refreshStatus();
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}