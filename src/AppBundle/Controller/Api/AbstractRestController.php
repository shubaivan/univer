<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Admin;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    /**
     * @return \AppBundle\Application\SubCourses\SubCoursesApplication|object
     */
    protected function getSubCoursesApplication()
    {
        return $this->get('app.application.sub_courses_application');
    }

    protected function prepareAuthor()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        /** @var AbstractUser $authUser */
        $authUser = $this->getUser();

        if ($authUser instanceof User) {
            $request->request->set('user', $this->getUser()->getId());
        } elseif ($authUser instanceof Admin) {
            $request->request->set('admin', $this->getUser()->getId());
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return ParamFetcher
     */
    protected function responsePrepareAuthor(ParamFetcher $paramFetcher)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        /** @var AbstractUser $authUser */
        $authUser = $this->getUser();

        if ($authUser instanceof User) {
            $request->query->set('user', $this->getUser()->getId());
            $param = new QueryParam();
            $param->name = 'user';
            $paramFetcher->addParam($param);
        }

        return $paramFetcher;
    }
}
