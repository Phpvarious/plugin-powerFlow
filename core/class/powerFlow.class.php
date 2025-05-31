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
      
      $replace['#version_widget#'] = 'Inconnu';
      $logicalId = "powerFlow";
      $update = update::byLogicalId($logicalId);
      if (is_object($update)) {
        $replace['#version_widget#'] = $update->getLocalVersion();
      }
      /////////////////////////////////////
      /////// Specific settings ///////////
      /////////////////////////////////////
      if ($this->getConfiguration('blink', '0') == '1') {
        $replace['#blink#'] = 1;
      }
      if ($this->getConfiguration('disableGauge', '0') == '1') {
        $replace['#activateGauge#'] = 0;
      }
      if ($this->getConfiguration('disableGaugeRatio', 0) == 1) {
        $replace['#activateGaugeRatio#'] = 0;
      }
      if ($this->getConfiguration('formatMillier', '0') == '1') {
        $replace['#formatMillier#'] = 1;
      }
      if ($this->getConfiguration('debug', '0') == '1') {
        $replace['#debug#'] = 1;
      }
      if ($this->getConfiguration('background::activate', 0) == 1) {
        if ($this->getConfiguration('background::color') != '') $replace['#Background#'] = $this->getConfiguration('background::color');
      }
      if ($this->getConfiguration('colorWarning') != '') {
        $replace['#colorDanger#'] = $this->getConfiguration('colorWarning');
      }
      /////////////////////////////////////
      ////////////// GRID /////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('grid::power::cmd') != '' && $this->getConfiguration('grid::desactivate', 1) == 0) {
        ///  POWER  \\\
        $replace['#grid_power_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::power::cmd'));
        ///  MAX POWER  \\\
        if ($this->getConfiguration('grid::power::max') != '') $replace['#gridMaxPower#'] = $this->getConfiguration('grid::power::max');
      }
      ///  DAILY BUY \\\
      if ($this->getConfiguration('grid::daily::buy::cmd') != '' && $this->getConfiguration('grid::daily::buy::desactivate', 1) == 0) {
        $replace['#grid_daily_buy_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::daily::buy::cmd'));
        $replace['#dailyGridBuyText#'] = ($this->getConfiguration('grid::daily::buy::txt') == '') ? __('ACHAT JOUR', __FILE__) : $this->getConfiguration('grid::daily::buy::txt');
        if ($this->getConfiguration('grid::color::buy') != '') $replace['#gridBuyColor#'] = $this->getConfiguration('grid::color::buy');
      }
      ///  DAILY SELL \\\
      if ($this->getConfiguration('grid::daily::sell::cmd') != '' && $this->getConfiguration('grid::daily::sell::desactivate', 1) == 0) {
        $replace['#grid_daily_sell_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::daily::sell::cmd'));
        $replace['#dailyGridSellText#'] = ($this->getConfiguration('grid::daily::sell::txt') == '') ? __('VENTE JOUR', __FILE__) : $this->getConfiguration('grid::daily::sell::txt');
        if ($this->getConfiguration('grid::color::sell') != '') $replace['#gridSellColor#'] = $this->getConfiguration('grid::color::sell');
      }
      /// STATUS  \\\
      if ($this->getConfiguration('grid::status::cmd') != '' && $this->getConfiguration('grid::status::desactivate', 1) == 0) {
        $replace['#grid_status_cmd#'] = str_replace('#', '', $this->getConfiguration('grid::status::cmd'));
        $replace['#noGridColor#'] = $this->getConfiguration('grid::color::nogrid');
      }
       /// COLOR  \\\
      if ($this->getConfiguration('grid::color') != '') $replace['#gridColor#'] = $this->getConfiguration('grid::color');
      /////////////////////////////////////
      ////////////// SOLAR ////////////////
      /////////////////////////////////////
      $has_solar = false;
      ///  SOLAR POWER  \\\
      if ($this->getConfiguration('solar::power::cmd') != '' && $this->getConfiguration('solar::power::desactivate', 1) == 0) {
        $replace['#solar_power_cmd#'] = str_replace('#', '', $this->getConfiguration('solar::power::cmd'));
        $has_solar = true;
      }
      ///  SOLAR Pvs  \\\
      $result_pv = array();
      if ($this->getConfiguration('pv','') != '' && count($this->getConfiguration('pv')) > 0) {
        $i = 1;
        $power = array();
        $voltage = array();
        foreach ($this->getConfiguration('pv') as $pv) {
          if ($pv['power::desactivate'] == 0 && $pv['power::cmd'] != '') {
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
      $replace['#pvarray#'] = json_encode($result_pv);
      /// SOLAR MAX  \\\
      if ($has_solar && $this->getConfiguration('solar::power::max') != '') $replace['#pvMaxPower#'] = $this->getConfiguration('solar::power::max');
      ///  SOLAR DAILY  \\\
      if ($this->getConfiguration('solar::daily::cmd') != '' && $this->getConfiguration('solar::daily::desactivate', 0) == 0) {
        $replace['#solar_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('solar::daily::cmd'));
        $replace['#dailySolarText#'] = ($this->getConfiguration('solar::daily::txt') == '') ? __('PROD JOUR', __FILE__) : $this->getConfiguration('solar::daily::txt');
      }
      ///  SOLAR COLOR  \\\
      if ($this->getConfiguration('solar::color') != '') $replace['#solarColor#'] = $this->getConfiguration('solar::color');
      if ($this->getConfiguration('solar::color::hide', 0) == 1) {
        $replace['#pvState0Color#'] = '#ffffff00';
      } else if ($this->getConfiguration('solar::color::0') != '') {
        $replace['#pvState0Color#'] = $this->getConfiguration('solar::color::0');
      }
      /////////////////////////////////////
      //////////// BATTERY ////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('battery::power::cmd') != '' && $this->getConfiguration('battery::desactivate', 1) == 0) {
        /// POWER  \\\
        $replace['#battery_power_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::power::cmd'));
        ///  MAX  \\\
        if ($this->getConfiguration('battery::power::max') != '') $replace['#batteryMaxPower#'] = $this->getConfiguration('battery::power::max');
        ///  MPPT  \\\
        if ($this->getConfiguration('battery::mppt::power::cmd') != '' && $this->getConfiguration('battery::mppt::desactivate', 1) == 0) {
          if ($this->getConfiguration('battery::mppt::color') != '') $replace['#batteryMpptColor#'] = $this->getConfiguration('battery::mppt::color');
          $replace['#battery_mppt_power_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::mppt::power::cmd'));
          $replace['#mpptName#'] = $this->getConfiguration('battery::mppt::name', '');
          if ($this->getConfiguration('battery::mppt::energy::cmd') != '') {
            $replace['#battery_mppt_energy_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::mppt::energy::cmd'));
          }
        }
      }
      ///  ICON  \\\
      if ($this->getConfiguration('battery::img') != '') $replace['#batteryIcon#'] = $this->getConfiguration('battery::img');
      ///  COLOR  \\\
      $replace['#batteryColor#'] = $this->getConfiguration('battery::color');
      ///  STATE  \\\
      if ($this->getConfiguration('battery::soc::cmd') != '' && $this->getConfiguration('battery::soc::desactivate', 1) == 0) {
        $replace['#battery_soc_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::soc::cmd'));
        if ($this->getConfiguration('battery::power::capacity') != '') $replace['#batteryCapacity#'] = $this->getConfiguration('battery::power::capacity');
        if ($this->getConfiguration('battery::soc::shutdown') != '') $replace['#batterySocShutdown#'] = $this->getConfiguration('battery::soc::shutdown');
        if ($this->getConfiguration('battery::color::state::0') != '') $replace['#batteryState0Color#'] = $this->getConfiguration('battery::color::state::0');
        if ($this->getConfiguration('battery::color::state::25') != '') $replace['#batteryState25Color#'] = $this->getConfiguration('battery::color::state::25');
        if ($this->getConfiguration('battery::color::state::50') != '') $replace['#batteryState50Color#'] = $this->getConfiguration('battery::color::state::50');
        if ($this->getConfiguration('battery::color::state::75') != '') $replace['#batteryState75Color#'] = $this->getConfiguration('battery::color::state::75');
        if ($this->getConfiguration('battery::color::state::100') != '') $replace['#batteryState100Color#'] = $this->getConfiguration('battery::color::state::100');
        //if ($this->getConfiguration('battery::img') != '') $replace['#batteryIcon#'] = $this->getConfiguration('battery::img');
        if ($this->getConfiguration('battery::color::state::desactivate', 0) == 1) $replace['#autoColorBattery#'] = 0;
      }
      ///  VOLTAGE  \\\
      if ($this->getConfiguration('battery::voltage::cmd') != '' && $this->getConfiguration('battery::voltage::desactivate', 1) == 0) {
        $replace['#battery_voltage_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::voltage::cmd'));
      }
      ///  CURRENT  \\\
      if ($this->getConfiguration('battery::current::cmd') != '' && $this->getConfiguration('battery::current::desactivate', 1) == 0) {
        $replace['#battery_current_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::current::cmd'));
      }
      ///  TEMP  \\\
      if ($this->getConfiguration('battery::temp::cmd') != '' && $this->getConfiguration('battery::temp::desactivate', 1) == 0) {
        $replace['#battery_temp_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::temp::cmd'));
      }
      ///  DAILY  \\\
      if ($this->getConfiguration('battery::daily::charge::cmd') != '' && $this->getConfiguration('battery::daily::charge::desactivate', 1) == 0) {
        $replace['#battery_daily_charge_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::daily::charge::cmd'));
        $replace['#dailyBatteryChargeText#'] = ($this->getConfiguration('battery::daily::charge::txt') == '') ? __('CHARGE JOUR', __FILE__) : $this->getConfiguration('battery::daily::charge::txt');
        //if ($this->getConfiguration('battery::daily::charge::txt') != '') $replace['#dailyBatteryChargeText#'] = $this->getConfiguration('battery::daily::charge::txt');
        if ($this->getConfiguration('battery::color::charge') != '') $replace['#batteryChargeColor#'] = $this->getConfiguration('battery::color::charge');
      }
      if ($this->getConfiguration('battery::daily::discharge::cmd') != '' && $this->getConfiguration('battery::daily::discharge::desactivate', 1) == 0) {
        $replace['#battery_daily_discharge_cmd#'] = str_replace('#', '', $this->getConfiguration('battery::daily::discharge::cmd'));
        $replace['#dailyBatteryDischargeText#'] = ($this->getConfiguration('battery::daily::discharge::txt') == '') ? __('DECHARGE JOUR', __FILE__) : $this->getConfiguration('battery::daily::discharge::txt');
        //if ($this->getConfiguration('battery::daily::discharge::txt') != '') $replace['#dailyBatteryDischargeText#'] = $this->getConfiguration('battery::daily::discharge::txt');
        if ($this->getConfiguration('battery::color::discharge') != '') $replace['#batteryDischargeColor#'] = $this->getConfiguration('battery::color::discharge');
      }
      /////////////////////////////////////
      /////////////// LOAD ////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('load::power::cmd') != '' && $this->getConfiguration('load::power::desactivate', 1) == 0) {
        $replace['#load_power_cmd#'] = str_replace('#', '', $this->getConfiguration('load::power::cmd'));
      }
      ///  COLOR  \\\
      if ($this->getConfiguration('load::color') != '') $replace['#loadColor#'] = $this->getConfiguration('load::color');
      ///  MAX  \\\
      if ($this->getConfiguration('load::power::max') != '') $replace['#loadMaxPower#'] = $this->getConfiguration('load::power::max');
      /// LOADs  \\\
      $result_load = array();
      if ($this->getConfiguration('load','') != '' && count($this->getConfiguration('load')) > 0) {
        $i = 1;
        $power = array();
        $voltage = array();
        foreach ($this->getConfiguration('load') as $load) {
          if ($load['power::desactivate'] == 0 && $load['power::cmd'] != '') {
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
            $i++;
          }
        }
      }
      $replace['#loadarray#'] = json_encode($result_load);
      ///  ANIMATE  \\\
      if ($this->getConfiguration('load::animate::disable', 0) == 1) $replace['#loadAnimate#'] = 0;
      ///  Force4Load  \\\
      if ($this->getConfiguration('load::force4load', 0) == 1) $replace['#force4Load#'] = 1;
      ///  DAILY  \\\
      if ($this->getConfiguration('load::daily::cmd') != '' && $this->getConfiguration('load::daily::desactivate', 1) == 0) {
        $replace['#load_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('load::daily::cmd'));
        $replace['#dailyLoadText#'] = ($this->getConfiguration('load::daily::txt') == '') ? __('CONSO JOUR', __FILE__) : $this->getConfiguration('load::daily::txt');
      }
      /////////////////////////////////////
      ///////////// INVERTER //////////////
      /////////////////////////////////////
      if ($this->getConfiguration('inverter::voltage::cmd') != '' && $this->getConfiguration('inverter::voltage::desactivate') == 0)
        $replace['#inverter_voltage_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::voltage::cmd'));
      if ($this->getConfiguration('inverter::current::cmd') != '' && $this->getConfiguration('inverter::current::desactivate') == 0)
        $replace['#inverter_current_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::current::cmd'));
      if ($this->getConfiguration('inverter::frequency::cmd') != '' && $this->getConfiguration('inverter::frenquency::desactivate') == 0)
        $replace['#inverter_frequency_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::frequency::cmd'));
      if ($this->getConfiguration('inverter::lcd::cmd') != '' && $this->getConfiguration('inverter::lcd::desactivate') == 0)
        $replace['#inverter_lcd_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::lcd::cmd'));
      if ($this->getConfiguration('inverter::temp::ac::cmd') != '' && $this->getConfiguration('inverter::temp::ac::desactivate') == 0)
        $replace['#inverter_temp_ac_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::temp::ac::cmd'));
      if ($this->getConfiguration('inverter::temp::dc::cmd') != '' && $this->getConfiguration('inverter::temp::dc::desactivate') == 0)
        $replace['#inverter_temp_dc_cmd#'] = str_replace('#', '', $this->getConfiguration('inverter::temp::dc::cmd'));
      
      if ($this->getConfiguration('inverter::img::none', 0) == 1) {
        $replace['#inverterModel#'] = 'none';
      } else if ($this->getConfiguration('inverter::img') != '') {
        $replace['#inverterModel#'] = $this->getConfiguration('inverter::img');
      }
      $replace['#inverterColor#'] = $this->getConfiguration('inverter::color', '#808080');
      $replace['#inverterColorTextIn#'] = $this->getConfiguration('inverter::color::in', '#000000');
      /////////////////////////////////////
      //////////////// AUX ////////////////
      /////////////////////////////////////
      if ($this->getConfiguration('aux::power::cmd') != '' && $this->getConfiguration('aux::desactivate', 1) == 0) {
        $replace['#aux_power_cmd#'] = str_replace('#', '', $this->getConfiguration('aux::power::cmd'));
        if ($this->getConfiguration('aux::color') != '') $replace['#auxColor#'] = $this->getConfiguration('aux::color');
        if ($this->getConfiguration('aux::power::max') != '') $replace['#auxMaxPower#'] = $this->getConfiguration('aux::power::max');
        if ($this->getConfiguration('aux::daily::cmd') != '' && $this->getConfiguration('aux::daily::desactivate', 1) == 0) {
          $replace['#aux_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('aux::daily::cmd'));
          //$replace['#dailyAuxText#'] = ($this->getConfiguration('aux::daily::txt') == '') ? __('PROD AUX', __FILE__) : $this->getConfiguration('aux::daily::txt');
          $replace['#dailyAuxText#'] = $this->getConfiguration('aux::daily::txt');
        }
      }
      /////////////////////////////////////
      ////////////// PERSO ////////////////
      /////////////////////////////////////
      $result_perso = array();
      if ($this->getConfiguration('perso','') != '' && count($this->getConfiguration('perso')) > 0) {
        $i = 1;
        foreach ($this->getConfiguration('perso') as $perso) {
          if ($perso['perso::desactivate'] == 0 && $perso['perso::cmd'] != '' && $perso['perso::x'] != '' && $perso['perso::y'] != '') {
            $string = '';
            $sep = ',';
            $result_perso[$i] = array('perso::cmd' => str_replace('#', '', $perso['perso::cmd']));
            $string = $perso['perso::x'] . $sep . $perso['perso::y'] . $sep;
            $string .= ($perso['perso::size'] != '') ? $perso['perso::size'] : 16;
            $string .= $sep . $perso['perso::color'];
            if ($perso['perso::text'] != '') {
              $string .= $sep . $perso['perso::text'] . $sep;
              $string .= ($perso['perso::text::size'] != '') ? $perso['perso::text::size'] : 16;
            }
            $result_perso[$i] = $result_perso[$i] + array('params' => $string);
            $i++;
          }
        }
      }
      log::add('powerFlow', 'debug', '$string -> ' . json_encode($result_perso));
      $replace['#persoarray#'] = json_encode($result_perso);
      ////////////////////////
      
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