<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Dto\FileDto;
use App\Exception\AppException;


#[Route('/api/files', name: 'api_files_')]
class FileController extends AbstractController
{
    /**
     * Lấy tất cả file (không phân trang).
     */
    #[Route('/all', name: 'get_all_files', methods: ['GET'])]
    public function getAllFiles(FileService $fileService): JsonResponse
    {
        $files = $fileService->getAllFiles();

        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Lấy tất cả file (có thể phân trang).
     */
    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getFilesPaginated(Request $request, FileService $fileService): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $files = $fileService->getFilesPaginated($page, $limit);

        // Chuyển Paginator thành mảng trước khi áp dụng array_map
        $fileDtos = array_map(fn ($file) => new FileDto($file), iterator_to_array($files));

        return $this->json($fileDtos, 200);
    }


    /**
     * Lấy thông tin file theo ID.
     */
    #[Route('/{id}', name: 'get_by_id', methods: ['GET'])]
    public function getFileById(int $id, FileService $fileService): JsonResponse
    {
        $file = $fileService->getFileById($id);

        if (!$file) {
            return $this->json(['error' => 'File not found'], 404);
        }

        return $this->json(new FileDto($file), 200);
    }

    /**
     * Lấy file theo tên.
     */
    #[Route('/search', name: 'get_by_name', methods: ['GET'])]
    public function getFilesByName(Request $request, FileService $fileService): JsonResponse
    {
        $fileName = $request->query->get('name', '');

        if (empty($fileName)) {
            return $this->json(['error' => 'File name is required'], 400);
        }

        $files = $fileService->getFilesByName($fileName);
        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Lấy danh sách file theo User ID.
     */
    #[Route('/user/{userId}', name: 'by_user', methods: ['GET'])]
    public function getFilesByUser(int $userId, FileService $fileService): JsonResponse
    {
        $files = $fileService->getFilesByUser($userId);
        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Lấy danh sách file theo Product ID.
     */
    #[Route('/product/{productId}', name: 'by_product', methods: ['GET'])]
    public function getFilesByProduct(int $productId, Request $request, FileService $fileService): JsonResponse
    {
        $onlyActive = $request->query->getBoolean('onlyActive', true);
        $files = $fileService->getFilesByProduct($productId, $onlyActive);
        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Lấy danh sách file theo Review ID.
     */
    #[Route('/review/{reviewId}', name: 'by_review', methods: ['GET'])]
    public function getFilesByReview(int $reviewId, Request $request, FileService $fileService): JsonResponse
    {
        $onlyActive = $request->query->getBoolean('onlyActive', true);
        $files = $fileService->getFilesByReview($reviewId, $onlyActive);
        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Lấy danh sách file không hoạt động.
     */
    #[Route('/inactive', name: 'inactive_files', methods: ['GET'])]
    public function getInactiveFiles(FileService $fileService): JsonResponse
    {
        $files = $fileService->getInactiveFiles();
        $fileDtos = array_map(fn ($file) => new FileDto($file), $files);

        return $this->json($fileDtos, 200);
    }

    /**
     * Tạo mới một file.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, FileService $fileService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $file = $fileService->createFile($data);
            return $this->json(new FileDto($file), 201);
        } catch (AppException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } 
    }

    /**
     * Cập nhật thông tin file.
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateFile(Request $request, File $file, FileService $fileService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $updatedFile = $fileService->updateFile($file, $data);

        return $this->json(new FileDto($updatedFile), 200);
    }

    /**
     * Bật hoặc tắt trạng thái hoạt động của file.
     */
    #[Route('/{id}/toggle-status', name: 'toggle_status', methods: ['PATCH'])]
    public function toggleFileStatus(File $file, FileService $fileService): JsonResponse
    {
        $fileService->toggleFileStatus($file);

        return $this->json(['message' => 'File status updated successfully!']);
    }

    /**
     * Xóa một file.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteFile(File $file, FileService $fileService): JsonResponse
    {
        $fileService->deleteFile($file);

        return $this->json(['message' => 'File deleted successfully!']);
    }
}
