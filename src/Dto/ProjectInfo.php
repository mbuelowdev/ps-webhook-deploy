<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Dto;


class ProjectInfo
{
    const PACKAGE_MANAGER_COMPOSER = 'composer';
    const PACKAGE_MANAGER_NPM = 'npm';
    const PACKAGE_MANAGER_UNKNOWN = 'unknown';

    const PROJECT_TYPE_SYMFONY = 'symfony';
    const PROJECT_TYPE_LARAVEL = 'laravel';
    const PROJECT_TYPE_REACT = 'react';
    const PROJECT_TYPE_VUE = 'vue';
    const PROJECT_TYPE_ANGULAR = 'angular';
    const PROJECT_TYPE_UNKNOWN = 'unknown';

    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';

    /**
     * @var string
     */
    private $deploymentName;

    /**
     * @var string
     */
    private $packageManager = self::PACKAGE_MANAGER_UNKNOWN; // Composer, NPM

    /**
     * @var string
     */
    private $projectType = self::PROJECT_TYPE_UNKNOWN; // Symfony, React, Vue, etc.

    /**
     * @var bool
     */
    private $hasDockerfile = false; // true/false

    /**
     * @var null|string
     */
    private $dockerfile = null;

    /**
     * @var string
     */
    private $visibility = self::VISIBILITY_PRIVATE;

    /**
     * @var bool
     */
    private $hasLinkedDirectory = false;

    /**
     * @var string
     */
    private $linkedDirectory;

    /**
     * @var array
     */
    private $secretIdentifiers;

    /**
     * ProjectInfo constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->visibility = $config['deployment']['visibility'];

        $secretIdentifiers = array();
        if (isset($config['deployment']['secrets'])) {
            foreach ($config['deployment']['secrets'] as $secretKey => $secretFilePath) {
                $secretIdentifiers[] = array($secretKey, $secretFilePath);
            }
        }

        if (isset($config['deployment']['link'])) {
            $this->linkedDirectory = $config['deployment']['link'];
            $this->hasLinkedDirectory = true;
        }

        $this->secretIdentifiers = $secretIdentifiers;
    }

    /**
     * @return string
     */
    public function getPackageManager(): string
    {
        return $this->packageManager;
    }

    /**
     * @param string $packageManager
     */
    public function setPackageManager(string $packageManager): void
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @return string
     */
    public function getProjectType(): string
    {
        return $this->projectType;
    }

    /**
     * @param string $projectType
     */
    public function setProjectType(string $projectType): void
    {
        $this->projectType = $projectType;
    }

    /**
     * @return bool
     */
    public function hasDockerfile(): bool
    {
        return $this->hasDockerfile;
    }

    /**
     * @param bool $hasDockerfile
     */
    public function setHasDockerfile(bool $hasDockerfile): void
    {
        $this->hasDockerfile = $hasDockerfile;
    }

    /**
     * @return string|null
     */
    public function getDockerfile(): ?string
    {
        return $this->dockerfile;
    }

    /**
     * @param string|null $dockerfile
     */
    public function setDockerfile(?string $dockerfile): void
    {
        $this->dockerfile = $dockerfile;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @return array
     */
    public function getSecretIdentifiers(): array
    {
        return $this->secretIdentifiers;
    }

    /**
     * @return string
     */
    public function getLinkedDirectory(): string
    {
        return $this->linkedDirectory;
    }

    /**
     * @param string $linkedDirectory
     */
    public function setLinkedDirectory(string $linkedDirectory): void
    {
        $this->linkedDirectory = $linkedDirectory;
    }

    /**
     * @return bool
     */
    public function hasLinkedDirectory(): bool
    {
        return $this->hasLinkedDirectory;
    }

    /**
     * @return string
     */
    public function getDeploymentName(): string
    {
        return $this->deploymentName;
    }

    /**
     * @param string $deploymentName
     */
    public function setDeploymentName(string $deploymentName): void
    {
        $this->deploymentName = $deploymentName;
    }
}
