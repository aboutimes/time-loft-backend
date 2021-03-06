<?php

namespace Tests;

use PDO;

/**
 * Singleton class to enable parallel PHPUnit processes
 *
 * 1) Generate a random testing database with automatic destroy upon finish
 * 2) Initialize the database schemas using SQL file specified by constant SQL_PATH
 * 3) Remove orphan test databases
 */
class Database
{
    /** @var  \Tests\Database singleton to drop test database in destructor */
    protected static $instance;

    /** @var string */
    protected static $db_name;

    /** @var string */
    protected static $host;

    /** @var string */
    protected static $username;

    /** @var string */
    protected static $password;

    public function __construct(string $db_name)
    {
        static::$db_name = $db_name;
    }

    public function __destruct()
    {
        if (static::$db_name) {
            $pdo = new PDO('mysql:host=' . static::$host . ';' . 'dbname=' . static::$db_name, static::$username, static::$password);
            $pdo->exec('DROP DATABASE `' . static::$db_name . '`');
        }
    }

    public static function getRandomDBName(
        string $prefix,
        string $host,
        string $username,
        string $password,
        string $charset = 'utf8mb4',
        string $collation = 'utf8mb4_unicode_ci'
    ): string
    {
        if (static::$instance) {
            return static::$instance->getDBName();
        }

        $db_name = $prefix . '_' . date('ymd') . '_' . str_random();

        $pdo = new PDO('mysql:host=' . $host, $username, $password);

        // Remove orphan database
        static::removeOrphans($pdo, $prefix);

        // Create random database
        $pdo->exec('CREATE DATABASE `' . $db_name . '` DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation);
        $pdo->exec('USE `' . $db_name . '`');

        // Create tables in specified random database
        $schema_file = __DIR__ . '/../database/seeds/mysql.sql';

        if ($pdo->exec(file_get_contents($schema_file)) === false) {
            throw new \ErrorException("Cannot create tables by sql file: " . $schema_file . ' because of ' . $pdo->errorInfo()[2]);
        }

        /*
        // Check if tables are inserted.
        $result = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
        dump($result);*/

        static::$instance = new static($db_name);
        static::$host     = $host;
        static::$username = $username;
        static::$password = $password;

        dump($db_name);
        return $db_name;
    }

    /**
     * Remove orphan database if exists.
     *
     * @param PDO $pdo
     * @param string $prefix
     */
    public static function removeOrphans(PDO $pdo, string $prefix)
    {
        $databases = $pdo->query('SHOW DATABASES LIKE "' . $prefix . '%"')->fetchAll();

        foreach ($databases as $database) {
            $database = reset($database);

            if (starts_with($database, $prefix) && is_numeric(explode('_', $database)[1])) {
                $pdo->exec('DROP DATABASE `' . $database . '`');

                echo 'Drop database ' . $database . PHP_EOL;
            }
        }
    }

    /**
     * @return string
     */
    public static function getDBName(): string
    {
        return static::$db_name;
    }

    /**
     * @return string
     */
    public static function getHost(): string
    {
        return static::$host;
    }

    /**
     * @return string
     */
    public static function getUsername(): string
    {
        return static::$username;
    }

    /**
     * @return string
     */
    public static function getPassword(): string
    {
        return static::$password;
    }
}