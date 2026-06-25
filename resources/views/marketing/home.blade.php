<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <meta name="author" content="StockScan">
    <meta name="application-name" content="StockScan">
    <meta name="geo.placename" content="{{ $meta['geo']['placename'] }}">
    <meta name="geo.region" content="{{ $meta['geo']['region'] }}">
    <meta name="ICBM" content="{{ $meta['geo']['position'] }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:url" content="{{ $meta['canonical'] }}">
    <meta property="og:site_name" content="StockScan">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] }}">
    <meta name="twitter:description" content="{{ $meta['description'] }}">
    <link rel="canonical" href="{{ $meta['canonical'] }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">
{!! $softwareSchemaJson !!}
    </script>
    <script type="application/ld+json">
{!! $faqSchemaJson !!}
    </script>
</head>
<body class="landing-body text-slate-900">
    <div class="landing-shell">
        <header class="landing-topbar">
            <div class="landing-container landing-topbar-inner">
                <a href="{{ url('/') }}" class="landing-logo" aria-label="StockScan home">
                    <span class="landing-logo-mark">
                        <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
                            <rect x="9" y="11" width="46" height="42" rx="10" fill="#0f6cbd" opacity="0.12"/>
                            <path d="M18 20h6v24h-6zM28 20h4v24h-4zM36 20h8v24h-8zM48 20h3v24h-3z" fill="#0f6cbd"/>
                            <path d="M20 14h24" stroke="#0f6cbd" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>
                        <span class="landing-logo-name">StockScan</span>
                        <span class="landing-logo-tag">Barcode inventory control</span>
                    </span>
                </a>

                <nav class="landing-nav" aria-label="Primary">
                    <a href="#features">Features</a>
                    <a href="#workflow">Workflow</a>
                    <a href="#faq">FAQ</a>
                    <a href="#contact">Contact</a>
                    <a href="{{ route('login') }}" class="landing-nav-login">Login</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="landing-hero">
                <div class="landing-container landing-hero-grid">
                    <div class="landing-hero-copy">
                        <p class="landing-kicker">PHP + MySQL inventory software</p>
                        <h1>Fast barcode inventory control for stores, warehouses, and field teams.</h1>
                        <p class="landing-lead">
                            StockScan helps teams track stock, scan products, print barcode stickers, monitor low-stock pressure,
                            and manage users from one clean browser-based system.
                        </p>

                        <div class="landing-proof-grid">
                            <div class="landing-proof-card">
                                <span class="landing-proof-label">Scan workflow</span>
                                <strong>Keyboard-wedge ready</strong>
                                <p>Works with standard USB barcode scanners through the browser.</p>
                            </div>
                            <div class="landing-proof-card">
                                <span class="landing-proof-label">Deployment</span>
                                <strong>Online-hosted + local scanners</strong>
                                <p>Use a live web app while each workstation keeps its own scanner locally.</p>
                            </div>
                            <div class="landing-proof-card">
                                <span class="landing-proof-label">Operations</span>
                                <strong>Alerts, reports, and labels</strong>
                                <p>Track low stock, export reports, and print Code 128 product stickers.</p>
                            </div>
                        </div>

                        <div class="landing-hero-actions">
                            <a href="{{ route('login') }}" class="btn btn-primary">Open StockScan</a>
                            <a href="#contact" class="landing-btn-secondary">Request a Demo</a>
                        </div>

                        <ul class="landing-feature-points" aria-label="Key product points">
                            <li>Auto-generated barcodes and category-based SKU logic</li>
                            <li>Multi-user access with secure username and password login</li>
                            <li>Product images, costs, reports, alerts, and quick stock updates</li>
                        </ul>
                    </div>

                    <div class="landing-hero-visual">
                        <div class="landing-dashboard-mock">
                            <div class="landing-dashboard-header">
                                <div>
                                    <p class="landing-mock-label">Live scan board</p>
                                    <h2>Operational visibility</h2>
                                </div>
                                <span class="landing-mock-badge">Ready</span>
                            </div>

                            <div class="landing-stat-strip">
                                <article>
                                    <span>In stock</span>
                                    <strong>248</strong>
                                </article>
                                <article>
                                    <span>Low stock</span>
                                    <strong>12</strong>
                                </article>
                                <article>
                                    <span>Movements today</span>
                                    <strong>37</strong>
                                </article>
                            </div>

                            <div class="landing-scanner-mockup" aria-label="Barcode scanner demo">
                                <div class="landing-scanner-device">
                                    <div class="landing-scanner-head"></div>
                                    <div class="landing-scanner-body"></div>
                                    <div class="landing-scanner-trigger"></div>
                                </div>
                                <div class="landing-scanner-beam"></div>
                                <div class="landing-scanner-panel">
                                    <p class="landing-mock-label">Barcode input simulation</p>
                                    <div class="landing-mock-field">6212562271</div>
                                    <div class="landing-mock-result">
                                        <span>Matched product</span>
                                        <strong>Orange Juice 1L</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="landing-task-list">
                                <div class="landing-task-item is-good">
                                    <span class="landing-task-dot"></span>
                                    <div>
                                        <strong>Known item found instantly</strong>
                                        <p>Open product actions, update quantity, or print sticker label.</p>
                                    </div>
                                </div>
                                <div class="landing-task-item is-warn">
                                    <span class="landing-task-dot"></span>
                                    <div>
                                        <strong>Low-stock alerts stay visible</strong>
                                        <p>Owners and staff can see products approaching threshold.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="landing-section">
                <div class="landing-container">
                    <div class="landing-section-head">
                        <p class="landing-kicker">Core features</p>
                        <h2>A practical inventory command center built for daily use.</h2>
                        <p>Clean enough for fast training, structured enough for teams that scan, count, receive, issue, and review stock every day.</p>
                    </div>

                    <div class="landing-card-grid">
                        <article class="landing-info-card">
                            <h3>Barcode-first operations</h3>
                            <p>Fast scan pages, scanner simulator for demos, unknown-barcode fallback, and stock movement actions designed for repeated use.</p>
                        </article>
                        <article class="landing-info-card">
                            <h3>Inventory and costing</h3>
                            <p>Track quantity, cost, selling price, current inventory value, low-stock thresholds, and product imagery in one place.</p>
                        </article>
                        <article class="landing-info-card">
                            <h3>Labels and printing</h3>
                            <p>Generate internal barcodes, preview stickers, and print Code 128 labels for shelves, bins, and products.</p>
                        </article>
                        <article class="landing-info-card">
                            <h3>Reports and exports</h3>
                            <p>Review inventory summaries, low-stock reports, movement history, CSV exports, and PDF-ready reporting views.</p>
                        </article>
                        <article class="landing-info-card">
                            <h3>Roles and security</h3>
                            <p>Support super admin, admin, and staff workflows with OTP-ready login, account settings, and session tracking.</p>
                        </article>
                        <article class="landing-info-card">
                            <h3>Online deployment model</h3>
                            <p>Host the app online while each local workstation keeps its own scanner, browser session, and operational flow.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="workflow" class="landing-section landing-section-soft">
                <div class="landing-container">
                    <div class="landing-section-head">
                        <p class="landing-kicker">Workflow</p>
                        <h2>How StockScan fits the real stock process.</h2>
                        <p>Designed for receiving, issuing, counting, and reporting without forcing a complex ERP flow.</p>
                    </div>

                    <div class="landing-workflow-grid">
                        <article class="landing-step-card">
                            <span class="landing-step-number">01</span>
                            <h3>Create products quickly</h3>
                            <p>Add category, image, cost, quantity, and minimum stock. The system can generate barcode and SKU values automatically.</p>
                        </article>
                        <article class="landing-step-card">
                            <span class="landing-step-number">02</span>
                            <h3>Scan or search items</h3>
                            <p>Use the scan station with a USB scanner or the built-in simulator for testing, training, and demo workflows.</p>
                        </article>
                        <article class="landing-step-card">
                            <span class="landing-step-number">03</span>
                            <h3>Update stock movements</h3>
                            <p>Record stock in, stock out, or adjustments while preserving movement history, user attribution, and timestamps.</p>
                        </article>
                        <article class="landing-step-card">
                            <span class="landing-step-number">04</span>
                            <h3>Act on alerts and reports</h3>
                            <p>Review low-stock warnings, current inventory value, recent activity, and exported reports for better daily control.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="landing-section">
                <div class="landing-container">
                    <div class="landing-summary-band">
                        <div>
                            <p class="landing-kicker">Search-ready summary</p>
                            <h2>StockScan is an inventory management app for barcode-driven businesses.</h2>
                        </div>
                        <div class="landing-summary-list">
                            <span>Barcode inventory management</span>
                            <span>Warehouse and retail stock control</span>
                            <span>PHP and MySQL deployment</span>
                            <span>Local scanner + online system model</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="faq" class="landing-section">
                <div class="landing-container">
                    <div class="landing-section-head">
                        <p class="landing-kicker">FAQ</p>
                        <h2>Clear answers for buyers, managers, and implementers.</h2>
                    </div>

                    <div class="landing-faq-list">
                        @foreach ($faqItems as $item)
                            <details class="landing-faq-item">
                                <summary>
                                    <span>{{ $item['question'] }}</span>
                                    <span class="landing-faq-plus">+</span>
                                </summary>
                                <p>{{ $item['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="contact" class="landing-section landing-section-soft">
                <div class="landing-container landing-contact-grid">
                    <div class="landing-contact-copy">
                        <p class="landing-kicker">Contact</p>
                        <h2>Request a demo, pricing discussion, or deployment help.</h2>
                        <p>
                            Use the form to ask about scanner setup, live hosting, label printing, product imports,
                            roles, reporting, or adapting StockScan for your operation.
                        </p>

                        <div class="landing-contact-points">
                            <div>
                                <strong>Best for</strong>
                                <p>Retail stores, warehouses, production teams, and stockrooms.</p>
                            </div>
                            <div>
                                <strong>Deployment model</strong>
                                <p>Browser-based app, online hosting, local scanner devices.</p>
                            </div>
                            <div>
                                <strong>Contact handling</strong>
                                <p>{{ $contactRecipient ? 'Messages are routed to the configured system mailbox.' : 'Messages are saved in the app log until mail is configured.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="landing-contact-card">
                        @include('partials.flash')

                        <form method="POST" action="{{ route('landing.contact') }}" class="landing-contact-form" data-prevent-double-submit>
                            @csrf
                            <div class="landing-form-grid">
                                <div>
                                    <label class="label" for="name">Name</label>
                                    <input id="name" name="name" type="text" class="input" value="{{ old('name') }}" required>
                                </div>
                                <div>
                                    <label class="label" for="email">Email</label>
                                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>
                                </div>
                                <div>
                                    <label class="label" for="company">Company</label>
                                    <input id="company" name="company" type="text" class="input" value="{{ old('company') }}">
                                </div>
                                <div>
                                    <label class="label" for="phone">Phone</label>
                                    <input id="phone" name="phone" type="text" class="input" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div>
                                <label class="label" for="business_type">Business Type</label>
                                <input id="business_type" name="business_type" type="text" class="input" value="{{ old('business_type') }}" placeholder="Warehouse, retail, production, stockroom...">
                            </div>

                            <div>
                                <label class="label" for="message">Message</label>
                                <textarea id="message" name="message" class="input min-h-[9.5rem]" required placeholder="Tell us about your stock process, scanner needs, labels, reports, users, or deployment goals.">{{ old('message') }}</textarea>
                            </div>

                            <button class="btn btn-primary w-full" data-submit-label="Sending...">Send Inquiry</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
