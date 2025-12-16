@extends('layouts.admin')

@section('title', 'Manajemen Komentar')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Komentar</h1>

            <form action="{{ route('admin.comments.rescan') }}" method="POST"
                onsubmit="return confirm('Proses ini akan mengecek ulang semua komentar dengan filter kata-kata terbaru. Lanjutkan?');">
                @csrf
                <button type="submit"
                    class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i> Scan Ulang Spam
                </button>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <a href="{{ route('admin.comments.index', ['status' => 'all']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition {{ $status == 'all' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-gray-500 text-sm">Total Komentar</div>
                <div class="text-2xl font-bold">{{ \App\Models\Comment::count() }}</div>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition {{ $status == 'pending' ? 'ring-2 ring-yellow-500' : '' }}">
                <div class="text-yellow-600 text-sm font-bold">Pending Review</div>
                <div class="text-2xl font-bold">{{ \App\Models\Comment::where('status', 'pending')->count() }}</div>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition {{ $status == 'spam' ? 'ring-2 ring-red-500' : '' }}">
                <div class="text-red-600 text-sm font-bold">Spam</div>
                <div class="text-2xl font-bold">{{ \App\Models\Comment::where('status', 'spam')->count() }}</div>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition {{ $status == 'approved' ? 'ring-2 ring-green-500' : '' }}">
                <div class="text-green-600 text-sm font-bold">Disetujui</div>
                <div class="text-2xl font-bold">{{ \App\Models\Comment::where('status', 'approved')->count() }}</div>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Komentar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Berita</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($comments as $comment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $comment->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $comment->email ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $comment->ip_address }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2 max-w-md" title="{{ $comment->content }}">
                                        {{ $comment->content }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('frontend.post', $comment->post->slug) }}" target="_blank"
                                        class="text-sm text-blue-600 hover:text-blue-900 truncate block max-w-xs">
                                        {{ $comment->post->title }} <i class="fas fa-external-link-alt text-xs ml-1"></i>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $comment->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($comment->status == 'approved')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                    @elseif($comment->status == 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($comment->status == 'spam')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Spam</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        @if($comment->status !== 'approved')
                                            <form action="{{ route('admin.comments.update-status', $comment->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($comment->status !== 'spam')
                                            <form action="{{ route('admin.comments.update-status', $comment->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="spam">
                                                <button type="submit" class="text-orange-600 hover:text-orange-900"
                                                    title="Mark as Spam">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($comment->status !== 'rejected')
                                            <form action="{{ route('admin.comments.update-status', $comment->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="text-gray-600 hover:text-gray-900" title="Reject">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <i class="far fa-comments text-4xl mb-3 text-gray-300"></i>
                                    <p>Tidak ada komentar ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $comments->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection