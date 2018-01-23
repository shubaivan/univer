<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;

class AbstractRestController extends FOSRestController
{
    const HTTP_STATUS_CODE_OK = 200;
    const HTTP_STATUS_CODE_NO_CONTENT = 204;
    const HTTP_STATUS_CODE_BAD_REQUEST = 400;
    const HTTP_STATUS_CODE_INTERNAL_ERROR = 500;
    const DATA_MESSAGE = 'message';
    const DELETED_SUCCESSFULLY = 'deleted successfully';

    const PARAM_DATE_FROM = 'date_from';
    const PARAM_DATE_TO = 'date_to';

    const REQUEST_HEADER_APPLICATION_JSON = 'application/json';

    /**
     * Returns starting prefix for all message strings.
     *
     * @return string
     */
    public function getMessagePrefix()
    {
        return date('[Y-m-d H:i:s] ').'['.posix_getpid().'] ';
    }

    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @param $data
     * @param null|array $groups
     * @param null|bool  $withEmptyField
     *
     * @return View
     */
    protected function createSuccessResponse($data, array $groups = null, $withEmptyField = null)
    {
        $context = SerializationContext::create()->enableMaxDepthChecks();
        if ($groups) {
            $context->setGroups($groups);
        }

        if ($withEmptyField) {
            $context->setSerializeNull(true);
        }

        return View::create()
            ->setStatusCode(self::HTTP_STATUS_CODE_OK)
            ->setData($data)
            ->setSerializationContext($context);
    }

    /**
     * @param string $data
     *
     * @return View
     */
    protected function createSuccessStringResponse($data)
    {
        return View::create()
            ->setStatusCode(self::HTTP_STATUS_CODE_OK)
            ->setData([self::DATA_MESSAGE => $data]);
    }

    /**
     * @param Request $request
     * @param $requestType
     * @param $class
     * @param array $groups
     *
     * @return array|\JMS\Serializer\scalar|mixed|object|\Symfony\Component\HttpFoundation\Response|View
     */
    protected function validateEntites(Request $request, $requestType, $class, array $groups = [])
    {
        return $this->get('app.auth')->validateEntites(
            $requestType,
            $class,
            $groups
        );
    }
}
