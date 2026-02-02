<?php

namespace App\Http\Controllers\Comment;

use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TestimoniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonial::all();
        return view('testimonials.index', compact('testimonials'));
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $testimonials = Testimonial::create([
                'uuid' => Str::uuid(),
                'code' => Refgenerate(Testimonial::class, 'T', 'code'),
                'name' => $request->name,
                'fonction' => 'Utilisateur de la plateforme MOKAZ',
                'content' => $request->content,
            ]);
            if (!$testimonials) {
                return response()->json([
                    'type' => 'error',
                    'urlback' => "",
                    'message' => "Une erreur est survenue lors de l'enregistrement",
                    'code' => 500,
                ]);
            }
            DB::commit();
            return response()->json([
                'type' => 'success',
                'urlback' => "back",
                'message' => "Enregistrer avec succes!",
                'code' => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => "",
                'message' => "Une erreur systeme est survenue " . $th,
                'code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        DB::beginTransaction();
        try {
            $testimonials = Testimonial::where('uuid', $uuid)->first();
            $testimonials->update([
                'name' => $request->name,
                'content' => $request->content,
            ]);
            if (!$testimonials) {
                return response()->json([
                    'type' => 'error',
                    'urlback' => "",
                    'message' => "Une erreur est survenue lors de la mise a jour",
                    'code' => 500,
                ]);
            }
            DB::commit();
            return response()->json([
                'type' => 'success',
                'urlback' => "back",
                'message' => "Le témoignage a été mise a jour!",
                'code' => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => "",
                'message' => "Une erreur systeme est survenue " . $th,
                'code' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $testimonials = Testimonial::where('uuid', $uuid)->first();
        $testimonials->delete();
        if (!$testimonials) {
            return response()->json([
                'type' => 'error',
                'urlback' => "",
                'message' => "Une erreur est survenue lors de la suppression",
                'code' => 500,
            ]);
        }
        return response()->json([
            'type' => 'success',
            'urlback' => "back",
            'message' => "Supprimer avec succes!",
            'code' => 200,
        ]);
    }
}
