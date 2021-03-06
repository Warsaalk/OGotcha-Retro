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
 * HR parser
 *
 * @category   Kokx
 * @package    Default
 * @subpackage Readers_Dutch
 */
class HarvestReport
{

    /**
     * Parse a harvest report
     *
     * @param string $source
     *
     * @return array  of {@link \Kokx\Model\HarvestReport}'s
     */
    public static function parse($source, Dictionary $dict)
    {
        
        /**
         * Fix for non printable characters
         * Edited on: 1/1/2013
         */
        
        $source = preg_replace( '/'.$dict->get('regex.print').'/', '', $source);
        $reports = array();

        /**
         * Example report:
		 * 
		 * 06-24 08:52:19 Fleet Harvesting report from DF on [2:150:11] .
		 * Your 2 recycler(s) have a total cargo capacity of 40.000. At the target, 10.800 metal and 6.900 crystal are floating in space. You have harvested 10.800 metal and 6.900 crystal.
         */
		
		$regex = $dict->get('regex.harvest');

        $matches = array();

        preg_match_all('/' . $regex . '/i', $source, $matches, PREG_SET_ORDER);
		
        foreach ($matches as $match) {
            $reports[] = new \Kokx\Model\HarvestReport(
                (float) str_replace('.', '', $match[2]),
                (float) str_replace('.', '', $match[3]),
                (float) str_replace('.', '', $match[1]),
                (float) str_replace('.', '', $match[4]),
                (float) str_replace('.', '', $match[5]),
                (float) str_replace('.', '', $match[6]),
				(float) str_replace('.', '', $match[7])
            );
        }

        return $reports;
    }
}
