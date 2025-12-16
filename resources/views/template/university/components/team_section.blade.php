@props(['teamMembers'])

@if(isset($teamMembers) && $teamMembers->count() > 0)
    <div class="w-full py-20 bg-white">
        <div class="container mx-auto px-4 md:px-6">

            {{-- Section Title --}}
            <div class="text-center mb-16">
                <h2
                    class="text-3xl md:text-4xl font-bold text-[#1e3a8a] mb-4 font-heading uppercase tracking-wider relative inline-block">
                    {{ \App\Helpers\TemplateHelper::getThemeConfig('sections.team_title', 'Our Team') }}
                </h2>
                <div class="w-24 h-1 bg-[#ffd700] mx-auto rounded-full"></div>
            </div>

            {{-- Team Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($teamMembers as $member)
                    <div
                        class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden border border-gray-100 p-6 flex flex-col items-center text-center">

                        {{-- Image --}}
                        <div
                            class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-gray-50 shadow-inner mb-6 relative group-hover:border-cyan-100 transition-colors">
                            @if($member->image_url)
                                <img src="{{ $member->image_url }}" alt="{{ $member->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-user text-4xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="mb-4 w-full">
                            <h3
                                class="text-xl font-bold text-gray-800 mb-1 font-heading group-hover:text-[#1e3a8a] transition-colors">
                                {{ $member->name }}
                            </h3>
                            @if($member->position)
                                <p class="text-cyan-600 font-medium text-sm w-full break-words">{{ $member->position }}</p>
                            @endif
                        </div>

                        {{-- Custom Fields --}}
                        @if(!empty($member->custom_fields) && count($member->custom_fields) > 0)
                            <div class="w-full bg-gray-50 rounded-lg p-3 text-sm text-gray-600 mb-4 space-y-1">
                                @foreach($member->custom_fields as $field)
                                    @if(isset($field['label']) && isset($field['value']))
                                        <div
                                            class="flex justify-between items-center text-xs border-b border-gray-100 last:border-0 pb-1 last:pb-0">
                                            <span class="font-bold text-gray-500">{{ $field['label'] }}:</span>
                                            <span class="font-medium text-gray-800">{{ $field['value'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Social Links --}}
                        @if(!empty($member->social_links) && count($member->social_links) > 0)
                            <div class="mt-auto flex gap-3 justify-center items-center">
                                @foreach($member->social_links as $link)
                                    @if(isset($link['platform']) && isset($link['url']))
                                        @php
                                            $icon = match (strtolower($link['platform'])) {
                                                'facebook' => 'fab fa-facebook-f',
                                                'twitter' => 'fab fa-twitter',
                                                'instagram' => 'fab fa-instagram',
                                                'linkedin' => 'fab fa-linkedin-in',
                                                'youtube' => 'fab fa-youtube',
                                                'whatsapp' => 'fab fa-whatsapp',
                                                'email' => 'fas fa-envelope',
                                                default => 'fas fa-globe'
                                            };
                                        @endphp
                                        <a href="{{ $link['url'] }}" target="_blank"
                                            class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-[#1e3a8a] hover:text-white transition-all duration-300 shadow-sm hover:shadow-md"
                                            title="{{ $link['platform'] }}">
                                            <i class="{{ $icon }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endif