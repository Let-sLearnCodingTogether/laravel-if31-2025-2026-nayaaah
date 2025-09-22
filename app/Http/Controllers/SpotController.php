<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Models\Categories;
use App\Models\Spot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpotRequest $request)
    {
        try {
            $validated = $request->safe()->all();
            
            // menyimpan gambar ke disk public dengan nama file yang random
            $picture_path = Storage::disk('public')->putFile('spots', $request->file('picture'));

            $validated['user_id'] = Auth::user()->id;
            $validated['picture'] = $picture_path;

            $spot = Spot::create($validated);

            if($spot) {
                $categories = [];
                
                foreach ($validated['category'] as $category) {
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                ];
                }

                Categories::fillAndInsert($categories);

                return Response::json([
                    'message' => "Berhasil menyimpan spot",
                    'data => null'
                ], 201);
            }

            return Response::json([
                'message' => "Gagal membuat spot baru",
                'data' => null
            ], 500);

        }catch(Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
            
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Spot $spot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spot $spot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Spot $spot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spot $spot)
    {
        //
    }
}
