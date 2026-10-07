<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Submitted = 'submitted';
    case PaymentPending = 'payment_pending';
    case PaymentVerified = 'payment_verified';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Passed = 'passed';
    case Failed = 'failed';
    case Withdrawn = 'withdrawn';

    public function label()
    {
        $labels = [
            'submitted' => 'Submitted',
            'payment_pending' => 'Payment Pending',
            'payment_verified' => 'Payment Verified',
            'under_review' => 'Under Review',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'passed' => 'Passed',
            'failed' => 'Failed',
            'withdrawn' => 'Withdrawn',
        ];

        return $labels[$this->value] ?? $this->value;
    }

    /** Statuses an admin can set from the application review form. */
    public static function adminReviewOptions()
    {
        return [
            self::UnderReview,
            self::PaymentVerified,
            self::Accepted,
            self::Rejected,
            self::Withdrawn,
        ];
    }
}
