<footer class="site-footer">
    <div class="container">
        <div class="site-footer-grid">
            {{-- Brand --}}
            <div class="site-footer-col site-footer-brand">
                <a href="{{ route('customer.home') }}" class="site-footer-logo">
                    <div class="site-footer-logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    Book<span>shop</span>
                </a>
                <p class="site-footer-description">
                    Your premium destination for books and stationery. Curated collections, fast delivery, exceptional service.
                </p>
                <div class="site-footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            {{-- Categories --}}
            <div class="site-footer-col">
                <h4 class="site-footer-heading">Categories</h4>
                <ul class="site-footer-links">
                    @foreach ($layoutCategories ?? \App\Models\Category::where('status', 'active')->limit(6)->get() as $cat)
                        <li>
                            <a href="{{ route('books.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Quick Links --}}
            <div class="site-footer-col">
                <h4 class="site-footer-heading">Quick Links</h4>
                <ul class="site-footer-links">
                    <li><a href="{{ route('customer.home') }}">Home</a></li>
                    <li><a href="{{ route('books.index') }}">All Books</a></li>
                    <li><a href="{{ route('books.index', ['sort' => 'latest']) }}">New Arrivals</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div class="site-footer-col">
                <h4 class="site-footer-heading">Support</h4>
                <ul class="site-footer-links">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="site-footer-bottom">
            <p>&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
            <div class="site-footer-payments">
                <span>We accept:</span>
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-stripe"></i>
            </div>
        </div>
    </div>
</footer>