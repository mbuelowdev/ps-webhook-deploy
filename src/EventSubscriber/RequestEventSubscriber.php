<?php
/**
 * @author 42Pollux
 * @since 2019-11-03
 */

namespace App\EventSubscriber;


use App\Helper\Configuration;
use App\Logger\Logger;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * RequestEventSubscriber constructor.
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                ['initialize', 3],
                ['authorize', 2],
                ['whitelist', 1]
            )
        );
    }

    /**
     * Initializes some static classes.
     *
     * @param RequestEvent $event
     */
    public function initialize(RequestEvent $event)
    {
        Configuration::init($this->parameterBag);
        Logger::init($this->parameterBag);
    }

    public function authorize(RequestEvent $event)
    {
        // Check authorization for the following paths
        $paths = array(
            '/secrets/add',   // name: secrets_add
            '/secrets/purge', // name: secrets_purge
            '/deployments',   // name: deployments_list
            '/deployments/undeploy',   // name: deployments_undeploy
            '/deployments/link',   // name: deployments_link
            '/deployments/unlink',   // name: deployments_unlink
        );
        if (!in_array($event->getRequest()->getPathInfo(), $paths)) {
            return;
        }

        $headers = $event->getRequest()->headers;
        if ($headers->has('Authorization')) {
            $should = 'Basic ' . base64_encode(Configuration::get('auth.username') . ':' . Configuration::get('auth.password'));
            $is = $headers->get('Authorization');

            if ($should === $is) {
                return;
            }
        }

        $event->setResponse(\App\Helper\Response::createResponse(
            new \stdClass(),
            401,
            array('Unauthorized')
        ));
    }

    /**
     * Checks whether the requesting repository is whitelisted or not.
     *
     * @param RequestEvent $event
     */
    public function whitelist(RequestEvent $event)
    {
        // Check whitelist for the following paths
        $paths = array(
            '/deploy' // name: deployments_deploy
        );
        if (!in_array($event->getRequest()->getPathInfo(), $paths)) {
            return;
        }

        $payload = $event->getRequest()->getContent();

        // Ignore if request is empty (for symfony commands)
        if ($payload === '') {
            return;
        }

        // Extract the webhook url
        $json = json_decode($payload);
        $url = $json->repository->clone_url;

        // Fetch the whitelist
        $whitelistedUrls = array();
        $whitelist = Configuration::get('whitelist');
        if (file_exists($whitelist)) {
            $json = json_decode(file_get_contents($whitelist));
            $whitelistedUrls = $json->whitelist;
        }

        if (!in_array($url, $whitelistedUrls)) {
            $event->setResponse(\App\Helper\Response::createResponse(
                new \stdClass(),
                200,
                array('Not whitelisted')
            ));
        }
    }

}