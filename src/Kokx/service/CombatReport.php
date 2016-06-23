<?php
/**   OGotcha, a combat report converter for Ogame
 *    Copyright (C) 2014  Klaas Van Parys
 *
 *   This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *   
 *    This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details. 
 *   
 *    You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 *   This program is based on the Kokx's CR Converter � 2009 kokx: https://github.com/kokx/kokx-converter
 */

namespace Kokx\Service;

use Kokx\Renderer\Renderer;

use Plinth\Main;

/**
 * Raid Model.
 *
 * @category   KokxConverter
 * @package    Default
 * @subpackage Service
 */
class CombatReport
{

	/**
	 * @var array
	 */
    protected	$_themes = array(
		'kokx'         => 'kokx',
		'kokx-nolines' => 'kokx-nolines',
		'tsjerk'       => 'Albert Fish',
		'virus'        => 'ViRuS',
		'nexus'        => 'Nexus',
		'vii'          => 'Vii'
	);
    
    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var Main
     */
	private $_main;
	
    /**
     * @var \Kokx\Reader\Readers\CombatReport
     */
	private $_report;
	
    /**
     * @var array
     */
	private $_settings;
	
	/**
	 * @param Main $main
	 */
	public function __construct (Main $main) {
	
		$this->_main = $main;

	}

	/**
	 * @return array
	 */
    public function getThemes()
    {
        return $this->_themes;
    }

	/**
	 * @return \Kokx\Reader\Readers\CombatReport
	 */
	public function getReport(){
		
		$variables = $this->_main->getValidator()->getVariables();
		$dict = $this->_main->getDict();
		
		$this->readCombatReport($variables['report'], $dict);
		
		$this->readRaids($variables['raids'], $dict);
		$this->readHarvests($variables['attacker_harvest'], $dict, \Kokx\Model\Team::ATTACKERS);
		$this->readDeuteriumCosts($variables['attacker_deuterium'], $dict, \Kokx\Model\Team::ATTACKERS);
		$this->readHarvests($variables['defender_harvest'], $dict, \Kokx\Model\Team::DEFENDERS);
		$this->readDeuteriumCosts($variables['defender_deuterium'], $dict, \Kokx\Model\Team::DEFENDERS);
		
		return $this->_report;
	
	}

	/**
	 * @param string $data
	 * @param array $regexes
	 */
    public function readCombatReport( $data, $dict )
    {
    	
        $this->_report = \Kokx\Reader\Readers\CombatReport::parse($data, $dict, $this->_settings);
        
	}
	
	/**
	 * @param string $data
	 * @param array $regexes
	 */
	public function readRaids( $data, $dict )
    {
    	
        if ( $data != "" ) $this->_report->setRaids( \Kokx\Reader\Readers\Raid::parse($data, $dict) );
        
	}

	/**
	 * @param string $data
	 * @param array $regexes
	 * @param int $team
	 */
	public function readHarvests( $data, $dict, $team )
   	{
   		
       	if ( $data != "" ){
       		if( $team === \Kokx\Model\Team::ATTACKERS ){
       			$this->_report->getAttackers()->setHarvestReports( \Kokx\Reader\Readers\HarvestReport::parse($data, $dict) );
       		}elseif( $team === \Kokx\Model\Team::DEFENDERS ){
       			$this->_report->getDefenders()->setHarvestReports( \Kokx\Reader\Readers\HarvestReport::parse($data, $dict) );
       		}
       	}
       	
	}

	/**
	 * @param string $data
	 * @param array $regexes
	 * @param int $team
	 */
	public function readDeuteriumCosts( $data, $dict, $team )
    {
    	
    	if ( $data != "" ){
       		if( $team === \Kokx\Model\Team::ATTACKERS ){
       			$this->_report->getAttackers()->setDeuteriumCosts( \Kokx\Reader\Readers\DeuteriumCosts::parse($data, $dict) );
       		}elseif( $team === \Kokx\Model\Team::DEFENDERS ){
       			$this->_report->getDefenders()->setDeuteriumCosts( \Kokx\Reader\Readers\DeuteriumCosts::parse($data, $dict) );
       		}
    	}
        
	}

    /**
     * Get the Renderer.
     *
     * @param array $settings
     *
     * @return Default_Renderer_Renderer
     */
    public function getRenderer(array $settings, $report)
    {
        
    	return new Renderer($settings, $report, $this->_main->getDict(), $this->_main->config->get('assets:version'));

	}

    /**
     * Get the default settings
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return array(
            'theme'    			=> 'kokx',
            'middle_text'		=> $this->_main->getDict()->get('After the battle...'),
            'hide_time'  		=> true,
            'merge_fleets' 		=> true,
            'advanced_summary'  => false,
            'harvest_spoiler' 	=> false,
			'lang'				=> 'en'
        );
    }

    /**
     * Get the data.
     *
     * @return array.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
		$variables = $this->_main->getValidator()->getVariables();
		
        $this->_settings = $this->getDefaultSettings();

		$theme 		= $variables['theme'];
		$middletext = $variables['middletext'];
		$hidetime 	= $variables['hidetime'];
		$merge 		= $variables['merge'];
		$advanced 	= $variables['advanced'];
		$quotes 	= $variables['spoiler'];
		
        if ($theme != "" && isset($this->_themes[$theme])) {
            $this->_settings['theme'] = $theme;
        }
        if ($middletext != "") {
            $this->_settings['middle_text'] = $middletext;
        }
        if ($hidetime != '1') {
            $this->_settings['hide_time'] = false;
        }
        if ($merge != '1') {
            $this->_settings['merge_fleets'] = false;
        }
        if ($advanced == '1') {
        	$this->_settings['advanced_summary'] = true;
        }
        if ($quotes == '1') {
        	$this->_settings['harvest_spoiler'] = true;
        }
		
		$this->_settings['lang'] = $this->_main->getLang();

        return $this->_settings;
    }
}
