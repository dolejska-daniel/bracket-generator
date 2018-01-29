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


class Node
{
	/** @var int $node_id */
	public $node_id;

	/** @var string $name1 */
	public $name1;

	/** @var int $score1 */
	public $score1;

	/** @var string $name1 */
	public $name2;

	/** @var int $score1 */
	public $score2;

	/** @var $data */
	public $data;


	/** @var $seed1 */
	protected $seed1;

	/** @var $seed2 */
	protected $seed2;


	/** @var Node $rightNode */
	public $rightNode;

	/** @var Node $leftNode */
	public $leftNode;


	public function __construct( $seed1 = null, $seed2 = null )
	{
		$this->seed1 = $seed1;
		$this->seed2 = $seed2;

		$this->rightNode = null;
		$this->leftNode  = null;
	}

	public function __get( $name )
	{
		return is_object($this->data) ? $this->data->$name : $this->data[$name];
	}


	/**
	 * @return int
	 */
	public function getNodeId(): int
	{
		return $this->node_id;
	}

	/**
	 * @param int $node_id
	 *
	 * @return Node
	 */
	public function setNodeId( int $node_id ): self
	{
		$this->node_id = $node_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName1()
	{
		return $this->name1;
	}

	/**
	 * @return int
	 */
	public function getScore1()
	{
		return $this->score1;
	}

	/**
	 * @return string
	 */
	public function getName2()
	{
		return $this->name2;
	}

	/**
	 * @return int
	 */
	public function getScore2()
	{
		return $this->score2;
	}

	/**
	 * @return string
	 */
	public function getSeed1(): string
	{
		return $this->seed1;
	}

	/**
	 * @return string
	 */
	public function getSeed2(): string
	{
		return $this->seed2;
	}

	/**
	 * @return bool
	 */
	public function isEmpty(): bool
	{
		return ($this->name1 || $this->name2 || $this->seed1 || $this->seed2) == false;
	}

	/**
	 * @return bool
	 */
	public function isLeftNodeEmpty(): bool
	{
		return $this->leftNode == false || $this->leftNode->isEmpty();
	}

	/**
	 * @return bool
	 */
	public function isRightNodeEmpty(): bool
	{
		return $this->rightNode == false || $this->rightNode->isEmpty();
	}

	/**
	 * Returns tree height - number of levels.
	 *
	 * @return int
	 */
	public function getHeight(): int
	{
		if ($this->isEmpty())
			return 0;

		return max($this->leftNode ? $this->leftNode->getHeight() : 0,  $this->rightNode ? $this->rightNode->getHeight() : 0) + 1;
	}

	/**
	 * Returns participant count in the tree.
	 *
	 * @return int
	 */
	public function getParticipantCount(): int
	{
		if ($this->isEmpty())
			return 0;

		return
			($this->leftNode && $this->leftNode->isEmpty() == false ? $this->leftNode->getParticipantCount() : 1)
			+ ($this->rightNode && $this->rightNode->isEmpty() == false ? $this->rightNode->getParticipantCount() : 1);
	}

	/**
	 * Returns match count in the tree.
	 *
	 * @return int
	 */
	public function getMatchCount(): int
	{
		if ($this->isEmpty())
			return 0;

		return
			($this->leftNode ? $this->leftNode->getMatchCount() : 0)
			+ ($this->rightNode ? $this->rightNode->getMatchCount() : 0)
			+ 1;
	}
}