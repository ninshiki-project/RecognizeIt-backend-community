<?php

namespace App\Mail;

use App\Enum\RedeemDeclineReasonEnum;
use App\Models\Redeem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RedeemDeclinedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Redeem $redeem) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redeem Declined',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.redeem-declined',
            with: [
                'messageContent' => $this->parseEmailContent(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function getEmailContent(string $category): string
    {
        return match ($category) {
            RedeemDeclineReasonEnum::ITEM_TEMPORARILY_UNAVAILABLE->value => RedeemDeclineReasonEnum::ITEM_TEMPORARILY_UNAVAILABLE->getEmailContent(),
            RedeemDeclineReasonEnum::EXCEED_REDEMPTION_LIMIT->value => RedeemDeclineReasonEnum::EXCEED_REDEMPTION_LIMIT->getEmailContent(),
            RedeemDeclineReasonEnum::ITEM_NO_LONGER_AVAILABLE->value => RedeemDeclineReasonEnum::ITEM_NO_LONGER_AVAILABLE->getEmailContent(),
            RedeemDeclineReasonEnum::INCORRECT_ITEM_PRICING->value => RedeemDeclineReasonEnum::INCORRECT_ITEM_PRICING->getEmailContent(),
            default => '',
        };
    }

    public function parseEmailContent(): string
    {
        return Str::of($this->getEmailContent($this->redeem->decline_reason_category))->replace('[$item.name]', "<i><b>{$this->redeem->product->name}</b></i>");
    }
}
