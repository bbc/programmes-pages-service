<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;

/**
 * "MATCH_AGAINST" "(" {StateFieldPathExpression ","}* InParameter {Literal}? ")"
 */
class MatchAgainst extends FunctionNode
{

    public $columns = array();

    /** @var InputParameter */
    public $needle;

    /** @var Literal */
    public $mode;

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        do {
            $this->columns[] = $parser->StateFieldPathExpression();
            $parser->match(Lexer::T_COMMA);
        } while ($parser->getLexer()->isNextToken(Lexer::T_IDENTIFIER));
        $this->needle = $parser->InParameter();
        while ($parser->getLexer()->isNextToken(Lexer::T_STRING)) {
            $this->mode = $parser->Literal();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        $haystack = null;
        $first = true;
        foreach ($this->columns as $column) {
            $first ? $first = false : $haystack .= ', ';
            $haystack .= $column->dispatch($sqlWalker);
        }
        $query = "MATCH(" . $haystack . ") AGAINST (" . $this->needle->dispatch($sqlWalker);
        if ($this->mode) {
            $query .= " " . str_replace("'", '', $this->mode->dispatch($sqlWalker)) . ")";
        } else {
            $query .= ")";
        }

        return $query;
    }
}
