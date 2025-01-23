<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <!-- Welcome Message -->
                    <h3 class="text-2xl font-bold text-gray-800">
                        Welcome, {{ Auth::user()->name }}!
                    </h3>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function timeAgo(unixtime) {
        const now = Math.floor(Date.now() / 1000);
        const diff = now - unixtime;

        const minutes = Math.floor(diff / 60);
        const hours = Math.floor(diff / 3600);
        const days = Math.floor(diff / (3600 * 24));
        const months = Math.floor(diff / (3600 * 24 * 30));

        // Case 1: Up to 5 minutes ago
        if (minutes < 5) {
            console.log("Case 1: just now");
            return "just now";
        }

        // Case 2: From 5 minutes to 1 hour ago
        if (minutes >= 5 && minutes < 60) {
            console.log(`Case 2: ${minutes} minute${minutes > 1 ? 's' : ''} ago`);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        }

        // Case 3: From 1 hour to 8 hours ago (rounded minutes to 5)
        if (hours >= 1 && hours < 8) {
            const roundedMinutes = Math.round(minutes / 5) * 5;
            console.log(`Case 3: ${hours} hour${hours > 1 ? 's' : ''} ${roundedMinutes % 60} minute${roundedMinutes % 60 !== 1 ? 's' : ''} ago`);
            return `${hours} hour${hours > 1 ? 's' : ''} ${roundedMinutes % 60} minute${roundedMinutes % 60 !== 1 ? 's' : ''} ago`;
        }

        // Case 4: From 8 hours to 1 day ago (rounded minutes to the nearest hour)
        if (hours >= 8 && hours < 24) {
            const roundedHours = Math.round(minutes / 60);
            console.log(`Case 4: ${roundedHours} hour${roundedHours > 1 ? 's' : ''} ago`);
            return `${roundedHours} hour${roundedHours > 1 ? 's' : ''} ago`;
        }

        // Case 5: From 1 day to 1 month ago
        if (days >= 1 && days < 30) {
            console.log(`Case 5: ${days} day${days > 1 ? 's' : ''} ago`);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }

        // Case 6: More than a month ago (formatted as dd.mm.yyyy)
        if (months >= 1) {
            const date = new Date(unixtime * 1000);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            console.log(`Case 6: ${day}.${month}.${year}`);
            return `${day}.${month}.${year}`;
        }
    }

</script>
