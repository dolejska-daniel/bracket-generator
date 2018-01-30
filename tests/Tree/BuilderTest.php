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

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use BracketGenerator\Tree\Node;
use BracketGenerator\Tree\Builder;


class BuilderTest extends TestCase
{
	public function levelsProvider()
	{
		function prepareNodeTree( $x, $normalized = false ) {
			$rootNode = new Node(1, 1);
			$levels   = [];

			if ($x == 0)
			{
				$levels = [
					0 => [
						$rootNode
					],
				];
			}
			elseif ($x == 1)
			{
				$rootNode->leftNode  = $l = new Node(1, 1);
				$rootNode->rightNode = $r = new Node(1, 1);

				$levels = [
					1 => [
						$rootNode
					],
					0 => [
						$l,
						$r,
					],
				];
			}
			elseif ($x == 2)
			{
				$rootNode->leftNode  = $l = new Node(1, 1);
				$rootNode->rightNode = $r = new Node(1, 1);

				$rl = $r->leftNode  = new Node(1, 1);
				$lr = $l->rightNode = new Node(1, 1);

				$lrl = $lr->leftNode = new Node(1, 1);
				$lrr = $lr->rightNode = new Node(1, 1);

				$lrrr = $lrr->rightNode = new Node(1, 1);

				$levels = [
					4 => [
						$rootNode
					],
					3 => [
						$l,
						$r,
					],
					2 => [
						$lr,
						$rl,
					],
					1 => [
						$lrl,
						$lrr,
					],
					0 => [
						$lrrr,
					],
				];
			}

			return [
				$rootNode,
				$levels
			];
		};

		return [
			//  Dataset #1
			prepareNodeTree(0),
			//  Dataset #2
			prepareNodeTree(1),
			//  Dataset #2
			prepareNodeTree(2),
		];
	}

	/**
	 * @dataProvider levelsProvider
	 *
	 * @param Node $node
	 * @param      $expectedLevels
	 */
	public function testLevels( Node $node, $expectedLevels )
	{
		$levels = Builder::buildLevels($node);

		$this->assertSameSize($expectedLevels, $levels);
		$this->assertSame($expectedLevels, $levels);
	}
}