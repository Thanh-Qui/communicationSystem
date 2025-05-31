<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Các phiên đăng nhập') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Quản lý và đăng xuất các phiên hoạt động của bạn trên các trình duyệt và thiết bị khác.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if (count($sessions) > 0)
            {{-- Other Browser Sessions --}}
            @foreach ($sessions as $session)
                <div class="flex items-center">
                    <div>
                        @if ($session->agent->isDesktop())
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;" class=" text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                            
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;" class=" text-gray-500">
                                <rect x="5" y="2" width="14" height="20" rx="3" ry="3"></rect>
                                <line x1="12" y1="18" x2="12" y2="18"></line>
                                <circle cx="12" cy="17" r="1"></circle>
                            </svg>
                        @endif
                    </div>

                    <div class="ms-3">
                        <div class="text-sm text-gray-600">
                            {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - 
                            {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }} - 
                            {{ $session->agent->isMobile() ? __('Mobile') : ($session->agent->isTablet() ? __('Tablet') : __('Desktop')) }}
                        </div>
                        
                        <div>
                            <div class="text-xs text-gray-500">
                                {{ $session->ip_address }},

                                @if ($session->is_current_device)
                                    <span style="color: green; font-weight: 600">{{ __('This device') }}</span>
                                @else
                                    {{ __('Last active') }} {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        @endif

        
        <div x-data="{ showModal: false }" x-init="showModal = @if($errors->has('password')) true @else false @endif">
            <!-- Nút mở modal -->
            <x-primary-button @click="showModal = true">
                {{ __('Log Out Other Browser Sessions') }}
            </x-primary-button>

            <!-- Modal -->
            <div x-show="showModal" class="fixed inset-0 flex items-center justify-center" style="background-color: rgb(31 41 55 / 21%);" 
                x-transition.opacity>
                <div class="bg-white p-6 rounded-lg shadow-lg w-96" @click.away="showModal = false">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Confirm Logout') }}</h2>
                    <p class="text-sm text-gray-600 mt-2">{{ __('Enter your password to log out all other sessions.') }}</p>

                    <form method="POST" action="{{ route('logout.other.sessions') }}">
                        @csrf
                        <div class="mt-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                       @error('password')
                            <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                       @enderror

                        <div class="mt-5 flex justify-end space-x-2">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 mt-2 mr-2 text-gray-600 bg-gray-200 rounded-md">
                                {{ __('Cancel') }}
                            </button>
                            <x-primary-button class="mt-2">{{ __('Confirm') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    </div>

</section>

