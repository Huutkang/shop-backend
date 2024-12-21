<?php

namespace App\Controller\Api;

use App\Service\CouponService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\CouponDto;



#[Route('/api/coupons', name: 'coupon_')]
class CouponController extends AbstractController
{
    private CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $coupons = $this->couponService->getAllCoupons();
            $couponDtos = array_map(fn($coupon) => new CouponDto($coupon), $coupons);
            return $this->json($couponDtos);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to fetch coupons', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $coupon = $this->couponService->getCouponById($id);
            if (!$coupon) {
                throw new AppException('Coupon not found', 404);
            }
            return $this->json(new CouponDto($coupon));
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return $this->json(['message' => 'Internal server error'], 500);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $coupon = $this->couponService->createCoupon($data);
            return $this->json(new CouponDto($coupon), 201);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Internal server error'], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $coupon = $this->couponService->updateCoupon($id, $data);
            return $this->json(new CouponDto($coupon));
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Internal server error'], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->couponService->deleteCoupon($id);
            return $this->json(['message' => 'Coupon deleted']);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Internal server error'], 500);
        }
    }
}
