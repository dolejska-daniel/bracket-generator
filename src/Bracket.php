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

namespace BracketGenerator;

use BracketGenerator\Tree;

use Nette\Utils\ArrayHash;
use Nette\Utils\Html;


class Bracket
{
	const
		BLOCK_TYPE_SPACE      = 'space',
		BLOCK_TYPE_MATCH_HEAD = 'match:head',
		BLOCK_TYPE_MATCH_FOOT = 'match:foot';

	const
		SET_ATTRS                    = 'SET_ATTRS',
		SET_BORDER_TOP_CLASS         = 'SET_BORDER_TOP_CLASS',
		SET_BORDER_RIGHT_CLASS       = 'SET_BORDER_RIGHT_CLASS',
		SET_BORDER_BOTTOM_CLASS      = 'SET_BORDER_BOTTOM_CLASS',
		SET_MATCH_ID_ELEMENT         = 'SET_MATCH_ID_ELEMENT',
		SET_MATCH_ID_ATTRS           = 'SET_MATCH_ID_ATTRS',
		SET_MATCH_HEAD_NAME_ATTRS    = 'SET_MATCH_HEAD_NAME_ATTRS',
		SET_MATCH_HEAD_NAME_ELEMENT  = 'SET_MATCH_HEAD_NAME_ELEMENT',
		SET_MATCH_HEAD_SCORE_ATTRS   = 'SET_MATCH_HEAD_SCORE_ATTRS',
		SET_MATCH_HEAD_SCORE_ELEMENT = 'SET_MATCH_HEAD_SCORE_ELEMENT',
		SET_MATCH_FOOT_NAME_ATTRS    = 'SET_MATCH_FOOT_NAME_ATTRS',
		SET_MATCH_FOOT_NAME_ELEMENT  = 'SET_MATCH_FOOT_NAME_ELEMENT',
		SET_MATCH_FOOT_SCORE_ATTRS   = 'SET_MATCH_FOOT_SCORE_ATTRS',
		SET_MATCH_FOOT_SCORE_ELEMENT = 'SET_MATCH_FOOT_SCORE_ELEMENT';


	/** @var array $settings */
	protected $settings = [
		self::SET_ATTRS => [ 'class' => 'bracket' ],

		self::SET_BORDER_TOP_CLASS    => 'border-top',
		self::SET_BORDER_RIGHT_CLASS  => 'border-right',
		self::SET_BORDER_BOTTOM_CLASS => 'border-bottom',

		self::SET_MATCH_ID_ELEMENT => 'div',
		self::SET_MATCH_ID_ATTRS   => [ 'class' => 'match-id' ],

		self::SET_MATCH_HEAD_NAME_ELEMENT  => 'div',
		self::SET_MATCH_HEAD_NAME_ATTRS    => [ 'class' => 'match-name-head' ],
		self::SET_MATCH_HEAD_SCORE_ELEMENT => 'div',
		self::SET_MATCH_HEAD_SCORE_ATTRS   => [ 'class' => 'match-score-head' ],

		self::SET_MATCH_FOOT_NAME_ELEMENT  => 'div',
		self::SET_MATCH_FOOT_NAME_ATTRS    => [ 'class' => 'match-name-foot' ],
		self::SET_MATCH_FOOT_SCORE_ELEMENT => 'div',
		self::SET_MATCH_FOOT_SCORE_ATTRS   => [ 'class' => 'match-score-foot' ],
	];

	/** @var array $map */
	protected $map;

	/** @var Html $html */
	protected $html;

	/** @var int $participant_count */
	protected $participant_count;

	/** @var Tree\Node $tree */
	protected $tree;

	/** @var Tree\Node[][] $levels */
	protected $levels;

	/**
	 * @return Tree\Node|null
	 */
	public function getTree()
	{
		return $this->tree;
	}

	/**
	 * @return Tree\Node[][]
	 */
	public function getLevels(): array
	{
		return $this->levels;
	}

	/**
	 * @return Html|null
	 */
	public function getHtml(): Html
	{
		return $this->html;
	}

	/**
	 * @return array
	 */
	public function getMap(): array
	{
		return $this->map;
	}


	// ==============================================d=d=
	//   INIT FUNCTIONS
	// ==============================================d=d=

	public static function create( int $participant_count, array $settings = [] ): self
	{
		$bracket = new static;
		$bracket->settings          = array_merge($bracket->settings, $settings);
		$bracket->participant_count = $participant_count;

		$bracket->tree   = Tree\Builder::buildTree($participant_count);
		$bracket->levels = Tree\Builder::buildLevels($bracket->tree);
		return $bracket;
	}

