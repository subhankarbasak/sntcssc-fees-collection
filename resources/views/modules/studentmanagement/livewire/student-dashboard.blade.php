<!-- resources/views/modules/studentmanagement/livewire/student-dashboard.blade.php -->
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Student Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ $student->user->name }}!</p>
    </div>
    
    <!-- Profile Summary -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <img src="{{ $student->user->getFirstMediaUrl('photos') ?: asset('images/default-avatar.png') }}" 
                 alt="Profile Photo" class="w-16 h-16 rounded-full object-cover mr-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $student->user->name }}</h2>
                <p class="text-gray-600 dark:text-gray-400">Student ID: {{ $student->student_id }}</p>
                <p class="text-gray-600 dark:text-gray-400">Category: {{ $student->category }}</p>
            </div>
        </div>
    </div>
    
    <!-- Enrollments -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">My Enrollments</h3>
        </div>
        
        @if ($enrollments->count() > 0)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($enrollments as $enrollment)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $enrollment->batch->programme->name }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Batch: {{ $enrollment->batch->name }}</p>
                        @if ($enrollment->section)
                            <p class="text-sm text-gray-600 dark:text-gray-400">Section: {{ $enrollment->section->name }}</p>
                        @endif
                        <div class="mt-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($enrollment->status === 'active')
                                    bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif ($enrollment->status === 'completed')
                                    bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                @elseif ($enrollment->status === 'inactive')
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                @else
                                    bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @endif">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">You are not enrolled in any programmes yet.</p>
        @endif
    </div>
    
    <!-- Fee Summary -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Fee Status</h3>
                <span class="text-2xl font-bold {{ $totalDue > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                    ₹{{ number_format($totalDue, 2) }}
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if ($totalDue > 0)
                    You have outstanding fees. Please make the payment at the earliest.
                @else
                    All your fees are paid up to date.
                @endif
            </p>
            @if ($totalDue > 0)
                <button wire:click="openPaymentModal" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    Pay Now
                </button>
            @endif
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Upcoming Fees</h3>
            @if ($upcomingFees->count() > 0)
                <div class="space-y-3">
                    @foreach ($upcomingFees as $fee)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $fee->feeStructure->feeType->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Due: {{ $fee->due_date->format('d M Y') }}</p>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">
                                ₹{{ number_format($fee->status === 'pending' ? $fee->amount : ($fee->amount - $fee->payments->sum('amount')), 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400">No upcoming fees.</p>
            @endif
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Transactions</h3>
        @if ($recentTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($recentTransactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if ($transaction->type === 'payment')
                                            bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @else
                                            bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                        @endif">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">₹{{ number_format($transaction->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($transaction->payment_method) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">No transactions found.</p>
        @endif
    </div>
    
    <!-- Payment Modal -->
    <x-modal wire:model="showPaymentModal" title="Make Payment">
        <form wire:submit.prevent="processPayment">
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Select Fees to Pay</h4>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach ($this->student->fees()->whereIn('status', ['pending', 'partial'])->orderBy('due_date')->get() as $fee)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-md">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="selectedFees" value="{{ $fee->id }}" wire:change="calculatePaymentAmount"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $fee->feeStructure->feeType->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Due: {{ $fee->due_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    ₹{{ number_format($fee->status === 'pending' ? $fee->amount : ($fee->amount - $fee->payments->sum('amount')), 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <label for="paymentMethod" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                    <select wire:model="paymentMethod" id="paymentMethod" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
                
                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Total Amount</span>
                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">₹{{ number_format($paymentAmount, 2) }}</span>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showPaymentModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3" :disabled="{{ $paymentAmount <= 0 }}">
                    {{ __('Pay Now') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>