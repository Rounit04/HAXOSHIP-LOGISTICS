@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid rgba(255, 117, 15, 0.1);
        }
        .report-selector {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
        }
        .report-content-container {
            min-height: 400px;
        }
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #FF750F;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #d1d5db;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--admin-gradient); box-shadow: 0 2px 8px rgba(255, 117, 15, 0.2);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-0.5">Reports</h1>
                    <p class="text-xs text-gray-600">Select a report to view detailed information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Selector -->
    <div class="report-selector">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Select Report</label>
                <select id="reportSelector" class="form-select w-full" style="padding: 12px 16px; font-size: 15px; font-weight: 500;">
                    <option value="">-- Select a Report --</option>
                    <option value="zone">Zone Report</option>
                    <option value="formula">Formula Report</option>
                    <option value="shipping-charges">Shipping Charges Report</option>
                    <option value="booking">Booking Report</option>
                    <option value="payment">Payment Report</option>
                    <option value="transaction">Transaction Report</option>
                    <option value="network">Network Report</option>
                    <option value="service">Service Report</option>
                    <option value="country">Country Report</option>
                    <option value="bank">Bank Report</option>
                    <option value="wallet">Wallet Report</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Report Content Container -->
    <div id="reportContent" class="report-content-container">
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Report Selected</h3>
            <p class="text-gray-600 text-sm">Please select a report from the dropdown above to view its data.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportSelector = document.getElementById('reportSelector');
            const reportContent = document.getElementById('reportContent');
            
            // Check if there's a report in URL hash
            const urlHash = window.location.hash.substring(1);
            if (urlHash) {
                reportSelector.value = urlHash;
                loadReport(urlHash);
            }

            reportSelector.addEventListener('change', function() {
                const selectedReport = this.value;
                if (selectedReport) {
                    // Update URL hash without reload
                    window.history.pushState(null, null, '#' + selectedReport);
                    loadReport(selectedReport);
                } else {
                    reportContent.innerHTML = `
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Report Selected</h3>
                            <p class="text-gray-600 text-sm">Please select a report from the dropdown above to view its data.</p>
                        </div>
                    `;
                }
            });

            function loadReport(reportType) {
                // Show loading spinner
                reportContent.innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                `;

                // Make AJAX request to load report
                fetch(`/admin/reports/${reportType}/content`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Create a temporary container to parse the HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // The content is inside main > div (with padding classes)
                    // Try to find the content div inside main
                    const mainElement = doc.querySelector('main');
                    let contentSection = null;
                    
                    if (mainElement) {
                        // Get the div inside main (the one with padding classes)
                        const mainDiv = mainElement.querySelector('div');
                        if (mainDiv) {
                            contentSection = mainDiv;
                        } else {
                            contentSection = mainElement;
                        }
                    } else {
                        // Fallback: try other selectors
                        contentSection = doc.querySelector('section.content') || 
                                       doc.querySelector('.content') ||
                                       doc.querySelector('body > div:last-child');
                    }
                    
                    // If still not found, extract from body
                    if (!contentSection || !contentSection.innerHTML.trim()) {
                        const body = doc.body;
                        const bodyChildren = Array.from(body.children).filter(el => 
                            el.tagName !== 'SCRIPT' && el.tagName !== 'STYLE' && el.tagName !== 'LINK'
                        );
                        if (bodyChildren.length > 0) {
                            const wrapper = document.createElement('div');
                            bodyChildren.forEach(child => {
                                wrapper.appendChild(child.cloneNode(true));
                            });
                            contentSection = wrapper;
                        } else {
                            contentSection = body;
                        }
                    }
                    
                    // Get the inner HTML of the content section
                    reportContent.innerHTML = contentSection.innerHTML || html;
                    
                    // Update form actions to work with AJAX loading
                    const forms = reportContent.querySelectorAll('form');
                    forms.forEach(form => {
                        const originalAction = form.getAttribute('action');
                        if (originalAction) {
                            // Keep the original action but ensure it works
                            form.addEventListener('submit', function(e) {
                                // Forms will submit normally, but we can intercept if needed
                            });
                        }
                    });
                    
                    // Re-initialize any scripts in the loaded content
                    const scripts = reportContent.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                })
                .catch(error => {
                    console.error('Error loading report:', error);
                    reportContent.innerHTML = `
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Error Loading Report</h3>
                            <p class="text-gray-600 text-sm">There was an error loading the report. Please try again.</p>
                        </div>
                    `;
                });
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                const hash = window.location.hash.substring(1);
                if (hash) {
                    reportSelector.value = hash;
                    loadReport(hash);
                } else {
                    reportSelector.value = '';
                    reportContent.innerHTML = `
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Report Selected</h3>
                            <p class="text-gray-600 text-sm">Please select a report from the dropdown above to view its data.</p>
                        </div>
                    `;
                }
            });
        });
    </script>

@endsection

