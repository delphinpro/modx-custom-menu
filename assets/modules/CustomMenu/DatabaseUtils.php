<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

if (empty($modx) || !($modx instanceof DocumentParser)) {
    die('Please use the MODX Content Manager instead of accessing this file directly.');
}

/**
 * Class DatabaseUtils
 */
class DatabaseUtils
{
    public $tables = [];

    private $modx;

    public function __construct(DocumentParser $modx)
    {
        $this->modx = $modx;
    }

    public function addTableDefinition($tableName, $def)
    {
        $fullTableName = $this->modx->getFullTableName($tableName);
        $this->tables[$tableName] = [];
        $this->tables[$tableName]['fullName'] = $fullTableName;
        $this->tables[$tableName]['createCode'] = $this->getCreateCode($fullTableName, $def);
        $this->tables[$tableName]['def'] = $def;
    }

    private function getCreateCode($fullTableName, $def)
    {
        $sql = "\nCREATE"." TABLE IF NOT EXISTS {$fullTableName} (";

        $fields = [];

        foreach ($def['fields'] as $fieldName => $field) {
            $str = "\n\t`{$fieldName}` {$field['type']}";
            if (array_key_exists('length', $field)) $str .= "({$field['length']})";
            $str .= ($field['isNull'] ? " NULL" : " NOT NULL");
            if (array_key_exists('attrs', $field)) $str .= " {$field['attrs']}";
            if (array_key_exists('default',
                $field)) {
                $str .= " DEFAULT ".self::getDefaultPresentValue($field['default']);
            }

            $fields[] = $str;
        }

        $fields[] = "\n\tPRIMARY KEY (`{$def['primary']}`)";

        foreach ($def['indexes'] as $indexName => $index) {
            $fields[] = "\n\t{$index['type']} `{$indexName}` (`{$index['field']}`)";
        }

        $sql .= join(',', $fields);
        $sql .= "\n)";

        foreach ($def['params'] as $param => $value) {
            $sql .= "\n{$param}={$value}";
        }

        $sql .= ";";

        return $sql;
    }

    private static function getDefaultPresentValue($val)
    {
        if (is_null($val)) return 'NULL';
        if (is_string($val) && empty($val)) return '\'\'';
        if (is_int($val)) return "'".$val."'";
        return (string)$val;
    }

    public function castTypes($tableName, array $data, $keysToCamelCase = false)
    {
        $fields =& $this->tables[$tableName]['def']['fields'];
        $result = [];

        foreach ($data as $key => $value) {
            $_key = camelCase2Shake($key);
            if (array_key_exists($_key, $fields)) {
                switch ($fields[$_key]['type']) {
                    case 'INT':
                        if (!is_null($value)) {
                            $value = (int)$value;
                        }
                        break;
                    case 'VARCHAR':
                        break;
                    default:
                }

                if ($keysToCamelCase) {
                    $_key = shake2CamelCase($_key);
                }

                $result[$_key] = $value;
            }
        }

        return $result;
    }

    public function createTables()
    {
        foreach ($this->tables as $table) {
            $this->modx->db->query($table['createCode']);
        }
    }

    public function getTableName($tableName)
    {
        return $this->tables[$tableName]['fullName'];
    }

    public function keysConvertToCamelCase(array $array)
    {
        $result = [];
        foreach ($array as $key => $item) {
            $convertedKey = shake2CamelCase($key);
            $result[$convertedKey] = $item;
        }
        return $result;
    }

    public function keysConvertToShake(array $array)
    {
        $result = [];
        foreach ($array as $key => $item) {
            $convertedKey = camelCase2Shake($key);
            $result[$convertedKey] = $item;
        }
        return $result;
    }
}
