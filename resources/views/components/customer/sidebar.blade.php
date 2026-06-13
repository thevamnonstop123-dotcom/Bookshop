{{-- W3Schools-style Sidebar Navigation --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-book-open"></i>
            <span>Bookshop</span>
        </div>
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('customer.home') }}" class="sidebar-link {{ request()->routeIs('customer.home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="{{ route('books.index') }}" class="sidebar-link {{ request()->routeIs('books.index') && !request('sort') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>All Books</span>
                </a>
            </li>
            <li>
                <a href="{{ route('books.index', ['sort' => 'latest']) }}" class="sidebar-link {{ request('sort') === 'latest' ? 'active' : '' }}">
                    <i class="fas fa-sparkles"></i>
                    <span>New Arrivals</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-divider"></div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">
                <i class="fas fa-layer-group"></i>
                <span>Categories</span>
            </div>
            <ul class="sidebar-menu">
                @foreach ($layoutCategories ?? \App\Models\Category::where('status', 'active')->get() as $cat)
                    <li>
                        <a href="{{ route('books.index', ['category' => $cat->id]) }}" class="sidebar-link">
                            <i class="fas fa-chevron-right"></i>
                            <span>{{ $cat->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>
</aside>

{{-- Mobile overlay for sidebar --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>