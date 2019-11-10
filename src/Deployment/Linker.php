<?php
/**
 * @author 42Pollux
 * @since 2019-11-07
 */

namespace App\Deployment;


use App\Dto\ProjectInfo;
use App\Helper\Configuration;
use App\Helper\Shell;
use App\Logger\Logger;

class Linker
{
    /**
     * @param ProjectInfo $projectInfo
     * @throws \Exception
     */
    public function link(ProjectInfo $projectInfo) : void
    {
        $installationPath = Configuration::get('deployment.install_dir') . '/' . $projectInfo->getDeploymentName();

        $deploymentPath = '';
        switch ($projectInfo->getVisibility()) {
            case 'public':
                $deploymentPath = Configuration::get('deployment.public_dir');
                break;
            case 'private':
                $deploymentPath = Configuration::get('deployment.private_dir');
                break;
            default:
                $deploymentPath = Configuration::get('deployment.public_dir');
                break;
        }

        // Remove previous symlinks if the exist
        if (file_exists($deploymentPath . '/' . $projectInfo->getDeploymentName())) {
            Shell::exec(
                'rm -f ' .
                $deploymentPath . '/' . $projectInfo->getDeploymentName()
            );
        }

        if ($projectInfo->hasLinkedDirectory()) { // TODO umbenennen
            $command =  'ln -s ' .
                $installationPath . '/' . $projectInfo->getLinkedDirectory() . ' ' .
                $deploymentPath . '/' . $projectInfo->getDeploymentName();
            Shell::exec($command);
        } else {
            $command =  'ln -s ' .
                $installationPath . ' ' .
                $deploymentPath . '/' . $projectInfo->getDeploymentName();
            Shell::exec($command);
        }
    }

    /**
     * @param ProjectInfo $projectInfo
     * @throws \Exception
     */
    public function unlink(ProjectInfo $projectInfo)
    {
        $deploymentPublic = Configuration::get('deployment.public_dir') . '/' . $projectInfo->getDeploymentName();
        $deploymentPrivate = Configuration::get('deployment.private_dir') . '/' . $projectInfo->getDeploymentName();

        if (file_exists($deploymentPublic)) {
            $command = 'rm -f ' . $deploymentPublic;
            Shell::exec($command);
        }

        if (file_exists($deploymentPrivate)) {
            $command = 'rm -f ' . $deploymentPrivate;
            Shell::exec($command);
        }
    }
}