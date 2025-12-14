@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">Edit Profile</h3>

        <div class="mt-8">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data"
                class="bg-white rounded-lg shadow-md p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column: User Info -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h4>

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                Email
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                                id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                Bio
                            </label>
                            <textarea
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('bio') border-red-500 @enderror"
                                id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                Phone
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('phone') border-red-500 @enderror"
                                id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column: Password & Photo -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Security & Avatar</h4>

                        <!-- Profile Photo -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_photo">
                                Profile Photo
                            </label>
                            <div class="flex items-center space-x-4">
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo"
                                        class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div
                                        class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-user text-2xl"></i>
                                    </div>
                                @endif
                                <input type="file" name="profile_photo" id="profile_photo"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    accept="image/*">
                            </div>
                            @error('profile_photo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Leave password fields empty if you don't want to change it.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">
                                Current Password
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('current_password') border-red-500 @enderror"
                                id="current_password" type="password" name="current_password"
                                placeholder="Required to set new password">
                            @error('current_password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                                New Password
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                                id="password" type="password" name="password" autocomplete="new-password">
                            @error('password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                                Confirm New Password
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="password_confirmation" type="password" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection