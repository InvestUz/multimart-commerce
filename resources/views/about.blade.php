@extends('layouts.app')

@section('title', 'About Us - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-gold-600 to-gold-800 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">About {{ config('app.name') }}</h1>
                <p class="text-xl text-gold-100">Your trusted online marketplace for quality products</p>
            </div>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
                <div class="prose prose-lg max-w-none text-gray-600">
                    <p class="mb-4">
                        Founded in 2024, {{ config('app.name') }} has grown to become one of the leading e-commerce platforms,
                        connecting millions of customers with thousands of trusted vendors worldwide.
                    </p>
                    <p class="mb-4">
                        Our mission is simple: to provide a seamless shopping experience where customers can find everything
                        they need in one place, backed by exceptional customer service and secure transactions.
                    </p>
                    <p>
                        We believe in empowering both buyers and sellers, creating a marketplace that fosters trust,
                        transparency, and mutual growth.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Values Section -->
    <div class="bg-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Our Core Values</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Value 1 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gold-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Trust & Security</h3>
                        <p class="text-gray-600">
                            We prioritize the security of your data and transactions with advanced encryption and fraud protection.
                        </p>
                    </div>

                    <!-- Value 2 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gold-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Quality Products</h3>
                        <p class="text-gray-600">
                            Every vendor is carefully vetted to ensure you receive only authentic, high-quality products.
                        </p>
                    </div>

                    <!-- Value 3 -->
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gold-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Customer First</h3>
                        <p class="text-gray-600">
                            Our dedicated support team is always ready to assist you with any questions or concerns.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-gold-600 mb-2">1M+</div>
                    <div class="text-gray-600">Happy Customers</div>
                </div>

                <!-- Stat 2 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-gold-600 mb-2">10K+</div>
                    <div class="text-gray-600">Products</div>
                </div>

                <!-- Stat 3 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-gold-600 mb-2">500+</div>
                    <div class="text-gray-600">Trusted Vendors</div>
                </div>

                <!-- Stat 4 -->
                <div class="text-center">
                    <div class="text-4xl font-bold text-gold-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Why Choose Us</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Feature 1 -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Competitive Prices</h3>
                            <p class="text-gray-600">Get the best deals with our price match guarantee and frequent sales.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Shipping</h3>
                            <p class="text-gray-600">Express delivery options available with real-time tracking.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Returns</h3>
                            <p class="text-gray-600">30-day hassle-free return policy on most items.</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Payment</h3>
                            <p class="text-gray-600">Multiple payment options with SSL encryption and fraud protection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto bg-gradient-to-r from-gold-600 to-gold-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-12 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to Start Shopping?</h2>
                <p class="text-xl text-gold-100 mb-8">
                    Join millions of satisfied customers and discover amazing deals today!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center px-8 py-3 bg-white text-gold-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Start Shopping
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center px-8 py-3 bg-gold-700 text-white rounded-lg font-semibold hover:bg-gold-800 transition-colors border-2 border-white">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
