<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    // Membuat pembayaran baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'       => 'required|integer',
            'user_id'        => 'required|integer',
            'amount'         => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        try {
            // 1️⃣ Validasi order
            $orderResp = Http::get("http://127.0.0.1:8003/api/orders/{$validated['order_id']}");
            if ($orderResp->failed()) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            $orderData = $orderResp->json();

            // 2️⃣ Validasi user
            $userResp = Http::get("http://127.0.0.1:8001/api/users/{$validated['user_id']}");
            if ($userResp->failed()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // 3️⃣ Ambil & perbarui stok di ProductService
            $prodResp = Http::get("http://127.0.0.1:8002/api/products/{$orderData['product_id']}");
            if ($prodResp->failed()) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $prod = $prodResp->json();
            if (!isset($prod['stock'])) {
                return response()->json(['message' => 'Product stock unavailable'], 500);
            }
            $newStock = $prod['stock'] - $orderData['quantity'];
            if ($newStock < 0) {
                return response()->json(['message' => 'Insufficient stock'], 400);
            }
            $updProd = Http::put("http://127.0.0.1:8002/api/products/{$orderData['product_id']}", [
                'stock' => $newStock,
            ]);
            if ($updProd->failed()) {
                return response()->json(['message' => 'Failed to update stock'], 500);
            }

            // 4️⃣ Simpan payment langsung sebagai “paid”
            $payment = Payment::create([
                'order_id'       => $validated['order_id'],
                'user_id'        => $validated['user_id'],
                'amount'         => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'payment_date'   => now(),
            ]);

            // 5️⃣ Update status order di OrderService ke “paid”
            $updOrder = Http::put("http://127.0.0.1:8003/api/orders/{$validated['order_id']}", [
                'status' => 'paid',
            ]);
            if ($updOrder->failed()) {
                // non-blocking: order tetap paid, tapi log error kalau perlu
            }

            return response()->json($payment, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Menampilkan pembayaran berdasarkan ID
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
    }

    // Mengupdate status pembayaran
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|string',
        ]);

        $payment->update([
            'payment_status' => $validated['payment_status'],
        ]);

        return response()->json($payment);
    }

    // Menghapus pembayaran
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(null, 204);
    }
}
