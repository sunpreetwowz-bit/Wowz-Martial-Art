<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Certificate::class);

        $certificates = Certificate::query()
            ->with(['belt', 'beltTest'])
            ->where('student_id', $request->user()->student->id)
            ->latest('issued_on')
            ->paginate(15);

        return view('student.certificates.index', compact('certificates'));
    }

    public function show(Request $request, Certificate $certificate)
    {
        $this->authorize('view', $certificate);

        $certificate->load(['belt', 'beltTest', 'issuer']);

        return view('student.certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        $this->authorize('download', $certificate);

        try {
            return $this->certificateService->downloadPdf($certificate);
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('student.certificates.show', $certificate)
                ->with('error', $e->getMessage());
        }
    }
}
