<?php

namespace App\Providers;

use App\Core\Container;
use PDO;
use Symfony\Component\Yaml\Yaml;

class DatabaseProvider
{
    public function register(Container $container)
    {
        // Load YAML configuration
        $yaml = Yaml::parseFile(__DIR__ . '/../../../ibrahima.yaml');

        $container->set(PDO::class, function (Container $c) use ($yaml) {
            $dsn = $yaml['DB_DSN'];
            $user = $yaml['DB_USER'];
            $password = $yaml['DB_PASS'];

            return new PDO($dsn, $user, $password);
        });

        $container->set('MysqlDatabase', function (Container $c) use ($yaml) {
            $pdo = $c->get(PDO::class);
            return new $yaml['MysqlDatabase']($pdo);
        });
    }
}
