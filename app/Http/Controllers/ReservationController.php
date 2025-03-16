<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\Quota;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Str;
use App\Services\ReservationService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    protected $reservationService;
    protected $stripe;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function index()
    {
        return redirect()->route('reservation.step1');
    }

    public function step1()
    {
        $availableDays = Cache::remember('available_days', now()->addMinutes(15), function () {
            return Slot::with('association')
                ->available()
                ->get()
                ->groupBy('date')
                ->mapWithKeys(function ($slots, $date) {
                    return [
                        $date => [ // Clé = date brute
                            'date' => $date,
                            'formatted_date' => now()->parse($date)->translatedFormat('l j F Y'),
                            'available_slots' => $slots->sum(...),
                            'status' => $this->getAvailabilityStatus(...)
                        ]
                    ];
                });
        });

        return view('reservation.partials.step1-days', [ // ← Correction du nom
            'currentStep' => 1,
            'availableDays' => $availableDays
        ]);
    }

    public function selectDay(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|date|after_or_equal:today'
        ]);

        $slot = Slot::whereDate('date', $validated['day'])
            ->available()
            ->firstOrFail();

        session()->put('reservation', [
            'step' => 2,
            'day' => $validated['day'],
            'association_id' => $slot->association_id
        ]);

        return redirect()->route('reservation.step2');
    }

    public function step2()
    {
        $this->validateStep(2);

        $slots = Slot::with('association')
            ->whereDate('date', session('reservation.day'))
            ->available()
            ->orderBy('start_time')
            ->get();

        // Dans la méthode step2()
        return view('reservation.partials.step2-hours', [ // ← Correction du nom
            'currentStep' => 2,
            'timeSlots' => $slots,
            'selectedDayFormatted' => now()->parse(session('reservation.day'))->translatedFormat('l j F Y')
        ]);
    }

    public function selectSlot(Request $request)
    {
        $this->validateStep(2);

        $validated = $request->validate([
            'slot_id' => 'required|exists:slots,id'
        ]);

        $slot = Slot::findOrFail($validated['slot_id']);
        
        if (!$slot->isAvailable()) {
            return back()->withErrors(['slot' => 'Ce créneau n\'est plus disponible']);
        }

        session()->put('reservation', array_merge(session('reservation'), [
            'step' => 3,
            'slot_id' => $validated['slot_id'],
            'slot_start_time' => $slot->start_time->format('H:i'),
            'slot_end_time' => $slot->end_time->format('H:i')
        ]));

        return redirect()->route('reservation.step3');
    }

    public function step3()
    {
        $this->validateStep(3);

        $quota = Quota::where('association_id', session('reservation.association_id'))
            ->firstOrFail();

        // Dans la méthode step3()
        return view('reservation.partials.step3-config', [ // ← Correction du nom
            'currentStep' => 3,
            'max_quantity' => min(5, $quota->{session('reservation.size', 'grand')} ?? 5),
            'quota' => $quota
        ]);
    }

    public function confirm(Request $request)
    {
        $this->validateStep(3);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:5',
            'size' => 'required|in:grand,moyen,petit'
        ]);

        $quota = Quota::where('association_id', session('reservation.association_id'))
            ->firstOrFail();

        if ($quota->{$validated['size']} < $validated['quantity']) {
            return back()->withErrors(['size' => 'Stock insuffisant pour cette taille']);
        }

        session()->put('reservation', array_merge(session('reservation'), [
            'step' => 4,
            'quantity' => $validated['quantity'],
            'size' => $validated['size']
        ]));

        return redirect()->route('reservation.step4');
    }

    public function processPayment(Request $request)
    {
        $this->validateStep(4);
        $reservationData = session('reservation');

        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => 20000,
                'currency' => 'eur',
                'metadata' => [
                    'user_id' => auth()->id(),
                    'reservation' => json_encode($reservationData)
                ],
                'payment_method_types' => ['card'],
                'description' => 'Acompte réservation agneau Eid'
            ]);

            $reservation = $this->reservationService->processReservation(
                $this->reservationService->prepareReservationData(
                    $reservationData,
                    $paymentIntent->id
                )
            );

            session()->forget('reservation');

            return view('reservation.partials.step4', [
                'currentStep' => 4,
                'clientSecret' => $paymentIntent->client_secret,
                'reservation' => $reservation
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['payment' => $e->getMessage()]);

        } catch (\Exception $e) {
            Log::error('Reservation Error: ' . $e->getMessage());
            return redirect()->route('reservation.partials.step1')->withErrors(['global' => $e->getMessage()]);
        }
    }

    public function generatePdf($code)
    {
        $reservation = Reservation::with(['slot.association', 'user'])
            ->where('code', $code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.reservation', [
            'reservation' => $reservation,
            'date' => now()->parse($reservation->slot->date)->translatedFormat('l j F Y'),
            'time' => $reservation->slot->start_time->format('H:i')
        ]);

        return $pdf->download("reservation-{$code}.pdf");
    }

    private function validateStep($requiredStep)
    {
        $currentStep = session('reservation.step', 1);
        
        if ($currentStep < $requiredStep) {
            abort(redirect()->route('reservation.step' . $currentStep)
                ->withErrors(['step' => 'Veuillez compléter les étapes précédentes']));
        }
    }

    private function getAvailabilityStatus($totalCapacity)
    {
        return match(true) {
            $totalCapacity === 0 => 'complet',
            $totalCapacity < 5 => 'presque_complet',
            default => 'disponible'
        };
    }
}