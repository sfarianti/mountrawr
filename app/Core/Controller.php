<?php

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data);
        
        // Render content view into buffer
        ob_start();
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View file not found: {$view}");
        }
        $content = ob_get_clean();

        // Render layout
        $layoutFile = BASE_PATH . '/app/Views/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
