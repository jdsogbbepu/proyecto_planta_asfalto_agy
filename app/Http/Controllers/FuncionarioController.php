<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Funcionarios/Index', [
            'funcionarios' => Funcionario::orderBy('nombre')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'activo' => ['required', 'boolean'],
        ]);

        Funcionario::create($validated);

        return redirect()->route('funcionarios.index')->with('success', 'Funcionario registrado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'activo' => ['required', 'boolean'],
        ]);

        $funcionario->update($validated);

        return redirect()->route('funcionarios.index')->with('success', 'Funcionario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        try {
            $funcionario->delete();
            return redirect()->route('funcionarios.index')->with('success', 'Funcionario eliminado correctamente.');
        } catch (\Exception $e) {
            // Logically deactivate if tied to transactions
            $funcionario->update(['activo' => false]);
            return redirect()->route('funcionarios.index')->with('success', 'El funcionario tiene registros asociados. Ha sido desactivado lógicamente.');
        }
    }
}
