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
		/*if (!is_object($cmd)) {
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
*/
		log::add(__CLASS__, 'debug', '└────────────────────');
	}
  
    public function toHtml($_version = 'dashboard') {
      $replace = $this->preToHtml($_version);
      if (!is_array($replace)) {
        return $replace;
      }
      $version = jeedom::versionAlias($_version);
      if ($this->getConfiguration('refresh') == '') {
        $replace['#refresh_id#'] = '';
      }
      $replace['#version#'] = $_version;
      
      
      $replace['#debug#'] = 1;
      /////////////////////////////////////
      /////// Specific settings ///////////
      /////////////////////////////////////
      
      /////////////////////////////////////
      ////////////// GRID /////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('blink', '0') == '1') {
        $replace['#blink#'] = 1;
      }
      if ($this->getConfiguration('disableGauge', '0') == '1') {
        $replace['#activateGauge#'] = 0;
      }
      if ($this->getConfiguration('formatMillier', '0') == '1') {
        $replace['#formatMillier#'] = 1;
      }
      ///  POWER  \\\
      if ($this->getConfiguration('grid::power::cmd') != '' && $this->getConfiguration('grid::activate', 0) == 1) {
        $replace['#grid_power_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::power::cmd'));
        if ($this->getConfiguration('grid::power::max') != '') $replace['#gridMaxPower#'] = $this->getConfiguration('grid::power::max');
        if ($this->getConfiguration('grid::color') != '') $replace['#gridColor#'] = $this->getConfiguration('grid::color');
        ///  DAILY  \\\
        if ($this->getConfiguration('grid::daily::buy::cmd') != '' && $this->getConfiguration('grid::daily::buy::activate') == 1) {
          $replace['#grid_daily_buy_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::daily::buy::cmd'));
          $replace['#dailyGridBuyText#'] = ($this->getConfiguration('grid::daily::buy::txt') == '') ? 'ACHAT JOUR' : $this->getConfiguration('grid::daily::buy::txt');
        }
        if ($this->getConfiguration('grid::daily::sell::cmd') != '' && $this->getConfiguration('grid::daily::sell::activate') == 1) {
          $replace['#grid_daily_sell_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::daily::sell::cmd'));
          $replace['#dailyGridSellText#'] = ($this->getConfiguration('grid::daily::sell::txt') == '') ? 'VENTE JOUR' : $this->getConfiguration('grid::daily::sell::txt');
        }
        /// COLOR  \\\
        if ($this->getConfiguration('grid::color::sell') != '') $replace['#gridSellColor#'] = $this->getConfiguration('grid::color::sell');
        if ($this->getConfiguration('grid::color::buy') != '') $replace['#gridBuyColor#'] = $this->getConfiguration('grid::color::buy');
      }
      /// STATUS  \\\
      if ($this->getConfiguration('grid::status::cmd') != '' && $this->getConfiguration('grid::status::activate', 0) == 1) {
        $replace['#grid_status_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::status::cmd'));
        $replace['#noGridColor#'] = $this->getConfiguration('grid::color::nogrid');
        //$replace['#noGridColor#'] = $this->getConfiguration('grid::color::nogrid');
      }
      /////////////////////////////////////
      ////////////// SOLAR ////////////////
      /////////////////////////////////////
      $has_solar = false;
      if ($this->getConfiguration('solar::power::cmd') != '' && $this->getConfiguration('solar::power::activate') == 1) {
        $replace['#solar_power_cmd#'] = str_replace('#', '', $this->getConfiguration('solar::power::cmd'));
        $has_solar = true;
      }
      ///  Pvs  \\\
      $result_pv = array();
      if ($this->getConfiguration('pv','') != '' && count($this->getConfiguration('pv')) > 0) {
        $i = 1;
        $power = array();
        $voltage = array();
        foreach ($this->getConfiguration('pv') as $pv) {
          if ($pv['power::activate'] == 1 && $pv['power::cmd'] != '') {
            //log::add('powerFlow', 'debug', json_encode($pv));
            $result_pv[$i] = array('power::cmd' => str_replace('#', '', $pv['power::cmd']));
            if ($pv['voltage::cmd'] != '') $result_pv[$i] = $result_pv[$i] + array('voltage::cmd' => str_replace('#', '', $pv['voltage::cmd']));
            if ($pv['current::cmd'] != '') $result_pv[$i] = $result_pv[$i] + array('current::cmd' => str_replace('#', '', $pv['current::cmd']));
            if ($pv['energy::cmd'] != '') $result_pv[$i] = $result_pv[$i] + array('energy::cmd' => str_replace('#', '', $pv['energy::cmd']));
            $result_pv[$i] = $result_pv[$i] + array('max_power' => ($pv['maxPower'] == '') ? false : $pv['maxPower']);
            $result_pv[$i] = $result_pv[$i] + array('name' => ($pv['name'] == '') ? false : $pv['name']);
            $has_solar = true;
            $i++;
          }
        }
      }
      log::add('powerFlow', 'debug', 'PVs -> ' . json_encode($result_pv));
      $replace['#pvarray#'] = json_encode($result_pv);
      ///  OTHER  \\\
      if ($has_solar) {
        if ($this->getConfiguration('solar::color') != '') $replace['#solarColor#'] = $this->getConfiguration('solar::color');
        if ($this->getConfiguration('solar::color::0') != '') $replace['#pvState0Color#'] = $this->getConfiguration('solar::color::0');
        if ($this->getConfiguration('solar::daily::cmd') != '' && $this->getConfiguration('solar::daily::activate') == 1) {
          $replace['#solar_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('solar::daily::cmd'));
          $replace['#dailySolarText#'] = ($this->getConfiguration('solar::daily::txt') == '') ? 'PROD JOUR' : $this->getConfiguration('solar::daily::txt');
        }
        if ($this->getConfiguration('solar::power::max') != '') $replace['#pvMaxPower#'] = $this->getConfiguration('solar::power::max');
      }
      /////////////////////////////////////
      //////////// BATTERY ////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('battery::power::cmd') != '' && $this->getConfiguration('battery::activate', 0) == 1) {
        /// POWER  \\\
        $replace['#battery_power_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::power::cmd'));
        ///  COLOR  \\\
        $replace['#batteryColor#'] = $this->getConfiguration('battery::color');
        ///  MAX  \\\
        if ($this->getConfiguration('battery::power::max') != '') $replace['#batteryMaxPower#'] = $this->getConfiguration('battery::power::max');
        ///  STATE  \\\
        if ($this->getConfiguration('battery::soc::cmd') != '' && $this->getConfiguration('battery::soc::activate', 0) == 1) {
          $replace['#battery_soc_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::soc::cmd'));
          if ($this->getConfiguration('battery::power::capacity') != '') $replace['#batteryCapacity#'] = $this->getConfiguration('battery::power::capacity');
          if ($this->getConfiguration('battery::soc::shutdown') != '') $replace['#batterySocShutdown#'] = $this->getConfiguration('battery::soc::shutdown');
          if ($this->getConfiguration('battery::color::state::0') != '') $replace['#batteryState0Color#'] = $this->getConfiguration('battery::color::state::0');
          if ($this->getConfiguration('battery::color::state::25') != '') $replace['#batteryState25Color#'] = $this->getConfiguration('battery::color::state::25');
          if ($this->getConfiguration('battery::color::state::50') != '') $replace['#batteryState50Color#'] = $this->getConfiguration('battery::color::state::50');
          if ($this->getConfiguration('battery::color::state::75') != '') $replace['#batteryState75Color#'] = $this->getConfiguration('battery::color::state::75');
          if ($this->getConfiguration('battery::color::state::100') != '') $replace['#batteryState100Color#'] = $this->getConfiguration('battery::color::state::100');
          if ($this->getConfiguration('battery::img') != '') $replace['#batteryIcon#'] = $this->getConfiguration('battery::img');
        }
        ///  VOLTAGE  \\\
        if ($this->getConfiguration('battery::voltage::cmd') != '' && $this->getConfiguration('battery::voltage::activate', 0) == 1) {
          $replace['#battery_voltage_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::voltage::cmd'));
        }
        ///  CURRENT  \\\
        if ($this->getConfiguration('battery::current::cmd') != '' && $this->getConfiguration('battery::current::activate', 0) == 1) {
          $replace['#battery_current_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::current::cmd'));
        }
        ///  CURRENT  \\\
        if ($this->getConfiguration('battery::temp::cmd') != '' && $this->getConfiguration('battery::temp::activate', 0) == 1) {
          $replace['#battery_temp_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::temp::cmd'));
        }
        ///  DAILY  \\\
        if ($this->getConfiguration('battery::daily::charge::cmd') != '' && $this->getConfiguration('battery::daily::charge::activate', 0) == 1) {
          $replace['#battery_daily_charge_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::daily::charge::cmd'));
          if ($this->getConfiguration('battery::daily::charge::txt') != '') $replace['#dailyBatteryChargeText#'] = $this->getConfiguration('battery::daily::charge::txt');
          if ($this->getConfiguration('battery::color::charge') != '') $replace['#batteryChargeColor#'] = $this->getConfiguration('battery::color::charge');
        }
        if ($this->getConfiguration('battery::daily::discharge::cmd') != '' && $this->getConfiguration('battery::daily::discharge::activate', 0) == 1) {
          $replace['#battery_daily_discharge_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::daily::discharge::cmd'));
          if ($this->getConfiguration('battery::daily::discharge::txt') != '') $replace['#dailyBatteryDischargeText#'] = $this->getConfiguration('battery::daily::discharge::txt');
          if ($this->getConfiguration('battery::color::discharge') != '') $replace['#batteryDischargeColor#'] = $this->getConfiguration('battery::color::discharge');
        }
        ///  MPPT  \\\
        if ($this->getConfiguration('battery::mppt::power::activate', '0') == '1') {
          if ($this->getConfiguration('battery::mppt::power::cmd') != '') {
            if ($this->getConfiguration('battery::mppt::color') != '') $replace['#batteryMpptColor#'] = $this->getConfiguration('battery::mppt::color');
            $replace['#battery_mppt_power_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::mppt::power::cmd'));
            $replace['#mpptName#'] = $this->getConfiguration('battery::mppt::name', '');
            if ($this->getConfiguration('battery::mppt::energy::activate', '0') == '1') {
              if ($this->getConfiguration('battery::mppt::energy::cmd') != '') {
                $replace['#battery_mppt_energy_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::mppt::energy::cmd'));
              }
            }
          }
        }
      }
      /////////////////////////////////////
      /////////////// LOAD ////////////////
      /////////////////////////////////////
      $has_load = false;
      if ($this->getConfiguration('load::power::cmd') != '' && $this->getConfiguration('load::power::activate') == 1) {
        $replace['#load_power_cmd#'] = str_replace('#', '', $this->getConfiguration('load::power::cmd'));
        $has_load = true;
      }
      /// LOADs  \\\
      $result_load = array();
      if ($this->getConfiguration('load','') != '' && count($this->getConfiguration('load')) > 0) {
        $i = 1;
        $power = array();
        $voltage = array();
        foreach ($this->getConfiguration('load') as $load) {
          if ($load['power::activate'] == 1 && $load['power::cmd'] != '') {
            //log::add('powerFlow', 'debug', json_encode($load));
            $result_load[$i] = array('power::cmd' => str_replace('#', '', $load['power::cmd']));
            //if ($load['voltage::cmd'] != '') $result_load[$i] = $result_load[$i] + array('voltage::cmd' => str_replace('#', '', $load['voltage::cmd']));
            if ($load['perso::cmd'] != '') $result_load[$i] = $result_load[$i] + array('perso::cmd' => str_replace('#', '', $load['perso::cmd']));
            if ($load['energy::cmd'] != '') $result_load[$i] = $result_load[$i] + array('energy::cmd' => str_replace('#', '', $load['energy::cmd']));
            $result_load[$i] = $result_load[$i] + array('max_power' => ($load['maxPower'] == '') ? false : $load['maxPower']);
            $result_load[$i] = $result_load[$i] + array('name' => ($load['name'] == '') ? false : $load['name']);
            $icon = '';
            if ($load['img::1'] != '') $icon .= $load['img::1'];
            if ($load['img::2'] != '') $icon = ($icon == '') ? $load['img::2'] : $icon . ',' . $load['img::2'];
            if ($icon == '') $icon = false;
            $result_load[$i] = $result_load[$i] + array('icon' => $icon);
            $has_load = true;
            $i++;
          }
        }
      }
      log::add('powerFlow', 'debug', 'LOADs -> ' . json_encode($result_load));
      $replace['#loadarray#'] = json_encode($result_load);
      
      if ($has_load) {
        if ($this->getConfiguration('load::color') != '') $replace['#loadColor#'] = $this->getConfiguration('load::color');
        if ($this->getConfiguration('load::daily::cmd') != '' && $this->getConfiguration('load::daily::activate') == 1) {
          $replace['#load_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('load::daily::cmd'));
          $replace['#dailyLoadText#'] = ($this->getConfiguration('load::daily::txt') == '') ? 'CONSO JOUR' : $this->getConfiguration('load::daily::txt');
        }
        if ($this->getConfiguration('load::power::max') != '') $replace['#loadMaxPower#'] = $this->getConfiguration('load::power::max');
        //$replace['#loadarray#'] = [];
      }
      /////////////////////////////////////
      ///////////// INVERTER //////////////
      /////////////////////////////////////
      if ($this->getConfiguration('inverter::voltage::cmd') != '') $replace['#inverter_voltage_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::voltage::cmd'));
      if ($this->getConfiguration('inverter::current::cmd') != '') $replace['#inverter_current_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::current::cmd'));
      if ($this->getConfiguration('inverter::frequency::cmd') != '') $replace['#inverter_frequency_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::frequency::cmd'));
      if ($this->getConfiguration('inverter::lcd::cmd') != '') $replace['#inverter_lcd_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::lcd::cmd'));
      
      return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'eqLogic', __CLASS__)));
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