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
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

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
	public static function deadCmd() {
		$return = array();
		$_search = array('grid::power::cmd', 'grid::daily::buy::cmd', 'grid::daily::sell::cmd', 'grid::status::cmd', 'solar::power::cmd', 'solar::daily::cmd', 'battery::power::cmd', 'battery::mppt::power::cmd', 'battery::mppt::energy::cmd', 'battery::soc::cmd', 'battery::voltage::cmd', 'battery::current::cmd', 'battery::temp::cmd', 'battery::daily::charge::cmd', 'battery::daily::discharge::cmd', 'load::power::cmd', 'load::daily::cmd', 'inverter::voltage::cmd', 'inverter::current::cmd', 'inverter::frequency::cmd', 'inverter::lcd::cmd', 'inverter::temp::ac::cmd', 'inverter::temp::dc::cmd', 'aux::power::cmd', 'aux::daily::cmd');
		$_searchOther = array('load' => array('power::cmd', 'energy::cmd', 'perso::cmd'), 'perso' => array('perso::cmd'), 'pv' => array('power::cmd', 'current::cmd', 'energy::cmd'));
		$_tab = array('grid' => '#configureGrid', 'solar' => '#configureSolar', 'pv' => '#configureSolar', 'battery' => '#configureBattery', 'load' => '#configureLoad', 'inverter' => '#configureAux', 'perso' => '#configurePerso');
		foreach (eqLogic::byType('powerFlow') as $eqLogic) {
			////////  GENERAL  \\\\\\\\
			foreach ($_search as $key) {
				if (preg_match("/^\#(\d+)\#$/", $eqLogic->getConfiguration($key, ''), $matche)) {
					if (!cmd::byId($matche[1])) {
						$category = explode("::", $key);
						$tab = array_key_exists($category[0], $_tab) ? $_tab[$category[0]] : '';
						$return[] = array('detail' => '<a href="/index.php?v=d&m=' . $eqLogic->getEqType_name() . '&p=' . $eqLogic->getEqType_name() . '&id=' . $eqLogic->getId() . $tab .'">' . $eqLogic->getHumanName() . '</a> ' . __('dans la catégorie', __FILE__) . ' ' . $category[0], 'help' => $key, 'who' => '#' . $matche[1] . '#');
					}
				}
			}
			////////  OTHERS  \\\\\\\\
			foreach ($_searchOther as $category => $value) {
				$tab = array_key_exists($category, $_tab) ? $_tab[$category] : '';
				$i = 1;
				foreach ($eqLogic->getConfiguration($category, []) as $cmd) {
					if (is_array($value)) {
						foreach ($value as $key) {
							if (isset($cmd[$key])) {
								if (preg_match("/^\#(\d+)\#$/", $cmd[$key], $matche)) {
									if (!cmd::byId($matche[1])) {
										$return[] = array('detail' => '<a href="/index.php?v=d&m=' . $eqLogic->getEqType_name() . '&p=' . $eqLogic->getEqType_name() . '&id=' . $eqLogic->getId() . $tab .'">' . $eqLogic->getHumanName() . '</a> ' . __('dans la catégorie', __FILE__) . ' ' . $category . ' N°' . $i, 'help' => $key, 'who' => '#' . $matche[1] . '#');
									}
								}
							}
						}
					}
					$i++;
				}
			}
		}
		return $return;
	}
	/*     * *********************Méthodes d'instance************************* */

	/**
	 * Call by core before save into bdd
	 
	public function preSave()
	{
		log::add(__CLASS__, 'debug', '┌──:fg-success: preSave() :/fg:──');
		log::add(__CLASS__, 'debug', '└────────────────────');
	}*/

	/**
	 * Call by core after save into bdd
	 
	public function postSave()
	{
		log::add(__CLASS__, 'debug', '┌──:fg-success: postSave() :/fg:──');
		log::add(__CLASS__, 'debug', '└────────────────────');
	}*/
  
	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		log::add(__CLASS__, 'debug', '┌──:fg-success: toHtml :/fg:──');
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
		if ($this->getConfiguration('autoConversionUnit', 0) == 1) {
			$replace['#autoConversionUnit#'] = 0;
		}
		if ($this->getConfiguration('trunc', '') != '') {
			$replace['#truncValue#'] = $this->getConfiguration('trunc');
		}
		/////////////////////////////////////
		////////////// GRID /////////////////
		/////////////////////////////////////
		///  POWER  \\\
		if ($this->getConfiguration('grid::desactivate', 1) == 0) {
			if ($this->getConfiguration('grid::power::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('grid::power::cmd', ''), $id)) {
					$replace['#grid_power_cmd#'] = $id[1];
					///  MAX POWER  \\\
					if ($this->getConfiguration('grid::power::max') != '') {
						if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('grid::power::max', ''), $dataStore)) {
							$result = jeedom::evaluateExpression($dataStore[1]);
							if (is_numeric($result)) {
								$replace['#gridMaxPower#'] = $result;
							} else log::add(__CLASS__, 'debug', '| KO  grid::power::max [' . $dataStore[1] . '] not numeric !');
						} else if (is_numeric($this->getConfiguration('grid::power::max'))) {
							$replace['#gridMaxPower#'] = $this->getConfiguration('grid::power::max');
						} else log::add(__CLASS__, 'debug', '| KO  grid::power::max not numeric !');
					}
					if ($this->getConfiguration('grid::invert', 0) == 1) $replace['#PowerInvert#'] = '1';
				} else log::add(__CLASS__, 'debug', '| KO  grid::power::cmd not command valid !');
			}
		}
		///  DAILY BUY \\\
		if ($this->getConfiguration('grid::daily::buy::desactivate', 1) == 0) {
			if ($this->getConfiguration('grid::daily::buy::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('grid::daily::buy::cmd', ''), $id)) {
					$replace['#grid_daily_buy_cmd#'] = $id[1];
					$replace['#dailyGridBuyText#'] = $this->getConfiguration('grid::daily::buy::txt', '');
					if ($this->getConfiguration('grid::color::buy') != '') $replace['#gridBuyColor#'] = $this->getConfiguration('grid::color::buy');
				} else log::add(__CLASS__, 'debug', '| KO  grid::daily::buy::cmd not command valid !');
			}
		}
		///  DAILY SELL \\\
		if ($this->getConfiguration('grid::daily::sell::desactivate', 1) == 0) {
			if ($this->getConfiguration('grid::daily::sell::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('grid::daily::sell::cmd', ''), $id)) {
					$replace['#grid_daily_sell_cmd#'] = $id[1];
					$replace['#dailyGridSellText#'] = $this->getConfiguration('grid::daily::sell::txt', '');
					if ($this->getConfiguration('grid::color::sell') != '') $replace['#gridSellColor#'] = $this->getConfiguration('grid::color::sell');
				} else log::add(__CLASS__, 'debug', '| KO  grid::daily::sell::cmd not command valid !');
			}
		}
		///  STATUS  \\\
		if ($this->getConfiguration('grid::status::desactivate', 1) == 0) {
			if ($this->getConfiguration('grid::status::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('grid::status::cmd', ''), $id)) {
					$replace['#grid_status_cmd#'] = $id[1];
					if ($this->getConfiguration('grid::color::nogrid') != '') $replace['#noGridColor#'] = $this->getConfiguration('grid::color::nogrid');
				} else log::add(__CLASS__, 'debug', '| KO  grid::status::cmd not command valid !');
			}
		}
		///  COLOR  \\\
		if ($this->getConfiguration('grid::color') != '') $replace['#gridColor#'] = $this->getConfiguration('grid::color');
		/////////////////////////////////////
		////////////// SOLAR ////////////////
		/////////////////////////////////////
		$has_solar = false;
		///  SOLAR POWER  \\\
		if ($this->getConfiguration('solar::power::desactivate', 1) == 0) {
			if ($this->getConfiguration('solar::power::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('solar::power::cmd', ''), $id)) {
					$replace['#solar_power_cmd#'] = $id[1];
					$has_solar = true;
				} else log::add(__CLASS__, 'debug', '| KO  solar::power::cmd not command valid !');
			}
		}
		///  SOLAR Pvs  \\\
		$result_pv = array();
		if ($this->getConfiguration('pv','') != '' && count($this->getConfiguration('pv')) > 0) {
			$i = 1;
			$i2 = 1;
			foreach ($this->getConfiguration('pv') as $pv) {
				if ($pv['power::desactivate'] == 0) {
					if ($pv['power::cmd'] != '') {
						if (preg_match("/^\#(\d+)\#$/", $pv['power::cmd'], $id)) {
							$result_pv[$i] = array('power::cmd' => $id[1]);
							if ($pv['voltage::cmd'] != '') {
								if (preg_match("/^\#(\d+)\#$/", $pv['voltage::cmd'], $voltageId)) {
									$result_pv[$i] = $result_pv[$i] + array('voltage::cmd' => $voltageId[1]);
								} else log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - voltage::cmd not command valid !');
							}
							if ($pv['current::cmd'] != '') {
								if (preg_match("/^\#(\d+)\#$/", $pv['current::cmd'], $currentId)) {
									$result_pv[$i] = $result_pv[$i] + array('current::cmd' => $currentId[1]);
								} else log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - current::cmd not command valid !');
							}
							if ($pv['energy::cmd'] != '') {
								if (preg_match("/^\#(\d+)\#$/", $pv['energy::cmd'], $energyId)) {
									$result_pv[$i] = $result_pv[$i] + array('energy::cmd' => $energyId[1]);
								} else log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - energy::cmd not command valid !');
							}
							if (preg_match("/^\#(variable\(.*?\))\#$/", $pv['maxPower'], $dataStore)) {
								$result = jeedom::evaluateExpression($dataStore[1]);
								if (is_numeric($result)) {
									$result_pv[$i] = $result_pv[$i] + array('max_power' => $result);
								} else {
									log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - Max power [' . $dataStore[1] . '] is not numeric !');
									$result_pv[$i] = $result_pv[$i] + array('max_power' => false);
								}
							} else if (is_numeric($pv['maxPower'])) {
								$result_pv[$i] = $result_pv[$i] + array('max_power' => $pv['maxPower']);
							} else {
								$result_pv[$i] = $result_pv[$i] + array('max_power' => false);
								if ($pv['maxPower'] != '') log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - Max power is not numeric !');
							}
							$result_pv[$i] = $result_pv[$i] + array('name' => ($pv['name'] == '') ? false : $pv['name']);
							$has_solar = true;
							$i++;
						} else log::add(__CLASS__, 'debug', '| KO  Solar N° ' . $i2 . ' - power::cmd not command valid !');
					}
				}
				$i2++;
			}
		}
		$replace['#pvarray#'] = json_encode($result_pv);
		/// SOLAR MAX  \\\
		if ($has_solar) {
			if ($this->getConfiguration('solar::power::max') != '') {
				if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('solar::power::max', ''), $dataStore)) {
					$result = jeedom::evaluateExpression($dataStore[1]);
					if (is_numeric($result)) {
						$replace['#pvMaxPower#'] = $result;
					} else log::add(__CLASS__, 'debug', '| KO  solar::power::max [' . $dataStore[1] . '] not numeric !');
				} else if (is_numeric($this->getConfiguration('solar::power::max'))) {
					$replace['#pvMaxPower#'] = $this->getConfiguration('solar::power::max');
				} else log::add(__CLASS__, 'debug', '| KO  solar::power::max not numeric !');
			}
        }
		///  SOLAR DAILY  \\\
		if ($this->getConfiguration('solar::daily::desactivate', 0) == 0) {
			if ($this->getConfiguration('solar::daily::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('solar::daily::cmd', ''), $id)) {
					$replace['#solar_daily_cmd#'] = $id[1];
					$replace['#dailySolarText#'] = $this->getConfiguration('solar::daily::txt', '');
				} else log::add(__CLASS__, 'debug', '| KO  solar::daily::cmd not command valid !');
			}
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
		if ($this->getConfiguration('battery::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::power::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::power::cmd', ''), $id)) {
					/// POWER  \\\
					$replace['#battery_power_cmd#'] = $id[1];
					if ($this->getConfiguration('battery::power::invert', 0) == 1) $replace['#batteryPowerInvert#'] = '1';
					///  MAX  \\\
					if ($this->getConfiguration('battery::power::max') != '') {
						if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('battery::power::max', ''), $dataStore)) {
							$result = jeedom::evaluateExpression($dataStore[1]);
							if (is_numeric($result)) {
								$replace['#batteryMaxPower#'] = $result;
							} else log::add(__CLASS__, 'debug', '| KO  battery::power::max [' . $dataStore[1] . '] not numeric !');
						} else if (is_numeric($this->getConfiguration('battery::power::max'))) {
							$replace['#batteryMaxPower#'] = $this->getConfiguration('battery::power::max');
						} else log::add(__CLASS__, 'debug', '| KO  battery::power::max not numeric !');
					}
					///  MPPT  \\\
					if ($this->getConfiguration('battery::mppt::desactivate', 1) == 0) {
						if ($this->getConfiguration('battery::mppt::power::cmd') != '') {
							if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::mppt::power::cmd', ''), $mpptPowerId)) {
								$replace['#battery_mppt_power_cmd#'] = $mpptPowerId[1];
								if ($this->getConfiguration('battery::mppt::color') != '') $replace['#batteryMpptColor#'] = $this->getConfiguration('battery::mppt::color');
								$replace['#mpptName#'] = $this->getConfiguration('battery::mppt::name', '');
								if ($this->getConfiguration('battery::mppt::energy::cmd') != '') {
									if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::mppt::energy::cmd', ''), $mpptEnergyId)) {
										$replace['#battery_mppt_energy_cmd#'] = $mpptEnergyId[1];
									} else log::add(__CLASS__, 'debug', '| KO  battery::mppt::energy::cmd not command valid !');
								}
							} else log::add(__CLASS__, 'debug', '| KO  battery::mppt::power::cmd not command valid !');
						}
					}
				} else log::add(__CLASS__, 'debug', '| KO  battery::power::cmd not command valid !');
			}
		}
		///  ICON  \\\
		if ($this->getConfiguration('battery::img') != '') $replace['#batteryIcon#'] = $this->getConfiguration('battery::img');
		///  COLOR  \\\
		$replace['#batteryColor#'] = $this->getConfiguration('battery::color');
		///  SOC  \\\
		if ($this->getConfiguration('battery::soc::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::soc::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::soc::cmd', ''), $id)) {
					///  STATE  \\\
					$replace['#battery_soc_cmd#'] = $id[1];
					///  CAPACITY  \\\
					if ($this->getConfiguration('battery::power::capacity') != '') {
						if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('battery::power::capacity', ''), $dataStore)) {
							$result = jeedom::evaluateExpression($dataStore[1]);
							if (is_numeric($result)) {
								$replace['#batteryCapacity#'] = $result;
							} else log::add(__CLASS__, 'debug', '| KO  battery::power::capacity [' . $dataStore[1] . '] not numeric !');
						} else if (is_numeric($this->getConfiguration('battery::power::capacity'))) {
							$replace['#batteryCapacity#'] = $this->getConfiguration('battery::power::capacity');
						} else log::add(__CLASS__, 'debug', '| KO  battery::power::capacity not numeric !');
					}
					///  SOC MIN  \\\
					if ($this->getConfiguration('battery::soc::shutdown') != '') {
						if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('battery::soc::shutdown', ''), $dataStore)) {
							$result = jeedom::evaluateExpression($dataStore[1]);
							if (is_numeric($result)) {
								$replace['#batterySocShutdown#'] = min($result, 100);
							} else log::add(__CLASS__, 'debug', '| KO  battery::soc::shutdown [' . $dataStore[1] . '] not numeric !');
						} else if (is_numeric($this->getConfiguration('battery::soc::shutdown'))) {
							$replace['#batterySocShutdown#'] = min($this->getConfiguration('battery::soc::shutdown'), 100);
						} else log::add(__CLASS__, 'debug', '| KO  battery::soc::shutdown not numeric !');
					}
					///  COLORS  \\\
					if ($this->getConfiguration('battery::color::state::0') != '') $replace['#batteryState0Color#'] = $this->getConfiguration('battery::color::state::0');
					if ($this->getConfiguration('battery::color::state::25') != '') $replace['#batteryState25Color#'] = $this->getConfiguration('battery::color::state::25');
					if ($this->getConfiguration('battery::color::state::50') != '') $replace['#batteryState50Color#'] = $this->getConfiguration('battery::color::state::50');
					if ($this->getConfiguration('battery::color::state::75') != '') $replace['#batteryState75Color#'] = $this->getConfiguration('battery::color::state::75');
					if ($this->getConfiguration('battery::color::state::100') != '') $replace['#batteryState100Color#'] = $this->getConfiguration('battery::color::state::100');
					if ($this->getConfiguration('battery::color::state::desactivate', 0) == 1) $replace['#autoColorBattery#'] = 0;
				} else log::add(__CLASS__, 'debug', '| KO  battery::soc::cmd not command valid !');
			}
		}
		///  VOLTAGE  \\\
		if ($this->getConfiguration('battery::voltage::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::voltage::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::voltage::cmd', ''), $id)) {
					$replace['#battery_voltage_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  battery::voltage::cmd not command valid !');
			}
		}
		///  CURRENT  \\\
		if ($this->getConfiguration('battery::current::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::current::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::current::cmd', ''), $id)) {
					$replace['#battery_current_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  battery::current::cmd not command valid !');
			}
		}
		///  TEMP  \\\
		if ($this->getConfiguration('battery::temp::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::temp::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::temp::cmd', ''), $id)) {
					$replace['#battery_temp_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  battery::temp::cmd not command valid !');
			}
		}
		///  DAILY  \\\
		if ($this->getConfiguration('battery::daily::charge::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::daily::charge::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::daily::charge::cmd', ''), $id)) {
					$replace['#battery_daily_charge_cmd#'] = $id[1];
					$replace['#dailyBatteryChargeText#'] = $this->getConfiguration('battery::daily::charge::txt', '');
					if ($this->getConfiguration('battery::color::charge') != '') $replace['#batteryChargeColor#'] = $this->getConfiguration('battery::color::charge');
				} else log::add(__CLASS__, 'debug', '| KO  battery::daily::charge::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('battery::daily::discharge::desactivate', 1) == 0) {
			if ($this->getConfiguration('battery::daily::discharge::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('battery::daily::discharge::cmd', ''), $id)) {
					$replace['#battery_daily_discharge_cmd#'] = $id[1];
					$replace['#dailyBatteryDischargeText#'] = $this->getConfiguration('battery::daily::discharge::txt', '');
					if ($this->getConfiguration('battery::color::discharge') != '') $replace['#batteryDischargeColor#'] = $this->getConfiguration('battery::color::discharge');
				} else log::add(__CLASS__, 'debug', '| KO  battery::daily::discharge::cmd not command valid !');
			}
		}
		/////////////////////////////////////
		/////////////// LOAD ////////////////
		/////////////////////////////////////
		if ($this->getConfiguration('load::power::desactivate', 1) == 0) {
			if ($this->getConfiguration('load::power::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('load::power::cmd', ''), $id)) {
					$replace['#load_power_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  load::power::cmd not command valid !');
			}
		}
		///  COLOR  \\\
		if ($this->getConfiguration('load::color') != '') $replace['#loadColor#'] = $this->getConfiguration('load::color');
		///  MAX  \\\
		if ($this->getConfiguration('load::power::max') != '') {
			if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('load::power::max', ''), $dataStore)) {
				$result = jeedom::evaluateExpression($dataStore[1]);
				if (is_numeric($result)) {
					$replace['#loadMaxPower#'] = $result;
				} else log::add(__CLASS__, 'debug', '| KO  load::power::max [' . $dataStore[1] . '] not numeric !');
			} else if (is_numeric($this->getConfiguration('load::power::max'))) {
				$replace['#loadMaxPower#'] = $this->getConfiguration('load::power::max');
			} else log::add(__CLASS__, 'debug', '| KO  load::power::max not numeric !');
		}
		/// LOADs  \\\
		$result_load = array();
		if ($this->getConfiguration('load','') != '' && count($this->getConfiguration('load')) > 0) {
			$i = 1;
			$i2 = 1;
			$power = array();
			$voltage = array();
			foreach ($this->getConfiguration('load') as $load) {
				if ($load['power::desactivate'] == 0) {
					if ($load['power::cmd'] != '') {
						if (preg_match("/^\#(\d+)\#$/", $load['power::cmd'], $id)) {
							$result_load[$i] = array('power::cmd' => $id[1]);
							if ($load['perso::cmd'] != '') {
								if (preg_match("/^\#(\d+)\#$/", $load['perso::cmd'], $persoId)) {
									$result_load[$i] = $result_load[$i] + array('perso::cmd' => $persoId[1]);
								} else log::add(__CLASS__, 'debug', '| KO  Load N° ' . $i2 . ' - perso::cmd not command valid !');
							}
							if ($load['energy::cmd'] != '') {
								if (preg_match("/^\#(\d+)\#$/", $load['energy::cmd'], $energyId)) {
									$result_load[$i] = $result_load[$i] + array('energy::cmd' => $energyId[1]);
								} else log::add(__CLASS__, 'debug', '| KO  Load N° ' . $i2 . ' - energy::cmd not command valid !');
							}
							if (preg_match("/^\#(variable\(.*?\))\#$/", $load['maxPower'], $dataStore)) {
								$result = jeedom::evaluateExpression($dataStore[1]);
								if (is_numeric($result)) {
									$result_load[$i] = $result_load[$i] + array('max_power' => $result);
								} else {
									log::add(__CLASS__, 'debug', '| KO  Load N° ' . $i2 . ' - Max power [' . $dataStore[1] . '] is not numeric !');
									$result_load[$i] = $result_load[$i] + array('max_power' => false);
								}
							} else if (is_numeric($load['maxPower'])) {
								$result_load[$i] = $result_load[$i] + array('max_power' => $load['maxPower']);
							} else {
								$result_load[$i] = $result_load[$i] + array('max_power' => false);
								if ($load['maxPower'] != '') log::add(__CLASS__, 'debug', '| KO  Load N° ' . $i2 . ' - Max power is not numeric !');
							}
							$result_load[$i] = $result_load[$i] + array('name' => ($load['name'] == '') ? false : $load['name']);
							$icon = '';
							if ($load['img::1'] != '') $icon .= $load['img::1'];
							if ($load['img::2'] != '') $icon = ($icon == '') ? $load['img::2'] : $icon . ',' . $load['img::2'];
							if ($icon == '') $icon = false;
							$result_load[$i] = $result_load[$i] + array('icon' => $icon);
							$i++;
						} else log::add(__CLASS__, 'debug', '| KO  Load N° ' . $i2 . ' - power::cmd not command valid !');
					}
				}
				$i2++;
			}
		}
		$replace['#loadarray#'] = json_encode($result_load);
		///  ANIMATE  \\\
		if ($this->getConfiguration('load::animate::disable', 0) == 1) $replace['#loadAnimate#'] = 0;
		///  Force4Load  \\\
		if ($this->getConfiguration('load::force4load', 0) == 1) $replace['#force4Load#'] = 1;
		///  DAILY  \\\
		if ($this->getConfiguration('load::daily::desactivate', 1) == 0) {
			if ($this->getConfiguration('load::daily::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('load::daily::cmd', ''), $id)) {
					$replace['#load_daily_cmd#'] = $id[1];
					$replace['#dailyLoadText#'] = $this->getConfiguration('load::daily::txt', '');
				} else log::add(__CLASS__, 'debug', '| KO  load::daily::cmd not command valid !');
			}
		}
		/////////////////////////////////////
		///////////// INVERTER //////////////
		/////////////////////////////////////
		if ($this->getConfiguration('inverter::voltage::desactivate') == 0) {
			if ($this->getConfiguration('inverter::voltage::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::voltage::cmd', ''), $id)) {
				$replace['#inverter_voltage_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::voltage::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('inverter::current::desactivate') == 0) {
			if ($this->getConfiguration('inverter::current::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::current::cmd', ''), $id)) {
					$replace['#inverter_current_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::current::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('inverter::frenquency::desactivate') == 0) {
			if ($this->getConfiguration('inverter::frequency::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::frequency::cmd', ''), $id)) {
					$replace['#inverter_frequency_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::frequency::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('inverter::lcd::desactivate') == 0) {
			if ($this->getConfiguration('inverter::lcd::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::lcd::cmd', ''), $id)) {
					$replace['#inverter_lcd_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::lcd::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('inverter::temp::ac::desactivate') == 0) {
			if ($this->getConfiguration('inverter::temp::ac::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::temp::ac::cmd', ''), $id)) {
					$replace['#inverter_temp_ac_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::temp::ac::cmd not command valid !');
			}
		}
		if ($this->getConfiguration('inverter::temp::dc::desactivate') == 0) {
			if ($this->getConfiguration('inverter::temp::dc::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('inverter::temp::dc::cmd', ''), $id)) {
					$replace['#inverter_temp_dc_cmd#'] = $id[1];
				} else log::add(__CLASS__, 'debug', '| KO  inverter::temp::dc::cmd not command valid !');
			}
		}
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
		if ($this->getConfiguration('aux::desactivate', 1) == 0) {
			if ($this->getConfiguration('aux::power::cmd') != '') {
				if (preg_match("/^\#(\d+)\#$/", $this->getConfiguration('aux::power::cmd', ''), $id)) {
					///  POWER  \\\
					$replace['#aux_power_cmd#'] = $id[1];
					///  COLOR  \\\
					if ($this->getConfiguration('aux::color') != '') $replace['#auxColor#'] = $this->getConfiguration('aux::color');
					///  MAX  \\\
					if ($this->getConfiguration('aux::power::max') != '') {
						if (preg_match("/^\#(variable\(.*?\))\#$/", $this->getConfiguration('aux::power::max', ''), $dataStore)) {
							$result = jeedom::evaluateExpression($dataStore[1]);
							if (is_numeric($result)) {
								$replace['#auxMaxPower#'] = $result;
							} else log::add(__CLASS__, 'debug', '| KO  aux::power::max [' . $dataStore[1] . '] not numeric !');
						} else if (is_numeric($this->getConfiguration('aux::power::max'))) {
							$replace['#auxMaxPower#'] = $this->getConfiguration('aux::power::max');
						} else log::add(__CLASS__, 'debug', '| KO  aux::power::max not numeric !');
					}
					///  DAILY  \\\
					if ($this->getConfiguration('aux::daily::cmd') != '' && $this->getConfiguration('aux::daily::desactivate', 1) == 0) {
						$replace['#aux_daily_cmd#'] = str_replace('#', '', $this->getConfiguration('aux::daily::cmd'));
						$replace['#dailyAuxText#'] = $this->getConfiguration('aux::daily::txt');
					}
				} else log::add(__CLASS__, 'debug', '| KO  aux::power::cmd not command valid !');
			}
		}
		/////////////////////////////////////
		////////////// PERSO ////////////////
		/////////////////////////////////////
		$result_perso = array();
		if ($this->getConfiguration('perso','') != '' && count($this->getConfiguration('perso')) > 0) {
			$i = 1;
			$i2 = 1;
          	$string = '';
          	$sep = ',';
			foreach ($this->getConfiguration('perso') as $perso) {
				if ($perso['perso::desactivate'] == 0) {
					if ($perso['perso::cmd'] != '') {
						if (preg_match("/^\#(\d+)\#$/", $perso['perso::cmd'], $id)) {
							if (is_numeric($perso['perso::x'])) {
								if (is_numeric($perso['perso::y'])) {
									$result_perso[$i] = array('perso::cmd' => $id[1]);
									//$result_perso[$i] = array('perso::cmd' => str_replace('#', '', $perso['perso::cmd']));
									$string = $perso['perso::x'];
									$string .= $sep . $perso['perso::y'];
									$string .= $sep . (($perso['perso::size'] != '') ? min(max($perso['perso::size'], 7), 16) : 16);
									$string .= $sep . $perso['perso::color'];
									$string .= $sep . $perso['perso::text'];
									$string .= $sep . (($perso['perso::text::size'] != '') ? min(max($perso['perso::text::size'], 7), 16) : (($perso['perso::size'] != '') ? min(max($perso['perso::size'], 7), 16) : 16));
									$string .= $sep . $perso['perso::text::position'];
									$result_perso[$i] = $result_perso[$i] + array('params' => $string);
									$i++;
								} else log::add(__CLASS__, 'debug', '| KO  Perso N° ' . $i2 . ' - perso::y not numeric !');
							} else log::add(__CLASS__, 'debug', '| KO  Perso N° ' . $i2 . ' - perso::x not numeric !');
						} else log::add(__CLASS__, 'debug', '| KO  Perso N° ' . $i2 . ' - perso::cmd not command valid !');
					}

				}
				$i2++;
			}
		}
		//log::add('powerFlow', 'debug', '$string -> ' . json_encode($result_perso));
		$replace['#persoarray#'] = json_encode($result_perso);
		////////////////////////
		log::add(__CLASS__, 'debug', '└────────────────────');
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
		if ($this->getLogicalId() == 'refresh') {
			//$eqLogic->refreshStatus();
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}