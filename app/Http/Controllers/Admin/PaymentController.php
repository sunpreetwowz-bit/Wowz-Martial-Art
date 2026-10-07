<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query()->with(['student.user', 'application.beltTest']);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('method')) {
            $query->where('method', $request->input('method'));
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
        ]);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['student.user', 'application.beltTest', 'verifier']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $this->authorize('verify', $payment);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($payment->status === PaymentStatus::Paid) {
            return back()->with('info', 'Payment is already verified.');
        }

        $this->paymentService->verifyOffline($payment, $request->user(), $data['notes'] ?? null);

        return back()->with('success', 'Payment verified successfully.');
    }
}