	public static function createFromTree( Tree\Node $tree, array $settings = [] ): self
	{
		$bracket = new static;
		$bracket->settings          = array_merge($bracket->settings, $settings);
		$bracket->participant_count = $tree->getParticipantCount();

		$bracket->tree   = $tree;
		$bracket->levels = Tree\Builder::buildLevels($bracket->tree);
		return $bracket;
	}

	public static function createFromList( array $list, $l, $r, array $settings = [] )
	{
		$list = self::createMatchTreeFromList($list, $l, $r);

		$bracket = new static;
		$bracket->tree   = Tree\Builder::buildTreeFromListTree($list, $l, $r);
		$bracket->levels = Tree\Builder::buildLevels($bracket->tree);

		$bracket->settings          = array_merge($bracket->settings, $settings);
		$bracket->participant_count = $bracket->tree->getParticipantCount();
		return $bracket;
	}

	public static function createFromListAndFill( array $list, $n1, $s1, $l, $n2, $s2, $r, array $settings = [] )
	{
		$list = self::createMatchTreeFromList($list, $l, $r);

		$bracket = new static;
		$bracket->tree   = Tree\Builder::buildTreeFromListTree($list, $l, $r);
		$bracket->levels = Tree\Builder::buildLevels($bracket->tree);

		$bracket->settings          = array_merge($bracket->settings, $settings);
		$bracket->participant_count = $bracket->tree->getParticipantCount();

		$bracket->fillByMatchTree($list, $n1, $s1, $l, $n2, $s2, $r);
		return $bracket;
	}


	// ==============================================d=d=
	//   BUILD FUNCTIONS
	// ==============================================d=d=

	protected function buildMap( int $count = null, $spacing = 0, $match_id = null, $current_level = 0 )
	{
		if ($count == null)
			$count = $this->participant_count;

		$count      = pow(2, ceil(log($count, 2)));
		$count_next = $count - ceil($count / 2);

		$is_odd = false;
		$x = -1;
		for ($k = 0; $k < $count / 2; $k++)
		{
			$match_node = $this->levels[$current_level][$k];

			$is_odd   = !$is_odd;
			$is_empty = is_null($match_node) || $match_node->isEmpty();

			$leftChild_empty  = $is_empty || $match_node->isLeftNodeEmpty();
			$rightChild_empty = $is_empty || $match_node->isRightNodeEmpty();

			for ($i = 0; $i < $spacing; $i++)
			{
				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"      => self::BLOCK_TYPE_SPACE,
					"hasBorder" => $is_odd == false && !$is_empty,
					"rowspan"   => 1,
				]);
			}

