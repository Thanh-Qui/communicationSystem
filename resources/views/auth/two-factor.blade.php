<x-guest-layout>

    <div class="font-bold text-2xl my-2">Verify code 2 FA</div>
   
        <form method="POST" action="{{ route('2fa.check') }}">
            @csrf
            <div>
                <x-input-label for="two_factor_code" :value="__('Vui lòng nhập mã xác thực để thực hiện đăng nhập')" />
                <x-text-input class="mt-1 block w-full" type="text" name="two_factor_code" id="two_factor_code"/>
            </div>
            @error('two_factor_code')
                <p style="color: red; font-weight: 500; margin: 7px">{{$message}}</p>
            @enderror
            <div class="mt-4" style="text-align: end">
                
                <x-primary-button>{{__('Confirm')}}</x-primary-button>
            </div>
        </form>

        <form action="{{ route('2fa.resend') }}" method="POST">
            @csrf
            <div style="margin-top: -34px">
               <x-primary-button>{{__('Resend code')}}</x-primary-button> 
            </div>
            
        </form>
            
</x-guest-layout>
