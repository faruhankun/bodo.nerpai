<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-xl text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <x-secondary-button>
            <a href="{{ route('lobby') }}">
                {{ __('Back to Lobby') }}
            </a>
        </x-secondary-button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-button2 :href="route('logout')" onclick="event.preventDefault();
                this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-button2>
        </form>
    </div>
</x-guest-layout>
