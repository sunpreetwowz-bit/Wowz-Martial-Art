<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function form()
    {
        return view('web.certificates.verify', [
            'certificateNumber' => null,
            'certificate' => null,
            'codeProvided' => false,
            'searched' => false,
        ]);
    }

    public function show(Request $request, string $certificateNumber, CertificateService $certificates)
    {
        $code = $request->query('code');
        $certificate = null;
        $hasCode = ! empty($code);

        if ($hasCode) {
            $certificate = $certificates->findForPublicVerification($certificateNumber, $code);
        }

        return view('web.certificates.verify', [
            'certificateNumber' => $certificateNumber,
            'certificate' => $certificate,
            'codeProvided' => $hasCode,
            'searched' => $hasCode,
        ]);
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'certificate_number' => ['required', 'string', 'max:100'],
            'verification_code' => ['required', 'string', 'max:64'],
        ]);

        return redirect()->route('certificates.verify', [
            'certificateNumber' => trim($data['certificate_number']),
            'code' => trim($data['verification_code']),
        ]);
    }
}
