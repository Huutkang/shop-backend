<?php

namespace App\Dto;

use App\Entity\File;

class FileDto
{
    public int $id;
    public int $userId;
    public string $fileName;
    public string $filePath;
    public int $fileSize;
    public ?int $sort;
    public string $uploadedAt;
    public bool $isActive;
    public ?int $productId;
    public ?int $reviewId;
    public ?string $description;

    public function __construct(File $file)
    {
        $this->id = $file->getId();
        $this->userId = $file->getUser()?->getId();
        $this->fileName = $file->getFileName();
        $this->filePath = $file->getFilePath();
        $this->fileSize = $file->getFileSize();
        $this->sort = $file->getSort();
        $this->uploadedAt = $file->getUploadedAt()->format('Y-m-d H:i:s');
        $this->isActive = $file->getIsActive();
        $this->productId = $file->getProduct()?->getId();
        $this->reviewId = $file->getReview()?->getId();
        $this->description = $file->getDescription();
    }
}
