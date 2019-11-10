<?php
/**
 * @author 42Pollux
 * @since 2019-11-09
 */

namespace App\Dto\Response;


use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ResponseDto
{
    /**
     * @SWG\Property(
     *  property="meta",
     *  type="object",
     *  ref=@Model(type=MetaDto::class)
     * )
     * @var MetaDto
     */
    public $meta;

    /**
     * @SWG\Property(
     *  property="data",
     *  type="object"
     * )
     * @var \stdClass
     */
    public $data;

    /**
     * @return MetaDto
     */
    public function getMeta(): MetaDto
    {
        return $this->meta;
    }

    /**
     * @param MetaDto $meta
     */
    public function setMeta(MetaDto $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return \stdClass
     */
    public function getData(): \stdClass
    {
        return $this->data;
    }

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data): void
    {
        $this->data = $data;
    }
}
