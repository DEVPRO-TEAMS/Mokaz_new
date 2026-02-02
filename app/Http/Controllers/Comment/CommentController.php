<?php

namespace App\Http\Controllers\Comment;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with('property', 'appart');

        if ($request->filled('search')) {
            $query->whereHas('appart', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('commodities', 'like', '%' . $request->search . '%')
                    ->orWhere('nbr_room', 'like', '%' . $request->search . '%')
                    ->orWhere('nbr_bathroom', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtre par date
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut . ' 00:00:00',
                $request->date_fin . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut . ' 00:00:00');
        } elseif ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin . ' 23:59:59');
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('etat', $request->status);
        }

        if (Auth::user()->user_type == 'admin') {
            $comments = $query->orderBy('created_at', 'desc')->get();
        } else {

            $comments = $query->where('partner_uuid', Auth::user()->partner_uuid)->orderBy('created_at', 'desc')->get();
        }
        return view('comments.index', compact('comments'));
    }

    public function approveComment($uuid)
    {
        DB::beginTransaction();
        try {
            
            $comment = Comment::where('uuid', $uuid)->first();
            
            // Vérification si la demande existe
            if (!$comment) {
                Log::info('Commentaire non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Commentaire non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($comment->etat == 'actif') {
                Log::info('Commentaire deja approuvee donc actif');
                return response()->json([
                    'type' => 'error',
                    'urlback' => '',
                    'status' => false,
                    'message' => 'Ce commentaire a déjà été approuvée'
                ], 400);
            }
            $comment->etat = 'actif';
            $comment->save();

            DB::commit();
            
            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => 'Commentaire approuvée avec succès',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'status' => false,
                'message' => "Une erreur s'est produite lors de l'approbation du commentaire",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function rejectComment($uuid)
    {
        DB::beginTransaction();
        try {
            
            $comment = Comment::where('uuid', $uuid)->first();
            
            // Vérification si la demande existe
            if (!$comment) {
                Log::info('Commentaire non trouvee donca 404 error');
                return response()->json([
                    'type' => 'error',
                    'status' => false,
                    'urlback' => '',
                    'message' => 'Commentaire non trouvée'
                ], 404);
            }

            // Vérification si la demande n'est pas déjà approuvée
            if ($comment->etat == 'inactif') {
                Log::info('Commentaire deja rejetee donc actif');
                return response()->json([
                    'type' => 'error',
                    'urlback' => '',
                    'status' => false,
                    'message' => 'Ce commentaire a déjà été rejete'
                ], 400);
            }
            $comment->etat = 'inactif';
            $comment->save();

            DB::commit();
            
            return response()->json([
                'type' => 'success',
                'status' => true,
                'urlback' => 'back',
                'message' => 'Commentaire rejete avec succès',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'status' => false,
                'message' => "Une erreur s'est produite lors du rejet du commentaire",
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $comment = Comment::where('uuid', $uuid)->first();

        $comment->etat = 'inactif';
        $isDeleted = $comment->save();

        if (!$isDeleted) {
            return response()->json([
                'status' => false,
                'message' => 'Une erreur s’est produite lors de la suppression du commentaire.'
            ], 500);
        }else {
            return response()->json([
                'status' => true,
                'message' => 'Le commentaire a été supprimée avec succès.'
            ], 200);
        }
    }
}
