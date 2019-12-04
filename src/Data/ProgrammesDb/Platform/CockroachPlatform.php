<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Platform;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;

class CockroachPlatform extends PostgreSQL94Platform
{
    /**
     * {@inheritDoc}
     */
    public function getAdvancedForeignKeyOptionsSQL(ForeignKeyConstraint $foreignKey)
    {
        $query = '';

        if ($foreignKey->hasOption('match')) {
            $query .= ' MATCH ' . $foreignKey->getOption('match');
        }

        $query .= AbstractPlatform::getAdvancedForeignKeyOptionsSQL($foreignKey);

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function getNowExpression()
    {
        return 'LOCALTIMESTAMP';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTypeDeclarationSQL(array $fieldDeclaration)
    {
        return 'TIMESTAMP WITHOUT TIME ZONE';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzTypeDeclarationSQL(array $fieldDeclaration)
    {
        return 'TIMESTAMP WITH TIME ZONE';
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeTypeDeclarationSQL(array $fieldDeclaration)
    {
        return 'TIME WITHOUT TIME ZONE';
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeDoctrineTypeMappings()
    {
        $this->doctrineTypeMapping = [
            'bool'          => 'boolean',
            'bytea'         => 'blob',
            'char'          => 'string',
            'name'          => 'string',
            'int8'          => 'bigint',
            'int2'          => 'smallint',
            'int2vector'    => 'blob',
            'int4'          => 'integer',
            'regproc'       => 'bigint',
            'text'          => 'text',
            'oid'           => 'bigint',
            'oidvector'     => 'blob',
            'float4'        => 'float',
            'float8'        => 'float',
            'inet'          => 'string',
            '_bool'         => 'blob',
            '_bytea'        => 'blob',
            '_char'         => 'blob',
            '_name'         => 'blob',
            '_int2'         => 'blob',
            '_int2vector'   => 'blob',
            '_int4'         => 'blob',
            '_regproc'      => 'blob',
            '_text'         => 'blob',
            '_oidvector'    => 'blob',
            '_bpchar'       => 'blob',
            '_varchar'      => 'blob',
            '_int8'         => 'blob',
            '_float4'       => 'blob',
            '_float8'       => 'blob',
            '_oid'          => 'blob',
            '_inet'         => 'blob',
            'bpchar'        => 'string',
            'varchar'       => 'string',
            'date'          => 'date',
            'time'          => 'time',
            'timestamp'     => 'datetime',
            '_timestamp'    => 'blob',
            '_date'         => 'blob',
            '_time'         => 'blob',
            'timestamptz'   => 'datetimetz',
            '_timestamptz'  => 'blob',
            'interval'      => 'string',
            '_interval'     => 'blob',
            '_numeric'      => 'blob',
            'timetz'        => 'time',
            '_timetz'       => 'blob',
            'bit'           => 'binary',
            '_bit'          => 'blob',
            'varbit'        => 'blob',
            '_varbit'       => 'blob',
            'numeric'       => 'decimal',
            'regprocedure'  => 'bigint',
            'regclass'      => 'bigint',
            'regtype'       => 'bigint',
            '_regprocedure' => 'blob',
            '_regclass'     => 'blob',
            '_regtype'      => 'blob',
            'uuid'          => 'guid',
            '_uuid'         => 'blob',
            'jsonb'         => 'json',
            '_jsonb'        => 'blob',
            'regnamespace'  => 'bigint',
            '_regnamespace' => 'blob',
        ];
    }
}
