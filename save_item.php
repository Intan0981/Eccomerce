<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = trim($_POST['shop_name']);
    if (!$shop_name) die("Missing shop name!");

    // Create shop item folder
    $baseDir = "Products/" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $shop_name) . "/items/";
    if (!file_exists($baseDir)) mkdir($baseDir, 0777, true);

    // File to store item info
    $dataFile = $baseDir . "items_data.json";
    $existingData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

    // Loop through items
    foreach ($_FILES['item_photos']['tmp_name'] as $i => $tmpName) {
        if (!is_uploaded_file($tmpName)) continue;

        $itemName = trim($_POST['item_names'][$i] ?? "Unnamed Item");
        $folderName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $itemName);
        $itemDir = $baseDir . $folderName . "/";
        if (!file_exists($itemDir)) mkdir($itemDir, 0777, true);

        // Save photo
        $fileExt = pathinfo($_FILES['item_photos']['name'][$i], PATHINFO_EXTENSION);
        $photoName = "photo_" . time() . "_" . $i . "." . $fileExt;
        $targetPath = $itemDir . $photoName;
        move_uploaded_file($tmpName, $targetPath);

        // Save item details
        $itemData = [
            "item_name" => $itemName,
            "color" => $_POST['item_colors'][$i] ?? '',
            "size" => $_POST['item_sizes'][$i] ?? '',
            "price" => $_POST['item_prices'][$i] ?? '',
            "discount_price" => $_POST['item_discount_prices'][$i] ?? '',
            "link" => $_POST['item_links'][$i] ?? '',
            "category" => $_POST['item_category'][$i] ?? '',
            "sku" => $_POST['item_sku'][$i] ?? '',
            "stock" => $_POST['item_stock'][$i] ?? '',
            "status" => $_POST['item_status'][$i] ?? '',
            "description" => $_POST['item_descriptions'][$i] ?? '',
            "photo_path" => $targetPath,
            "date_added" => date("Y-m-d H:i:s")
        ];

        $existingData[] = $itemData;
    }

    // Save back to JSON
    file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT));

    echo "<h2>✅ Items saved successfully!</h2>";
    echo "<a href='index.php'>← Back to add more</a>";
    echo "<hr>";

    // Display all items
    foreach ($existingData as $item) {
        echo "<div style='display:inline-block; border:1px solid #ccc; margin:10px; padding:10px;'>";
        echo "<img src='{$item['photo_path']}' style='width:150px;height:150px;object-fit:cover;'><br>";
        echo "<b>{$item['item_name']}</b><br>";
        echo "Color: {$item['color']} | Size: {$item['size']}<br>";
        echo "₱{$item['price']}";
        if (!empty($item['discount_price'])) echo " (Sale: ₱{$item['discount_price']})";
        echo "<br>Status: {$item['status']}<br>";
        echo "Category: {$item['category']}<br>";
        echo "<small>{$item['description']}</small>";
        echo "</div>";
    }
} else {
    echo "Invalid request.";
}
?>
