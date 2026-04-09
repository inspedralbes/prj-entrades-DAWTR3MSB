<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\Seient;
use Illuminate\Support\Facades\Http;

use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seients_ids' => 'required|array',
            'seients_ids.*' => 'exists:seients,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        $seients = Seient::with('esdeveniment')->whereIn('id', $validated['seients_ids'])->get();

        foreach ($seients as $seient) {
            if ($seient->estat === 'venut') {
                return response()->json(['message' => "El seient {$seient->numero} ja ha estat venut."], 400);
            }
        }

        $purchases = [];
        
        DB::transaction(function () use ($seients, $validated, &$purchases) {
            /** @var Seient $seient */
            foreach ($seients as $seient) {
                $purchase = Purchase::create([
                    'seient_id' => $seient->id,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'amount' => $seient->preu,
                ]);

                $seient->update(['estat' => 'venut']);
                $purchases[] = $purchase;

                // Notificar al socket per cada seient
                try {
                    Http::post('http://socket:3001/internal/broadcast', [
                        'event' => 'seient_actualitzat',
                        'data' => ['seientId' => $seient->id, 'estat' => 'venut']
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Error avisant al socket: " . $e->getMessage());
                }
            }
        });

        // Enviar Correu amb el resum i QR (simulat via MailHog)
        try {
            Mail::to($validated['customer_email'])->send(new TicketMail($seients, $validated['customer_name']));
        } catch (\Exception $e) {
            \Log::error("Error enviant correu: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Compra realitzada amb èxit',
            'purchases' => $purchases,
            'event' => $seients->first()->esdeveniment->nom,
            'date' => $seients->first()->esdeveniment->data_hora,
            'recinte' => $seients->first()->esdeveniment->recinte,
            'seats' => $seients->map(fn($s) => ['fila' => $s->fila, 'numero' => $s->numero])
        ], 201);
    }
}
