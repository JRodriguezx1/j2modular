<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected static $db;
    //protected string $entityClass;
  
    public static function setDB($database):void { 
        self::$db = $database; 
    }

    public static function getDB(){
        return self::$db;
    }

    protected function fetchAll(string $sql): array
    {
        $res = self::$db->query($sql);
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $res->free();
        return $rows;
    }

    protected function fetchAllStd(string $sql): array
    {
        $res = self::$db->query($sql);
        $rows = [];
        while ($row = $res->fetch_object()) {
            $rows[] = $row;
        }
        $res->free();
        return $rows;
    }

    protected function escape(string $value): string
    {
        return self::$db->escape_string($value);
    }
}