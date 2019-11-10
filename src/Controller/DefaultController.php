<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Controller;


use App\Dto\Request\AddSecretsActionDto;
use App\Dto\Request\LinkDeploymentActionDto;
use App\Dto\Request\PurgeSecretsActionDto;
use App\Dto\Request\UndeployActionDto;
use App\Dto\Request\UnlinkDeploymentActionDto;
use App\Dto\Response\ResponseDto;
use App\Facade\DeploymentFacade;
use App\Facade\SecretFacade;
use App\Serializer\Serializer;
use App\Validator\ViolationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class DefaultController extends AbstractController
{
    /**
     * @var DeploymentFacade
     */
    private $fDeployment;

    /**
     * @var SecretFacade
     */
    private $fSecret;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * DefaultController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->fDeployment = new DeploymentFacade($entityManager);
        $this->fSecret = new SecretFacade($entityManager);
        $this->validator = $validator;
    }

    /**
     * GitHub webhook endpoint
     *
     * GitHub webhooks should fire against this endpoint.
     *
     * @Route("/deploy", name="deploy", methods={"POST"})
     *
     * @SWG\Tag(name="Webhook")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object"
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function deployAction(Request $request)
    {
        // Processing
        $this->fDeployment->deploy($request);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }

    /**
     * Undeploys an active deployment
     *
     * @Route("/deployments/undeploy", name="deployments_undeploy", methods={"POST"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              ref=@Model(type=UndeployActionDto::class)
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function undeployAction(Request $request)
    {
        // Deserialization
        $serializer = Serializer::getInstance();
        $undeployDto = $serializer->deserialize($request->getContent(), UndeployActionDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($undeployDto);
        if (count($violations) > 0) {
            return \App\Helper\Response::createResponse(
                new \stdClass(),
                400,
                ViolationHelper::mapViolationsToArray($violations)
            );
        }

        // Processing
        $this->fDeployment->undeploy($undeployDto);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }

    /**
     * Adds secrets to a deployment
     *
     * @Route("/secrets/add", name="secrets_add", methods={"POST"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              ref=@Model(type=AddSecretsActionDto::class)
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addSecretsAction(Request $request)
    {
        // Deserialization
        $serializer = Serializer::getInstance();
        $secretDto = $serializer->deserialize($request->getContent(), AddSecretsActionDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($secretDto);
        if (count($violations) > 0) {
            return \App\Helper\Response::createResponse(
                new \stdClass(),
                400,
                ViolationHelper::mapViolationsToArray($violations)
            );
        }

        // Processing
        $this->fSecret->setSecrets($secretDto);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }

    /**
     * Removes all secrets of a deployment
     *
     * @Route("/secrets/purge", name="secrets_purge", methods={"POST"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              ref=@Model(type=PurgeSecretsActionDto::class)
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function purgeSecretsAction(Request $request)
    {
        // Deserialization
        $serializer = Serializer::getInstance();
        $purgeDto = $serializer->deserialize($request->getContent(), PurgeSecretsActionDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($purgeDto);
        if (count($violations) > 0) {
            return \App\Helper\Response::createResponse(
                new \stdClass(),
                400,
                ViolationHelper::mapViolationsToArray($violations)
            );
        }

        // Processing
        $this->fSecret->purgeSecrets($purgeDto);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }

    /**
     * Lists all deployments
     *
     * @Route("/deployments", name="deployments_list", methods={"GET"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @return Response
     */
    public function listDeploymentsAction()
    {
        // Get a list of all deployments
        $deploymentsInfo = $this->fDeployment->getDeployments();

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            $deploymentsInfo,
            200
        );
    }

    /**
     * Links a deployment
     *
     * @Route("/deployments/link", name="deployments_link", methods={"POST"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              ref=@Model(type=LinkDeploymentActionDto::class)
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function linkDeploymentAction(Request $request)
    {
        // Deserialization
        $serializer = Serializer::getInstance();
        $linkDto = $serializer->deserialize($request->getContent(), LinkDeploymentActionDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($linkDto);
        if (count($violations) > 0) {
            return \App\Helper\Response::createResponse(
                new \stdClass(),
                400,
                ViolationHelper::mapViolationsToArray($violations)
            );
        }

        // Processing
        $this->fDeployment->link($linkDto);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }

    /**
     * Unlinks a deployment
     *
     * @Route("/deployments/unlink", name="deployments_unlink", methods={"POST"})
     *
     * @SWG\Tag(name="Endpoints")
     * @SWG\Post(
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Payload",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              type="object",
     *              ref=@Model(type=UnlinkDeploymentActionDto::class)
     *          )
     *      ),
     * @SWG\Response(
     *     response=200,
     *     description="Erfolgreich",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=ResponseDto::class)
     *     )
     *   )
     * )
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function unlinkDeploymentAction(Request $request)
    {
        // Deserialization
        $serializer = Serializer::getInstance();
        $unlinkDto = $serializer->deserialize($request->getContent(), UnlinkDeploymentActionDto::class, 'json');

        // Validation
        $violations = $this->validator->validate($unlinkDto);
        if (count($violations) > 0) {
            return \App\Helper\Response::createResponse(
                new \stdClass(),
                400,
                ViolationHelper::mapViolationsToArray($violations)
            );
        }

        // Processing
        $this->fDeployment->unlink($unlinkDto);

        // Response 200 OK
        return \App\Helper\Response::createResponse(
            new \stdClass(),
            200
        );
    }
}