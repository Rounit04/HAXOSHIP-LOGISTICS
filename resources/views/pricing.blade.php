@extends('layouts.app')
@section('title', 'Pricing - Haxo Shipping')
@section('content')
<div class="pricing-section-wrapper">
    <div class="container mx-auto px-4 py-12 max-w-7xl">
        <!-- Title Section with Decorative Lines -->
        <div class="pricing-title-section">
            <h1 class="pricing-main-title">Haxo Shipping Pricing</h1>
            <div class="pricing-title-decoration">
                <div class="decoration-line"></div>
                <div class="decoration-line"></div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="pricing-tabs-container">
            <div class="pricing-tabs-wrapper">
                <button class="pricing-tab active" data-tab="same-day">
                    Same Day
                </button>
                <button class="pricing-tab" data-tab="next-day">
                    Next Day
                </button>
                <button class="pricing-tab" data-tab="sub-city">
                    Sub City
                </button>
                <button class="pricing-tab" data-tab="outside-city">
                    Outside City
                </button>
            </div>
            <div class="pricing-tabs-underline"></div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="pricing-cards-container">
            <div class="pricing-tab-content active" id="same-day-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="next-day-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="sub-city-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>

            <div class="pricing-tab-content" id="outside-city-content">
                <div class="pricing-grid">
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 30 (KG)</div>
                        <div class="pricing-card-price">{{ currency(50.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 2 (KG)</div>
                        <div class="pricing-card-price">{{ currency(90.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 3 (KG)</div>
                        <div class="pricing-card-price">{{ currency(130.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 4 (KG)</div>
                        <div class="pricing-card-price">{{ currency(170.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 5 (KG)</div>
                        <div class="pricing-card-price">{{ currency(210.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 6 (KG)</div>
                        <div class="pricing-card-price">{{ currency(250.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 7 (KG)</div>
                        <div class="pricing-card-price">{{ currency(290.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 8 (KG)</div>
                        <div class="pricing-card-price">{{ currency(340.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 9 (KG)</div>
                        <div class="pricing-card-price">{{ currency(380.00) }}</div>
                    </div>
                    <div class="pricing-card-item">
                        <div class="pricing-card-weight">Up To 10 (KG)</div>
                        <div class="pricing-card-price">{{ currency(420.00) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.pricing-tab');
    const contents = document.querySelectorAll('.pricing-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab + '-content').classList.add('active');
        });
    });
});
</script>
@endsection


