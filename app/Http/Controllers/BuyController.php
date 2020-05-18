<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
/* ログイン情報を取得するためのライブラリ */
use Illuminate\Support\Facades\Auth;

class BuyController extends Controller
{
    public function index()
    {
        /* Auth::~　でログイン情報を取得できる */
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
            ->where('user_id', Auth::id())
            ->join('items','items.id','=','cart_items.item_id')
            ->get();
        $subtotal = 0;
        foreach ($cartitems as $cartitem) {
            $subtotal += $cartitem->amount * $cartitem->quantity;
        }
        return view('buy/index', ['cartitems' => $cartitems, 'subtotal'=> $subtotal]);
    }

    /* 入力した郵送先の情報を処理するstore()アクション */
    public function store(Request $request)
    {
        /* POST情報があれば分岐 */
        if ($request->has('post')) {
            CartItem::where('user_id', Auth::id())->delete();
            return view('buy/complete');
        }
        $request->flash();
        return $this->index();
    }
}
