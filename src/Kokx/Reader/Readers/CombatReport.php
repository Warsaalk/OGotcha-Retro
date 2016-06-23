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

namespace Kokx\Reader\Readers;
use Plinth\Dictionary;

/**
 * CR parser
 *
 * @category   KokxConverter
 * @package    Default
 * @subpackage Readers_Dutch
 */
class CombatReport
{

    /**
     * Source
     *
     * @var string
     */
    protected $_source = '';

    /**
     * Merge fleets or not.
     *
     * @var boolean
     */
    protected $_mergeFleets = true;

    /**
     * The resulting report.
     *
     * @var \Kokx\Model\CombatReport
     */
    protected $_report;

    /**
     * @var Dictionary
     */
	protected $_dict;

    /**
     * Set if we have to merge fleets or not.
     *
     * @param boolean $mergeFleets
     *
     * @return void
     */
    public function setMergeFleets($mergeFleets)
    {
        $this->_mergeFleets = $mergeFleets;
    }

    /**
     * Check if we have to merge fleets.
     * 
     * @return boolean
     */
    public function getMergeFleets()
    {
        return $this->_mergeFleets;
    }

    /**
     * Parse a crash report
     *
     * @param string $source
     * @param boolean $mergeFleets
     *
     * @return \Kokx\Model\CombatReport
     */
    public static function parse($source, Dictionary $dict, $settings)
    {
		$self = new self();
		$self->_dict = $dict;
        $self->_source = stristr($source, $dict->get('regex.start'));

        // check the CR
        if (false === $self->_source) {
            throw new \Exception('Bad CR');
        }

        $self->_report = new \Kokx\Model\CombatReport();

        $matches = array();
        if (preg_match('#'.$dict->get('regex.time').'#i', $self->_source, $matches)) {
            $self->_report->setTime(new \DateTime($matches[1] . "/" . $matches[2] . " " . $matches[3] . ":" . $matches[4] . ":" . $matches[5]));
        } else {
            throw new \Exception('Bad CR time');
        }


        $self->_source = substr($self->_source, strlen($matches[0]));
        while (preg_match('#'.$dict->get('regex.round').'#i', $self->_source)) {
            $self->_report->addRound($self->_parseRound());
        }
        $self->_parseResult();

        // check if we should merge multiple fleets of the same attacker or defender into one
        if ($settings['merge_fleets']) {
            $self->_mergeFleets();
        }

        return $self->_report;
		
    }

    /**
     * Normalize the name of a ship.
     */
    public function normalizeShipName($ship)
    {
        return ucwords(strtolower($ship));
    }

    /**
     * Parse a redesign round
     *
     * @return array
     */
    protected function _parseRound()
    {
        $round = new \Kokx\Model\CombatRound();

        // first find the first attacker
        $this->_source = stristr($this->_source, $this->_dict->get('regex.round_start'));

        /*
         * Aanvaller Touch [2:193:9] Wapens: 110% Schilden: 90% Pantser: 110%
         * Soort 	L. Gevechtsschip 	Kruiser 	Slagschip 	Interceptor.
         * Aantal 	6.531 	1.139 	457 	315
         * Wapens: 	105 	840 	2.100 	1.470
         * Schilden 	19 	95 	380 	760
         * Romp 	840 	5.670 	12.600 	14.700
         *
         *
         * Verdediger Rambi vernietigd.
         */

        // complicated regex that extracts all info from a fleet slot
        $regex = $this->_dict->get('regex.round_fleet');

        $foundDefender = false;

        $matches = array();
        // loop trough the text until we have found all fleets in the round
        while (preg_match('#' . $regex . '#si', $this->_source, $matches)) {
            // extract the info
            $fleet = new \Kokx\Model\Fleet();

            $fleet->setPlayer($matches[2]);


            if ($matches[9] != $this->_dict->get('regex.battle_end')) {
                $matches[10] = str_replace(array("\n", "\r", "  "), "\t", $matches[10]);
                $matches[11] = str_replace(array("\n", "\r", "  "), "\t", $matches[11]);

                // add the ships info
                $ships   = explode("\t", trim($matches[10]));
                $numbers = explode("\t", trim($matches[11]));
                
                // Fix wierd error
                if (count($ships) == 1 && strlen($ships[0]) > 30) {
                    $ships = explode(" ", $ships[0]);
                    
                    foreach ($ships as $elem) {
                        if (preg_match('/^[A-Z]\.$/', $elem)) {
                            $concat = $elem;
                            continue;
                        }
                        $result[] = strtolower($concat.$elem);
                        $concat = '';
                    }
                    
                    $ships = $result;
                    $numbers = explode(" ", $numbers[0]);
                    
                }
                
                foreach ($ships as $key => $ship) {
                    $fleet->addShip($this->_createShip($ship, $this->_convertToInt($numbers[$key])));
                }
            }

            // check if it is an attacker or a defender
            if (strtolower($matches[1]) == $this->_dict->get('regex.attacker')) {
                if ($foundDefender) {
                    break;
                }

                $round->addAttackingFleet($fleet);
            } else {
                $round->addDefendingFleet($fleet);

                $foundDefender = true;
            }

            $this->_source = substr($this->_source, strlen($matches[0]));

            // always reset this array at the end
            $matches = array();
        }

        return $round;
    }

