@if(isset($programStudis) && $programStudis->count() > 0)
    <div class="py-16 bg-gradient-to-br from-blue-900 to-blue-800 relative overflow-hidden">
        {{-- Background Elements --}}
        <div
            class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-700 rounded-full mix-blend-multiply filter blur-3xl opacity-20">
        </div>
        <div
            class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-purple-700 rounded-full mix-blend-multiply filter blur-3xl opacity-20">
        </div>

        <div class="container mx-auto px-4 relative z-10">
            {{-- Section Header --}}
            <div class="text-center mb-12">
                <h3 class="text-3xl md:text-4xl font-heading font-bold text-white mb-3">
                    Program <span class="text-yellow-400">Studi</span>
                </h3>
                <div class="h-1 w-24 bg-yellow-400 mx-auto rounded-full"></div>
                <p class="text-blue-100 mt-4 max-w-2xl mx-auto">
                    Pilihan program studi unggulan untuk masa depan profesional Anda.
                </p>
            </div>

            {{-- Swiper --}}
            <div class="swiper prodiSwiper pb-12 px-4">
                <div class="swiper-wrapper">
                    @foreach($programStudis as $prodi)
                        <div class="swiper-slide h-auto">
                            <div
                                class="group h-[400px] w-full bg-blue-600 rounded-2xl shadow-xl relative overflow-hidden transform transition-all duration-300 hover:-translate-y-2">

                                {{-- Default State (Visible) --}}
                                <div
                                    class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center transition-all duration-500 group-hover:opacity-10 opacity-100">
                                    {{-- Accreditation Badge --}}
                                    @if($prodi->accreditation)
                                        <div class="absolute top-6 right-6">
                                            <div
                                                class="bg-yellow-400 text-blue-900 w-12 h-12 rounded-lg flex flex-col items-center justify-center shadow-lg transform rotate-3">
                                                <span class="text-lg font-bold">{{ $prodi->accreditation }}</span> 
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Image/Logo --}}
                                    <div
                                        class="mb-6 w-32 h-32 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-inner">
                                        @if($prodi->image)
                                            <img src="{{ Storage::url($prodi->image) }}" alt="{{ $prodi->name }}"
                                                class="w-24 h-24 object-contain">
                                        @else
                                            <i class="fas fa-graduation-cap text-5xl text-white/50"></i>
                                        @endif
                                    </div>

                                    <h4 class="text-yellow-400 font-bold text-lg uppercase tracking-wide mb-1">Program Studi
                                    </h4>
                                    <h3 class="text-white font-heading font-bold text-2xl md:text-3xl leading-tight">
                                        {{ $prodi->name }}</h3>
                                </div>

                                {{-- Hover State (Overlay) --}}
                                <div
                                    class="absolute inset-0 bg-blue-900/95 flex flex-col items-center justify-center p-8 text-center translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out">
                                    <h3
                                        class="text-white font-heading font-bold text-xl mb-4 border-b-2 border-yellow-400 pb-2 inline-block">
                                        {{ $prodi->name }}
                                    </h3>

                                    <div
                                        class="text-blue-100 text-sm leading-relaxed mb-8 flex-grow overflow-y-auto custom-scrollbar">
                                        {!! $prodi->description ?? 'Informasi detail mengenai program studi ini.' !!}
                                    </div>

                                    <div class="flex gap-4 mt-auto">
                                        @if($prodi->website_url)
                                            <a href="{{ $prodi->website_url }}" target="_blank"
                                                class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors duration-200"
                                                title="Kunjungi Website">
                                                <i class="fas fa-globe"></i> Website
                                            </a>
                                        @endif
                                        <a href="{{ $prodi->code ? route('frontend.prodi.detail', $prodi->code) : '#' }}"
                                            class="px-6 py-2 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-blue-900 transition-all shadow-lg flex items-center gap-2">
                                            <i class="fas fa-info-circle"></i> Detail
                                        </a>
                                    </div>
                                </div>

                                {{-- Decorative Bottom Wave --}}
                                <div class="absolute bottom-0 left-0 right-0 h-16 opacity-30 pointer-events-none">
                                    <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                                        <path
                                            d="M0.00,49.98 C149.99,150.00 349.20,-49.98 500.00,49.98 L500.00,150.00 L0.00,150.00 Z"
                                            style="stroke: none; fill: #fff;"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Navigation --}}
                <div
                    class="swiper-button-next !text-yellow-400 !w-10 !h-10 !bg-blue-900/50 !rounded-full !backdrop-blur-sm after:!text-lg hover:!bg-blue-900 transition-colors">
                </div>
                <div
                    class="swiper-button-prev !text-yellow-400 !w-10 !h-10 !bg-blue-900/50 !rounded-full !backdrop-blur-sm after:!text-lg hover:!bg-blue-900 transition-colors">
                </div>

                <div class="swiper-pagination !text-yellow-400 !bottom-0"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new Swiper(".prodiSwiper", {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: true,
                    centeredSlides: true,
                    effect: 'coverflow',
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows: false,
                    },
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 1,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 30,
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 40,
                        },
                    },
                });
            });
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 204, 0, 0.5);
                border-radius: 2px;
            }
        </style>
    @endpush
@endif