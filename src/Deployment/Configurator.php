<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Deployment;


use App\Dto\ProjectInfo;
use App\Helper\Texts;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Configurator
{
    /**
     * Collects information about the project from a local installation.
     * Also tries to parse our config file in case it exists.
     *
     * @param string $installation
     * @return ProjectInfo|null
     * @throws \Exception
     */
    public function getProjectInfoFromInstallation(string $installation) : ?ProjectInfo
    {
        // Check if psdeploy yaml file exists
        if (file_exists($installation . '/' . 'psdeploy.yaml')) {
            $configFilePath = $installation . '/' . 'psdeploy.yaml';
        } elseif (file_exists($installation . '/' . 'psdeploy.yml')) {
            $configFilePath = $installation . '/' . 'psdeploy.yml';
        } else {
            return null;
        }

        try {
            // Parse yaml configuration file
            $config = Yaml::parseFile($configFilePath);

            // Validate yaml configuration file
            if (!$this->isValidConfigurationFile($config)) {
                throw new ParseException(Texts::EXCEPTION_FAILED_TO_VALIDATE_YAML, 500);
            }
        } catch (ParseException $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        // Populate $projectInfo
        $projectInfo = new ProjectInfo($config);

        // Name
        $projectInfo->setDeploymentName(basename($installation));

        // Determine package manager and project type
        if (file_exists($installation . '/' . 'composer.json')) {
            $projectInfo->setPackageManager(ProjectInfo::PACKAGE_MANAGER_COMPOSER);

            // Parse the composer.json file and extract it's information
            $str = file_get_contents($installation . '/' . 'composer.json');
            $json = json_decode($str);

            if (isset($json->require->{'symfony/framework-bundle'})) {
                $projectInfo->setProjectType(ProjectInfo::PROJECT_TYPE_SYMFONY);
            }
            if (isset($json->require->{'laravel/framework'})) {
                $projectInfo->setProjectType(ProjectInfo::PROJECT_TYPE_LARAVEL);
            }


        } elseif (file_exists($installation . '/' . 'package.json')) {
            $projectInfo->setPackageManager(ProjectInfo::PACKAGE_MANAGER_NPM);

            // Parse the package.json file and extract it's information
            $str = file_get_contents($installation . '/' . 'package.json');
            $json = json_decode($str);

            if (isset($json->dependencies->react)) {
                $projectInfo->setProjectType(ProjectInfo::PROJECT_TYPE_REACT);
            }
            if (isset($json->dependencies->vue)) {
                $projectInfo->setProjectType(ProjectInfo::PROJECT_TYPE_VUE);
            }
            if (isset($json->dependencies->angular)) {
                $projectInfo->setProjectType(ProjectInfo::PROJECT_TYPE_ANGULAR);
            }
        }

        // Check if a dockerfile exists
        if (file_exists($installation . '/' . 'Dockerfile')) {
            $projectInfo->setHasDockerfile(true);
            $projectInfo->setDockerfile($installation . '/' . 'Dockerfile');
        }

        return $projectInfo;
    }

    private function isValidConfigurationFile(array $config) : bool
    {
        // Check if required fields are present
        if (!isset($config['version'])) return false;
        if (!isset($config['deployment'])) return false;
        if (!isset($config['deployment']['visibility'])) return false;

        // Validate values of fields
        // TODO

        return true;
    }
}