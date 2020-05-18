<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
/* ログイン情報を取得するためのライブラリ */
use Illuminate\Support\Facades\Auth;

use App\Mail\Buy;
use Illuminate\Support\Facades\Mail;



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


    public function store(Request $request)
    {
        /* リクエストパラメータにpostという情報が含まれているか */
        if ($request->has('post')) {

            /* MailクラスとBuyクラスを使ってメールを送信する */
            Mail::to(Auth::user()->email)->send(new Buy());
            /* ログインユーザIDを取得して、そのIDが保持するcartitemを消去 */
            CartItem::where('user_id', Auth::id())->delete();
            /* 購入完了画面へ遷移 */
            return view('buy/complete');
        }
        /* フォームのリクエスト情報をセッションに記録する */
        $request->flash();
        /* 購入画面のビューを再度表示 */
        return $this->index();
    }
}
