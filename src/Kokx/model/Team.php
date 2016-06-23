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

namespace Kokx\Model;

/**
 * Combat Report Model
*
* @package    Default
* @subpackage Models
*/
class Team
{
	
	const 	ATTACKERS = 0,
			DEFENDERS = 1;

	/**
	 * Array of {@link \Kokx\Model\HarvestReport}, for the debris field.
	 *
	 * @var array
	 */
	protected $_hrs = array();

	/**
	 * Metal losses
	 *
	 * @var int
	*/
	protected $_metal;

	/**
	 * Crystal losses
	 *
	 * @var int
	 */
	protected $_crystal;

	/**
	 * Deuterium losses
	 *
	 * @var int
	 */
	protected $_deuterium;

	/**
	 * Deuterium costs
	 *
	 * @var int
	 */
	protected $_deuteriumCosts;


	/**
	 * Set the Harvest Reports.
	 *
	 * @param array $reports array of {@link \Kokx\Model\HarvestReport}'s
	 *
	 * @return \Kokx\Model\CombatReport
	 */
	public function setHarvestReports(array $reports)
	{
		$this->_hrs = $reports;

		return $this;
	}

	/**
	 * Get the Harvest reports.
	 *
	 * @return array
	 */
	public function getHarvestReports()
	{
		return $this->_hrs;
	}

	/**
	 * Set the Deuterium costs.
	 *
	 * @param array $raids array of {@link \Kokx\Model\DeuteriumCosts}'s
	 *
	 * @return \Kokx\Model\DeuteriumCosts
	 */
	public function setDeuteriumCosts(array $deuterium)
	{
		$this->_deuteriumCosts = $deuterium;

		return $this;
	}

	/**
	 * Get the deuterium costs
	 *
	 * @return array
	 */
	public function getDeuteriumCosts()
	{
		return $this->_deuteriumCosts;
	}

	/**
	 * Metal losses.
	 *
	 * @return int
	 */
	public function getMetal()
	{
		return $this->_metal;
	}

	/**
	 * Crystal losses.
	 *
	 * @return int
	 */
	public function getCrystal()
	{
		return $this->_crystal;
	}

	/**
	 * Deuterium losses.
	 *
	 * @return int
	 */
	public function getDeuterium()
	{
		return $this->_deuterium;
	}
	
	/**
	 * Total losses.
	 *
	 * @return int
	 */
	public function getTotalLosses()
	{
		return $this->getMetal() + $this->getCrystal() + $this->getDeuterium();
	}

}
