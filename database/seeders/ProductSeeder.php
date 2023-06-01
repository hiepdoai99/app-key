<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production')) {
            $this->insertProducts();
            $this->insertPlans();
        } else {
            Product::factory(20)
                ->has(Plan::factory()->count(6))
                ->create();
        }
    }

    private function insertProducts()
    {
        DB::insert("
            INSERT INTO `products` (`id`, `name`, `slug`, `prefix_key`, `description`, `version`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (1, 'Phần mềm MKT Care', 'phan-mem-mkt-care', 'MKT-CARE-', 'Phần mềm nuôi ních số lượng cực lớn', '1.0.0', 1, '2022-12-01 05:45:39', '2022-12-01 05:45:39', NULL),
            (2, 'Phần mềm MKT Data', 'phan-mem-mkt-data', 'MKT-DATA-', 'Phần mềm quét data số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:46:47', '2022-12-01 05:46:47', NULL),
            (3, 'Phần mềm MKT Viral', 'phan-mem-mkt-viral', 'MKT-VIRAL-', 'Phần mềm đăng bài số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:47:52', '2022-12-01 05:47:52', NULL),
            (4, 'Phần mềm MKT TikTokShop', 'phan-mem-mkt-tiktokshop', 'MKT-TIKTOKSHOP-', 'Phần mềm quản lý TikTokShop số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:49:27', '2022-12-01 05:49:27', NULL),
            (5, 'Phần mềm MKT BĐS', 'phan-mem-mkt-bds', 'MKT-BDS-', 'Phần mềm đăng tin bất động sản tự động số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:50:43', '2022-12-01 05:50:43', NULL),
            (6, 'Phần mềm MKT Telegram', 'phan-mem-mkt-telegram', 'MKT-TELEGRAM-', 'Phần mềm MKT Telegram tự động số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:52:32', '2022-12-01 05:52:32', NULL),
            (7, 'Phần mềm MKT Tube', 'phan-mem-mkt-tube', 'MKT-TUBE-', 'Phần mềm MKT Tube tự động tăng tương tác YouTube số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:53:39', '2022-12-01 05:53:39', NULL),
            (8, 'Phần mềm MKT Zalo', 'phan-mem-mkt-zalo', 'MKT-ZALO-', 'Phần mềm MKT Zalo tự động tăng tương tác Zalo số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:54:48', '2022-12-01 05:54:48', NULL),
            (9, 'Phần mềm MKT Market', 'phan-mem-mkt-merket', 'MKT-MARKET-', 'Phần mềm MKT Market tự động đăng bài MarketPlace số lượng cực lớn siêu nhanh khổng lồ', '1.0.0', 1, '2022-12-01 05:56:01', '2022-12-01 05:56:01', NULL),
            (10, 'Phần mềm MKT Tuyển Dụng', 'phan-mem-mkt-tuyen-dung', 'MKT-TD-', 'Phần mềm MKT Tuyển Dụng', '1.0.0', 1, '2022-12-19 00:25:00', '2022-12-19 00:25:00', NULL),
            (11, 'Phần mềm MKT Post', 'phan-mem-mkt-post', 'MKT-POST-', 'Phần mềm MKT POST', '1.0.0', 1, '2022-12-19 00:26:39', '2022-12-19 00:26:39', NULL);
        ");
    }

    private function insertPlans()
    {
        DB::insert("
            INSERT INTO `plans` (`id`, `tag`, `name`, `description`, `is_active`, `price`, `signup_fee`, `currency`, `trial_period`, `trial_interval`, `trial_mode`, `grace_period`, `grace_interval`, `invoice_period`, `invoice_interval`, `tier`, `created_at`, `updated_at`, `deleted_at`, `product_id`) VALUES
            (1, 'mkt-care-1-thang', 'MKT Care 1 Tháng', 'MKT Care 1 Tháng', 1, '800000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 18:46:39', '2022-12-05 18:46:39', NULL, 1),
            (2, 'mkt-care-6-thang', 'MKT Care 6 Tháng', 'MKT Care 6 Tháng', 1, '2500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 19:31:50', '2022-12-05 19:31:50', NULL, 1),
            (3, 'mkt-care-1-nam', 'MKT Care 1 Năm', 'MKT Care 1 Năm', 1, '4000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 19:33:16', '2022-12-05 19:33:16', NULL, 1),
            (4, 'mkt-care-2-nam', 'MKT Care 2 Năm', 'MKT Care 2 Năm', 1, '8000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 19:34:12', '2022-12-05 19:34:12', NULL, 1),
            (5, 'mkt-care-vinh-vien', 'MKT Care Vĩnh Viễn', 'MKT Care Vĩnh Viễn', 1, '15000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 19:36:23', '2022-12-05 19:36:23', NULL, 1),
            (6, 'mkt-data-1-thang', 'MKT Data 1 Tháng', 'MKT Data 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 19:39:05', '2022-12-05 19:39:05', NULL, 2),
            (7, 'mkt-data-6-thang', 'MKT Data 6 Tháng', 'MKT Data 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 19:40:04', '2022-12-05 19:40:04', NULL, 2),
            (8, 'mkt-data-1-nam', 'MKT Data 1 Năm', 'MKT Data 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 19:41:03', '2022-12-05 19:41:03', NULL, 2),
            (9, 'mkt-data-2-nam', 'MKT Data 2 Năm', 'MKT Data 2 Năm', 1, '3500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 19:41:53', '2022-12-05 19:41:53', NULL, 2),
            (10, 'mkt-data-vinh-vien', 'MKT Data Vĩnh Viễn', 'MKT Data Vĩnh Viễn', 1, '7000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 19:42:39', '2022-12-05 19:42:39', NULL, 2),
            (11, 'mkt-viral-1-thang', 'MKT Viral 1 Tháng', 'MKT Viral 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 19:43:55', '2022-12-05 19:43:55', NULL, 3),
            (12, 'mkt-viral-6-thang', 'MKT Viral 6 Tháng', 'MKT Viral 6 Tháng', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 19:44:46', '2022-12-05 19:44:46', NULL, 3),
            (13, 'mkt-viral-1-nam', 'MKT Viral 1 Năm', 'MKT Viral 1 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 19:45:35', '2022-12-05 19:45:35', NULL, 3),
            (14, 'mkt-viral-2-nam', 'MKT Viral 2 Năm', 'MKT Viral 2 Năm', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 19:46:13', '2022-12-05 19:46:13', NULL, 3),
            (15, 'mkt-viral-vinh-vien', 'MKT Viral Vĩnh Viễn', 'MKT Viral Vĩnh Viễn', 1, '10000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 19:47:12', '2022-12-05 19:47:12', NULL, 3),
            (16, 'mkt-tiktokshop-3s-1-thang', 'MKT Tiktokshop 3Shop 1 Tháng', 'MKT Tiktokshop 3Shop 1 Tháng', 1, '800000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 19:49:55', '2022-12-05 19:49:55', NULL, 4),
            (17, 'mkt-tiktokshop-3s-3-thang', 'MKT Tiktokshop 3Shop 3 Tháng', 'MKT Tiktokshop 3Shop 3 Tháng', 1, '100000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 19:50:54', '2022-12-05 19:50:54', NULL, 4),
            (18, 'mkt-tiktokshop-3s-6-thang', 'MKT Tiktokshop 3Shop 6 Tháng', 'MKT Tiktokshop 3Shop 6 Tháng', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 19:51:30', '2022-12-05 19:51:30', NULL, 4),
            (19, 'mkt-tiktokshop-3s-1-nam', 'MKT Tiktokshop 3Shop 1 Năm', 'MKT Tiktokshop 3Shop 1 Năm', 1, '3500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 19:52:09', '2022-12-05 19:52:09', NULL, 4),
            (20, 'mkt-tiktokshop-3s-2-nam', 'MKT Tiktokshop 3Shop 2 Năm', 'MKT Tiktokshop 3Shop 2 Năm', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 19:53:10', '2022-12-05 19:53:10', NULL, 4),
            (21, 'mkt-tiktokshop-3s-vinh-vien', 'MKT Tiktokshop 3Shop Vĩnh Viễn', 'MKT Tiktokshop 3Shop Vĩnh Viễn', 1, '9000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 19:53:52', '2022-12-05 19:53:52', NULL, 4),
            (22, 'mkt-bds-1-thang', 'MKT BĐS 1 Tháng', 'MKT BĐS 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 19:54:56', '2022-12-05 19:54:56', NULL, 5),
            (23, 'mkt-bds-3-thang', 'MKT BĐS 3 Tháng', 'MKT BĐS 3 Tháng', 1, '1000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 19:55:59', '2022-12-05 19:55:59', NULL, 5),
            (24, 'mkt-bds-6-thang', 'MKT BĐS 6 Tháng', 'MKT BĐS 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 19:56:44', '2022-12-05 19:56:44', NULL, 5),
            (25, 'mkt-bds-1-nam', 'MKT BĐS 1 Năm', 'MKT BĐS 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 19:57:20', '2022-12-05 19:57:20', NULL, 5),
            (26, 'mkt-bds-2-nam', 'MKT BĐS 2 Năm', 'MKT BĐS 2 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 19:59:12', '2022-12-05 19:59:12', NULL, 5),
            (27, 'mkt-bds-vinh-vien', 'MKT BĐS Vĩnh Viễn', 'MKT BĐS Vĩnh Viễn', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 19:59:47', '2022-12-05 19:59:47', NULL, 5),
            (28, 'mkt-telegram-1-thang', 'MKT Telegram 1 Tháng', 'MKT Telegram 1 Tháng', 1, '800000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 20:00:50', '2022-12-05 20:00:50', NULL, 6),
            (29, 'mkt-telegram-3-thang', 'MKT Telegram 3 Tháng', 'MKT Telegram 3 Tháng', 1, '100000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 20:01:16', '2022-12-05 20:01:16', NULL, 6),
            (30, 'mkt-telegram-6-thang', 'MKT Telegram 6 Tháng', 'MKT Telegram 6 Tháng', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 20:01:39', '2022-12-05 20:01:39', NULL, 6),
            (31, 'mkt-telegram-1-nam', 'MKT Telegram 1 Năm', 'MKT Telegram 1 Năm', 1, '3500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 20:02:33', '2022-12-05 20:02:33', NULL, 6),
            (32, 'mkt-telegram-2-nam', 'MKT Telegram 2 Năm', 'MKT Telegram 2 Năm', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 20:03:05', '2022-12-05 20:03:05', NULL, 6),
            (33, 'mkt-telegram-vinh-vien', 'MKT Telegram Vĩnh Viễn', 'MKT Telegram Vĩnh Viễn', 1, '9000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 20:03:37', '2022-12-05 20:03:37', NULL, 6),
            (34, 'mkt-tube-1-thang', 'MKT Tube 1 Tháng', 'MKT Tube 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 20:05:11', '2022-12-05 20:05:11', NULL, 7),
            (35, 'mkt-tube-3-thang', 'MKT Tube 3 Tháng', 'MKT Tube 3 Tháng', 1, '1000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 20:06:14', '2022-12-05 20:06:14', NULL, 7),
            (36, 'mkt-tube-6-thang', 'MKT Tube 6 Tháng', 'MKT Tube 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 20:11:13', '2022-12-05 20:11:13', NULL, 7),
            (37, 'mkt-tube-1-nam', 'MKT Tube 1 Năm', 'MKT Tube 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 20:11:59', '2022-12-05 20:11:59', NULL, 7),
            (38, 'mkt-tube-2-nam', 'MKT Tube 2 Năm', 'MKT Tube 2 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 20:12:32', '2022-12-05 20:12:32', NULL, 7),
            (39, 'mkt-tube-vinh-vien', 'MKT Tube Vĩnh Viễn', 'MKT Tube Vĩnh Viễn', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 20:13:12', '2022-12-05 20:13:12', NULL, 7),
            (40, 'mkt-zalo-1-thang', 'MKT Zalo 1 Tháng', 'MKT Zalo 1 Tháng', 1, '800000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 20:14:42', '2022-12-05 20:14:42', NULL, 8),
            (41, 'mkt-zalo-3-thang', 'MKT Zalo 3 Tháng', 'MKT Zalo 3 Tháng', 1, '100000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 20:15:43', '2022-12-05 20:15:43', NULL, 8),
            (42, 'mkt-zalo-6-thang', 'MKT Zalo 6 Tháng', 'MKT Zalo 6 Tháng', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 20:16:18', '2022-12-05 20:16:18', NULL, 8),
            (43, 'mkt-zalo-1-nam', 'MKT Zalo 1 Năm', 'MKT Zalo 1 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 20:18:36', '2022-12-05 20:18:36', NULL, 8),
            (44, 'mkt-zalo-2-nam', 'MKT Zalo 2 Năm', 'MKT Zalo 2 Năm', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 20:21:13', '2022-12-05 20:21:13', NULL, 8),
            (45, 'mkt-zalo-vinh-vien', 'MKT Zalo Vĩnh Viễn', 'MKT Zalo Vĩnh Viễn', 1, '9000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 20:21:48', '2022-12-05 20:21:48', NULL, 8),
            (46, 'mkt-market-1-thang', 'MKT Market 1 Tháng', 'MKT Market 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-05 20:22:42', '2022-12-05 20:22:42', NULL, 9),
            (47, 'mkt-market-3-thang', 'MKT Market 3 Tháng', 'MKT Market 3 Tháng', 1, '1000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 2, '2022-12-05 20:23:08', '2022-12-05 20:23:08', NULL, 9),
            (48, 'mkt-market-6-thang', 'MKT Market 6 Tháng', 'MKT Market 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 3, '2022-12-05 20:23:39', '2022-12-05 20:23:39', NULL, 9),
            (49, 'mkt-market-1-nam', 'MKT Market 1 Năm', 'MKT Market 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 4, '2022-12-05 20:24:09', '2022-12-05 20:24:09', NULL, 9),
            (50, 'mkt-market-2-nam', 'MKT Market 2 Năm', 'MKT Market 2 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 5, '2022-12-05 20:24:34', '2022-12-05 20:24:34', NULL, 9),
            (51, 'mkt-market-vinh-vien', 'MKT Market Vĩnh Viễn', 'MKT Market Vĩnh Viễn', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 6, '2022-12-05 20:25:06', '2022-12-05 20:25:06', NULL, 9),
            (52, 'mkt-td-1-thang', 'MKT Tuyển Dụng 1 Tháng', 'MKT Tuyển Dụng 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-19 00:31:12', '2022-12-19 00:31:12', NULL, 10),
            (53, 'mkt-td-3-thang', 'MKT Tuyển Dụng 3 Tháng', 'MKT Tuyển Dụng 3 Tháng', 1, '1000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 1, '2022-12-19 00:32:18', '2022-12-19 00:32:18', NULL, 10),
            (54, 'mkt-td-6-thang', 'MKT Tuyển Dụng 6 Tháng', 'MKT Tuyển Dụng 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 1, '2022-12-19 00:33:03', '2022-12-19 00:33:03', NULL, 10),
            (55, 'mkt-td-1-nam', 'MKT Tuyển Dụng 1 Năm', 'MKT Tuyển Dụng 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 1, '2022-12-19 00:34:17', '2022-12-19 00:34:17', NULL, 10),
            (56, 'mkt-td-2-nam', 'MKT Tuyển Dụng 2 Năm', 'MKT Tuyển Dụng 2 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 1, '2022-12-19 00:34:57', '2022-12-19 00:34:57', NULL, 10),
            (57, 'mkt-td-vinh-vien', 'MKT Tuyển Dụng Vĩnh Viễn', 'MKT Tuyển Dụng Vĩnh Viễn', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 1, '2022-12-19 00:37:02', '2022-12-19 00:37:02', NULL, 10),
            (58, 'mkt-post-1-thang', 'MKT Post 1 Tháng', 'MKT Post 1 Tháng', 1, '500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 30, 'day', 1, '2022-12-19 00:38:19', '2022-12-19 00:38:19', NULL, 11),
            (59, 'mkt-post-3-thang', 'MKT Post 3 Tháng', 'MKT Post 3 Tháng', 1, '1000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 90, 'day', 1, '2022-12-19 00:38:54', '2022-12-19 00:38:54', NULL, 11),
            (60, 'mkt-post-6-thang', 'MKT Post 6 Tháng', 'MKT Post 6 Tháng', 1, '1500000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 180, 'day', 1, '2022-12-19 00:39:30', '2022-12-19 00:39:30', NULL, 11),
            (61, 'mkt-post-1-nam', 'MKT Post 1 Năm', 'MKT Post 1 Năm', 1, '2000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 365, 'day', 1, '2022-12-19 00:40:05', '2022-12-19 00:40:05', NULL, 11),
            (62, 'mkt-post-2-nam', 'MKT Post 2 Năm', 'MKT Post 2 Năm', 1, '3000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 730, 'day', 1, '2022-12-19 00:40:31', '2022-12-19 00:40:31', NULL, 11),
            (63, 'mkt-post-vinh-vien', 'MKT Post Vĩnh Viễn', 'MKT Post Vĩnh Viễn', 1, '5000000.00', '0.00', 'VND', 0, 'day', 'inside', 0, 'day', 11111, 'day', 1, '2022-12-19 00:41:05', '2022-12-19 00:41:05', NULL, 11),

			(64, 'mkt-care-dung-thu-3-ngay', 'MKT Care dùng thử 3 Ngày', 'MKT Care dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 18:46:39', '2022-12-05 18:46:39', NULL, 1),
			(65, 'mkt-data-dung-thu-3-ngay', 'MKT Data dùng thử 3 Ngày', 'MKT Data dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 19:39:05', '2022-12-05 19:39:05', NULL, 2),
			(66, 'mkt-viral-dung-thu-3-ngay', 'MKT Viral dùng thử 3 Ngày', 'MKT Viral dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 19:43:55', '2022-12-05 19:43:55', NULL, 3),
			(67, 'mkt-tiktokshop-3s-dung-thu-3-ngay', 'MKT Tiktokshop 3Shop dùng thử 3 Ngày', 'MKT Tiktokshop 3Shop dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 19:49:55', '2022-12-05 19:49:55', NULL, 4),
			(68, 'mkt-bds-dung-thu-3-ngay', 'MKT BĐS dùng thử 3 Ngày', 'MKT BĐS dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 19:54:56', '2022-12-05 19:54:56', NULL, 5),
			(69, 'mkt-telegram-dung-thu-3-ngay', 'MKT Telegram dùng thử 3 Ngày', 'MKT Telegram dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 20:00:50', '2022-12-05 20:00:50', NULL, 6),
			(70, 'mkt-tube-dung-thu-3-ngay', 'MKT Tube dùng thử 3 Ngày', 'MKT Tube dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 20:05:11', '2022-12-05 20:05:11', NULL, 7),
			(71, 'mkt-zalo-dung-thu-3-ngay', 'MKT Zalo dùng thử 3 Ngày', 'MKT Zalo dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 20:14:42', '2022-12-05 20:14:42', NULL, 8),
			(72, 'mkt-market-dung-thu-3-ngay', 'MKT Market dùng thử 3 Ngày', 'MKT Market dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-05 20:22:42', '2022-12-05 20:22:42', NULL, 9),
			(73, 'mkt-td-dung-thu-3-ngay', 'MKT Tuyển Dụng dùng thử 3 Ngày', 'MKT Tuyển Dụng dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-19 00:31:12', '2022-12-19 00:31:12', NULL, 10),
			(74, 'mkt-post-dung-thu-3-ngay', 'MKT Post dùng thử 3 Ngày', 'MKT Post dùng thử 3 Ngày', 1, '0.00', '0.00', 'VND', 3, 'day', 'inside', 0, 'day', 0, 'day', 1, '2022-12-19 00:38:19', '2022-12-19 00:38:19', NULL, 11);
        ");
    }
}
