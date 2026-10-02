<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Item;

class CatalogController extends Controller
{
    public function index(): void
    {
        $categorySlug = $_GET['category'] ?? null;
        $search = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;

        $selectedCategory = null;
        $categoryId = null;

        if ($categorySlug) {
            $selectedCategory = Category::findBySlug($categorySlug);
            if ($selectedCategory) {
                $categoryId = (int)$selectedCategory['id'];
            }
        }

        $totalItems = Item::countActive($categoryId, $search);
        $totalPages = ceil($totalItems / $perPage);
        $offset = ($page - 1) * $perPage;

        $items = Item::allActive($categoryId, $search, $perPage, $offset);
        $categories = Category::all();

        $this->render('catalog/index', [
            'title' => 'Katalog Alat Pendakian — Mountrawr',
            'items' => $items,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ]);
    }

    public function show(string $slug): void
    {
        $item = Item::findBySlug($slug);
        if (!$item) {
            http_response_code(404);
            require_once BASE_PATH . '/app/Views/errors/404.php';
            exit;
        }

        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+2 days'));

        $availableStock = Item::availableStock((int)$item['id'], $startDate, $endDate);

        // Build 30-day availability calendar matrix
        $calendar = [];
        $startDt = new \DateTime();
        for ($i = 0; $i < 30; $i++) {
            $currentDateStr = $startDt->format('Y-m-d');
            $stockOnDay = Item::availableStock((int)$item['id'], $currentDateStr, $currentDateStr);
            $calendar[] = [
                'date' => $currentDateStr,
                'day' => $startDt->format('d'),
                'day_name' => $startDt->format('D'),
                'month' => $startDt->format('M'),
                'available' => $stockOnDay,
                'is_available' => ($stockOnDay > 0)
            ];
            $startDt->modify('+1 day');
        }

        // Calculate estimated total price
        $startObj = new \DateTime($startDate);
        $endObj = new \DateTime($endDate);
        $days = max(1, $startObj->diff($endObj)->days + 1);
        $estimatedPrice = $days * (float)$item['price_per_day'];

        $this->render('catalog/show', [
            'title' => e($item['name']) . ' — Mountrawr',
            'item' => $item,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'days' => $days,
            'availableStock' => $availableStock,
            'estimatedPrice' => $estimatedPrice,
            'calendar' => $calendar
        ]);
    }
}
