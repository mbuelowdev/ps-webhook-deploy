<?php

namespace App\Command;

use App\Helper\Configuration;
use App\Helper\Texts;
use App\Logger\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InitCommand extends Command
{
    protected static $defaultName = 'app:init';

    /**
     * InitCommand constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        Configuration::init($parameterBag);
        Logger::init($parameterBag);

        parent::__construct();
        $this->setDescription('Initializes the directory structure used by this application.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = array();
        $directories['install'] = Configuration::get('deployment.install_dir');
        $directories['public'] = Configuration::get('deployment.public_dir');
        $directories['private'] = Configuration::get('deployment.private_dir');

        // Required directory for composer
        $directories['composer_home'] = $directories['install'] . '/' . '.composer';

        // Check for missing configurations
        foreach ($directories as $dir) {
            if ($dir === '') {
                throw new \Exception(Texts::EXCEPTION_MISSING_PARAMETERS, 500);
            }
        }

        // Create all directories of the path in case they don't exist
        // For example:
        // install: /var/www/html
        // Tries to create the following directories in order:
        // /var, /var/www, /var/www/html
        foreach ($directories as $dir) {
            $subDirs = explode('/', $dir);
            $path = '';
            foreach ($subDirs as $sub) {
                if ($sub === '') {
                    continue;
                }
                $path = $path . '/' . $sub;
                mkdir($path);
            }

            // Lastly change the directory permissions
            chmod($dir, 0777);
        }

        return 0;
    }
}
