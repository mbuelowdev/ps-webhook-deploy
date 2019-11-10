<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Facade;


use App\Deployment\Configurator;
use App\Deployment\Installer;
use App\Deployment\Linker;
use App\Dto\ProjectInfo;
use App\Dto\Request\LinkDeploymentActionDto;
use App\Dto\Request\UndeployActionDto;
use App\Dto\Request\UnlinkDeploymentActionDto;
use App\Entity\Job;
use App\Helper\Configuration;
use App\Logger\Logger;
use App\Repository\JobRepository;
use App\Worker\Worker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DeploymentFacade
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DeploymentFacade constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDeployments()
    {
        $installer = new Installer();
        $deployments = $installer->getCurrentDeployments();

        $deploymentsInfo = new \stdClass();
        $deploymentsInfo->count = count($deployments);
        $deploymentsInfo->deployments = $deployments;

        return $deploymentsInfo;
    }

    /**
     * Starts deploying projects or just saves the
     * webhook in case we received one.
     *
     * @param Request|null $request
     * @throws \Exception
     */
    public function deploy(Request $request = null) : void
    {
        // If $request IS NOT null then this is an incoming webhook
        // If $request IS null then this is the async worker
        if ($request !== null) {
            $this->saveWebhookForLater($request);
            $this->startAsyncWorker();
        } else {
            $this->processAllWebhooks();
        }
    }

    /**
     * @param UndeployActionDto $undeployDto
     * @throws \Exception
     */
    public function undeploy(UndeployActionDto $undeployDto) : void
    {
        $configurator = new Configurator();
        $projectInfo = $configurator->getProjectInfoFromInstallation(
            Configuration::get('deployment.install_dir') . '/' . $undeployDto->getDeploymentName()
        );

        $linker = new Linker();
        $linker->unlink($projectInfo);

        $installer = new Installer();
        $installer->uninstall($projectInfo);

    }

    /**
     * Saves the webhook into some database to be processed later
     * by the async worker unit.
     *
     * @param Request $request
     * @throws \Exception
     */
    private function saveWebhookForLater(Request $request) : void
    {
        // Put into database
        $this->entityManager->getRepository(Job::class)->insertNewJob(
            $request->getContent()
        );

        // Log new entry
        $json = json_decode($request->getContent());

        // Grab users of the commits
        $users = array();
        foreach ($json->commits as $commit) {
            if (!in_array($commit->author->username, $users)) {
                $users[] = $commit->author->username;
            }
        }

        // Grab the branch
        $branch = substr($json->ref, 11);

        Logger::info(
            'Repository: ' . $json->repository->url . ', Branch: ' . $branch . ', User: (' . implode(', ', $users) . ')'
        );
    }

    /**
     * Starts an async worker by calling a symfony command from the
     * command line.
     * There can only be one running worker instance. For further
     * information go into the StartWorkerCommand.
     */
    private function startAsyncWorker() : void
    {
        $strPathToConsole = Configuration::get('console.path');
        exec(
            'php ' . $strPathToConsole . ' app:create-worker > /dev/null 2> /dev/null &'
        );
    }

    /**
     * Creates and starts the worker.
     */
    private function processAllWebhooks() : void
    {
        $worker = new Worker($this->entityManager);
        $worker->process();
    }

    /**
     * @param LinkDeploymentActionDto $linkDto
     * @throws \Exception
     */
    public function link(LinkDeploymentActionDto $linkDto) : void
    {
        $configurator = new Configurator();
        $projectInfo = $configurator->getProjectInfoFromInstallation(
            Configuration::get('deployment.install_dir') . '/' . $linkDto->getDeploymentName()
        );

        $linker = new Linker();
        $linker->link($projectInfo);
    }

    /**
     * @param UnlinkDeploymentActionDto $unlinkDto
     * @throws \Exception
     */
    public function unlink(UnlinkDeploymentActionDto $unlinkDto) : void
    {
        $configurator = new Configurator();
        $projectInfo = $configurator->getProjectInfoFromInstallation(
            Configuration::get('deployment.install_dir') . '/' . $unlinkDto->getDeploymentName()
        );

        $linker = new Linker();
        $linker->unlink($projectInfo);
    }

}