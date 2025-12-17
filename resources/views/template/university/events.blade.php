@extends('template.university.layouts.app')

@section('title', 'Events - ' . \App\Models\SiteSetting::getValue('site_name'))
@section('description', 'Discover upcoming events, seminars, and activities at ' . \App\Models\SiteSetting::getValue('site_name'))
@section('keywords', 'events, seminars, workshops, activities, ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Header --}}
    <div class="bg-cyan-600 py-12 border-b border-cyan-700 relative overflow-hidden">
        {{-- Background Pattern Abstract --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
            </svg>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4">Upcoming Events</h1>
            <p class="text-cyan-100 text-lg max-w-2xl mx-auto">
                Jangan lewatkan berbagai kegiatan menarik, seminar, workshop, dan acara kampus lainnya.
            </p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 md:px-6 py-12" x-data="eventPage()">

        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Left Column: Search & List --}}
            <div class="w-full lg:w-2/3 order-2 lg:order-1">

                {{-- Search Bar --}}
                <div class="mb-8">
                    <form action="{{ route('frontend.events') }}" method="GET" class="relative max-w-xl">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-all shadow-sm"
                            placeholder="Cari acara, seminar, atau kegiatan...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <button type="submit"
                            class="absolute right-2 top-2 bottom-2 bg-cyan-600 text-white px-4 rounded-md hover:bg-cyan-700 transition-colors font-medium text-sm">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- Events Grid --}}
                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($posts as $event)
                            <div
                                class="group flex flex-col bg-white border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 rounded-xl overflow-hidden hover:-translate-y-1">
                                {{-- Image Section --}}
                                <div class="relative aspect-[16/9] overflow-hidden">
                                    <img src="{{ !empty($event->featured_image_url) ? $event->featured_image_url : asset('images/default-post.jpg') }}"
                                        alt="{{ $event->title }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                                    {{-- Date Badge --}}
                                    <div
                                        class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm shadow-md text-center rounded-lg overflow-hidden min-w-[50px]">
                                        <div
                                            class="bg-cyan-600 text-white text-[10px] uppercase py-1 font-bold tracking-wider px-2">
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wider block leading-tight">{{ $event->published_at ? $event->published_at->format('M') : $event->created_at->format('M') }}</span>
                                        </div>
                                        <span
                                            class="text-2xl font-bold block leading-none">{{ $event->published_at ? $event->published_at->format('d') : $event->created_at->format('d') }}</span>
                                    </div>

                                    {{-- Category --}}
                                    <div class="absolute bottom-3 left-3">
                                        <span
                                            class="bg-black/50 backdrop-blur-sm text-white text-[10px] uppercase font-bold px-2 py-1 rounded">
                                            {{ $event->category->name ?? 'Event' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Content Section --}}
                                <div class="p-5 flex flex-col flex-grow relative">
                                    <h4
                                        class="font-bold text-gray-800 text-lg leading-snug mb-3 group-hover:text-cyan-600 transition-colors line-clamp-2">
                                        <a href="{{ route('frontend.post', $event->slug) }}" class="before:absolute before:inset-0">
                                            {{ $event->title }}
                                        </a>
                                    </h4>

                                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $event->excerpt }}</p>

                                    <div
                                        class="mt-auto pt-4 border-t border-dashed border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <i class="far fa-clock text-cyan-500"></i>
                                            <span>{{ $event->published_at ? $event->published_at->format('H:i') : $event->created_at->format('H:i') }}
                                                WIB</span>
                                        </div>
                                        <span
                                            class="group-hover:translate-x-1 transition-transform duration-300 text-cyan-600 font-medium">
                                            Detail <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12 flex justify-center">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>

                @else
                    <div class="bg-gray-50 p-12 text-center rounded-xl border border-gray-100">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4 text-gray-400">
                            <i class="far fa-calendar-times text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak ada acara ditemukan</h3>
                        <p class="text-gray-500 mb-6">Coba kata kunci lain atau lihat semua acara.</p>
                        <a href="{{ route('frontend.events') }}"
                            class="inline-block px-6 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Reset Pencarian
                        </a>
                    </div>
                @endif
            </div>

            {{-- Right Column: Calendar & Widgets --}}
            <div class="w-full lg:w-1/3 order-1 lg:order-2 space-y-8">

                {{-- Calendar Widget --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden sticky top-32">
                    <div class="bg-cyan-600 p-4 text-white">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <i class="far fa-calendar-alt"></i> Kalender Acara
                        </h3>
                    </div>

                    <div class="p-6">
                        {{-- Month Navigation --}}
                        <div class="flex items-center justify-between mb-6">
                            <button @click="prevMonth()"
                                class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h4 class="font-bold text-gray-800 text-lg" x-text="monthNames[month] + ' ' + year"></h4>
                            <button @click="nextMonth()"
                                class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-600">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        {{-- Week Days --}}
                        <div class="grid grid-cols-7 mb-2 text-center">
                            <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                <div class="text-xs font-bold text-gray-400 uppercase py-2" x-text="day"></div>
                            </template>
                        </div>

                        {{-- Digits --}}
                        <div class="grid grid-cols-7 gap-1 text-center text-sm">
                            {{-- Empty slots --}}
                            <template x-for="blank in blanks">
                                <div class="h-10"></div>
                            </template>

                            {{-- Days --}}
                            <template x-for="date in no_of_days">
                                <div class="h-10 w-10 mx-auto flex items-center justify-center rounded-full transition-all relative group cursor-pointer"
                                    :class="{
                                                     'bg-cyan-600 text-white font-bold shadow-md': isToday(date) && !isSelected(date),
                                                     'bg-cyan-100 text-cyan-700 font-bold': isSelected(date),
                                                     'hover:bg-gray-100 text-gray-700': !isToday(date) && !isSelected(date)
                                                 }" @click="selectDate(date)">

                                    <span x-text="date"></span>

                                    {{-- Event Dot --}}
                                    <div x-show="hasEvent(date)" class="absolute bottom-1 w-1.5 h-1.5 rounded-full"
                                        :class="isToday(date) || isSelected(date) ? 'bg-white' : 'bg-red-500'">
                                    </div>

                                    {{-- Tooltip (Simple) --}}
                                    <div x-show="hasEvent(date)"
                                        class="hidden group-hover:block absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 bg-gray-800 text-white text-xs p-2 rounded shadow-xl z-50 text-left">
                                        <div class="font-bold border-b border-gray-600 pb-1 mb-1"
                                            x-text="date + ' ' + monthNames[month]"></div>
                                        <template x-for="evt in getEvents(date)">
                                            <div class="truncate mb-0.5" x-text="evt.title"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Selected Date Events --}}
                    <div class="bg-gray-50 p-4 border-t border-gray-100" x-show="selectedDateEvents.length > 0"
                        x-transition>
                        <h5 class="text-xs font-bold text-gray-500 uppercase mb-3">Acara pada tanggal ini:</h5>
                        <div class="space-y-2">
                            <template x-for="evt in selectedDateEvents">
                                <a :href="'/post/' + evt.slug"
                                    class="flex items-start gap-3 p-2 bg-white rounded border border-gray-200 hover:border-cyan-400 transition-colors group">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded object-cover overflow-hidden">
                                        <img :src="evt.img" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h6 class="text-sm font-bold text-gray-800 truncate group-hover:text-cyan-600"
                                            x-text="evt.title"></h6>
                                        <p class="text-xs text-gray-500" x-text="evt.time"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Alpine JS Script for Calendar --}}
    <script>
        function eventPage() {
            return {
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                no_of_days: [],
                blanks: [],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                events: @json($calendarEvents),
                selectedDate: null,
                selectedDateEvents: [],

                init() {
                    this.getNoOfDays();
                },

                isToday(date) {
                    const today = new Date();
                    const d = new Date(this.year, this.month, date);
                    return today.toDateString() === d.toDateString();
                },

                isSelected(date) {
                    return this.selectedDate === date;
                },

                selectDate(date) {
                    this.selectedDate = date;
                    this.selectedDateEvents = this.getEvents(date).map(evt => {
                        // Simplify event object for display
                        return {
                            title: evt.title,
                            slug: evt.slug,
                            time: new Date(evt.published_at || evt.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                            img: evt.featured_image ? '/storage/' + evt.featured_image : '/images/default-post.jpg' // Simple fix, ideally use accessor
                        };
                    });
                },

                getNoOfDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let dayOfWeek = new Date(this.year, this.month).getDay();

                    this.blanks = Array.from({ length: dayOfWeek });
                    this.no_of_days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
                },

                prevMonth() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    this.getNoOfDays();
                    this.selectedDate = null;
                    this.selectedDateEvents = [];
                },

                nextMonth() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    this.getNoOfDays();
                    this.selectedDate = null;
                    this.selectedDateEvents = [];
                },

                hasEvent(date) {
                    return this.getEvents(date).length > 0;
                },

                getEvents(date) {
                    return this.events.filter(e => {
                        const eventDate = new Date(e.published_at || e.created_at);
                        return eventDate.getDate() === date &&
                            eventDate.getMonth() === this.month &&
                            eventDate.getFullYear() === this.year;
                    });
                }
            }
        }
    </script>
@endsection