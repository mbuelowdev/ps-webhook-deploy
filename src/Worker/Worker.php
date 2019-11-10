<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Worker;


use App\Deployment\Configurator;
use App\Deployment\Inserter;
use App\Deployment\Installer;
use App\Deployment\Linker;
use App\Dto\ProjectInfo;
use App\Dto\Request\LinkDeploymentActionDto;
use App\Dto\WebhookInfo;
use App\Entity\Job;
use App\Entity\Secret;
use App\Helper\Configuration;
use App\Helper\Shell;
use App\Helper\Texts;
use App\Logger\Logger;
use App\Repository\JobRepository;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class Worker
{
    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @var SecretRepository
     */
    private $secretRepository;

    /**
     * Worker constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->jobRepository = $entityManager->getRepository(Job::class);
        $this->secretRepository = $entityManager->getRepository(Secret::class);
    }

    /**
     * Processes jobs in a loop.
     */
    public function process()
    {
        // Initially get all unfinished jobs
        $jobs = $this->jobRepository->getAllUnfinishedJobs();

        // The index of the current job
        $index = 0;

        // While there are jobs process them
        while (!empty($jobs)) {
            $job = $jobs[$index];

            // Process the current job
            try {
                $this->processJob($job);
            } catch (\Exception $e) {
                // Log the error and remove the current job from the jobs array
                Logger::error($e->getMessage());
                Logger::error(Texts::EXCEPTION_FAILED_TO_PROCESS_JOB);
                unset($jobs[$index]);
                $index++;
                continue;
            }

            // Remove current job from the $jobs array
            unset($jobs[$index]);

            // After we are done, check again for jobs that came in while processing
            //if (empty($jobs)) {
            //    $jobs = $this->jobRepository->getAllUnfinishedJobs();
            //    $index = -1;
            //} TODO think of a better logic // TODO deutsch

            $index++;
        }
    }

    /**
     * Processes a single job.
     * Grabs information from the installation and psdeploy yaml file.
     *
     * @param Job $job
     * @throws \Exception
     */
    private function processJob(Job $job)
    {
        $installer = new Installer();
        $configurator = new Configurator();

        $webhookInfo = new WebhookInfo($job->getDaten());

        Logger::info(
            'Deploying ' . $webhookInfo->getRepositoryName() . '/' . $webhookInfo->getBranchName() . '...'
        );

        // Install or update the local clone
        $installation = $installer->installOrUpdateLocalClone($webhookInfo);

        // Collect information about the project
        $projectInfo = $configurator->getProjectInfoFromInstallation(
            $installation['installationPath']
        );

        // In case $projectInfo is null we don't deploy at all.
        if ($projectInfo === null) {
            Logger::info(
                Texts::LOG_NO_PSDEPLOY_FILE_FOUND
            );
            return;
        }

        // Determine deploy directory
        $deploymentPath = null;
        switch ($projectInfo->getVisibility()) {
            case ProjectInfo::VISIBILITY_PRIVATE:
                $deploymentPath = Configuration::get('deployment.private_dir');
                break;
            case ProjectInfo::VISIBILITY_PUBLIC:
                $deploymentPath = Configuration::get('deployment.public_dir');
                break;
        }

        // Insertion of secrets
        $inserter = new Inserter($this->secretRepository);
        if ($inserter->hasInsertions($installation['directoryName'])) {
            $inserter->insertSecrets(
                $installation['installationPath'],
                $projectInfo->getSecretIdentifiers()
            );
        } else {
            Logger::info('No secrets found. Skipping injection...');
        }

        // Docker deploy or normal deploy
        if ($projectInfo->hasDockerfile()) {
            // TODO Docker deploy
        } else {
            // Trigger package manager installation
            switch ($projectInfo->getPackageManager()) {
                case ProjectInfo::PACKAGE_MANAGER_COMPOSER:
                    // Install packages
                    $command =  'cd ' . $installation['installationPath'] . ' && ' .
                                'export COMPOSER_HOME="/deployment/.composer" && ' .
                                'composer install --ignore-platform-reqs';
                    Shell::exec($command);
                    break;
                case ProjectInfo::PACKAGE_MANAGER_NPM:
                case ProjectInfo::PACKAGE_MANAGER_UNKNOWN:
                    // Do nothing at all
                    break;
            }

            // Link the project to the specified $deployDir
            $linker = new Linker();
            $linker->link($projectInfo);
        }

        // Cleanup
        $this->jobRepository->setFinished($job);

        Logger::info(
            'Deployed ' . $projectInfo->getProjectType() . '/' . $projectInfo->getPackageManager() . ' ' .
            'project into ' . $deploymentPath . '/' . $installation['directoryName']
        );

    }
}