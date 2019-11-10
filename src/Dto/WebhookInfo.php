<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Dto;


use App\Helper\Texts;

class WebhookInfo
{
    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $repositoryURL;

    /**
     * @var string
     */
    private $branchName;

    /**
     * @var string
     */
    private $ownerName;

    /**
     * @var string
     */
    private $pusherName;

    /**
     * WebhookInfo constructor.
     * @param string $strJson
     * @throws \Exception
     */
    public function __construct(string $strJson)
    {
        // Deserialize
        $json = json_decode($strJson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                Texts::EXCEPTION_FAILED_TO_DESERIALIZE_GITHUB_RESPONSE,
                500
            );
        }

        $this->repositoryName = $json->repository->name;
        $this->repositoryURL = $json->repository->url;
        $this->branchName = substr($json->ref, 11);
        $this->ownerName = $json->repository->owner->name;
        $this->pusherName =  $json->pusher->name;
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    /**
     * @return string
     */
    public function getRepositoryURL(): string
    {
        return $this->repositoryURL;
    }

    /**
     * @return string
     */
    public function getBranchName(): string
    {
        return $this->branchName;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getPusherName(): string
    {
        return $this->pusherName;
    }
}
