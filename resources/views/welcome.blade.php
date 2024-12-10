@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section id="hero-section" class="min-h-screen bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
        <div class="container mx-auto px-6 py-12 text-center">
            <h1 class="text-4xl font-bold mb-4">SiBatch</h1>
            <p class="text-lg mb-6">Sistem Informasi Batching Plant</p>
            <a href="#product-section" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-medium hover:bg-gray-100 transition duration-300">View Products</a>
        </div>
    </section>

    <!-- Product Section -->
    <section id="product-section" class="py-12 bg-gray-50">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Our Products</h2>
            <p class="text-gray-600">Discover our wide range of products.</p>
        </div>

        @if ($products->isEmpty())
            <p class="text-center text-gray-600">No products available at the moment.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 max-w-7xl mx-auto gap-x-6 gap-y-8">
                @foreach ($products as $product)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="w-full h-64">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($product->description, 100) }}</p>
                            <p class="text-lg text-blue-600 font-bold mt-4">Rp {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Inquiry Section -->
    <section id="inquiry-section" class="py-12 bg-gray-50">
        <div class="max-w-lg mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">CONTACT US</h2>
            <form action="{{ route('inquiries.store') }}" method="POST" class="space-y-6 bg-white rounded-2xl p-8 shadow-md">
                @csrf

                @if (session('success'))
                    <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800 flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Inquiry Fields -->
                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-1">
                        <label for="name" class="text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" required class="input input-bordered w-full" placeholder="Enter Your Name">
                        @error('name')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="company" class="text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company" id="company" required class="input input-bordered w-full" placeholder="Enter Your Company">
                        @error('company')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label for="email" class="text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" required class="input input-bordered w-full" placeholder="Enter Your Email Address">
                            @error('email')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="phone" class="text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone" required class="input input-bordered w-full" placeholder="Enter Your Phone Number">
                            @error('phone')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="address" class="text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" required class="input input-bordered w-full" placeholder="Enter Your Address">
                        @error('address')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Product Selection -->
                    <div class="space-y-1">
                        <label for="products" class="text-sm font-medium text-gray-700">Select Products</label>
                        <div id="products-list" class="space-y-4">
                            @foreach ($products as $product)
                                <div class="flex items-center space-x-4">
                                    <input type="checkbox" id="product-{{ $product->id }}" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}" class="checkbox">
                                    <label for="product-{{ $product->id }}" class="text-sm font-medium text-gray-700">
                                        {{ $product->name }} (Rp {{ number_format($product->price, 2, ',', '.') }})
                                    </label>
                                    <input type="number" name="products[{{ $loop->index }}][quantity]" placeholder="Quantity" class="input input-bordered w-20 quantity-input" min="1" data-price="{{ $product->price }}" disabled>
                                </div>
                            @endforeach
                        </div>
                        <p id="total-price" class="text-lg font-bold text-blue-600 mt-4">Total: Rp 0,00</p>
                    </div>

                    <div class="space-y-1">
                        <label for="message" class="text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" id="message" rows="4" class="input input-bordered w-full" placeholder="Tell us about your needs..."></textarea>
                        @error('message')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary w-full">Send Inquiry</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- JavaScript for Dynamic Total Calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('#products-list input[type="checkbox"]');
            const totalPriceElement = document.getElementById('total-price');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const quantityInput = this.closest('.flex').querySelector('.quantity-input');
                    quantityInput.disabled = !this.checked;
                    calculateTotal();
                });
            });

            const calculateTotal = () => {
                let total = 0;
                document.querySelectorAll('#products-list .quantity-input:not([disabled])').forEach(input => {
                    const quantity = parseFloat(input.value) || 0;
                    const price = parseFloat(input.dataset.price);
                    total += quantity * price;
                });
                totalPriceElement.textContent = `Total: Rp ${total.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
            };

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
        });
    </script>
@endsection
