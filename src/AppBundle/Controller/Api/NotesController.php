<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Admin;
use AppBundle\Entity\Notes;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NotesController extends AbstractRestController
{
    /**
     * Get note by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/note/{id} <br>.
     *
     * @Rest\Get("/api/note/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get note by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Note"
     * )
     *
     * @RestView()
     *
     * @param Notes $notes
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getNotesAction(Notes $notes)
    {
        return $this->createSuccessResponse($notes, ['get_note'], true);
    }

    /**
     * Get list notes.
     * <strong>Simple example:</strong><br />
     * http://host/api/notes <br>.
     *
     * @Rest\Get("/api/notes")
     * @ApiDoc(
     * resource = true,
     * description = "Get list notes",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Note"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - text")
     * @Rest\QueryParam(name="questions", requirements="\d+", description="questions id")
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
    public function getNoteAction(Request $request, ParamFetcher $paramFetcher)
    {
        try {
            /** @var AbstractUser $authUser */
            $authUser = $this->getUser();
            if ($authUser->hasRole(AbstractUser::ROLE_USER)) {
                $request->query->set('user', $this->getUser()->getId());
                $param = new Rest\QueryParam();
                $param->name = 'user';
                $paramFetcher->addParam($param);
            }

            $subCourses = $this->getSubCoursesApplication()
                ->getSubCoursesCollection($paramFetcher);

            return $this->createSuccessResponse(
                $subCourses,
                ['get_sub_courses'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create note.
     * <strong>Simple example:</strong><br />
     * http://host/api/note <br>.
     *
     * @Rest\Post("/api/note")
     * @ApiDoc(
     * resource = true,
     * description = "Create note",
     * authentication=true,
     *  parameters={
     *      {"name"="text", "dataType"="string", "required"=true, "description"="note text"},
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Note"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postNotesAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            $this->prepareAuthor();

            /** @var Notes $notes */
            $notes = $auth->validateEntites('request', Notes::class, ['post_note']);

            $em->persist($notes);
            $em->flush();

            return $this->createSuccessResponse($notes, ['get_note'], true);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Put note.
     * <strong>Simple example:</strong><br />
     * http://host/api/note/{id} <br>.
     *
     * @Rest\Put("/api/note/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put note",
     * authentication=true,
     *  parameters={
     *      {"name"="first_name", "dataType"="string", "required"=false, "description"="note first name"},
     *      {"name"="last_name", "dataType"="string", "required"=false, "description"="note last name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Note"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     * @param Notes $notes
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putNotesAction(Request $request, Notes $notes)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $notes->getId());
            /** @var Notes $notes */
            $notes = $auth->validateEntites('request', Notes::class, ['put_note']);

            $em->flush();

            return $this->createSuccessResponse($notes, ['get_note'], true);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Delete note.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/note/{id} <br>.
     *
     * @Rest\Delete("/api/note/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete note",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Note"
     * )
     *
     * @RestView()
     *
     * @param Notes $notes
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedNotesAction(Notes $notes)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($notes);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
