<?php
/**
 * @author 42Pollux
 * @since 2019-11-09
 */

namespace App\Dto\Response;

use Swagger\Annotations as SWG;

class MetaDto
{
    /**
     * @SWG\Property(
     *  property="code",
     *  type="integer"
     * )
     * @var int
     */
    public $code;

    /**
     * @SWG\Property(
     *  property="errors",
     *  type="array",
     *  @SWG\Items(
     *     type="string"
     *  )
     * )
     * @var array
     */
    public $errors;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
