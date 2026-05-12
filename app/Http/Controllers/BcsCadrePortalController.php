<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\MealOrderRequest;
use App\Models\Feedback;
use App\Models\MealOrder;
use App\Models\MenuItem;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BcsCadrePortalController extends Controller
{
    public function booking(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.booking', [
            'activeMenu' => 'booking',
            'rooms' => Room::query()
                ->where('status', 'available')
                ->orderBy('floor')
                ->orderBy('room_number')
                ->get(),
        ]);
    }

    public function mealOrder(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.meal-order', [
            'activeMenu' => 'meal',
            'mealOptions' => $this->mealOptions(),
            'orders' => MealOrder::query()
                ->where('cadre_reference', BcsCadreAuthController::DEMO_CADRE_REFERENCE)
                ->latest('order_date')
                ->latest()
                ->get(),
            'editingOrder' => $this->editableMealOrder($request),
        ]);
    }

    public function storeMealOrder(MealOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $menuItem = MenuItem::query()
            ->where('name', $validated['menu_item'])
            ->where('meal_type', $validated['meal_type'])
            ->where('is_active', true)
            ->first();
        $unitPrice = $menuItem ? (float) $menuItem->price_bcs : ($this->mealOptions()[$validated['menu_item']]['price'] ?? 0);
        $reference = $this->uniqueMealReference();

        MealOrder::query()->create([
            ...$validated,
            'cadre_reference' => BcsCadreAuthController::DEMO_CADRE_REFERENCE,
            'ref' => $reference,
            'reference' => $reference,
            'menu_item_id' => $menuItem?->id,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * (int) $validated['quantity'],
            'total' => $unitPrice * (int) $validated['quantity'],
            'status' => 'pending',
        ]);

        return redirect()->route('cadre.meals');
    }

    public function updateMealOrder(MealOrderRequest $request, MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfDifferentCadre($mealOrder->cadre_reference);

        $validated = $request->validated();
        $menuItem = MenuItem::query()
            ->where('name', $validated['menu_item'])
            ->where('meal_type', $validated['meal_type'])
            ->where('is_active', true)
            ->first();
        $unitPrice = $menuItem ? (float) $menuItem->price_bcs : ($this->mealOptions()[$validated['menu_item']]['price'] ?? 0);

        $mealOrder->update([
            ...$validated,
            'menu_item_id' => $menuItem?->id,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * (int) $validated['quantity'],
            'total' => $unitPrice * (int) $validated['quantity'],
        ]);

        return redirect()->route('cadre.meals');
    }

    public function destroyMealOrder(MealOrder $mealOrder): RedirectResponse
    {
        $this->abortIfDifferentCadre($mealOrder->cadre_reference);

        $mealOrder->delete();

        return redirect()->route('cadre.meals');
    }

    public function feedback(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        return view('bcs-cadre.feedback', [
            'activeMenu' => 'feedback',
            'feedbackOptions' => $this->feedbackOptions(),
            'feedbackItems' => Feedback::query()
                ->where('cadre_reference', BcsCadreAuthController::DEMO_CADRE_REFERENCE)
                ->latest()
                ->get(),
            'editingFeedback' => $this->editableFeedback($request),
        ]);
    }

    public function storeFeedback(FeedbackRequest $request): RedirectResponse
    {
        Feedback::query()->create([
            'cadre_reference' => BcsCadreAuthController::DEMO_CADRE_REFERENCE,
            'options' => array_values($request->validated('options')),
            'status' => 'submitted',
        ]);

        return redirect()->route('cadre.feedback');
    }

    public function updateFeedback(FeedbackRequest $request, Feedback $feedback): RedirectResponse
    {
        $this->abortIfDifferentCadre($feedback->cadre_reference);

        $feedback->update([
            'options' => array_values($request->validated('options')),
        ]);

        return redirect()->route('cadre.feedback');
    }

    public function destroyFeedback(Feedback $feedback): RedirectResponse
    {
        $this->abortIfDifferentCadre($feedback->cadre_reference);

        $feedback->delete();

        return redirect()->route('cadre.feedback');
    }

    public function billing(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $charges = Room::query()
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room, int $index): array {
                $days = ($index % 3) + 1;

                return [
                    'reference' => 'BK-' . str_pad((string) (83185 - ($index * 1413)), 5, '0', STR_PAD_LEFT),
                    'check_in' => now()->subDays($index + 2)->toDateString(),
                    'check_out' => now()->subDays($index + 2)->addDays($days)->toDateString(),
                    'days' => $days,
                    'total' => (int) $room->base_rate * $days,
                    'status' => $index % 4 === 0 ? 'confirmed' : 'pending',
                ];
            });

        return view('bcs-cadre.billing', [
            'activeMenu' => 'billing',
            'charges' => $charges,
            'totalBookings' => $charges->count(),
            'outstanding' => $charges->where('status', 'pending')->sum('total'),
            'paid' => $charges->where('status', 'confirmed')->sum('total'),
        ]);
    }

    private function guard(Request $request): ?RedirectResponse
    {
        if ($request->session()->get('cadre_auth') !== true) {
            return redirect()->route('home');
        }

        return null;
    }

    private function mealOptions(): array
    {
        $items = MenuItem::query()
            ->where('is_active', true)
            ->orderBy('meal_type')
            ->orderBy('name')
            ->get();

        if ($items->isNotEmpty()) {
            return $items
                ->mapWithKeys(fn (MenuItem $item): array => [
                    $item->name => [
                        'meal' => $item->meal_type,
                        'price' => (float) $item->price_bcs,
                    ],
                ])
                ->all();
        }

        return [
            'Paratha set' => ['meal' => 'breakfast', 'price' => 50],
            'Rice and chicken' => ['meal' => 'lunch', 'price' => 100],
            'Vegetable khichuri' => ['meal' => 'lunch', 'price' => 80],
            'Rice and fish' => ['meal' => 'supper', 'price' => 120],
            'Light supper' => ['meal' => 'supper', 'price' => 70],
        ];
    }

    private function feedbackOptions(): array
    {
        return [
            'Room cleanliness',
            'Meal quality',
            'Staff behavior',
            'Maintenance support',
            'Billing concern',
            'Booking experience',
        ];
    }

    private function editableMealOrder(Request $request): ?MealOrder
    {
        $id = $request->integer('edit');
        if ($id === 0) {
            return null;
        }

        return MealOrder::query()
            ->where('cadre_reference', BcsCadreAuthController::DEMO_CADRE_REFERENCE)
            ->find($id);
    }

    private function editableFeedback(Request $request): ?Feedback
    {
        $id = $request->integer('edit');
        if ($id === 0) {
            return null;
        }

        return Feedback::query()
            ->where('cadre_reference', BcsCadreAuthController::DEMO_CADRE_REFERENCE)
            ->find($id);
    }

    private function uniqueMealReference(): string
    {
        do {
            $reference = 'MO-' . Str::upper(Str::random(5));
        } while (MealOrder::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function abortIfDifferentCadre(string $cadreReference): void
    {
        abort_if($cadreReference !== BcsCadreAuthController::DEMO_CADRE_REFERENCE, 404);
    }
}
