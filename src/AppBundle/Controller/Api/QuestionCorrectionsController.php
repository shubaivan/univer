<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\QuestionCorrections;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Helper\FileUploader;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class QuestionCorrectionsController extends AbstractRestController
{
    /**
     * Get QuestionCorrections by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/question_corrections/{id} <br>.
     *
     * @Rest\Get("/api/question_corrections/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get question by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question_Corrections"
     * )
     *
     * @RestView()
     *
     * @param QuestionCorrections $questionCorrections
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getQuestionCorrectionsAction(QuestionCorrections $questionCorrections)
    {
        return $this->createSuccessResponse(
            $questionCorrections,
            ['get_question_correction'],
            true
        );
    }

    /**
     * Get list corrections questions.
     * <strong>Simple example:</strong><br />
     * http://host/api/questions_corrections <br>.
     *
     * @Rest\Get("/api/questions_corrections")
     * @ApiDoc(
     * resource = true,
     * description = "Get list corrections questions",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question_Corrections"
     * )
     *
     * @RestView()
     *
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
    public function getQuestionsCorrectionsAction(ParamFetcher $paramFetcher)
    {
        try {
            $this->prepareAuthor();

            $questions = $this->getDoctrine()
                ->getRepository(QuestionCorrections::class);

            return $this->createSuccessResponse(
                [
                    'questions_corrections' => $questions->getEntitiesByParams($paramFetcher),
                    'total' => $questions->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_questions_corrections'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create/Put questionCorrections .
     * <strong>Simple example:</strong><br />
     * http://host/api/question_corrections <br>.
     *
     * @Rest\Post("/api/question_corrections")
     * @ApiDoc(
     * resource = true,
     * description = "Create/Put questionCorrections  ",
     * authentication=true,
     *  parameters={
     *      {"name"="id", "dataType"="string", "required"=false, "description"="question id"},
     *      {"name"="custom_id", "dataType"="string", "required"=false, "description"="custom id"},
     *      {"name"="year", "dataType"="integer", "required"=false, "description"="year"},
     *      {"name"="type", "dataType"="enum", "required"=true, "description"="open or test"},
     *      {"name"="question_number", "dataType"="integer", "required"=false, "description"="question number"},
     *      {"name"="notes", "dataType"="text", "required"=false, "description"="notes"},
     *      {"name"="text", "dataType"="text", "required"=true, "description"="notes"},
     *      {"name"="semesters", "dataType"="integer", "required"=true, "description"="semesters id"},
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions_id"},
     *      {"name"="exam_periods", "dataType"="integer", "required"=true, "description"="exam periods id"},
     *      {"name"="sub_courses", "dataType"="integer", "required"=true, "description"="sub courses id"},
     *      {"name"="lectors", "dataType"="integer", "required"=true, "description"="lectors id"},
     *      {"name"="image_url", "dataType"="file", "required"=false, "description"="file for upload"},
     *      {"name"="question_answers_corrections", "dataType"="array", "required"=false, "description"="question answers array objects"},
     *      {"name"="courses", "dataType"="integer", "required"=true, "description"="courses id or object"},
     *      {"name"="courses_of_study", "dataType"="integer", "required"=true, "description"="coursesOfStudy id or object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question_Corrections"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postQuestionCorrectionsAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $serializerGroup = 'post_question_corrections';
            $persist = true;
            $questionCorrections = null;
            if ($request->get('id')) {
                $questionCorrections = $em->getRepository('AppBundle\Entity\QuestionCorrections')
                    ->findOneBy(['id' => $request->get('id')]);
                if ($questionCorrections instanceof QuestionCorrections) {
                    $request->request->set('id', $questionCorrections->getId());
                    $serializerGroup = 'put_question_corrections';
                    $persist = false;

                    if ($this->getUser() instanceof User && $questionCorrections->getUser() !== $this->getUser()) {
                        throw new AccessDeniedException();
                    }
                }
            }

            $this->prepareAuthor();

            /** @var QuestionCorrections $questionCorrections */
            $questionCorrections = $auth->validateEntites('request', QuestionCorrections::class, [$serializerGroup]);

            if ($request->files->get('image_url') instanceof UploadedFile) {
                $service = $this->get('app.file_uploader');

                $fileName = $service->upload(
                    $request->files->get('image_url'),
                    FileUploader::LOCAL_STORAGE
                );

                $questionCorrections->setImageUrl('/files/'.$fileName);
            }

            !$persist ?: $em->persist($questionCorrections);
            $em->flush();

            return $this->createSuccessResponse($questionCorrections, ['get_question_corrections'], true);
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
