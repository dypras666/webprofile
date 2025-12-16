@extends('layouts.admin')

@section('title', 'Ads Management')
@section('page-title', 'Ads Management')

@push('styles')
    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
@endpush

@section('content')
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">All Ads</h2>
                
                <div class="flex items-center space-x-3">
                    <!-- Add New Button -->
                    <a href="{{ route('admin.ads.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Ad
                    </a>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Image
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Target URL
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ads as $ad)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ad->featured_image)
                                    <img class="h-12 w-20 rounded object-cover border border-gray-200" 
                                         src="{{ Storage::url($ad->featured_image) }}" 
                                         alt="{{ $ad->title }}">
                                @else
                                    <div class="h-12 w-20 rounded bg-gray-200 flex items-center justify-center border border-gray-200">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $ad->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $ad->category->name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate">
                                @if($ad->excerpt)
                                    <a href="{{ $ad->excerpt }}" target="_blank" class="text-blue-600 hover:underline text-sm truncate block">
                                        {{ $ad->excerpt }}
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge {{ $ad->is_published ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas fa-circle text-xs mr-1"></i>
                                    {{ $ad->is_published ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.ads.edit', $ad->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="inline-block" 
                                          onsubmit="return confirm('Are you sure you want to delete this ad?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-ad text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No ads found</p>
                                    <p class="text-sm mb-4">Get started by creating your first ad.</p>
                                    <a href="{{ route('admin.ads.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create New Ad
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $ads->links() }}
            </div>
        @endif
    </div>
@endsection