<?php
/**
 * @author 42Pollux
 * @since 2019-11-03
 */

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

class AddSecretsActionDto
{
    /**
     * @SWG\Property(
     *  property="secrets",
     *  type="array",
     *  @SWG\Items(
     *     type="array",
     *     minItems=2,
     *     maxItems=2,
     *     @SWG\Items(
     *      type="string"
     *     )
     *  )
     * )
     * @Assert\NotNull()
     * @Assert\All({
     *     @Assert\Count(min=2, max=2),
     *     @Assert\All({
     *          @Assert\Type("string"),
     *          @Assert\NotNull(),
     *          @Assert\NotBlank(),
     *     })
     *
     * })
     *
     * @var array[]
     */
    public $secrets;

    /**
     * @SWG\Property(
     *  property="deploymentName",
     *  type="string"
     * )
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Type("string")
     *
     * @var string
     */
    public $deploymentName;

    /**
     * @return array
     */
    public function getSecrets(): array
    {
        return $this->secrets;
    }

    /**
     * @param array $secrets
     */
    public function setSecrets(array $secrets): void
    {
        $this->secrets = $secrets;
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