    /**
     * Create a ship
     *
     * @param string $name
     * @param int $count
     *
     * @return \Kokx\Model\Ship
     */
    protected function _createShip($name, $count)
    {
        $name = trim(str_replace(' ', '', strtolower($name)));
        
        switch ($name) {
            // ships
            case $this->_dict->get('regex.ship_sc'):
                $name = \Kokx\Model\Ship::SMALL_CARGO;
                break;
            case $this->_dict->get('regex.ship_lc'):
                $name = \Kokx\Model\Ship::LARGE_CARGO;
                break;
            case $this->_dict->get('regex.ship_lf'):
                $name = \Kokx\Model\Ship::LIGHT_FIGTHER;
                break;
            case $this->_dict->get('regex.ship_hf'):
                $name = \Kokx\Model\Ship::HEAVY_FIGHTER;
                break;
            case $this->_dict->get('regex.ship_xx'):
                $name = \Kokx\Model\Ship::CRUISER;
                break;
            case $this->_dict->get('regex.ship_bs'):
                $name = \Kokx\Model\Ship::BATTLESHIP;
                break;
            case $this->_dict->get('regex.ship_col'):
                $name = \Kokx\Model\Ship::COLONY_SHIP;
                break;
            case $this->_dict->get('regex.ship_rec'):
                $name = \Kokx\Model\Ship::RECYCLER;
                break;
            case $this->_dict->get('regex.ship_spio'):
                $name = \Kokx\Model\Ship::ESPIONAGE_PROBE;
                break;
            case $this->_dict->get('regex.ship_bb'):
                $name = \Kokx\Model\Ship::BOMBER;
                break;
            case $this->_dict->get('regex.ship_sol'):
                $name = \Kokx\Model\Ship::SOLAR_SATTELITE;
                break;
            case $this->_dict->get('regex.ship_des'):
                $name = \Kokx\Model\Ship::DESTROYER;
                break;
            case $this->_dict->get('regex.ship_rip'):
                $name = \Kokx\Model\Ship::DEATHSTAR;
                break;
            case $this->_dict->get('regex.ship_bc'):
                $name = \Kokx\Model\Ship::BATTLECRUISER;
                break;
            // defenses
            case $this->_dict->get('regex.def_rl'):
                $name = \Kokx\Model\Ship::ROCKET_LAUNCHER;
                break;
            case $this->_dict->get('regex.def_ll'):
                $name = \Kokx\Model\Ship::LIGHT_LASER;
                break;
            case $this->_dict->get('regex.def_hl'):
                $name = \Kokx\Model\Ship::HEAVY_LASER;
                break;
            case $this->_dict->get('regex.def_gauss'):
                $name = \Kokx\Model\Ship::GAUSS_CANNON;
                break;
            case $this->_dict->get('regex.def_ion'):
                $name = \Kokx\Model\Ship::ION_CANNON;
                break;
            case $this->_dict->get('regex.def_plasma'):
                $name = \Kokx\Model\Ship::PLASMA_TURRET;
                break;
            case $this->_dict->get('regex.def_ssd'):
                $name = \Kokx\Model\Ship::SMALL_SHIELD_DOME;
                break;
            case $this->_dict->get('regex.def_lsd'):
                $name = \Kokx\Model\Ship::LARGE_SHIELD_DOME;
                break;
        }
        
        return new \Kokx\Model\Ship($name, $count);
    }

