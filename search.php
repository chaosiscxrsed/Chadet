<?php
require_once 'config.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode(['success' => false, 'message' => 'Query too short']);
    exit();
}

try {
    $search_query = '%' . $query . '%';
    $sql = "SELECT p.id, p.name, p.price, p.image_url, c.name as category 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?) 
            AND p.is_active = TRUE 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$search_query, $search_query, $search_query]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    error_log("Search error: " . $e->getMessage());
}
?>