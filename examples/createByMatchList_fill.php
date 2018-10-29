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
// Name of field containing name of first participant
$left_name = 'participant1';
// Name of field containing score for first participant
$left_score = 'participant1_score';
// Match reference for first participant
$left_ref = 'left';

// Name of field containing name of second participant
$right_name = 'participant2';
// Name of field containing score for second participant
$right_score = 'participant2_score';
// Match reference for second participant
$right_ref    = 'right';

// Match list - matches can have any ID whatsoever, they also don't have to be in any particular order
$matches = [
	// Match 1
	34 => [
		$left_name => "Participant 1",
		$left_score => "0",
		$left_ref  => null,

		$right_name => "Participant 2",
		$right_score => "1",
		$right_ref => null,
	],
	// Match 2
	12 => [
		$left_name => "Participant 3",
		$left_score => "0",
		$left_ref  => null,

		$right_name => "Participant 4",
		$right_score => "0",
		$right_ref => null,
	],
	// Last match, can have any I
	89 => [
		$left_name => "Participant 2",
		$left_score => "0",
		$left_ref  => 34,

		$right_name => null,
		$right_score => "0",
		$right_ref => 12,
	],
];

//  Creates match bracket from match list
$bracket = Bracket::createFromListAndFill($matches, $left_name, $left_score, $left_ref, $right_name, $right_score, $right_ref);

//  Bracket can be rendered just by using echo
echo $bracket;
