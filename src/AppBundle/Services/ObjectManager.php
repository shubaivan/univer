<?php

namespace AppBundle\Services;

use AppBundle\Controller\Api\AbstractRestController;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectManager
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * Authenticator constructor.
     *
     * @param Serializer            $serializer
     * @param ValidatorInterface    $validatorInterface
     * @param RequestStack          $requestStack
     * @param TokenStorageInterface $tokenStorageInterface
     */
    public function __construct(
        Serializer $serializer,
        ValidatorInterface $validatorInterface,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorageInterface
    ) {
        $this->serializer = $serializer;
        $this->validator = $validatorInterface;
        $this->requestStack = $requestStack;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    /**
     * @param string $requestType
     * @param string $class
     * @param array  $groups
     *
     * @return array|\JMS\Serializer\scalar|mixed|object|View
     */
    public function validateEntites($requestType, $class, array $groups = [])
    {
        $paramRequest = $this->requestStack->getCurrentRequest();
        if (0 === strpos(
                $paramRequest
                    ->headers->get(
                        'content_type'
                    ),
                    AbstractRestController::REQUEST_HEADER_APPLICATION_JSON
            )) {
            $dataJson = $paramRequest->getContent();
            $authUser = $this->getUser();
            if ($paramRequest->request->get('id')) {
                $authData = $this->getSerializer()
                    ->serialize(['id' => $authUser->getId()], 'json');
                $dataJson = json_encode(
                    array_merge(
                        (array) json_decode($authData),
                        (array) json_decode($dataJson)
                    )
                );
            }
            $serializedData = $dataJson;
        } else {
            $data = $this->requestStack->getCurrentRequest()->$requestType->all();
            $serializedData = $this->getSerializer()
                ->serialize($data, 'json');
        }

        $deserializationContext = null;
        $validateGroups = [];
        if ($groups) {
            $deserializationContext = DeserializationContext::create()->setGroups($groups);
            $validateGroups = $groups;
        }

        $dataValidate = $this->getSerializer()
                ->deserialize(
                    $serializedData,
                    $class,
                    'json',
                    $deserializationContext
            );

        $this->validateEntity($dataValidate, $validateGroups);

        return $dataValidate;
    }

    /**
     * @param object $entity
     * @param array  $validateGroups
     *
     * @throws ValidatorException
     */
    private function validateEntity(
        $entity,
        array $validateGroups = []
    ) {
        $validateGroups = $validateGroups ? $validateGroups : null;
        $errors = $this->getValidatorInterface()
            ->validate($entity, null, $validateGroups);
        if (count($errors)) {
            $validatorException = new ValidatorException();
            $validatorException->addError([$errors]);

            throw $validatorException;
        }
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidatorInterface()
    {
        return $this->validator;
    }

    /**
     * @return null|User
     */
    private function getUser()
    {
        return $this->getTokenStorageInterface()
            ->getToken()->getUser();
    }

    /**
     * @return TokenStorageInterface
     */
    private function getTokenStorageInterface()
    {
        return $this->tokenStorageInterface;
    }
}
