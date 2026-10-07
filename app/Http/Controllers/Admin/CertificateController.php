<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CertificateStatus;
use App\Http\Controllers\Controller;
use App\Models\BeltTestResult;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->authorizeResource(Certificate::class, 'certificate');
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $query = Certificate::query()->with(['student.user', 'belt', 'beltTest']);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', $search)
                    ->orWhere('student_name_snapshot', 'like', $search)
                    ->orWhere('student_code_snapshot', 'like', $search);
            });
        }

        $certificates = $query->latest('issued_on')->paginate(20);

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'statuses' => CertificateStatus::cases(),
        ]);
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['student.user', 'belt', 'beltTest', 'result', 'issuer']);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function issue(Request $request, BeltTestResult $result)
    {
        $this->authorize('create', Certificate::class);

        $data = $request->validate([
            'authorized_by_name' => ['nullable', 'string', 'max:150'],
        ]);

        try {
            $certificate = $this->certificateService->issueForResult(
                $result,
                $request->user(),
                $data['authorized_by_name'] ?? null,
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate issued successfully.');
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        $this->authorize('revoke', $certificate);

        $data = $request->validate([
            'revoke_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->certificateService->revoke($certificate, $request->user(), $data['revoke_reason']);

        return back()->with('success', 'Certificate revoked.');
    }

    public function download(Certificate $certificate)
    {
        $this->authorize('download', $certificate);

        try {
            return $this->certificateService->downloadPdf($certificate);
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.certificates.show', $certificate)
                ->with('error', $e->getMessage());
        }
    }
}
