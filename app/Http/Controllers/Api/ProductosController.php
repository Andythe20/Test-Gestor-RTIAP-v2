<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    /**
     * Lista los productos del tenant actual, con soporte para busqueda y paginacion.
     */
    public function index(Request $request)
    {
        // Construye la consulta base
        // productos que pertenecen al tenant actual ordenados por fecha de creacion descendente
        $query = Producto::on('tenant')->orderByDesc('created_at');

        // Aplica filtro de busqueda si se proporciona el parametro 'q'
        if ($q = $request->query('q')) {

            $q = trim($request->query('q', '')); // Limpiar espacios en blanco

            // Si q no viene vacio, filtrar por nombre o descripcion que contengan q
            if ($q !== '') {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = $perPage > 0 ? min(200, $perPage) : 20;

        return response()->json($query->paginate($perPage));
    }

    /**
     * Muestra los detalles de un producto especifico por id.
     */
    public function show(Request $request, $path, $id)
    {
        $producto = Producto::on('tenant')->find($id);

        if (! $producto) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['producto' => $producto]);
    }
}
