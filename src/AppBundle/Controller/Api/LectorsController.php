<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Lectors;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LectorsController extends AbstractRestController
{
    /**
     * Get lector by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/lector/{id} <br>.
     *
     * @Rest\Get("/api/lector/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get lector by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Lector"
     * )
     *
     * @RestView()
     *
     * @param Lectors $lectors
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getLectorsAction(Lectors $lectors)
    {
        return $this->createSuccessResponse($lectors, ['get_lector'], true);
    }

    /**
     * Get list lectors.
     * <strong>Simple example:</strong><br />
     * http://host/api/lectors <br>.
     *
     * @Rest\Get("/api/lectors")
     * @ApiDoc(
     * resource = true,
     * description = "Get list lectors",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Lector"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - first_name, last_name")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getAdminLectorAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $lectors = $em->getRepository('AppBundle:Lectors');

            return $this->createSuccessResponse(
                [
                    'lectors' => $lectors->getEntitiesByParams($paramFetcher),
                    'total' => $lectors->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_lectors'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create lector by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/lector <br>.
     *
     * @Rest\Post("/api/admins/lector")
     * @ApiDoc(
     * resource = true,
     * description = "Create lector by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="first_name", "dataType"="string", "required"=false, "description"="lector first name"},
     *      {"name"="last_name", "dataType"="string", "required"=false, "description"="lector last name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Lector"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminLectorsAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var Lectors $lectors */
            $lectors = $auth->validateEntites('request', Lectors::class, ['post_lector']);

            $em->persist($lectors);
            $em->flush();

            return $this->createSuccessResponse($lectors, ['get_lector'], true);
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Put lector by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/lector/{id} <br>.
     *
     * @Rest\Put("/api/admins/lector/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put lector by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="first_name", "dataType"="string", "required"=false, "description"="lector first name"},
     *      {"name"="last_name", "dataType"="string", "required"=false, "description"="lector last name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Lector"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     * @param Lectors $lectors
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminLectorsAction(Request $request, Lectors $lectors)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $lectors->getId());
            /** @var Lectors $lectors */
            $lectors = $auth->validateEntites('request', Lectors::class, ['put_lector']);

            $em->flush();

            return $this->createSuccessResponse($lectors, ['get_lector'], true);
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Delete lector by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/lector/{id} <br>.
     *
     * @Rest\Delete("/api/admins/lector/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete lector by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins Lector"
     * )
     *
     * @RestView()
     *
     * @param Lectors $lectors
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedLectorsAction(Lectors $lectors)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($lectors);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
