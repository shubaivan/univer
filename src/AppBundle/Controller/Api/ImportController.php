<?php

namespace AppBundle\Controller\Api;

use AppBundle\Domain\Favorites\FavoritesDomain;
use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Favorites;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Model\Request\FavoritesRequestModel;
use AppBundle\Services\ObjectManager;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImportController extends AbstractRestController
{
    /**
     * Import Questions.
     * <strong>Simple example:</strong><br />
     * http://host/admins/import/questions <br>.
     *
     * @Rest\Post("/admins/import/questions")
     * @ApiDoc(
     * resource = true,
     * description = "Import Questions",
     * authentication=true,
     *  parameters={
     *      {"name"="file", "dataType"="file", "required"=true, "format"="xlsx", "description"="only xlsx"},
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Import"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @param Request $request
     *
     * @return Response|View
     */
    public function postImportQuestionsAction(Request $request)
    {
        $logger = $this->container->get('logger');

        try {
            if (!$request->files->get('file') instanceof UploadedFile
                || $request->files->get('file')->getClientOriginalExtension() !== 'xlsx'
            ) {
                throw new FileException('only xlsx');
            }
            $file = $request->files->get('file');
            $this->container->get('app.service.import_manager')
                ->importQuestions($file->getPathname());
            return $this->createSuccessStringResponse('success');
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
