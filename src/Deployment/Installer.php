<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Deployment;


use App\Dto\ProjectInfo;
use App\Dto\WebhookInfo;
use App\Helper\Configuration;
use App\Helper\Shell;
use App\Helper\Texts;
use App\Logger\Logger;

class Installer
{
    /**
     * @var array
     */
    private $disallowedRepositoryCharacters = array(
        '_',
        '/'
    );

    /**
     * @param WebhookInfo $webhookInfo
     * @return array
     * @throws \Exception
     */
    public function installOrUpdateLocalClone(WebhookInfo $webhookInfo) : array
    {
        $installDir = Configuration::get('deployment.install_dir');
        $cloneDirName = $this->getCloneDirName($webhookInfo->getOwnerName(), $webhookInfo->getRepositoryName(), $webhookInfo->getBranchName());
        $cloneInstallDir = $installDir . '/' . $cloneDirName;

        // Check if a copy of the specific branch already exists or not
        if (!file_exists($installDir . '/' . $cloneDirName)) {
            // Git clone into new directory
            $command =  'git clone ' .
                        '"' . $webhookInfo->getRepositoryURL() . '" ' .
                        $cloneInstallDir . ' ' .
                        '-b ' . $webhookInfo->getBranchName();
            Shell::exec($command);
        } else {
            // Jump into the directory, reset to HEAD, fetch and pull the changes
            $command =  'cd ' . $cloneInstallDir . ' && ' .
                        'git reset --hard HEAD && ' .
                        'git fetch && ' .
                        'git pull';
            Shell::exec($command);
        }

        return array(
            'installationPath' => $cloneInstallDir,
            'directoryName' => $cloneDirName
        );
    }

    /**
     * Returns an array with stdClass objects containing information about
     * deployments.
     *
     * @return array
     */
    public function getCurrentDeployments() : array
    {
        $installDir = Configuration::get('deployment.install_dir');

        $di = new \DirectoryIterator($installDir);

        $deployments = array();

        foreach ($di as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if (!$fileInfo->isDir()) {
                continue;
            }
            if (file_exists($fileInfo->getRealPath() . '/' . 'psdeploy.yaml') || file_exists($fileInfo->getRealPath() . '/' . 'psdeploy.yml')) {
                $deployment = new \stdClass();
                $deployment->name = basename($fileInfo->getRealPath());
                $deployment->visibility = $this->getLinkInfoOfDeployment($deployment->name);
                $deployments[] = $deployment;
            }
        }

        return $deployments;
    }

    /**
     * @param ProjectInfo $projectInfo
     * @throws \Exception
     */
    public function uninstall(ProjectInfo $projectInfo) : void
    {
        if (Configuration::get('deployment.install_dir') === '/') {
            throw new \Exception(Texts::EXCEPTION_NEARLY_DELETED_FS, 500);
        }

        $installationPath = Configuration::get('deployment.install_dir') . '/' . $projectInfo->getDeploymentName();

        $command = 'rm -rf ' . $installationPath;
        Shell::exec($command);

    }

    /**
     * @param string $repositoryName
     * @param string $branchName
     * @return string
     */
    private function getCloneDirName(string $ownerName, string $repositoryName, string $branchName) : string
    {
        $ownerName = str_replace($this->disallowedRepositoryCharacters, '-', $ownerName);
        $repositoryName = str_replace($this->disallowedRepositoryCharacters, '-', $repositoryName);
        $branchName = str_replace($this->disallowedRepositoryCharacters, '-', $branchName);

        return strtolower($ownerName . '--' . $repositoryName . '--' . $branchName);
    }

    /**
     * Looks if the symlinks have been created to to public or private deployment
     * locations and either returns 'public', 'private' or 'none' if no symlink is found.
     *
     * @param string $dirName
     * @return string
     */
    private function getLinkInfoOfDeployment(string $dirName) : string
    {
        $publicDir = Configuration::get('deployment.public_dir');
        $privateDir = Configuration::get('deployment.private_dir');

        if (file_exists($publicDir . '/' . $dirName)) {
            return 'public';
        }

        if (file_exists($privateDir . '/' . $dirName)) {
            return 'private';
        }

        return 'none';
    }
}