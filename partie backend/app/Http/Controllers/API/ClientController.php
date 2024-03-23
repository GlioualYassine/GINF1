<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $client = new Client();
        $client->name = $request->input('name');
        $client->address = $request->input('address');
        $client->user_id = $this->createUser($request->input('email'), $request->input('password'),$client->name);
        $client->save();

        return response()->json($client,201);
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->name = $request->input('name');
        $client->address = $request->input('address');
        $client->update();

        return response()->json($client);
    }

    // public function destroy($id)
    // {
    //     $client = Client::findOrFail($id);
    //     $client->delete();

    //     return response()->json(['message' => 'Client deleted successfully']);
    // }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
    
        // Supprimer l'utilisateur associé au client s'il existe
        if ($client->user) {
            // Supprimer d'abord les clients associés à l'utilisateur
            $client->user->client()->delete();
            
            // Ensuite, supprimer l'utilisateur
            $client->user->delete();
        }
    
        // Supprimer le client
        $client->delete();
    
        return response()->json(['message' => 'Client and associated user deleted successfully']);
    }
    


    private function createUser($email, $password,$name)
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->role = 0;
        $user->save();

        return $user->id;
    }
}
