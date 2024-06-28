<?php

namespace App\Http\Controllers;

//使用するモデルを記載
use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;

class productsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //商品一覧画面
    //CRUD→Read(読み取り)
    //メゾット→index=データ一覧表示
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $companyId = $request->input('company');

            $query = Product::query();

            if ($search) {
                $query->where('product_name', 'like', '%' . $search . '%');
            }

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $products = $query->get();
            $companies = Company::all();

            return view('products.index', compact('products', 'search', 'companies', 'companyId'));
        } catch (\Exception $e) {
            
            return back()->withErrors(['error' => 'エラーが発生しました。']);
        }

        //$products = Product::all();
        //$companies = Company::get();
        //Productモデルに基づいて操作要求(クリエ)を初期化
        //この行の後にクエリを逐次構築
        //$query = Product::query();

        //if($search = $request->search){
            //$query->where('product_name','LIKE',"%{$search}%")->get();
        //}

        //if($search = $request->search){
            //$query->where('company_id',  'LIKE', "%{$search}%")->get();
        //}
    
        $products = $query->paginate(10); //10個で１ページ
        
    
        // 商品一覧ビューを表示し、取得した商品情報をビューに渡す
        //return view('products.index', ['products' => $products], compact('companies', 'products'));
       
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Create(作成)
    //create=新規作成用フォーム表示
    public function create()
    {
        //商品新規登録画面
        //会社情報必要
        $companies = Company::all();
                
        return view('products.create', compact('companies'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     //store=データ新規保存
     //商品新規登録
    public function store(ProductRequest $request, Product $product) {     

        // アップロードされた画像を取得
        $file = $request->file('img_path');
        // 取得したファイル名で保存
        if ($file) {
            $file_name = $file->getClientOriginalName();
            $file->storeAs('public/products', $file_name);
        } else {
            $file_name = null;
        }

        $product->product_name = $request->product_name;
                    $product->company_id = $request->company_id;
                    $product->price = $request->price;
                    $product->stock = $request->stock;
                    $product->comment = $request->comment;
                    $product->img_path = $file;
                    $product->save();

        //トランザクション
        DB::beginTransaction();
        try {
        // 登録処理呼び出し
           $product = new Product();
           $product ->createProduct($request, $file_name);
        DB::commit();
        } catch (\Exception $e) {
        DB::rollback();
        return back();
        }


        //処理が完了したら自画面にリダイレクト
        return redirect()->route('products.create');
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     //Read(読み取り)
     //show=データ個別表示
    public function show($id)
    {
        //商品情報詳細画面
        //指定されたIDでデータベースから検索する
        $product = Product::find($id);
        $companies = Company::all();

        return view('products.show',compact('companies','product') );

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

  
    //edit=データ編集用フォーム表示
     public function edit($id)
    {
        $product = Product::find($id);
        $companies = Company::all();
        //→会社情報が必要

        return view('products.edit', compact('product', 'companies'));

    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Update(更新)
    //update=データ更新
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            // 既存画像を取得
            $path = $request->img_path;

            // 新規画像を取得
            $img = $request->file('img_path');
            
            // 画像を更新する場合
            if (isset($img)) {
                
                // 現在の画像ファイルの削除
                $img_name = $product->img_path;

                // /storage/app/public/img/画像ファイル名 を削除
                $img_name = str_replace('public/products/', '', $img_name);
                Storage::disk('public')->delete('products/' . $img_name);

                // 拡張子付きでファイル名を取得
                $filename_with_ext = $img->getClientOriginalName();

                // ファイル名のみを取得
                $filename = pathinfo($filename_with_ext, PATHINFO_FILENAME);

                // 拡張子を取得
                $extension = $img->getClientOriginalExtension();

                // 保存するファイル名を構築
                $filename_to_store = $filename."_".date('Ymd_His').".".$extension;

                // 画像フォームでリクエストした画像を取得してstorage > public > img配下に画像を保存
                $path = $img->storeAs("public/products", $filename_to_store);

                // リサイズされた画像を保存
                // Image::make($img)->resize(
                //     450, // 横幅
                //     300, // 縦幅
                //     function ($constraint) {
                //         $constraint->aspectRatio();
                //         $constraint->upsize();
                //     } 
                // )->save(storage_path('app/public/img/'. $filename_to_store));

                // store処理が実行できたらDBに保存処理を実行
                if ($path) {
                    // DBに登録する処理
                    $product->product_name = $request->product_name;
                    $product->company_id = $request->company_id;
                    $product->price = $request->price;
                    $product->stock = $request->stock;
                    $product->comment = $request->comment;
                    $product->img_path = $path;
                    $product->save();
                }
            }

            // 画像を更新しない場合
            if (!isset($img)) {

                $product->product_name = $request->product_name;
                $product->company_id = $request->company_id;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->comment = $request->comment;
                $product->save();
            }

            return to_route('products.edit', ['id' => $product->id ] );
        } catch (\Exception $e) {
            report($e);
            session()->flash('flash_message', '更新が失敗しました');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Delete(削除)
    //destroy=データ削除
        /**
     * 削除処理
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product_id = $product->id;
            $product->delete();

            return to_route('/products');

        }catch (\Exception $e){
            report($e);
            session()->flash('flash_message', '更新が失敗しました');
        }
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
