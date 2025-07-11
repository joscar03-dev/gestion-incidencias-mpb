<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

// Ruta para crear ticket rápido desde la página de bienvenida
Route::post('/reportar/tickets', function () {
    // Solo usuarios autenticados pueden crear tickets
    if (!Auth::check()) {
        return redirect('/login')->with('error', 'Debes iniciar sesión para reportar una incidencia');
    }

    $validated = request()->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
        'area_id' => 'required|exists:areas,id',
    ]);

    $ticket = \App\Models\Ticket::create([
        'titulo' => $validated['titulo'],
        'descripcion' => $validated['descripcion'],
        'prioridad' => $validated['prioridad'],
        'area_id' => $validated['area_id'],
        'creado_por' => Auth::id(),
        'estado' => 'Abierto',
        // Campos de asignación como null inicialmente
        'asignado_a' => null,
        'asignado_por' => null,
    ]);

    return redirect('/')->with('success', 'Incidencia reportada exitosamente. Ticket #' . $ticket->id);
})->name('tickets.quick-create');

// Notification routes
Route::middleware('auth')->group(function () {
    Route::get('/web/notifications', [NotificationController::class, 'index']);
    Route::patch('/web/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/web/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    
    // Rutas para dispositivos de usuario
    Route::get('/dispositivos', App\Livewire\DispositivosUsuario::class)->name('dispositivos.usuario');
});

require __DIR__.'/auth.php';
