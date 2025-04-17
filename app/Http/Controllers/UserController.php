<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Listar todos os usuários.
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['error' => 'Falha ao buscar os usuários.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Criar um novo usuário.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Falha ao criar o usuário.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Exibir um usuário específico.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Falha ao buscar o usuário.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Atualizar um usuário existente.
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $data = $request->only(['name', 'email', 'password']);
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);

            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Falha ao atualizar o usuário.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Excluir um usuário.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'Usuário excluído com sucesso.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuário não encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Falha ao excluir o usuário.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
