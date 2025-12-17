@if(isset($programStudis) && $programStudis->count() > 0)
    <div class="py-12 bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 md:px-6">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold font-heading text-gray-800 uppercase tracking-wide">
                    Daftar Program Studi
                </h3>
                <div class="h-1 w-16 bg-blue-600 mx-auto mt-2 rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($programStudis as $prodi)
                    <a href="{{ $prodi->code ? route('frontend.prodi.detail', $prodi->code) : '#' }}"
                        class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 flex flex-col items-center text-center border border-gray-100 group">

                        {{-- Image / Logo --}}
                        <div
                            class="w-20 h-20 mb-4 flex items-center justify-center bg-gray-50 rounded-full group-hover:scale-110 transition-transform duration-300">
                            @if($prodi->image)
                                <img src="{{ Storage::url($prodi->image) }}" alt="{{ $prodi->name }}"
                                    class="w-14 h-14 object-contain">
                            @else
                                <i class="fas fa-graduation-cap text-3xl text-gray-300"></i>
                            @endif
                        </div>

                        {{-- Name --}}
                        <h4
                            class="font-bold text-gray-800 text-sm md:text-base mb-2 font-heading leading-tight group-hover:text-blue-600 transition-colors">
                            {{ $prodi->name }}
                        </h4>

                        {{-- Accreditation --}}
                        @if($prodi->accreditation)
                            <div class="mt-auto pt-2">
                                <span class="px-3 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-full">
                                    Akre: {{ $prodi->accreditation }}
                                </span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif