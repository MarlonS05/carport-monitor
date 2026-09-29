<?php

declare(strict_types=1);

namespace App\Livewire\Monitor;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Connect extends Component
{
    public function render(): View
    {
        $qrCodeSvg = (new Builder(
            writer: new SvgWriter,
            data: url('/'),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 192,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))->build()->getString();

        return view('livewire.monitor.connect', [
            'qrCodeSvg' => $qrCodeSvg,
        ])
            ->layout('layouts.monitor', [
                'activeNav' => 'connection',
                'connected' => false,
                'showHeader' => false,
            ]);
    }
}
