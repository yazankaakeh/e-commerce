@php
    use Modules\Theme\Helpers\Helpers;
    $configData = Helpers::appClasses();
    $locale = app()->getLocale();
@endphp

@extends('theme::user.layouts.layoutFront')

@section('title', __('Portfolio'))

@section('content')
    {{-- Portfolio hero — same Codliy hero pattern used by blog/index so
         the theme-settings primary color + radius + fonts flow through. --}}
    <section class="codliy-hero position-relative">
        <div class="container position-relative">
            <div class="row align-items-end g-4">
                <div class="col-lg-8">
                    <div class="codliy-hero__kicker">CODLIY &middot; PORTFOLIO</div>
                    <h1 class="codliy-hero__title mb-3">
                        {{ __('Selected work from the studio') }}
                    </h1>
                    <p class="codliy-hero__sub mb-0">
                        {{ __('Explore our latest projects and see how we bring ideas to life.') }}
                    </p>
                </div>
                <div class="col-lg-4">
                    <form action="{{ route('portfolio.index') }}" method="GET" class="position-relative">
                        <div class="input-group input-group-merge codliy-card p-2">
                            <span class="input-group-text bg-transparent border-0 text-codliy-mute">
                                <i class="ti tabler-search"></i>
                            </span>
                            <input type="text" name="search"
                                   class="form-control bg-transparent border-0 text-codliy-soft"
                                   placeholder="{{ __('Search projects...') }}"
                                   value="{{ $search ?? '' }}"
                                   style="color: var(--codliy-text-soft);">
                            <button type="submit" class="btn-codliy px-3 py-1">
                                <i class="ti tabler-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Category filter — chip row. Uses codliy outline buttons so the
         pill shape and active-state color obey theme_settings.primary_color. --}}
    @if(count($categories) > 0)
        <section class="codliy-section py-4">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('portfolio.index') }}"
                       class="{{ !$category ? 'btn-codliy' : 'btn-codliy-outline' }} rounded-pill px-3 py-2">
                        <i class="ti tabler-apps me-1"></i>{{ __('All') }}
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('portfolio.index', ['category' => $cat]) }}"
                           class="{{ $category === $cat ? 'btn-codliy' : 'btn-codliy-outline' }} rounded-pill px-3 py-2">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Active filter banner --}}
    @if($search || $category)
        <section class="py-3">
            <div class="container">
                <div class="codliy-card d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-codliy-soft">
                        <i class="ti tabler-filter me-2 text-codliy-primary"></i>
                        @if($search)
                            {{ __('Results for') }}: <strong class="text-codliy-soft">"{{ $search }}"</strong>
                        @else
                            {{ __('Category') }}: <strong class="text-codliy-soft">{{ $category }}</strong>
                        @endif
                        <span class="text-codliy-mute ms-2">({{ $portfolios->total() }} {{ __('projects') }})</span>
                    </div>
                    <a href="{{ route('portfolio.index') }}" class="btn-codliy-outline px-3 py-1 rounded-pill">
                        <i class="ti tabler-x me-1"></i>{{ __('Clear Filter') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Projects grid --}}
    <section class="codliy-section">
        <div class="container">
            @if($portfolios->count() > 0)
                <div class="row g-4">
                    @foreach($portfolios as $portfolio)
                        <div class="col-sm-6 col-lg-4">
                            <article class="codliy-card h-100 p-0 overflow-hidden d-flex flex-column">
                                <div class="position-relative">
                                    @if($portfolio->getFirstMediaUrl('featured_image'))
                                        <img src="{{ $portfolio->getFirstMediaUrl('featured_image') }}"
                                             alt="{{ $portfolio->getTranslation('title', $locale) }}"
                                             class="w-100"
                                             style="height: 220px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-codliy-solid"
                                             style="height: 220px;">
                                            <i class="ti tabler-photo display-3 text-codliy-mute"></i>
                                        </div>
                                    @endif
                                    @if($portfolio->is_featured)
                                        <span class="badge position-absolute top-0 end-0 m-3 bg-codliy-primary">
                                            <i class="ti tabler-star-filled me-1"></i>{{ __('Featured') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    @if($portfolio->getTranslation('category', $locale))
                                        <div class="codliy-card__eyebrow mb-2">
                                            {{ $portfolio->getTranslation('category', $locale) }}
                                        </div>
                                    @endif
                                    <h3 class="codliy-card__title mb-2">
                                        <a href="{{ route('portfolio.show', $portfolio->slug) }}"
                                           class="stretched-link text-decoration-none"
                                           style="color: inherit;">
                                            {{ $portfolio->getTranslation('title', $locale) }}
                                        </a>
                                    </h3>
                                    @if($portfolio->getTranslation('short_description', $locale))
                                        <p class="codliy-card__body mb-3">
                                            {{ Str::limit($portfolio->getTranslation('short_description', $locale), 110) }}
                                        </p>
                                    @endif

                                    @if($portfolio->technologies && count($portfolio->technologies) > 0)
                                        <div class="mt-auto d-flex flex-wrap gap-1">
                                            @foreach(array_slice($portfolio->technologies, 0, 3) as $tech)
                                                <span class="badge bg-transparent text-codliy-mute border border-codliy">
                                                    {{ $tech }}
                                                </span>
                                            @endforeach
                                            @if(count($portfolio->technologies) > 3)
                                                <span class="badge bg-transparent text-codliy-mute border border-codliy">
                                                    +{{ count($portfolio->technologies) - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($portfolios->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $portfolios->links() }}
                    </div>
                @endif
            @else
                {{-- Empty state --}}
                <div class="codliy-card text-center py-5">
                    <div class="mb-4">
                        <i class="ti tabler-folder-off display-4 text-codliy-mute"></i>
                    </div>
                    <h4 class="codliy-card__title mb-2">{{ __('No projects found') }}</h4>
                    <p class="codliy-card__body mb-4">
                        {{ __("Try adjusting your search or filter to find what you're looking for.") }}
                    </p>
                    <a href="{{ route('portfolio.index') }}" class="btn-codliy px-4 py-2 d-inline-block">
                        <i class="ti tabler-refresh me-1"></i>{{ __('View All Projects') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA — uses `.bg-codliy-soft`, a primary-tinted pane that adapts per
         mode (faint glow in dark, soft tint in light). The kicker/title
         colors come from Theme Settings via --codliy-primary / --codliy-heading,
         so changing the admin primary color changes this whole section. --}}
    <section class="codliy-section bg-codliy-soft">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="codliy-section__kicker">{{ __('Start a Project') }}</div>
                    <h2 class="codliy-section__title mb-3">{{ __('Have a project in mind?') }}</h2>
                    <p class="codliy-section__sub mx-auto mb-4">
                        {{ __("Let's work together to bring your ideas to life.") }}
                    </p>
                    <a href="{{ route('landing.home') }}#contactUs"
                       class="btn-codliy px-4 py-2 d-inline-flex align-items-center gap-2">
                        <i class="ti tabler-message"></i>{{ __('Get in Touch') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
