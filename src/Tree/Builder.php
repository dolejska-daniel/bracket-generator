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

namespace BracketGenerator\Tree;

class Builder
{
	const SIDE_LEFT = 'leftNode';

	const SIDE_RIGHT = 'rightNode';


	// ==============================================d=d=
	//   TREE BUILDING
	// ==============================================d=d=

	/**
	 * @param int $size
	 *
	 * @return Node
	 */
	public static function buildTree( int $size ): Node
	{
		$node = new Node(1, 2);
		self::createSubnodes($node, $size - 2, 1);
		self::normalizeSubtree($node, $node->getHeight() - 1);
		return $node;
	}

	/**
	 * @param Node $rootNode
	 * @param int  $count
	 * @param int  $level
	 *
	 * @return Node
	 */
	protected static function createSubnodes( Node $rootNode, int $count, int $level ): Node
	{
		$left_count  = floor($count / 2);
		$right_count = ceil($count / 2);

		if ($right_count)
		{
			$rightNode = self::createSubnode($rootNode, self::SIDE_RIGHT, $level + 1);
			if ($right_count > 1)
				self::createSubnodes($rightNode, $right_count - 1, $level + 1);
		}

		if ($left_count)
		{
			$leftNode = self::createSubnode($rootNode, self::SIDE_LEFT, $level + 1);
			if ($left_count > 1)
				self::createSubnodes($leftNode, $left_count - 1, $level + 1);
		}

		return $rootNode;
	}

	/**
	 * @param Node   $node
	 * @param string $side
	 * @param int    $level
	 *
	 * @return Node
	 */
	protected static function createSubnode( Node $node, string $side, int $level ): Node
	{
		$seeds = range(1, pow(2, $level));
		return $node->$side = new Node(
			$side == self::SIDE_LEFT ? $node->getSeed1() : $node->getSeed2(),
			array_slice($seeds, $side == self::SIDE_LEFT ? -$node->getSeed1() : -$node->getSeed2(), 1)[0]
		);
	}


	// ==============================================d=d=
	//   TREE FROM LIST BUILDING
	// ==============================================d=d=

	/**
	 * @param $rootList
	 * @param $l
	 * @param $r
	 *
	 * @return Node
	 */
	public static function buildTreeFromListTree( $rootList, $l, $r ): Node
	{
		$node = new Node(1, 1);
		$node->data = $rootList;

		self::createSubnodeFromTree($node, self::SIDE_RIGHT, @$rootList[$r], $l, $r);
		self::createSubnodeFromTree($node, self::SIDE_LEFT, @$rootList[$l], $l, $r);
		self::normalizeTree($node);

		return $node;
	}

	/**
	 * @param Node $node
	 * @param      $side
	 * @param      $list
	 * @param      $l
	 * @param      $r
	 */
	protected static function createSubnodeFromTree( Node $node, $side, $list, $l, $r )
	{
		if ($list == false)
			return;

		$node->$side = $newNode = new Node(1, 1);
		$newNode->data = $list;

		self::createSubnodeFromTree($newNode, self::SIDE_RIGHT, @$list[$r], $l, $r);
		self::createSubnodeFromTree($newNode, self::SIDE_LEFT, @$list[$l], $l, $r);
	}

	protected static function normalizeTree( Node $rootNode )
	{
		$height = $rootNode->getHeight();
		self::normalizeSubtree($rootNode, $height - 1);
	}

	protected static function normalizeSubtree( Node $node, $current_level )
	{
		if ($current_level)
		{
			if ($node->rightNode == null)
				$node->rightNode = new Node();

			self::normalizeSubtree($node->rightNode, $current_level - 1);

			if ($node->leftNode == null)
				$node->leftNode = new Node();

			self::normalizeSubtree($node->leftNode, $current_level - 1);
		}
	}


	// ==============================================d=d=
	//   LEVEL BUILDING
	// ==============================================d=d=

	/**
	 * @param Node $rootNode
	 *
	 * @return Node[][]
	 */
	public static function buildLevels( Node $rootNode ): array
	{
		$levels      = [];
		$level_count = $rootNode->getHeight();

		return self::fillSublevels($rootNode, $levels, $level_count - 1);
	}

	/**
	 * @param Node  $node
	 * @param array $levels
	 * @param int   $current_level
	 *
	 * @return Node[][]
	 */
	protected static function fillSublevels( Node $node, array &$levels, int $current_level ): array
	{
		if ($current_level < 0)
			return [];

		$levels[$current_level][] = $node;

		if ($node->leftNode)
			self::fillSublevels($node->leftNode, $levels, $current_level - 1);

		if ($node->rightNode)
			self::fillSublevels($node->rightNode, $levels, $current_level - 1);

		return $levels;
	}
}