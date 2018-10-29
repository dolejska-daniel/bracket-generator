<?php

/**
 * Copyright (C) 2018  Daniel Dolejška
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require "_init.php";
use BracketGenerator\Bracket;

//  Settings
// Match reference for first participant
$left_ref = 'left';
// Match reference for second participant
$right_ref = 'right';
// Match list - matches can have any ID whatsoever, they also don't have to be in any particular order
$matches = [
	// Match 1
	34 => [
		$left_ref  => null,
		$right_ref => null,
	],
	// Match 2
	12 => [
		$left_ref  => null,
		$right_ref => null,
	],
	// Last match, can have any I
	89 => [
		$left_ref  => 34,
		$right_ref => 12,
	],
];

//  Creates match bracket from match list
$bracket = Bracket::createFromList($matches, $left_ref, $right_ref);

//  Bracket can be rendered just by using echo
echo $bracket;
