<?php

namespace AppBundle\Helper;

use Gaufrette\Adapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem;

class FileUploader
{
    const LOCAL_STORAGE = 'local_storage';

    /**
     * @var array
     */
    private static $allowedMimeTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif'
    );

    /**
     * @var array
     */
    private $filesystem;


    /**
     * FileUploader constructor.
     * @param Filesystem $localStorage
     */
    public function __construct(
        Filesystem $localStorage
    ) {
        $this->filesystem[self::LOCAL_STORAGE] = $localStorage;


    }

    /**
     * @param UploadedFile $file
     * @param $target
     * @param null $allowedMimeTypesArray
     * @return mixed
     */
    public function upload(
        UploadedFile $file,
        $target,
        $allowedMimeTypesArray = null
    ) {
        if (!$allowedMimeTypesArray) {
            $allowedMimeTypesArray = self::$allowedMimeTypes;
        }
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), $allowedMimeTypesArray)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Files of type %s are not allowed.',
                    $file->getClientMimeType()
                )
            );
        }
        $filename = sprintf(
            '%s/%s/%s/%s.%s',
            date('Y'),
            date('m'),
            date('d'),
            uniqid(),
            $file->getClientOriginalExtension()
        );

        /** @var Adapter $adapter */
        $adapter = $this->filesystem[$target]->getAdapter();
        $adapter->write($filename, file_get_contents($file->getPathname()));

        return $filename;
    }

    /**
     * @param $path
     * @param $target
     * @return bool|string
     */
    public function read(
        $path,
        $target
    ) {
        /** @var Adapter $adapter */
        $adapter = $this->filesystem[$target]->getAdapter();
        $result = $adapter->read($path);

        return $result;
    }
}
