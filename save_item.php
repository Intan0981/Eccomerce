<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $_POST['shop_name']);
    $dir = "Products/$shopName/items";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $savedItems = [];

    foreach ($_FILES['item_photos']['tmp_name'] as $i => $tmpName) {
        if ($_FILES['item_photos']['error'][$i] === 0) {
            $photoName = basename($_FILES['item_photos']['name'][$i]);
            $targetPath = "$dir/$photoName";
            move_uploaded_file($tmpName, $targetPath);

            $savedItems[] = [
                'name' => $_POST['item_names'][$i] ?? '',
                'variation' => $_POST['item_variations'][$i] ?? '',
                'price' => $_POST['item_prices'][$i] ?? '',
                'discount_price' => $_POST['item_discount_prices'][$i] ?? '',
                'link' => $_POST['item_links'][$i] ?? '',
                'category' => $_POST['item_category'][$i] ?? '',
                'sku' => $_POST['item_sku'][$i] ?? '',
                'stock' => $_POST['item_stock'][$i] ?? '',
                'status' => $_POST['item_status'][$i] ?? '',
                'description' => $_POST['item_descriptions'][$i] ?? '',
                'photo' => $photoName
            ];
        }
    }

    $data = [
        'shop_name' => $shopName,
        'items' => $savedItems,
        'created_at' => date('Y-m-d H:i:s')
    ];

    file_put_contents("$dir/items_data.json", json_encode($data, JSON_PRETTY_PRINT));

    echo "<h3>Items saved successfully in $dir!</h3>";
    echo "<a href='index.php'>Add more</a><br><br>";
    foreach ($savedItems as $item) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px; width:240px; display:inline-block; text-align:center;'>";
        echo "<img src='$dir/{$item['photo']}' style='width:100%; height:150px; object-fit:cover;'><br>";
        echo "<b>{$item['name']}</b><br>";
        echo "Category: {$item['category']}<br>";
        echo "Variation: {$item['variation']}<br>";
        echo "SKU: {$item['sku']}<br>";
        echo "Stock: {$item['stock']}<br>";
        echo "Price: {$item['price']}<br>";
        if (!empty($item['discount_price'])) echo "Discount: {$item['discount_price']}<br>";
        echo "Status: {$item['status']}<br>";
        if (!empty($item['link'])) echo "<a href='{$item['link']}' target='_blank'>View Link</a><br>";
        echo "<small>{$item['description']}</small>";
        echo "</div>";
    }
}
?>

