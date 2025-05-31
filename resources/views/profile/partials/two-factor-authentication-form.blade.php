<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Xác thực hai yếu tố') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Tăng cường bảo mật cho tài khoản của bạn bằng cách sử dụng xác thực hai yếu tố.') }}
        </p>
    </header>

    <div class="mt-6" x-data="{ showModal: @error('password2fa') true @else false @enderror  }">
        {{-- Button enabled 2 FA --}}
        @if (!auth()->user()->two_factor_code)
                <x-primary-button class="my-2" @click="showModal = true">
                    {{ __('Enabled') }}
                </x-primary-button>        
        @else
            {{-- Button diabled --}}
                <x-danger-button class="my-2" @click="showModal = true">
                    {{ __('Disabled') }}
                </x-danger-button>
        @endif
        
        <!-- Modal -->
        <div x-show="showModal" class="fixed inset-0 flex items-center justify-center" style="background-color: rgb(31 41 55 / 21%);" 
            x-transition.opacity>
            <div class="bg-white p-6 rounded-lg shadow-lg w-96" @click.away="showModal = false">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Confirm Password') }}</h2>
                <p class="text-sm text-gray-600 mt-2">{{ __('Enter your password to use Two Factor Authentication.') }}</p>

                <form method="POST" action="{{ route('2fa.create') }}">
                    @csrf
                    <div class="mt-4">
                        <input type="hidden" name="user_id" id="" value="{{$user->id}}">
                        <label for="password2fa" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input id="password2fa" name="password2fa" type="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    @error('password2fa')
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

      
</section>