    /**
     * Parse the battle's result
     *
     * @return void
     */
    protected function _parseResult()
    {
        // check who has won the fight
        if (preg_match('#'.$this->_dict->get('regex.won').'#i', $this->_source)) {
            if (preg_match('#'.$this->_dict->get('regex.won_attacker').'#i', $this->_source)) {
                $this->_report->setWinner(\Kokx\Model\CombatReport::ATTACKER);

                // the attacker won, get the number of stolen resources

                // De aanvaller heeft het gevecht gewonnen! De aanvaller steelt 26.971 metaal, 16.303 kristal en 11.528 deuterium.
                $regex = $this->_dict->get('regex.steals');

                $matches = array();
                preg_match('#' . $regex . '#si', $this->_source, $matches);

                $this->_report->setLoot((float) str_replace('.', '', $matches[1]),
                                        (float) str_replace('.', '', $matches[2]),
                                        (float) str_replace('.', '', $matches[3]));
            } else {
                $this->_report->setWinner(\Kokx\Model\CombatReport::DEFENDER);
            }
        } else {
                $this->_report->setWinner(\Kokx\Model\CombatReport::DRAW);
        }

        // get the attacker's losses
        $matches = array();
        preg_match('#'.$this->_dict->get('regex.lost_attacker').'#i', $this->_source, $matches);

        $this->_report->setLossesAttacker((float) str_replace('.', '', $matches[1]));

        // get the defender's losses
        $matches = array();
        preg_match('#'.$this->_dict->get('regex.lost_defender').'#i', $this->_source, $matches);

        $this->_report->setLossesDefender((float) str_replace('.', '', $matches[1]));

        // get the debris
        $matches = array();
        preg_match('#'.$this->_dict->get('regex.debris').'#i', $this->_source, $matches);

        $this->_report->setDebris((float) str_replace('.', '', $matches[1]), (float) str_replace('.', '', $matches[2]));

        // moonchance
        $matches = array();
        if (preg_match('#'.$this->_dict->get('regex.moon_chance').'#i', $this->_source, $matches)) {
            $this->_report->setMoonChance((float) str_replace('.', '', $matches[1]));
        }

        // moon creation

        // De enorme hoeveelheden van rondzwevende metaal- en kristaldeeltjes trekken elkaar aan
        // en vormen langzaam een maan, in een baan rond de planeet.
        $regex = $this->_dict->get('regex.moon');
        $matches = array();
        if (preg_match("#{$regex}#i", $this->_source, $matches)) {
            $this->_report->setMoonGiven(true);
        } else {
            $this->_report->setMoonGiven(false);
        }
    }

    /**
     * Merge fleets together.
     *
     * @return void
     */
    private function _mergeFleets()
    {
        foreach ($this->_report->getRounds() as $round) {
            // first merge the fleets of the attackers
            $attackers = array();
            foreach ($round->getAttackers() as $fleet) {
                $player = $fleet->getPlayer();
                if (isset($attackers[$player])) {
                    // merge this fleet into the previous one
                    $attackers[$player] = $this->_mergeFleet($attackers[$player], $fleet);
                } else {
                    // we haven't seen this attacker before, create a new slot
                    $attackers[$player] = $fleet;
                }
            }

            $round->setAttackers(array_values($attackers));
            unset($attackers);

            // now merge the fleets of the defenders
            $defenders = array();
            foreach ($round->getDefenders() as $fleet) {
                $player = $fleet->getPlayer();
                if (isset($defenders[$player])) {
                    // merge this fleet into the previous one
                    $defenders[$player] = $this->_mergeFleet($defenders[$player], $fleet);
                } else {
                    // we haven't seen this attacker before, create a new slot
                    $defenders[$player] = $fleet;
                }
            }

            $round->setDefenders(array_values($defenders));
        }
    }

    /**
     * Merge two fleets
     *
     * @param \Kokx\Model\Fleet $fleet1
     * @param \Kokx\Model\Fleet $fleet2
     *
     * @return array
     */
    public function _mergeFleet(\Kokx\Model\Fleet $fleet1, \Kokx\Model\Fleet $fleet2)
    {
        foreach ($fleet2->getShips() as $ship) {
            if ($fleet1->hasShip($ship->getName())) {
                $fleet1->getShip($ship->getName())->addCount($ship->getCount());
            } else {
                $fleet1->addShip($ship);
            }
        }
        return $fleet1;
    }

    /**
     * Convert to integer.
     *
     * @param string $number
     *
     * @return int
     */
    protected function _convertToInt($number)
    {
        return (float) str_replace('.', '', $number);
    }
}
