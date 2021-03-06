<?php

namespace AppBundle\Services;

use AppBundle\Controller\Api\AbstractRestController;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
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
     * @param $requestType
     * @param $class
     * @param array $groups
     * @param array $data
     *
     * @throws \Exception|ValidatorException
     *
     * @return object
     */
    public function validateEntites($requestType, $class, array $groups = [], $data = [])
    {
        $paramRequest = $this->requestStack->getCurrentRequest();
        if (0 === strpos(
                $paramRequest
                    ->headers->get(
                        'content_type'
                    ),
                    AbstractRestController::REQUEST_HEADER_APPLICATION_JSON
            ) && !$data) {
            $dataJson = $paramRequest->getContent();

            if ($paramRequest->request->get('id')) {
                $dataJson = $this->mergeData('id', $dataJson);
            }

            if ($paramRequest->request->get('admin')) {
                $dataJson = $this->mergeData('admin', $dataJson);
            }

            if ($paramRequest->request->get('user')) {
                $dataJson = $this->mergeData('user', $dataJson);
            }
            $serializedData = $dataJson;
        } elseif ($data) {
            $serializedData = $data;
        } else {
            $serializedData = $this->requestStack->getCurrentRequest()->$requestType->all();
        }

        if (is_array($serializedData)) {
            $serializedData = (array_filter($serializedData, function($v, $k) {
                if ($v === 'null') {
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_BOTH));
        }

        return $this->processEntity($groups, $serializedData, $class);
    }

    /**
     * @param $groups
     * @param $serializedData
     * @param $class
     *
     * @throws ValidatorException
     *
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function processEntity($groups, $serializedData, $class)
    {
        if (is_array($serializedData)) {
            $serializedData = $this->getSerializer()
                ->serialize($serializedData, 'json');
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
     * @param $data
     * @param $dataJson
     *
     * @return string
     */
    private function mergeData($data, $dataJson)
    {
        $authData = $this->getSerializer()
            ->serialize(
                [
                    $data => $this->requestStack
                        ->getCurrentRequest()->request->get($data),
                ],
                'json'
            );

        return json_encode(
            array_merge(
                (array) json_decode($authData),
                (array) json_decode($dataJson)
            )
        );
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
            $validatorException->setConstraintViolatinosList($errors);

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
