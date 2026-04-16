<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    // ... method lain biarkan saja ...

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // Izinkan JIKA user adalah admin DAN user adalah pembuat produk tersebut
        return $user->role === 'admin' && $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Izinkan JIKA user adalah admin DAN user adalah pembuat produk tersebut
        return $user->role === 'admin' && $user->id === $product->user_id;
    }

    // ... method lain biarkan saja ...
}