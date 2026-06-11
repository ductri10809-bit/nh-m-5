<?php
/**
 * dinh_dang_san_pham.php - Chuan hoa du lieu product cho API/frontend
 */
function dinhDangSanPham(array $row): array
{
    return [
        'id'           => (int) ($row['product_id'] ?? $row['id'] ?? 0),
        'category_id'  => (int) ($row['category_id'] ?? 0),
        'ten'          => $row['product_name'] ?? $row['ten'] ?? '',
        'gia'          => (int) ($row['price'] ?? $row['gia'] ?? 0),
        'sale_price'   => isset($row['sale_price']) && $row['sale_price'] !== null
            ? (int) $row['sale_price'] : null,
        'hinh_anh'     => $row['image'] ?? $row['hinh_anh'] ?? '',
        'mo_ta'        => $row['description'] ?? $row['mo_ta'] ?? '',
        'ton_kho'      => (int) ($row['stock_quantity'] ?? $row['ton_kho'] ?? 0),
        'noi_bat'      => (int) ($row['is_bestseller'] ?? $row['noi_bat'] ?? 0),
        'is_trend'     => (int) ($row['is_trend'] ?? 0),
        'is_sale'      => (int) ($row['is_sale'] ?? 0),
        'ten_danh_muc' => $row['category_name'] ?? $row['ten_danh_muc'] ?? '',
    ];
}

function dinhDangBienThe(array $row): array
{
    return [
        'id'         => (int) ($row['variant_id'] ?? 0),
        'mau_sac'    => $row['color_name'] ?? '',
        'ma_mau'     => $row['color_hex'] ?? '#000000',
        'hinh_anh'   => $row['image_url'] ?? $row['image'] ?? '',
        'product_id' => (int) ($row['base_product_id'] ?? 0),
    ];
}

function dinhDangDanhMuc(array $row): array
{
    return [
        'id'  => (int) ($row['category_id'] ?? $row['id'] ?? 0),
        'ten' => (string) ($row['category_name'] ?? $row['name'] ?? $row['ten'] ?? ''),
        'category_name' => (string) ($row['category_name'] ?? ''),
    ];
}
