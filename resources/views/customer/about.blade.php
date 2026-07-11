@extends('layouts.customer')

@section('title', 'Contact Us — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/about.css') }}">
@endpush

@section('content')

{{-- Hero --}}
<div class="about-hero">
    <div class="container">
        <span class="about-hero-badge"><i class="fas fa-paper-plane"></i> Get in Touch</span>
        <h1>We're Here to Help</h1>
        <p>Have a question about an order, a book, or just want to say hello? Reach out and we'll get back to you within 24 hours.</p>
        <div class="about-hero-stats">
            <div class="about-hero-stat">
                <i class="fas fa-clock"></i>
                <span>24h Response Time</span>
            </div>
            <div class="about-hero-stat">
                <i class="fas fa-star"></i>
                <span>4.9/5 Support Rating</span>
            </div>
            <div class="about-hero-stat">
                <i class="fas fa-users"></i>
                <span>10,000+ Happy Readers</span>
            </div>
        </div>
    </div>
</div>

<div class="about-page">
    <div class="container">

        {{-- Contact Cards --}}
        <div class="about-contact">
            <div class="about-contact-card">
                <div class="about-contact-icon"><i class="fas fa-envelope"></i></div>
                <div class="about-contact-info">
                    <h4>Email Us</h4>
                    <p>support@bookshop.com</p>
                    <span class="about-contact-sub">We reply within 24 hours</span>
                </div>
            </div>
            <div class="about-contact-card">
                <div class="about-contact-icon"><i class="fas fa-phone"></i></div>
                <div class="about-contact-info">
                    <h4>Call Us</h4>
                    <p>+95 9 123 456 789</p>
                    <span class="about-contact-sub">Mon - Sat, 9AM - 6PM</span>
                </div>
            </div>
            <div class="about-contact-card">
                <div class="about-contact-icon"><i class="fas fa-location-dot"></i></div>
                <div class="about-contact-info">
                    <h4>Visit Us</h4>
                    <p>123 Book Street, Yangon</p>
                    <span class="about-contact-sub">Myanmar (Burma)</span>
                </div>
            </div>
            <div class="about-contact-card">
                <div class="about-contact-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="about-contact-info">
                    <h4>Live Chat</h4>
                    <p>Available on Telegram & Messenger</p>
                    <span class="about-contact-sub">@bookshop_myanmar</span>
                </div>
            </div>
        </div>

        {{-- Contact Form + Map Row --}}
        <div class="about-bottom-row">
            {{-- Form --}}
            <div class="about-form-card">
                <h2>Send Us a Message</h2>
                <p>Fill out the form and we'll get back to you as soon as possible.</p>
                <form class="about-form">
                    <div class="about-form-grid">
                        <div class="about-form-group">
                            <label>Your Name</label>
                            <input type="text" placeholder="Enter your full name" required>
                        </div>
                        <div class="about-form-group">
                            <label>Email Address</label>
                            <input type="email" placeholder="you@example.com" required>
                        </div>
                        <div class="about-form-group about-form-full">
                            <label>Subject</label>
                            <input type="text" placeholder="How can we help?">
                        </div>
                        <div class="about-form-group about-form-full">
                            <label>Message</label>
                            <textarea rows="4" placeholder="Tell us more about your inquiry..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="about-form-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            {{-- Map --}}
            <div class="about-map-container" id="mapContainer">
                <div class="about-map-overlay" id="mapOverlay">
                    <i class="fas fa-map-pin"></i>
                    <h3>Our Location</h3>
                    <p>123 Book Street, Yangon, Myanmar</p>
                    <span class="loading-text">Loading map...</span>
                </div>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3819.0!2d96.155!3d16.84!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1949a3e3a3a3b%3A0x3a3a3a3a3a3a3a3a!2sYangon!5e0!3m2!1sen!2smm!4v1" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    onload="document.getElementById('mapOverlay').style.display='none'"></iframe>
            </div>
        </div>

    </div>
</div>
@endsection
