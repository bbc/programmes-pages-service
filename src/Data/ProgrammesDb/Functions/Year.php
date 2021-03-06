<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Year extends FunctionNode
{
    public $date;

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dbPlatform = $sqlWalker->getConnection()->getDatabasePlatform();

        if ($dbPlatform instanceof MySqlPlatform) {
            return "YEAR(" . $sqlWalker->walkArithmeticPrimary($this->date) . ")";
        }

        if ($dbPlatform instanceof SqlitePlatform) {
            // Mysql returns a number, Sqlite should do the same, rather than
            // a (potentially zero padded) string that looks like a number
            return "CAST(strftime('%Y', " . $sqlWalker->walkArithmeticPrimary($this->date) . ") AS INTEGER)";
        }

        throw DBALException::notSupported("YEAR not supported by Platform.");
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
