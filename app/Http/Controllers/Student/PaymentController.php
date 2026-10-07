<?php

namespace App\Http\Controllers\Student;

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

    public function checkout(Payment $payment)
    {
        $this->authorize('checkout', $payment);

        $payment->loadMissing(['application.beltTest', 'student.user']);

        if ($payment->status === PaymentStatus::Paid) {
            return redirect()
                ->route('student.applications.show', $payment->application)
                ->with('success', 'This payment is already marked as paid.');
        }

        if ($payment->status === PaymentStatus::Failed) {
            return redirect()
                ->route('student.applications.show', $payment->application)
                ->with('error', 'This payment failed. Contact the academy if you need to retry.');
        }

        return view('student.payments.checkout', compact('payment'));
    }

    public function complete(Request $request, Payment $payment)
    {
        $this->authorize('checkout', $payment);

        $data = $request->validate([
            'outcome' => ['required', 'in:success,failed'],
        ]);

        if ($payment->status !== PaymentStatus::Pending) {
            return redirect()
                ->route('student.applications.show', $payment->application)
                ->with('error', 'This payment is no longer awaiting checkout.');
        }

        $updated = $this->paymentService->completeSandbox(
            $payment,
            $data['outcome'] === 'success',
        );

        $message = $updated->status === PaymentStatus::Paid
            ? 'Sandbox payment completed successfully.'
            : 'Sandbox payment marked as failed.';

        return redirect()
            ->route('student.applications.show', $updated->application)
            ->with($updated->status === PaymentStatus::Paid ? 'success' : 'error', $message);
    }
}
