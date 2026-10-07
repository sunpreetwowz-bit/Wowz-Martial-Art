<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; margin: 0; padding: 32px; }
        .frame { border: 4px solid #0f172a; padding: 36px 40px; }
        .eyebrow { font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: #64748b; margin: 0; }
        h1 { font-size: 28px; margin: 10px 0 0; }
        .status { float: right; font-size: 11px; text-transform: uppercase; border: 1px solid #cbd5e1; padding: 4px 8px; }
        .center { text-align: center; margin-top: 48px; clear: both; }
        .muted { color: #64748b; font-size: 13px; margin: 0 0 8px; }
        .name { font-size: 30px; font-weight: bold; margin: 8px 0 12px; }
        .belt { font-size: 22px; font-weight: bold; color: #b91c1c; margin: 16px 0; }
        table { width: 80%; margin: 28px auto 0; border-collapse: collapse; }
        td { width: 50%; padding: 8px 12px; vertical-align: top; font-size: 12px; }
        .label { color: #64748b; display: block; margin-bottom: 2px; }
        .value { font-weight: bold; }
        .footer { margin-top: 48px; border-top: 1px solid #e2e8f0; padding-top: 14px; font-size: 11px; color: #64748b; }
        .mono { font-family: DejaVu Sans Mono, monospace; }
    </style>
</head>
<body>
<div class="frame">
    <div class="status">{{ $certificate->status->label() }}</div>
    <p class="eyebrow">{{ config('academy.name') }}</p>
    <h1>Certificate of Achievement</h1>

    <div class="center">
        <p class="muted">This certifies that</p>
        <p class="name">{{ $certificate->student_name_snapshot }}</p>
        <p class="muted">
            Student ID <span class="mono">{{ $certificate->student_code_snapshot }}</span>
            has successfully graded to
        </p>
        <p class="belt">{{ $certificate->belt_name_snapshot }}</p>

        <table>
            <tr>
                <td>
                    <span class="label">Certificate number</span>
                    <span class="value mono">{{ $certificate->certificate_number }}</span>
                </td>
                <td>
                    <span class="label">Issued on</span>
                    <span class="value">{{ optional($certificate->issued_on)->format('d M Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Test date</span>
                    <span class="value">{{ optional($certificate->test_date)->format('d M Y') ?: '—' }}</span>
                </td>
                <td>
                    <span class="label">Authorized by</span>
                    <span class="value">{{ $certificate->authorized_by_name ?? '—' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Verify at <span class="mono">{{ url('/verify/certificate/'.$certificate->certificate_number) }}</span>
        @if ($certificate->isIssued())
            · Code <span class="mono">{{ $certificate->verification_code }}</span>
        @endif
    </div>
</div>
</body>
</html>