			if ($is_empty)
			{
				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"      => self::BLOCK_TYPE_SPACE,
					"hasBorder" => false,
					"rowspan"   => 1,
				]);

				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"      => self::BLOCK_TYPE_SPACE,
					"hasBorder" => false,
					"rowspan"   => 1,
				]);
			}
			else
			{
				$match_node->setNodeId($match_id = $this->getNextMatchId($match_id));

				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"            => self::BLOCK_TYPE_MATCH_HEAD,
					"hasPreBorder"    => $spacing != 0 && $leftChild_empty == false,
					"hasBorder"       => $count_next > 1,
					"borderDirection" => $is_odd ? self::SET_BORDER_BOTTOM_CLASS : self::SET_BORDER_RIGHT_CLASS,
					"node"            => $match_node,
				]);

				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"            => self::BLOCK_TYPE_MATCH_FOOT,
					"hasPreBorder"    => $spacing != 0 && ($rightChild_empty == false && $leftChild_empty),
					"hasBorder"       => $count_next > 1,
					"borderDirection" => $is_odd ? self::SET_BORDER_RIGHT_CLASS : self::SET_BORDER_TOP_CLASS,
					"node"            => $match_node,
				]);
			}

			for ($i = 0; $i < $spacing; $i++)
			{
				$x++;
				$this->map[$x][$current_level] = ArrayHash::from([
					"type"      => self::BLOCK_TYPE_SPACE,
					"hasBorder" => $is_odd && !$is_empty && $count_next > 1,
					"rowspan"   => 1,
				]);
			}
		}

		if ($count_next > 1)
			$this->buildMap($count_next, $spacing * 2 + 1, $match_id, $current_level + 1);
	}

	protected function buildHtml()
	{
		//  Creating table & table body - containers
		$this->html = $table = Html::el('table');
		$table->addAttributes($this->settings[self::SET_ATTRS]);

		$tbody = Html::el('tbody');
		$table->addHtml($tbody);

		foreach ($this->map as $row_columns)
		{
			//  Creating new table row
			$tr = Html::el('tr');
			$tbody->addHtml($tr);

			foreach ($row_columns as $block)
			{
				//  Filling the row according to bracket map

				if ($block->type == self::BLOCK_TYPE_SPACE)
				{
					$tr->addHtml(Html::el('td', [
						'rowspan' => $block->rowspan,
						'colspan' => 6,
						'class'   => $block->hasBorder ? $this->settings[self::SET_BORDER_RIGHT_CLASS] : '',
					]));
				}
				elseif ($block['type'] == self::BLOCK_TYPE_MATCH_HEAD)
				{
					$id    = $this->getMatchIdElement($block->node);
					$name  = $this->getMatchNameElementForHead($block->node);
					$score = $this->getMatchScoreElementForHead($block->node);

					$tr->addHtml(Html::el('td', [
						'class' => $block->hasPreBorder ? $this->settings[self::SET_BORDER_BOTTOM_CLASS] : '',
					]));

					$tr->addHtml(Html::el('td', [
						'rowspan' => 2,
					])->addHtml($id));

					$tr->addHtml(Html::el('td', [ 'style' => 'vertical-align: bottom;' ])->addHtml($name));

					$tr->addHtml(Html::el('td', [ 'style' => 'vertical-align: bottom;' ])->addHtml($score));

					$tr->addHtml(Html::el('td', [
						'class' => 'match-space-after',
					]));

					if ($block['hasBorder'])
					{
						$tr->addHtml(Html::el('td', [
							'class' => $this->settings[$block['borderDirection']],
						]));
					}
					$block['node']->data = rand(0, 32);
				}
				elseif ($block['type'] == self::BLOCK_TYPE_MATCH_FOOT)
				{
					$name  = $this->getMatchNameElementForFoot($block->node);
					$score = $this->getMatchScoreElementForFoot($block->node);

					$tr->addHtml(Html::el('td', [
						'class' => $block['hasPreBorder'] ? $this->settings[self::SET_BORDER_TOP_CLASS] : '',
					]));

					$tr->addHtml(Html::el('td', [ 'style' => 'vertical-align: top;' ])->addHtml($name));

					$tr->addHtml(Html::el('td', [ 'style' => 'vertical-align: top;' ])->addHtml($score));

					$tr->addHtml(Html::el('td', [
						'class' => 'match-space',
					]));

					if ($block['hasBorder'])
					{
						$tr->addHtml(Html::el('td', [
							'class' => $this->settings[$block['borderDirection']],
						]));
					}
				}
			}
		}
	}


	// ==============================================d=d=
	//   BUILD HELP FUNCTIONS
	// ==============================================d=d=

	protected function getNextMatchId( $current_match_id = null )
	{
		return $current_match_id + 1;
	}

	protected function getMatchIdElement( Tree\Node $node ): Html
	{
		$el = Html::el($this->settings[self::SET_MATCH_ID_ELEMENT], $this->settings[self::SET_MATCH_ID_ATTRS]);
		$el->setText($node->getNodeId());
		return $el;
	}

	protected function getMatchNameElementForHead( Tree\Node $node ): Html
	{
		$el = Html::el($this->settings[self::SET_MATCH_HEAD_NAME_ELEMENT], $this->settings[self::SET_MATCH_HEAD_NAME_ATTRS]);
		$el->setText($node->getName1());
		return $el;
	}

	protected function getMatchScoreElementForHead( Tree\Node $node ): Html
	{
		$el = Html::el($this->settings[self::SET_MATCH_HEAD_SCORE_ELEMENT], $this->settings[self::SET_MATCH_HEAD_SCORE_ATTRS]);
		$el->setText($node->getScore1());
		return $el;
	}

	protected function getMatchNameElementForFoot( Tree\Node $node ): Html
	{
		$el = Html::el($this->settings[self::SET_MATCH_FOOT_NAME_ELEMENT], $this->settings[self::SET_MATCH_FOOT_NAME_ATTRS]);
		$el->setText($node->getName2());
		return $el;
	}

	protected function getMatchScoreElementForFoot( Tree\Node $node ): Html
	{
		$el = Html::el($this->settings[self::SET_MATCH_FOOT_SCORE_ELEMENT], $this->settings[self::SET_MATCH_FOOT_SCORE_ATTRS]);
		$el->setText($node->getScore2());
		return $el;
	}


	// ==============================================d=d=
	//   BRACKET FILLING FUNCTIONS
	// ==============================================d=d=

	public function fillByParticipantList( array $participants, $n = 0, $s = 1 )
	{
		if (count($participants) != $this->participant_count)
			throw new \Exception('Count of provided participants does not match with bracket size.');

		$this->fillByParticipantListRecursive(0, [], $participants, $n, $s);
	}

	protected function fillByParticipantListRecursive( int $current_level, array $seeds_set, array $participants, $n = 0, $s = 1 )
	{
		$level_nodes = @$this->getLevels()[$current_level];
		if ($level_nodes == false)
			return;

		foreach ($level_nodes as $node)
		{
			if ($node->isEmpty())
				continue;

			if (isset($seeds_set[$node->getSeed1()]) == false)
			{
				$p1 = $participants[$node->getSeed1() - 1];
				$node->name1  = $p1[$n];
				$node->score1 = $p1[$s];
				$node->data   = $p1;

				$seeds_set[$node->getSeed1()] = true;
			}

			if (isset($seeds_set[$node->getSeed2()]) == false)
			{
				$p2 = $participants[$node->getSeed2() - 1];
				$node->name2  = $p2[$n];
				$node->score2 = $p2[$s];
				$node->data   = $p2;

				$seeds_set[$node->getSeed2()] = true;
			}
		}
		$this->fillByParticipantListRecursive($current_level + 1, $seeds_set, $participants, $n, $s);
	}

	public function fillByMatchList( array $matches, $n1, $s1, $l, $n2, $s2, $r )
	{
		$tree = $this->createMatchTreeFromList($matches, $l, $r);
		$this->fillByMatchTree(reset($tree), $n1, $s1, $l, $n2, $s2, $r);
	}

	public function fillByMatchTree( $root_match, $n1, $s1, $l, $n2, $s2, $r )
	{
		$this->fillByMatchTreeRecursive($this->tree, $root_match, $n1, $s1, $l, $n2, $s2, $r );
	}

	protected function fillByMatchTreeRecursive( Tree\Node &$root_node = null, $root_match, $n1, $s1, $l, $n2, $s2, $r )
	{
		if ($root_match == false || $root_node->isEmpty())
			return;

		$is_object = is_object($root_match);

		$root_node->name1  = $is_object ? $root_match->$n1 : $root_match[$n1];
		$root_node->score1 = $is_object ? $root_match->$s1 : $root_match[$s1];

		$root_node->name2  = $is_object ? $root_match->$n2 : $root_match[$n2];
		$root_node->score2 = $is_object ? $root_match->$s2 : $root_match[$s2];

		$root_node->data = $root_match;

		$this->fillByMatchTreeRecursive($root_node->rightNode, $is_object ? $root_match->$r : $root_match[$r], $n1, $s1, $l, $n2, $s2, $r);
		$this->fillByMatchTreeRecursive($root_node->leftNode, $is_object ? $root_match->$l : $root_match[$l], $n1, $s1, $l, $n2, $s2, $r);
	}


	// ==============================================d=d=
	//   OTHER HELP FUNCTIONS
	// ==============================================d=d=

	protected static function createMatchTreeFromList( array $match_list, $l, $r)
	{
		$tree = [];
		$refd = [];

		function joinRefs( &$refd, &$match, &$match_list, $ref, $l, $r ) {
			$match = $match_list[$ref];
			if ($match[$l])
			{
				$refd[] = $match[$l];
				joinRefs($refd, $match[$l], $match_list, $match[$l], $l, $r);
			}
			if ($match[$r])
			{
				$refd[] = $match[$r];
				joinRefs($refd, $match[$r], $match_list, $match[$r], $l, $r);
			}
		}

		foreach ($match_list as $id => $match)
		{
			$tree[$id] = $match;
			if ($match[$l])
			{
				$refd[] = $match[$l];
				joinRefs($refd, $tree[$id][$l], $match_list, $match[$l], $l, $r);
			}
			if ($match[$r])
			{
				$refd[] = $match[$r];
				joinRefs($refd, $tree[$id][$r], $match_list, $match[$r], $l, $r);
			}
		}

		foreach ($refd as $id)
			unset($tree[$id]);

		return reset($tree);
	}

	public function __toString(): string
	{
		return $this->render();
	}

	public function render(): string
	{
		$this->buildMap();
		$this->buildHtml();
		return $this->html->render();
	}
}