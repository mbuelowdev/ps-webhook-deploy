<?php
/**
 * @author 42Pollux
 * @since 2019-11-03
 */

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

class PurgeSecretsActionDto
{
    /**
     * @SWG\Property(
     *  property="deploymentName",
     *  type="string"
     * )
     * @Assert\NotNull()
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $deploymentName;

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
