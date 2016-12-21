<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\DBALException;

class Day extends FunctionNode
{
    public $date;

    public function getSql(SqlWalker $sqlWalker)
    {
        $dbPlatform = $sqlWalker->getConnection()->getDatabasePlatform();

        if ($dbPlatform instanceof MySqlPlatform) {
            return "DAY(" . $sqlWalker->walkArithmeticPrimary($this->date) . ")";
        }

        if ($dbPlatform instanceof SqlitePlatform) {
            // Mysql returns a number, Sqlite should do the same, rather than
            // a (potentially zero padded) string that looks like a number
            return "CAST(strftime('%d', " . $sqlWalker->walkArithmeticPrimary($this->date) . ") AS INTEGER)";
        }

        throw DBALException::notSupported("DAY not supported by Platform.");
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
