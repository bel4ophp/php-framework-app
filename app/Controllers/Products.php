<?php
namespace App\Controllers;

use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class Products
{
    public function index()
    {
        // require __DIR__ . "/../models/product.php";
        
        $productModel = new Product;
        $products = $productModel->getPaginatedProducts();

        return new Response(json_encode(['products' => $products]), 200);

        // echo json_encode($products);
        // require __DIR__ . "/../views/products_index.php";
    }

    public function show($id = null)
    {
        echo json_encode(['message' => "Show product with ID: $id"]);
    }
}