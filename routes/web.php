<?php

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\LoanController;
use App\Controllers\Admin\LoanController as AdminLoanController;
use App\Controllers\Admin\ItemController as AdminItemController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;

// Public & Home
Router::get('/', [HomeController::class, 'index']);

// Guest Auth
Router::get('/login', [AuthController::class, 'showLogin'], ['guest']);
Router::post('/login', [AuthController::class, 'login'], ['guest']);
Router::get('/register', [AuthController::class, 'showRegister'], ['guest']);
Router::post('/register', [AuthController::class, 'register'], ['guest']);

// Auth User
Router::post('/logout', [AuthController::class, 'logout'], ['auth']);

// Catalog
Router::get('/catalog', [CatalogController::class, 'index']);
Router::get('/catalog/{slug}', [CatalogController::class, 'show']);

// Loans (Peminjam)
Router::get('/loans', [LoanController::class, 'index'], ['auth']);
Router::get('/loans/history', [LoanController::class, 'history'], ['auth']);
Router::post('/loans', [LoanController::class, 'store'], ['auth']);
Router::get('/loans/{id}', [LoanController::class, 'show'], ['auth']);
Router::post('/loans/{id}/cancel', [LoanController::class, 'cancel'], ['auth']);
Router::post('/loans/{id}/return', [LoanController::class, 'returnItem'], ['auth']);

// Admin Loans
Router::get('/admin/loans', [AdminLoanController::class, 'index'], ['admin']);
Router::get('/admin/loans/{id}', [AdminLoanController::class, 'show'], ['admin']);
Router::post('/admin/loans/{id}/approve', [AdminLoanController::class, 'approve'], ['admin']);
Router::post('/admin/loans/{id}/reject', [AdminLoanController::class, 'reject'], ['admin']);
Router::post('/admin/loans/{id}/borrowed', [AdminLoanController::class, 'markBorrowed'], ['admin']);
Router::post('/admin/loans/{id}/confirm-return', [AdminLoanController::class, 'confirmReturn'], ['admin']);

// Admin Items & Categories
Router::get('/admin/items', [AdminItemController::class, 'index'], ['admin']);
Router::get('/admin/items/create', [AdminItemController::class, 'create'], ['admin']);
Router::post('/admin/items', [AdminItemController::class, 'store'], ['admin']);
Router::get('/admin/items/{id}/edit', [AdminItemController::class, 'edit'], ['admin']);
Router::post('/admin/items/{id}', [AdminItemController::class, 'update'], ['admin']);

Router::get('/admin/categories', [AdminCategoryController::class, 'index'], ['admin']);
Router::post('/admin/categories', [AdminCategoryController::class, 'store'], ['admin']);
Router::post('/admin/categories/{id}', [AdminCategoryController::class, 'update'], ['admin']);
Router::post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'delete'], ['admin']);
