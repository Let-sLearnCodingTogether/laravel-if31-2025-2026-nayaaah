<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Models\Categories;
use App\Models\Spot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $spots = Spot::with([
                'user:id,name',
                'categories:category,spot_id'
            ])
            ->withCount([
                'reviews'
                ])
                ->withSum('reviews', 'rating')
                ->orderBy('created_at', 'desc')
                ->paginate(request('size', 10));

            return Response::json([
                'message' => "List Spot",
                'data' => $spots
            ], 200);

        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 200);
        }
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
        try {
            return Response::json([
                'message' => "Detail Spot",
                'data' => $spot->load([
                    'user:id,name',
                    'category:category,spot_id'
                ])
                ->loadCount([
                    'reviews'
                ])
                ->loadSum('reviews', 'rating')
                ], 200);
        }catch(Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
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
    public function update(UpdateSpotRequest $request, Spot $spot)
    {
        try {
            $validate = $request->safe()->all();

            // cek apakah request ada picture
            if(isset($validate['picture'])) {

            }$picture_path = Storage::disk('public')->putFile('spots',$request->file('picture'));

            // apakah request ada category
            if(isset($validate['category'])) {
                Categories::where('spot_id', $spot->id)->delete();

            $category =[];

            foreach ($validate['category'] as $category) {
                $categories[] = [
                    'spot_id' => $spot->id,
                    'category' => $category
                ];
            }

            Categories::fillAndInsert($categories);
        }
        $spot->update([
            'name' => $validate['name'],
            'picture' => $picture_path ?? $spot->picture,
            'address' => $validate['address']
        ]);

        return Response::json([
            'message' => "Berhasil update spot",
            'data' => $spot
        ], 200);

        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spot $spot)
    {
        try{
            $user = Auth::user();

            if($spot->user_id == $user->id || $user->role == 'ADMIN'){
                if($spot->delete()) {
                    return Response::json([
                        'message' => "Spot berhasil di hapus",
                        'data' => null
                    ], 200);
                }
            } else {
                return Response::json([
                    'message' => "Spot gagal di hapus",
                    'data' => null
                ], 200);
            }
        }catch(Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
