<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.pages.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $partner_uuid = Auth::user()->partner_uuid;

        try {

            $user = User::create([
                'uuid' => Str::uuid(),
                'code' => Refgenerate(User::class, 'U', 'code'),
                'name' => $request->first_name,
                'lastname' => $request->last_name,
                'user_type' => 'partner',
                'phone' => $request->phone,
                'partner_uuid' => $partner_uuid,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
                'etat' => 'actif',
            ]);

            DB::commit();

            return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Une erreur s\'est produite lors de la création de l\'utilisateur' . $e], 500);
        }
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, $uuid)
    {

        try {
            DB::beginTransaction();

            $user = User::where('uuid', $uuid)->update([
                'name'       => $request->name,
                'lastname'   => $request->lastname,
                'phone'      => $request->phone,
                'email'      => $request->email,
            ]);

            Log::info('User updated', ['user' => $user]);

            DB::commit();

            if ($user) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Mise à jour reussie!",
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la mise à jour!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }

    /**
     * Remove (soft delete) the specified user.
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($uuid);
            $user->delete();

            DB::commit();

            if ($user) {

                $dataResponse = [
                    'type' => 'success',
                    'urlback' => "back",
                    'message' => "Supprimé avec succes!",
                    'code' => 200,
                ];
                DB::commit();
            } else {
                DB::rollback();
                $dataResponse = [
                    'type' => 'error',
                    'urlback' => '',
                    'message' => "Erreur lors de la suppression!",
                    'code' => 500,
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $dataResponse = [
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur systeme! $th",
                'code' => 500,
            ];
        }
        return response()->json($dataResponse);
    }
}
