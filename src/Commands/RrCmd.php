<?php

namespace Monken\CIBurner\RoadRunner\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;
use Monken\CIBurner\RoadRunner\Integration;

class RrCmd extends BaseCommand
{
    use GeneratorTrait;

    protected $group       = 'burner';
    protected $name        = 'burner:rr';
    protected $description = 'Run commands directly to RoadRunner\'s rr binary.';
    protected $usage       = 'burner:rr [commands]';

    public function run(array $params)
    {
        $driver = 'RoadRunner';

        // init choose driver
        $integration = new Integration();
        if ($driver === 'RoadRunner') {
            $workDir = __DIR__ . DIRECTORY_SEPARATOR;
            if (file_exists($workDir . '../../../../autoload.php')) {
                $loaderPath = realpath($workDir . '../../../../bin/rr_server');
            } elseif (file_exists('../../dev/vendor/autoload.php')) {
                $loaderPath = realpath('../../dev/vendor/bin/rr_server');
            }
            if ($loaderPath === false) {
                CLI::write(
                    CLI::color(
                        "Error! Roadrunner Server is not init. Please use 'burner:init RoadRunner' to init Roadrunner.",
                        'red'
                    )
                );

                return;
            }
        } else {
            $loaderPath = realpath(__DIR__ . '/../FrontLoader.php');
        }

        $argvs = $_SERVER['argv'];

        foreach ($argvs as $key => $argv) {
            if (in_array($argv, ['spark', $this->name], true)) {
                unset($argvs[$key]);
                if ($argv === '--driver') {
                    unset($argvs[$key + 1]);
                }
            }
        }
        $command = implode(' ', $argvs);
        $integration->runCmd($loaderPath, $command);
    }
}
