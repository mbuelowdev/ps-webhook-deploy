<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Command;

use App\Facade\DeploymentFacade;
use App\Helper\Configuration;
use App\Helper\Texts;
use App\Logger\Logger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CreateWorkerCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'app:create-worker';

    private $fDeployment;

    /**
     * CreateWorkerCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        Configuration::init($parameterBag);
        Logger::init($parameterBag);

        $this->fDeployment = new DeploymentFacade($entityManager);

        parent::__construct();
        $this->setDescription('Starts a worker unit that processes deployment jobs.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // If we can not get the lock that means another instance is already running.
        // So we just quit.
        if (!$this->lock()) {
            return 0;
        }

        // Initialize and start processing jobs
        Logger::info(Texts::LOG_WORKER_START);
        try {
            $this->fDeployment->deploy(null);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return 1;
        }


        return 0;
    }
}
