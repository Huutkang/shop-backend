<?php
// namespace App\Service;

// class SearchService
// {
//     private UserService $usersService;
//     private ProductService $productService;
//     private CategoryService $categoryService;
//     private GroupService $groupService;
//     private OrderService $orderService;
//     private CartService $cartService;
//     private NotificationService $notificationService;



//     public function __construct(
//         UserService $usersService,
//         ProductService $productService,
//         CategoryService $categoryService,
//         GroupService $groupService,
//         OrderService $orderService,
//         CartService $cartService,
//         NotificationService $notificationService,
//     ) {
//         $this->usersService = $usersService;
//         $this->productService = $productService;
//         $this->categoryService = $categoryService;
//         $this->groupService = $groupService;
//         $this->orderService = $orderService;
//         $this->cartService = $cartService;
//         $this->notificationService = $notificationService;
//     }

//     public function searchAll(string $keywords): array
//     {

//         return [];
//     }

//     public function searchUser(string $keywords): array
//     {

//         return [];
//     }

//     public function searchGroup(string $keywords): array
//     {

//         return [];
//     }

//     public function searchProduct(string $keywords): array
//     {

//         return [];
//     }
//     public function searchProductInCategory(string $keywords): array
//     {

//         return [];
//     }
//     public function searchProductInCart(string $keywords): array
//     {

//         return [];
//     }
    
//     public function searchOrdersForUsers(string $keywords): array
//     {
//         return [];
//     }

//     public function smartSearchFresher(string $keywords): array
//     {
//         $byName = $this->smartSearchFresherByName($keywords);
//         $byProgrammingLanguage = $this->smartSearchFresherByProgrammingLanguage($keywords);
//         $byEmail = $this->smartSearchFresherByEmail($keywords);

//         return [
//             'name' => $byName,
//             'programmingLanguage' => $byProgrammingLanguage,
//             'email' => $byEmail,
//             'combined' => $this->mergeSearchResults($byName, $byProgrammingLanguage, $byEmail),
//         ];
//     }

//     private function mergeSearchResults(array ...$searchResults): array
//     {
//         $mergedResults = [];

//         foreach ($searchResults as $searches) {
//             foreach ($searches as $search) {
//                 $id = $search->getId();
//                 if (!isset($mergedResults[$id]) || $mergedResults[$id]['rank'] < $this->calculateRankForMerge($search)) {
//                     $mergedResults[$id] = ['search' => $search, 'rank' => $this->calculateRankForMerge($search)];
//                 }
//             }
//         }

//         return array_map(fn($item) => $item['search'], $mergedResults);
//     }

//     private function calculateRankForMerge(Search $search): int
//     {
//         return 10; // Adjust ranking logic as needed
//     }

//     public function findCenterByName(string $name): array
//     {
//         return $this->convertToListDTO($this->categoryService->findByName($name));
//     }

//     public function findProjectByName(string $name): array
//     {
//         return $this->convertToListDTO($this->groupService->findByName($name));
//     }

//     public function findProjectsByFresherId(int $fresherId): array
//     {
//         $projects = $this->cartService->findFresherProjects($fresherId, 0);
//         return array_map(fn($project) => $this->convertToDTO($project), $projects);
//     }

//     private function smartSearchFresherByEmail(string $email): array
//     {
//         return $this->rankedSearch($this->productService->findFreshersByEmail($email), $email, fn($fresher) => $fresher->getEmail());
//     }

//     private function smartSearchFresherByProgrammingLanguage(string $language): array
//     {
//         return $this->rankedSearch($this->productService->findFreshersByProgrammingLanguage($language), $language, fn($fresher) => $fresher->getProgrammingLanguage());
//     }

//     private function smartSearchFresherByName(string $name): array
//     {
//         return $this->rankedSearch($this->productService->findFreshersByName($name), $name, fn($fresher) => $fresher->getName());
//     }

//     private function rankedSearch(array $freshers, string $keyword, callable $extractor): array
//     {
//         $results = [];

//         foreach ($freshers as $fresher) {
//             $target = $extractor($fresher);
//             $rank = $this->calculateRank($keyword, $target);
//             if ($rank > 0) {
//                 $results[] = ['search' => $this->convertToDTO($fresher), 'rank' => $rank];
//             }
//         }

//         usort($results, fn($a, $b) => $b['rank'] <=> $a['rank']);
//         return array_map(fn($item) => $item['search'], $results);
//     }

//     private function calculateRank(string $content, string $target): int
//     {
//         $content = trim(strtolower($content));
//         $target = trim(strtolower($target));

//         if ($content === $target) return 10;
//         if (str_contains($target, $content)) return 9;

//         $levenshteinDistance = levenshtein($content, $target);
//         if ($levenshteinDistance <= 2) return 8;

//         foreach (explode(' ', $target) as $part) {
//             if (str_contains($part, $content)) return 7;
//         }

//         if (str_starts_with($target, $content) || str_ends_with($target, $content)) return 6;

//         return 0;
//     }
// }
