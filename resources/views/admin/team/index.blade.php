@extends('layouts.admin')

@section('title', 'Team Management')
@section('page-title', 'Team Members')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Team Members / Data Dosen</h2>
            <a href="{{ route('admin.team.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <i class="fas fa-plus"></i> Add Member
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <th class="p-4 border-b">Order</th>
                        <th class="p-4 border-b">Image</th>
                        <th class="p-4 border-b">Name</th>
                        <th class="p-4 border-b">Position</th>
                        <th class="p-4 border-b text-center">Status</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="team-list">
                    @forelse($members as $member)
                        <tr class="hover:bg-gray-50 transition-colors border-b last:border-0" data-id="{{ $member->id }}">
                            <td class="p-4 text-gray-500 font-mono text-sm cursor-move handle">
                                <i class="fas fa-grip-vertical text-gray-300 hover:text-gray-500"></i>
                            </td>
                            <td class="p-4">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 border border-gray-200">
                                    @if($member->image_url)
                                        <img src="{{ $member->image_url }}" alt="{{ $member->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 font-semibold text-gray-800">{{ $member->name }}</td>
                            <td class="p-4 text-gray-600">{{ $member->position ?? '-' }}</td>
                            <td class="p-4 text-center">
                                <button type="button" onclick="toggleStatus({{ $member->id }})"
                                    class="px-2 py-1 text-xs rounded-full font-semibold transition-colors duration-200 {{ $member->status ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                    title="Click to toggle status">
                                    <span
                                        id="status-text-{{ $member->id }}">{{ $member->status ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.team.edit', $member->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.team.destroy', $member->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                No team members found. Click "Add Member" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        function toggleStatus(id) {
            if (!confirm('Ubah status member ini?')) return;

            fetch(`/admin/team/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload or update UI
                        window.location.reload();
                    } else {
                        alert('Gagal mengubah status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengubah status.');
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('team-list');
            var sortable = Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function (evt) {
                    var itemEl = evt.item;  // dragged HTMLElement
                    var newIndex = evt.newIndex; // New index within parent container

                    // Collect all IDs in new order
                    var ids = [];
                    document.querySelectorAll('#team-list tr').forEach(function (row) {
                        ids.push(row.getAttribute('data-id'));
                    });

                    // Send to server
                    fetch('{{ route("admin.team.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: ids })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Optional: Show toast
                                console.log('Order updated');
                            } else {
                                alert('Failed to update order');
                            }
                        });
                }
            });
        });
    </script>
@endpush