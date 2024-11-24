<?php

namespace App\Service;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Component\Filesystem\Filesystem;

class FileService
{
    private FileRepository $fileRepository;
    private Filesystem $filesystem;

    public function __construct(FileRepository $fileRepository, Filesystem $filesystem)
    {
        $this->fileRepository = $fileRepository;
        $this->filesystem = $filesystem;
    }

    public function deleteFile(File $file): void
    {
        // Xóa file thực tế
        if ($this->filesystem->exists($file->getFilePath())) {
            $this->filesystem->remove($file->getFilePath());
        }

        // Xóa bản ghi trong database
        $this->fileRepository->remove($file);
    }

    public function getFilesByProduct(int $productId): array
    {
        return $this->fileRepository->findByProduct($productId);
    }

    public function getFilesByReview(int $reviewId): array
    {
        return $this->fileRepository->findByReview($reviewId);
    }
}


