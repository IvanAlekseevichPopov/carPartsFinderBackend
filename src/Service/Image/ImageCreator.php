<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Entity\File\PartImage;
use App\Entity\Part;
use App\Service\Guzzle\DownloadClientInterface;
use GuzzleHttp\ClientInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageCreator
{
    public const MAX_FILE_SIZE = 204962048; // 2mb
    public const VALID_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];
    public const MIN_FILE_SIZE = 10240; // 10kb

    private DataStorageOperator $dataStorageOperator;
    private FilesystemOperator $fileSystemOperator;
    private ClientInterface $imageDownloader;
    private string $cacheDir;

    public function __construct(
        DataStorageOperator $dataStorage,
        FilesystemOperator $operator,
        DownloadClientInterface $imageDownloader,
        string $cacheDir
    ) {
        $this->dataStorageOperator = $dataStorage;
        $this->fileSystemOperator = $operator;
        $this->imageDownloader = $imageDownloader;
        $this->cacheDir = $cacheDir;
    }

    public function createImage(Part $part, UserInterface $uploadedBy, string $imageUrl): PartImage
    {
        // TODO check that file was not uploaded before
        $file = $this->downloadFile($imageUrl);

        $entity = $this->dataStorageOperator->write($part, $uploadedBy, $file);

        try {
            $this->fileSystemOperator->write($entity->getStorageFilePath(), $file->getContent());
        } catch (\Throwable $e) {
            $this->dataStorageOperator->delete($entity);
            throw $e;
        }

        return $entity;
    }

    private function downloadFile(string $imageUrl): File
    {
        $localFilePath = $this->cacheDir.'/files'.uniqid();
        $response = $this->imageDownloader->request(
            'GET',
            $imageUrl, [
                'on_headers' => function (ResponseInterface $response) {
                    $size = $response->getHeaderLine('Content-Length');
                    if ($size > self::MAX_FILE_SIZE) {
                        throw new \RuntimeException('The file is too big!'); // TODO custom exception
                    }
                    if ($size < self::MIN_FILE_SIZE) {
                        throw new \RuntimeException('The file is too small!'); // TODO custom exception
                    }

                    $mime = $response->getHeaderLine('Content-Type');
                    if (!in_array($mime, self::VALID_MIME_TYPES)) {
                        throw new \RuntimeException('Invalid mime type: '.$mime);  // TODO custom exception
                    }
                },
                'sink' => $localFilePath,
            ]
        );

//       return new UploadedFile($localFilePath, uniqid(),$response->getHeaderLine('Content-Type'));

        return new File($localFilePath);
    }
}
