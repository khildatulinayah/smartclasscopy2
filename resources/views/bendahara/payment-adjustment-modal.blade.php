<!-- Modal Payment Adjustment -->
<div id="paymentAdjustmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Payment Adjustment (Semua)</h3>
            <button type="button" onclick="closePaymentAdjustmentModal()" class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-semibold">
                Tutup
            </button>
        </div>

        <div class="p-6">
            @if(empty($pendingAdjustments) || $pendingAdjustments->count() === 0)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center text-sm text-gray-700">
                    Tidak ada penyesuaian.
                </div>
            @else
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6 text-center">
                    <div class="text-sm font-semibold text-indigo-800">
                        {{ $pendingAdjustments->count() }} penyesuaian
                    </div>
                    <div class="text-xs text-gray-600">Klik tombol untuk Lunasi/Kembalikan</div>
                </div>

                <div class="space-y-4">
                    @foreach($pendingAdjustments as $adj)
                        @php
                            $studentName = optional($adj->student)->name ?? 'Siswa';
                            $weeklyPayment = $adj->weeklyPayment;
                            $weekLabel = $weeklyPayment ? 'Minggu '.$weeklyPayment->week_number : '-';
                            $dateLabel = $weeklyPayment && isset($weeklyPayment->wednesday_date)
                                ? optional($weeklyPayment->wednesday_date)->locale('id')->translatedFormat('d M Y')
                                : null;
                            $amountText = $adj->formatted_amount;
                            $isShortage = $adj->isShortage();
                            $statusLabel = method_exists($adj, 'isProcessed') && $adj->isProcessed() ? 'Processed' : 'Pending';
                        @endphp

                        <div class="bg-white border border-gray-200 rounded-lg p-4">

                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">{{ $weekLabel }} • {{ $studentName }}</div>
                                    <div class="mt-1 flex items-center gap-2">
                                        @if($isShortage)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-900 text-xs font-semibold">Kurang {{ $amountText }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">Lebih {{ $amountText }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-800">Nominal Adjustment</div>
                                    <div class="text-lg font-bold text-gray-900">Rp {{ number_format($adj->adjustment_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-3">
                                @if($isShortage)
                                    <form method="POST" action="{{ route('bendahara.adjustment.shortage', $adj->id) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600 transition-colors">
                                            Lunasi
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('bendahara.adjustment.refund', $adj->id) }}" class="flex-1">
                                        @csrf
                                        <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
                                            Kembalikan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

