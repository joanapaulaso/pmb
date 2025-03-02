<!-- resources/views/components/public-profile.blade.php -->

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h1 class="text-2xl font-semibold mb-4">{{ $user->name }}'s Profile</h1>

            <p><strong>Email:</strong> {{ $user->email }}</p>

            <h2 class="text-xl font-semibold mt-6">Teams</h2>
            <ul>
                @forelse ($user->teams as $team)
                    <li>{{ $team->name }}</li>
                @empty
                    <li>No teams</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
