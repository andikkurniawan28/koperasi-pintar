<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'type' => 'required|in:member,customer',
            'customer_id' => 'nullable|exists:customers,id',
            'member_id' => 'nullable|exists:members,id',
            'subtotal' => 'required',
            'discount' => 'required',
            'taxes' => 'required',
            'expenses' => 'required',
            'grand_total' => 'required',
            'account_id' => 'required|exists:accounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Item penjualan wajib diisi.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.qty.min' => 'Qty minimal 1.',
        ];
    }
}
