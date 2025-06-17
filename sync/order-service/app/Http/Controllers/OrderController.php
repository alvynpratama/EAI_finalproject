<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // Menampilkan semua pesanan
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    // Menampilkan pesanan berdasarkan ID
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);

        return response()->json($order);
    }

    public function store(Request $request)
    {
        // Validasi input data
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        // Tentukan status jika tidak diberikan
        $status = $request->has('status') ? $request->status : 'pending';

        // Validasi user lewat UserService
        $user = $this->validateUser($validated['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found in UserService'], 404);
        }

        // Validasi produk lewat ProductService
        $product = $this->validateProduct($validated['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found in ProductService'], 404);
        }

        // Menghitung total harga berdasarkan harga produk dan kuantitas
        $totalPrice = $product['price'] * $validated['quantity'];

        // Membuat order baru
        $order = Order::create([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => $status,
            'order_date' => now(),
        ]);

        // Mengirim respons dengan 'order_id'
        return response()->json([
            'order_id' => $order->id,
            'order' => $order
        ], 201);
    }




    // Mengupdate pesanan
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Validasi input data
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
        ]);

        // 🔍 Validasi user lewat UserService
        $user = $this->validateUser($validated['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found in UserService'], 404);
        }

        // 🔍 Validasi produk lewat ProductService
        $product = $this->validateProduct($validated['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found in ProductService'], 404);
        }

        // Menghitung total harga berdasarkan harga produk dan kuantitas
        $totalPrice = $product['price'] * $validated['quantity'];

        // Mengupdate order yang sudah ada
        $order->update([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => $validated['status'],
        ]);

        return response()->json($order);
    }

    // Menghapus pesanan
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        // Menampilkan pesan konfirmasi berhasil menghapus
        return response()->json(['message' => 'Order successfully deleted'], 200);
    }
    // Validasi User lewat UserService
    private function validateUser($userId)
    {
        $response = Http::get("http://127.0.0.1:8001/api/users/{$userId}");
        return $response->successful() ? $response->json() : null;
    }

    // Validasi Produk lewat ProductService
    private function validateProduct($productId)
    {
        $response = Http::get("http://127.0.0.1:8002/api/products/{$productId}");
        return $response->successful() ? $response->json() : null;
    }
}
