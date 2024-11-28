<?php

namespace App\Service;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Exception\AppException;



class FileService
{
    private UserService $userService;
    private FileRepository $fileRepository;
    private Filesystem $filesystem;
    private EntityManagerInterface $entityManager;
    private ProductService $productService;
    private ReviewService $reviewService;

    public function __construct(
        UserService $userService,
        FileRepository $fileRepository,
        Filesystem $filesystem,
        EntityManagerInterface $entityManager,
        ProductService $productService,
        ReviewService $reviewService
    ) {
        $this->userService = $userService;
        $this->fileRepository = $fileRepository;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    // Lấy tất cả các file
    public function getAllFiles(): array
    {
        return $this->fileRepository->findAll();
    }

    // Lấy tất cả các file với phân trang
    public function getFilesPaginated(int $page, int $limit): Paginator
    {
        return $this->fileRepository->findAllPaginated($page, $limit);
    }

    // Lấy file theo ID
    public function getFileById(int $id): ?File
    {
        return $this->fileRepository->findById($id);
    }

    // Lấy file theo tên
    public function getFilesByName(string $fileName): array
    {
        return $this->fileRepository->findByName($fileName);
    }

    // Lấy danh sách file theo User
    public function getFilesByUser(int $userId): array
    {
        return $this->fileRepository->findByUser($userId);
    }

    // Lấy danh sách file theo Product
    public function getFilesByProduct(int $productId, bool $onlyActive = true): array
    {
        return $this->fileRepository->findByProduct($productId, $onlyActive);
    }

    // Lấy danh sách file theo Review
    public function getFilesByReview(int $reviewId, bool $onlyActive = true): array
    {
        return $this->fileRepository->findByReview($reviewId, $onlyActive);
    }

    // Lấy file trong khoảng thời gian tải lên
    public function getFilesByUploadedDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->fileRepository->findByUploadedDateRange($start, $end);
    }

    // Lấy danh sách file không hoạt động
    public function getInactiveFiles(): array
    {
        return $this->fileRepository->findInactiveFiles();
    }

    function generateRandomString(int $length = 32): string {
        // Sử dụng random_bytes để tạo chuỗi an toàn
        return substr(bin2hex(random_bytes(ceil($length / 2))), 0, $length);
    }

    public function createFile(array $data): File
    {
        $file = new File();

        if (!empty($data['userId'])) {
            $user = $this->userService->getUserById($data['userId']);
            if (!$user) {
                throw new AppException("E1004");
            }
            $file->setUser($user);
        }else{
            throw new AppException("E10700"); 
        }
        $file->setFileName($data['fileName'] ?? throw new AppException('E10701')) // File name required
            ->setFilePath($this->generateRandomString())
            ->setFileSize($data['fileSize'] ?? throw new AppException('E10702')) // File size required
            ->setUploadedAt(new \DateTime())
            ->setIsActive($data['isActive'] ?? true)
            ->setDescription($data['description'] ?? null)
            ->setSort($data['sort'] ?? null);

        // Set User if userId is provided
        

        // Set Product if productId is provided
        if (!empty($data['productId'])) {
            $product = $this->productService->getProductById($data['productId']);
            if (!$product) {
                throw new AppException("E10200"); // Product not found
            }
            $file->setProduct($product);
        }

        // Set Review if reviewId is provided
        if (!empty($data['reviewId'])) {
            $review = $this->reviewService->getReviewById( $data['reviewId']);
            if (!$review) {
                throw new AppException("E10600"); // Review not found
            }
            $file->setReview($review);
        }

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    public function updateFile(File $file, array $data): File
    {
        // Update User if userId is provided
        if (isset($data['userId'])) {
            $user = $this->userService->getUserById($data['userId']);
            if (!$user) {
                throw new AppException("E1004"); // User not found
            }
            $file->setUser($user);
        }
    
        // Update File data
        $file->setFileName($data['fileName'] ?? $file->getFileName())
            ->setFileSize($data['fileSize'] ?? $file->getFileSize())
            ->setIsActive($data['isActive'] ?? $file->getIsActive())
            ->setDescription($data['description'] ?? $file->getDescription())
            ->setSort($data['sort'] ?? $file->getSort());

        
        // Update Product if productId is provided
        if (isset($data['productId'])) {
            $product = $this->productService->getProductById($data['productId']);
            if (!$product) {
                throw new AppException("E10200"); // Product not found
            }
            $file->setProduct($product);
        }

        // Update Review if reviewId is provided
        if (isset($data['reviewId'])) {
            $review = $this->reviewService->getReviewById( $data['reviewId']);
            if (!$review) {
                throw new AppException("E10600"); // Review not found
            }
            $file->setReview($review);
        }
        
        $this->entityManager->flush();
        return $file;
    }

    // Kích hoạt hoặc vô hiệu hóa file
    public function toggleFileStatus(File $file): void
    {
        $file->setIsActive(!$file->getIsActive());
        $this->entityManager->flush();
    }

    // Xóa file
    public function deleteFile(File $file): void
    {
        if ($this->filesystem->exists($file->getFilePath())) {
            $this->filesystem->remove($file->getFilePath());
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }
}
