<?php

namespace Monken\CIBurner\RoadRunner;

use CodeIgniter\CLI\CLI;
use Monken\CIBurner\IntegrationInterface;

class Integration implements IntegrationInterface
{
    public function initServer(string $configType = 'basic', string $frontLoader = '')
    {
        $allowConfigType = ['basic'];
        if (in_array($configType, $allowConfigType, true) === false) {
            CLI::write(
                CLI::color(
                    sprintf(
                        'Error config type! We only support: %s. The config type you have entered is: %s.',
                        implode(', ', $allowConfigType),
                        $configType
                    ),
                    'red'
                )
            );
            echo PHP_EOL;

            exit;
        }

        $basePath   = ROOTPATH . DIRECTORY_SEPARATOR;
        $configPath = $basePath . '.rr.yaml';

        if (file_exists($configPath)) {
            rename($configPath, $basePath . 'backup.' . time() . '.rr.yaml');
        }

        $rr = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . '.rr.yaml');
        $rr = str_replace('{{front_loader}}', $frontLoader, $rr);
        $rr = str_replace('{{static_paths}}', ROOTPATH . 'public', $rr);
        $rr = str_replace('{{reload_paths}}', realpath(APPPATH . '../'), $rr);
        file_put_contents(ROOTPATH . '.rr.yaml', $rr);

        CLI::write(
            CLI::color("Initializing RoadRunner Server binary ......\n", 'blue')
        );
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = '&&vendor\\bin\\rr get';
        } else {
            $command = ';./vendor/bin/rr get';
        }

        $init = popen('cd ' . ROOTPATH . $command, 'w');
        pclose($init);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $targetPath = ROOTPATH . 'vendor\\bin\\rr_server.exe';
            $nowRRPath  = ROOTPATH . 'rr.exe';
        } else {
            $targetPath = ROOTPATH . 'vendor/bin/rr_server';
            $nowRRPath  = ROOTPATH . 'rr';
        }
        CLI::write(
            'Moveing RoadRunner Server binary to: ' .
            CLI::color("{$targetPath}", 'green') .
            "\n"
        );

        rename($nowRRPath, $targetPath);
        @chmod($targetPath, 0777 & ~umask());
    }

    public function startServer(string $frontLoader, string $commands = '')
    {
        $workDir    = __DIR__ . DIRECTORY_SEPARATOR;
        $configFile = ROOTPATH . '.rr.yaml';
        if ($commands === '') {
            $start      = popen("{$frontLoader} serve -w {$workDir} -c {$configFile}", 'w');
        } else {
            $start      = popen("{$frontLoader} $commands -w {$workDir} -c {$configFile}", 'w');
        }

        pclose($start);
        echo PHP_EOL;
    }
}
