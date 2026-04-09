<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\Esdeveniment;
use App\Models\Seient;

class AdminController extends Controller
{
    public function stats()
    {
        $totalRevenue = Purchase::sum('amount');
        $totalSold = Seient::where('estat', 'venut')->count();
        $totalSeats = Seient::count();
        $occupancyPercent = $totalSeats > 0 ? round(($totalSold / $totalSeats) * 100, 2) : 0;

        $events = Esdeveniment::withCount(['seients', 'seients as venuts_count' => function($query) {
            $query->where('estat', 'venut');
        }])->get();

        $eventsDetail = $events->map(function($ev) {
            return [
                'id' => $ev->id,
                'nom' => $ev->nom,
                'occupancy' => $ev->seients_count > 0 ? round(($ev->venuts_count / $ev->seients_count) * 100, 1) : 0
            ];
        });

        $recentSales = Purchase::with('seient.esdeveniment')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'customer_name' => $p->customer_name,
                    'amount' => $p->amount,
                    'event_nom' => $p->seient->esdeveniment->nom ?? 'N/A'
                ];
            });

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_sold' => $totalSold,
            'occupancy_percent' => $occupancyPercent,
            'events_detail' => $eventsDetail,
            'recent_sales' => $recentSales
        ]);
    }

    public function createEvent(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'data_hora' => 'required|date',
            'recinte' => 'required|string',
            'aforament' => 'required|integer|min:1'
        ]);

        if (!isset($validated['imatge_url'])) {
            $validated['imatge_url'] = 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1000&auto=format&fit=crop';
        }

        if (!isset($validated['imatge_url'])) {
            $validated['imatge_url'] = 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1000&auto=format&fit=crop';
        }

        $event = Esdeveniment::create($validated);
        
        // Auto-crear seients per defecte per a l'esdeveniment
        for ($i = 1; $i <= $event->aforament; $i++) {
            Seient::create([
                'esdeveniment_id' => $event->id,
                'fila' => ceil($i / 10),
                'numero' => ($i - 1) % 10 + 1,
                'estat' => 'disponible',
                'preu' => 30.00
            ]);
        }

        return response()->json($event, 201);
    }
}
