<?php

// namespace App\Http\Controllers;

// use App\Models\Order;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use App\Events\OrderCreated;
// use App\Services\RabbitMQService;

// class OrderController extends Controller
// {
//     // Menampilkan semua pesanan
//     public function index()
//     {
//         $orders = Order::all();
//         return response()->json($orders);
//     }

//     // Menampilkan pesanan berdasarkan ID
//     public function show($id)
//     {
//         $order = Order::findOrFail($id);
//         return response()->json($order);
//     }

//     // Menyimpan pesanan baru
//     public function store(Request $request)
//     {
//         // Validasi input dari request
//         $validated = $request->validate([
//             'user_id' => 'required|integer',
//             'product_id' => 'required|integer',
//             'quantity' => 'required|integer|min:1',
//         ]);

//         $status = $request->has('status') ? $request->status : 'pending';

//         // Validasi User dengan mengakses user-service
//         $user = $this->validateUser($validated['user_id']);
//         if (!$user) {
//             return response()->json(['message' => 'User not found in UserService'], 404);
//         }

//         // Validasi Product dengan mengakses product-service
//         $product = $this->validateProduct($validated['product_id']);
//         if (!$product) {
//             return response()->json(['message' => 'Product not found in ProductService'], 404);
//         }

//         // Hitung harga total
//         $totalPrice = $product['price'] * $validated['quantity'];

//         // Simpan order ke database
//         $order = Order::create([
//             'user_id' => $validated['user_id'],
//             'product_id' => $validated['product_id'],
//             'quantity' => $validated['quantity'],
//             'total_price' => $totalPrice,
//             'status' => $status,
//             'order_date' => now(),
//         ]);

//         // Trigger event OrderCreated untuk memproses pembayaran
//         OrderCreated::dispatch($order);

//         return response()->json([
//             'order_id' => $order->id,
//             'order' => $order
//         ], 201);
//     }

//     // Mengupdate pesanan
//     public function update(Request $request, $id)
//     {
//         $order = Order::findOrFail($id);

//         // Validasi input request untuk update
//         $validated = $request->validate([
//             'user_id' => 'required|integer',
//             'product_id' => 'required|integer',
//             'quantity' => 'required|integer|min:1',
//             'status' => 'required|string',
//         ]);

//         // Validasi User dengan mengakses user-service
//         $user = $this->validateUser($validated['user_id']);
//         if (!$user) {
//             return response()->json(['message' => 'User not found in UserService'], 404);
//         }

//         // Validasi Product dengan mengakses product-service
//         $product = $this->validateProduct($validated['product_id']);
//         if (!$product) {
//             return response()->json(['message' => 'Product not found in ProductService'], 404);
//         }

//         // Hitung harga total setelah update
//         $totalPrice = $product['price'] * $validated['quantity'];

//         // Update pesanan
//         $order->update([
//             'user_id' => $validated['user_id'],
//             'product_id' => $validated['product_id'],
//             'quantity' => $validated['quantity'],
//             'total_price' => $totalPrice,
//             'status' => $validated['status'],
//         ]);

//         return response()->json($order);
//     }

//     // Menghapus pesanan
//     public function destroy($id)
//     {
//         $order = Order::findOrFail($id);
//         $order->delete();

//         return response()->json(['message' => 'Order successfully deleted'], 200);
//     }

//     // Validasi User dengan memanggil UserService
//     private function validateUser($userId)
//     {
//         // Mengakses user-service melalui nama service di Docker Compose
//         $response = Http::get("http://user-service:8000/api/users/{$userId}");
//         return $response->successful() ? $response->json() : null;
//     }

//     // Validasi Product dengan memanggil ProductService
//     private function validateProduct($productId)
//     {
//         // Mengakses product-service melalui nama service di Docker Compose
//         $response = Http::get("http://product-service:8000/api/products/{$productId}");
//         return $response->successful() ? $response->json() : null;
//     }
// }

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Events\OrderCreated;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }


    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $status = $request->has('status') ? $request->status : 'pending';


        $user = $this->validateUser($validated['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found in UserService'], 404);
        }


        $product = $this->validateProduct($validated['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found in ProductService'], 404);
        }


        $totalPrice = $product['price'] * $validated['quantity'];


        $order = Order::create([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => $status,
            'order_date' => now(),
        ]);


        OrderCreated::dispatch($order->id, $totalPrice);

        return response()->json([
            'order_id' => $order->id,
            'order' => $order
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);


        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
        ]);


        $user = $this->validateUser($validated['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found in UserService'], 404);
        }


        $product = $this->validateProduct($validated['product_id']);
        if (!$product) {
            return response()->json(['message' => 'Product not found in ProductService'], 404);
        }


        $totalPrice = $product['price'] * $validated['quantity'];


        $order->update([
            'user_id' => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => $validated['status'],
        ]);

        return response()->json($order);
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order successfully deleted'], 200);
    }


    private function validateUser($userId)
    {
        // Mengakses user-service melalui nama service di Docker Compose
        $response = Http::get("http://user-service:8001/api/users/{$userId}");
        return $response->successful() ? $response->json() : null;
    }


    private function validateProduct($productId)
    {

        $response = Http::get("http://product-service:8002/api/products/{$productId}");
        return $response->successful() ? $response->json() : null;
    }
}
