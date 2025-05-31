<div class="flex -space-x-2 overflow-hidden">
    @if($users && $users->count() > 0)
    @foreach($users->take($limit) as $user)
    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
        src="{{ $user->profile_photo_url }}"
        alt="{{ $user->name }}"
        title="{{ $user->name }}">
    @endforeach

    @if($users->count() > $limit)
    <span class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 ring-2 ring-white">
        <span class="text-xs text-gray-600">+{{ $users->count() - $limit }}</span>
    </span>
    @endif
    @else
    <span class="text-gray-400 text-sm">Sin usuarios asignados</span>
    @endif
</div>