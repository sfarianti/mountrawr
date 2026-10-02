<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Item;

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = Category::all();
        $featuredItems = Item::allActive(null, null, 6);
        $this->render('home/index', [
            'title' => 'Mountrawr — Rental Alat Pendakian Gunung Premium',
            'categories' => $categories,
            'featuredItems' => $featuredItems
        ]);
    }
}
