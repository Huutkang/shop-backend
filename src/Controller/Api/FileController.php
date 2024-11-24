<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route('/file/{id}/delete', name: 'file_delete', methods: ['DELETE'])]
    public function delete(File $file, FileService $fileService): Response
    {
        $fileService->deleteFile($file);

        return $this->json([
            'message' => 'File deleted successfully!',
        ]);
    }
}
