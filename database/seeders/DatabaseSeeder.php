<?php

namespace Database\Seeders;

use App\Core\Database;

class DatabaseSeeder
{
    public static function run(): void
    {
        $db = Database::getInstance();
        echo "Seeding database...\n";

        // Seed Users
        $db->exec("DELETE FROM users;");
        $stmtUser = $db->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        
        $adminPass = password_hash('admin12345', PASSWORD_BCRYPT);
        $userPass = password_hash('user12345', PASSWORD_BCRYPT);

        $stmtUser->execute(['Administrator Rawr', 'admin@mountrawr.test', $adminPass, '081234567890', 'admin']);
        $stmtUser->execute(['Budi Pendaki', 'user@mountrawr.test', $userPass, '089876543210', 'peminjam']);
        echo " - Users seeded (Admin: admin@mountrawr.test / admin12345, User: user@mountrawr.test / user12345)\n";

        // Seed Categories
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $db->exec("TRUNCATE TABLE loan_status_logs;");
        $db->exec("TRUNCATE TABLE loan_returns;");
        $db->exec("TRUNCATE TABLE loan_items;");
        $db->exec("TRUNCATE TABLE loans;");
        $db->exec("TRUNCATE TABLE items;");
        $db->exec("TRUNCATE TABLE categories;");
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        $categories = [
            ['Tenda', 'tenda', 'Tenda camping berbagai kapasitas anti badai dan waterproof.'],
            ['Carrier & Tas', 'carrier-tas', 'Tas gunung ergonomic berbagai kapasitas (40L - 80L).'],
            ['Alat Masak', 'alat-masak', 'Kompor portable, nesting, cooking set, dan gas adapter.'],
            ['Sleeping Gear', 'sleeping-gear', 'Sleeping bag, matras tiup, dan alumunium foil mat.'],
            ['Penerangan', 'penerangan', 'Headlamp, senter outdoor, dan lampu tenda LED.']
        ];

        $stmtCat = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $catIds = [];
        foreach ($categories as $cat) {
            $stmtCat->execute($cat);
            $catIds[$cat[1]] = $db->lastInsertId();
        }
        echo " - Categories seeded\n";

        // Seed Items
        $items = [
            [
                'category_id' => $catIds['tenda'],
                'name' => 'Tenda Eiger Kapasitas 4 Orang',
                'slug' => 'tenda-eiger-kapasitas-4-orang',
                'description' => 'Tenda double layer kokoh tahan air hujan deras & angin kencang gunung. Dilengkapi vestibule luas.',
                'stock_total' => 3,
                'price_per_day' => 45000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['tenda'],
                'name' => 'Tenda Consina Magnum 4',
                'slug' => 'tenda-consina-magnum-4',
                'description' => 'Tenda dome favorit pendaki dengan kerangka aluminium ringan dan setup sangat cepat.',
                'stock_total' => 4,
                'price_per_day' => 40000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['carrier-tas'],
                'name' => 'Carrier Osprey Atmos AG 65L',
                'slug' => 'carrier-osprey-atmos-ag-65l',
                'description' => 'Carrier ultra-comfort dengan teknologi Anti-Gravity mesh backsystem. Sangat nyaman untuk ekspedisi panjang.',
                'stock_total' => 2,
                'price_per_day' => 60000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['carrier-tas'],
                'name' => 'Carrier Deuter Aircontact 60+10L',
                'slug' => 'carrier-deuter-aircontact-60-10l',
                'description' => 'Tas pendakian tangguh bahan Cordura tahan gesekan dengan raincover bawaan.',
                'stock_total' => 3,
                'price_per_day' => 50000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['alat-masak'],
                'name' => 'Cooking Set Nesting DS-308 (4 In 1)',
                'slug' => 'cooking-set-nesting-ds-308',
                'description' => 'Panci dan wajan lipat berbahan anodized aluminium antilengket + mangkok & sendok plastik.',
                'stock_total' => 5,
                'price_per_day' => 20000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['alat-masak'],
                'name' => 'Kompor Portable Mawar Windproof',
                'slug' => 'kompor-portable-mawar-windproof',
                'description' => 'Kompor kovar bunga pelindung angin dengan pemantik otomatis super efisien gas.',
                'stock_total' => 6,
                'price_per_day' => 15000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['sleeping-gear'],
                'name' => 'Sleeping Bag Bulu Angsa Extreme -5°C',
                'slug' => 'sleeping-bag-bulu-angsa-extreme-5c',
                'description' => 'Sleeping bag hangat ultralight isian goose down hangat dan kompresibel.',
                'stock_total' => 4,
                'price_per_day' => 30000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
            [
                'category_id' => $catIds['penerangan'],
                'name' => 'Headlamp Petzl Tikka 350 Lumens',
                'slug' => 'headlamp-petzl-tikka-350-lumens',
                'description' => 'Headlamp tahan air IPX4 dengan mode cahaya merah dan sorot putih terang.',
                'stock_total' => 5,
                'price_per_day' => 15000,
                'condition_status' => 'baik',
                'image_path' => null,
                'is_active' => 1
            ],
        ];

        $stmtItem = $db->prepare("INSERT INTO items (category_id, name, slug, description, stock_total, price_per_day, condition_status, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmtItem->execute([
                $item['category_id'],
                $item['name'],
                $item['slug'],
                $item['description'],
                $item['stock_total'],
                $item['price_per_day'],
                $item['condition_status'],
                $item['image_path'],
                $item['is_active']
            ]);
        }
        echo " - Items seeded\n";
        echo "Seeding completed successfully!\n";
    }
}
